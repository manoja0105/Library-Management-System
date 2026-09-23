<?php
require_once "../../config/db.php";
require_once "../../includes/auth.php";

$id = (int)($_GET["id"] ?? 0);

$s = $conn->prepare(
    "DELETE FROM students 
     WHERE id=? 
     AND NOT EXISTS(
         SELECT 1 
         FROM borrowings 
         WHERE student_id=students.id
     )"
);

$s->bind_param("i", $id);
$s->execute();

header("Location: index.php");
exit();
?>