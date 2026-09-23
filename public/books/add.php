<?php
require_once "../../config/db.php";
require_once "../../includes/auth.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $book_id  = trim($_POST["book_id"] ?? "");
    $title    = trim($_POST["title"] ?? "");
    $author   = trim($_POST["author"] ?? "");
    $isbn     = trim($_POST["isbn"] ?? "");
    $category = trim($_POST["category"] ?? "");
    $quantity = (int)($_POST["quantity"] ?? 0);

    // Validation
    if ($book_id === "" || $title === "" || $author === "" || $quantity < 1) {

        $error = "Please enter all required fields.";

    } else {

        $stmt = $conn->prepare("
            INSERT INTO books
            (book_id, title, author, isbn, category, quantity, available_quantity)
            VALUES (?, ?, ?, ?, ?, ?, ?)");

        $stmt->bind_param(
            "sssssii",
            $book_id,
            $title,
            $author,
            $isbn,
            $category,
            $quantity,
            $quantity
        );

        if ($stmt->execute()) {
            header("Location: index.php");
            exit();

        } else {
            if ($conn->errno == 1062) {
                $error = "Book ID already exists. Please use a different Book ID.";
            } else {
                $error = "Unable to save book.";
            }
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Add Book</title>

    <link rel="stylesheet" href="../../assets/css/style.css">
</head>

<body>

<?php include "../../includes/navbar.php"; ?>

<div class="container">

    <div class="page-head">
        <h1>Add Book</h1>
        <a class="btn" href="index.php">← Back to Books</a>
    </div>

    <?php if ($error): ?>
        <div class="alert error">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form class="form-card" method="POST">

        <label>Book ID *</label>
        <input
            type="text"
            name="book_id"
            placeholder="Example: B024"
            value="<?= htmlspecialchars($_POST["book_id"] ?? "") ?>"
            required>

        <label>Title *</label>
        <input
            type="text"
            name="title"
            placeholder="Enter book title"
            value="<?= htmlspecialchars($_POST["title"] ?? "") ?>"
            required>

        <label>Author *</label>
        <input
            type="text"
            name="author"
            placeholder="Enter author name"
            value="<?= htmlspecialchars($_POST["author"] ?? "") ?>"
            required >

        <label>ISBN</label>
        <input
            type="text"
            name="isbn"
            placeholder="Enter ISBN"
            value="<?= htmlspecialchars($_POST["isbn"] ?? "") ?>">

        <label>Category</label>
        <input
            type="text"
            name="category"
            placeholder="Example: Fiction, Fantasy, GK"
            value="<?= htmlspecialchars($_POST["category"] ?? "") ?>">

        <label>Quantity *</label>
        <input
            type="number"
            name="quantity"
            min="1"
            value="<?= htmlspecialchars($_POST["quantity"] ?? "1") ?>"
            required>

        <br>

        <button type="submit" class="btn primary">
            Save Book
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

   



