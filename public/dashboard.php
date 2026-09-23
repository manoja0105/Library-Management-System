<?php
require_once "../config/db.php";
require_once "../includes/auth.php";

$totalBooks = $conn->query(
    "SELECT COUNT(*) total FROM books"
)->fetch_assoc()["total"];

$totalStudents = $conn->query(
    "SELECT COUNT(*) total FROM students"
)->fetch_assoc()["total"];

$availableBooks = $conn->query(
    "SELECT COALESCE(SUM(available_quantity),0) total FROM books"
)->fetch_assoc()["total"];

$issuedBooks = $conn->query(
    "SELECT COUNT(*) total FROM borrowings WHERE status='Issued'"
)->fetch_assoc()["total"];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>

    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>

<?php include "../includes/navbar.php"; ?>

<div class="container">
    <h1>Dashboard</h1>
    <div class="dashboard">

        <div class="card">
            <h3>Total Books</h3>
            <p><?= $totalBooks ?></p>
        </div>

        <div class="card">
            <h3>Total Students</h3>
            <p><?= $totalStudents ?></p>
        </div>

        <div class="card">
            <h3>Available Copies</h3>
            <p><?= $availableBooks ?></p>
        </div>

        <div class="card">
            <h3>Issued Books</h3>
            <p><?= $issuedBooks ?></p>
        </div>

    </div>
</div>

<script src="../../assets/js/script.js"></script>

</body>
</html>