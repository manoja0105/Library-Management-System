<?php
require_once "../../config/db.php";
require_once "../../includes/auth.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $idx = trim($_POST["index_number"]);
    $name = trim($_POST["name"]);
    $grade = trim($_POST["grade"]);
    $contact = trim($_POST["contact"]);
    $date = $_POST["registration_date"];

    if ($idx === "" || $name === "" || $date === "") {

        $error = "Please enter required fields.";

    } else {

        $s = $conn->prepare(
            "INSERT INTO students(index_number,name,grade,contact_number,registration_date) 
             VALUES(?,?,?,?,?)"
        );

        $s->bind_param(
            "sssss",
            $idx,
            $name,
            $grade,
            $contact,
            $date
        );

        if ($s->execute()) {

            header("Location: index.php");
            exit();

        }

        $error = "Index number may already exist.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Add Student</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>

<body>

<?php include "../../includes/navbar.php"; ?>

<div class="container">

    <div class="student-header">
    <h1>Add Student</h1>

    <a href="index.php" class="btn back-btn">
        ← Back to Students
    </a>
</div>
    <?php if ($error): ?>

        <div class="alert error">
            <?= htmlspecialchars($error) ?>
        </div>

    <?php endif; ?>

    <form class="form-card" method="POST">

        <label>Index Number *</label>
        <input name="index_number" required>

        <label>Student Name *</label>
        <input name="name" required>

        <label>Grade</label>
        <input name="grade">

        <label>Contact Number</label>
        <input name="contact">

        <label>Registration Date *</label>
        <input 
            type="date" 
            name="registration_date" 
            value="<?= date('Y-m-d') ?>" 
            required
        >

        <button class="btn primary">
            Save Student
        </button>

        <button type="button" class="btn" onclick="clearForm()">
        Cancel
    </button>

    </form>

</div>
<script>
function clearForm() {
    document.querySelector(".form-card").reset();
}
</script>
</body>
</html>