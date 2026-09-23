<?php
require_once "../../config/db.php";
require_once "../../includes/auth.php";

$id = (int)($_GET["id"] ?? 0);

$s = $conn->prepare(
    "SELECT br.*,b.title,s.name 
     FROM borrowings br 
     JOIN books b ON br.book_id=b.id 
     JOIN students s ON br.student_id=s.id 
     WHERE br.id=? AND br.status='Issued'"
);

$s->bind_param("i", $id);
$s->execute();

$row = $s->get_result()->fetch_assoc();

if (!$row) {
    die("Borrowing record not found.");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $conn->begin_transaction();

    try {

        $s = $conn->prepare(
            "UPDATE borrowings 
             SET return_date=CURDATE(),status='Returned' 
             WHERE id=? AND status='Issued'"
        );

        $s->bind_param("i", $id);
        $s->execute();

        $s = $conn->prepare(
            "UPDATE books 
             SET available_quantity=available_quantity+1 
             WHERE id=?"
        );

        $s->bind_param("i", $row["book_id"]);
        $s->execute();

        $conn->commit();

        header("Location: index.php");
        exit();

    } catch (Exception $e) {

        $conn->rollback();
        die("Return failed.");
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Return Book</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>

<body>

<?php include "../../includes/navbar.php"; ?>

<div class="container">

    <div class="form-card">

        <h1>Confirm Return</h1>

        <p>
            Return 
            <strong>
                <?= htmlspecialchars($row["title"]) ?>
            </strong>
            borrowed by 
            <strong>
                <?= htmlspecialchars($row["name"]) ?>
            </strong>?
        </p>



        <form method="POST">

            <button class="btn primary">
                Confirm Return
            </button>

            <a class="btn" href="index.php">
                Cancel
            </a>

        </form>

        

    </div>

</div>

</body>
</html> 