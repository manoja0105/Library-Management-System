<?php 
require_once "../../config/db.php"; 
require_once "../../includes/auth.php"; 

$id = (int)($_GET["id"] ?? 0); 

$stmt = $conn->prepare("SELECT * FROM books WHERE id=?"); 
$stmt->bind_param("i", $id); 
$stmt->execute(); 
$book = $stmt->get_result()->fetch_assoc(); 

if (!$book) die("Book not found."); 

$error = ""; 

if ($_SERVER["REQUEST_METHOD"] === "POST") { 
    $title = trim($_POST["title"]); 
    $author = trim($_POST["author"]); 
    $isbn = trim($_POST["isbn"]); 
    $category = trim($_POST["category"]); 

    $newQty = (int)$_POST["quantity"]; 
    $oldQty = (int)$book["quantity"]; 
    $oldAvail = (int)$book["available_quantity"]; 

    $issued = $oldQty - $oldAvail; 

    if ($newQty < $issued) {
        $error = "Quantity cannot be less than currently issued copies."; 
    } else {
        $newAvail = $newQty - $issued; 

        $s = $conn->prepare(
            "UPDATE books SET title=?,author=?,isbn=?,category=?,quantity=?,available_quantity=? WHERE id=?"
        ); 

        $s->bind_param(
            "ssssiii",
            $title,
            $author,
            $isbn,
            $category,
            $newQty,
            $newAvail,
            $id
        ); 

        if ($s->execute()) {
            header("Location: index.php");
            exit();
        } 

        $error = "Update failed."; 
    } 
} 
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Edit Book</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>

<body>

<?php include "../../includes/navbar.php"; ?>

<div class="container">

    <h1>Edit Book</h1>

    <?php if ($error): ?>
        <div class="alert error">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form class="form-card" method="POST">

        <label>Book ID</label>
        <input value="<?= htmlspecialchars($book["book_id"]) ?>" disabled>

        <label>Title *</label>
        <input 
            name="title" 
            value="<?= htmlspecialchars($book["title"]) ?>" 
            required>

        <label>Author *</label>
        <input 
            name="author" 
            value="<?= htmlspecialchars($book["author"]) ?>" 
            required>

        <label>ISBN</label>
        <input 
            name="isbn" 
            value="<?= htmlspecialchars($book["isbn"]) ?>">

        <label>Category</label>
        <input 
            name="category" 
            value="<?= htmlspecialchars($book["category"]) ?>">

        <label>Quantity *</label>
        <input 
            type="number" 
            name="quantity" 
            min="1" 
            value="<?= $book["quantity"] ?>" 
            required>

        <button class="btn primary">Update</button>

        <a class="btn" href="index.php">Cancel</a>

    </form>

</div>

</body>
</html>