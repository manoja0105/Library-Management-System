<?php
require_once "../../config/db.php";
require_once "../../includes/auth.php";

$id = (int)($_GET["id"] ?? 0);

if ($id <= 0) {
    header("Location: index.php");
    exit();
}


$stmt = $conn->prepare("
    SELECT quantity, available_quantity
    FROM books
    WHERE id = ?");

$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: index.php");
    exit();
}

$book = $result->fetch_assoc();


if ($book["available_quantity"] < $book["quantity"]) {
    echo "<script>
        alert('This book cannot be deleted because some copies are currently borrowed.');
        window.location.href='index.php';
    </script>";
    exit();
}


$stmt = $conn->prepare("DELETE FROM books WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    header("Location: index.php");
    exit();
} else {
    echo "<script>
        alert('Unable to delete the book.');
        window.location.href='index.php';
    </script>";
    exit();
}
?>