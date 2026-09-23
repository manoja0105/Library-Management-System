<?php
require_once "../../config/db.php";
require_once "../../includes/auth.php";

$id = (int)($_GET["id"] ?? 0);

$s = $conn->prepare(
    "SELECT * FROM students WHERE id=?"
);

$s->bind_param("i", $id);
$s->execute();

$student = $s->get_result()->fetch_assoc();

if (!$student) {
    die("Student not found.");
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["name"]);
    $grade = trim($_POST["grade"]);
    $contact = trim($_POST["contact"]);
    $date = $_POST["registration_date"];

    $s = $conn->prepare(
        "UPDATE students 
         SET name=?,grade=?,contact_number=?,registration_date=? 
         WHERE id=?"
    );

    $s->bind_param(
        "ssssi",
        $name,
        $grade,
        $contact,
        $date,
        $id
    );

    if ($s->execute()) {

        header("Location: index.php");
        exit();

    }

    $error = "Update failed.";
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Edit Student</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>

<body>

<?php include "../../includes/navbar.php"; ?>

<div class="container">

    <h1>Edit Student</h1>

    <?php if ($error): ?>

        <div class="alert error">
            <?= htmlspecialchars($error) ?>
        </div>

    <?php endif; ?>

    <form class="form-card" method="POST">

        <label>Index Number</label>
        <input 
            value="<?= htmlspecialchars($student["index_number"]) ?>" 
            disabled>

        <label>Name *</label>
        <input 
            name="name" 
            value="<?= htmlspecialchars($student["name"]) ?>" 
            required>

        <label>Grade</label>
        <input 
            name="grade" 
            value="<?= htmlspecialchars($student["grade"]) ?>">

        <label>Contact</label>
        <input 
            name="contact" 
            value="<?= htmlspecialchars($student["contact_number"]) ?>">

        <label>Registration Date</label>
        <input 
            type="date" 
            name="registration_date" 
            value="<?= $student["registration_date"] ?>" 
            required>

        <button class="btn primary">
            Update
        </button>

        <a class="btn" href="index.php">
            Cancel
        </a>

    </form>

</div>

</body>
</html>