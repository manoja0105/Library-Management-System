<?php
require_once "../../config/db.php";
require_once "../../includes/auth.php";

$error = "";

$students = $conn->query(
    "SELECT id,index_number,name FROM students ORDER BY name"
);

$books = $conn->query(
    "SELECT id,book_id,title,available_quantity 
     FROM books 
     WHERE available_quantity>0 
     ORDER BY title"
);

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $student = (int)$_POST["student_id"];
    $book = (int)$_POST["book_id"];
    $issue = $_POST["issue_date"];
    $due = $_POST["due_date"];

    if ($due < $issue) {

        $error = "Due date cannot be before issue date.";

    } else {

        $conn->begin_transaction();

        try {

            $s = $conn->prepare(
                "SELECT available_quantity FROM books WHERE id=? FOR UPDATE"
            );

            $s->bind_param("i", $book);
            $s->execute();

            $available = $s->get_result()->fetch_assoc()["available_quantity"] ?? 0;

            if ($available < 1) {
                throw new Exception("Book is not available.");
            }

            $s = $conn->prepare(
                "INSERT INTO borrowings(student_id,book_id,issue_date,due_date,status) 
                 VALUES(?,?,?,?, 'Issued')"
            );

            $s->bind_param(
                "iiss",
                $student,
                $book,
                $issue,
                $due
            );

            $s->execute();

            $s = $conn->prepare(
                "UPDATE books 
                 SET available_quantity=available_quantity-1 
                 WHERE id=?"
            );

            $s->bind_param("i", $book);
            $s->execute();

            $conn->commit();

            header("Location: index.php");
            exit();

        } catch (Exception $e) {

            $conn->rollback();
            $error = $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Issue Book</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>

<body>

<?php include "../../includes/navbar.php"; ?>

<div class="container">

    <div class="student-header">
    <h1>Add Student</h1>
    <a class="btn" href="index.php">← Back to Browwings</a>
</div>

    <?php if ($error): ?>

        <div class="alert error">
            <?= htmlspecialchars($error) ?>
        </div>

    <?php endif; ?>

    <form class="form-card" method="POST">

        <label>Student *</label>
        <select name="student_id" required>
            <option value="">Select student</option>
            <?php while ($s = $students->fetch_assoc()): ?>
                <option value="<?= $s["id"] ?>">
                    <?= htmlspecialchars($s["index_number"] . " - " . $s["name"]) ?>
                </option>

            <?php endwhile; ?>
        </select>


        <label>Available Book *</label>
        <select name="book_id" required>
            <option value="">Select book</option>
            <?php while ($b = $books->fetch_assoc()): ?>
                <option value="<?= $b["id"] ?>">
                    <?= htmlspecialchars(
                        $b["book_id"] . " - " . 
                        $b["title"] . " (" . 
                        $b["available_quantity"] . 
                        " available)"
                    ) ?>
                </option>

            <?php endwhile; ?>

        </select>


        <label>Issue Date *</label>
        <input 
            type="date" 
            name="issue_date" 
            value="<?= date('Y-m-d') ?>" 
            required>


        <label>Due Date *</label>

        <input 
            type="date" 
            name="due_date" 
            required>


        <button class="btn primary">
            Issue Book
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