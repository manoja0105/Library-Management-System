<?php
require_once "../config/db.php";

$q = trim($_GET["q"] ?? "");

if ($q !== "") {

    $like = "%" . $q . "%";

    $stmt = $conn->prepare(
        "SELECT * FROM books 
         WHERE available_quantity > 0 
         AND (book_id LIKE ? OR title LIKE ? OR author LIKE ?) 
         ORDER BY title"
    );

    $stmt->bind_param("sss", $like, $like, $like);
    $stmt->execute();

    $books = $stmt->get_result();

} else {

    $books = $conn->query(
        "SELECT * FROM books 
         WHERE available_quantity > 0 
         ORDER BY title"
    );
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Library Books</title>

    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>

<nav class="navbar">

    <div class="logo">
        Library Management System
    </div>

    <a href="login.php">
        Librarian Login
    </a>

</nav>

<div class="container">
    <h1>Available Books</h1>
    <form class="search-form" method="GET">

        <input type="text" name="q" value="<?= htmlspecialchars($q) ?>"
         placeholder="Search by ID, title or author">

        <button class="btn primary">
            Search
        </button>

    </form>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Book ID</th>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Category</th>
                    <th>Available</th>
                </tr>
            </thead>
            <tbody>
                
            <?php while ($book = $books->fetch_assoc()): ?>
                <tr>
                    <td>
                         <?= htmlspecialchars($book["book_id"]) ?>
                    </td>
                    <td>
                        <?= htmlspecialchars($book["title"]) ?>
                    </td>
                    <td>
                        <?= htmlspecialchars($book["author"]) ?>
                     </td>

                     <td>
                        <?= htmlspecialchars($book["category"]) ?>
                    </td>

                    <td>
                        <?= (int)$book["available_quantity"] ?>
                    </td>

                    </tr>

                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>