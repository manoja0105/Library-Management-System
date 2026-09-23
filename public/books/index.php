<?php
require_once "../../config/db.php";
require_once "../../includes/auth.php";

$q = trim($_GET["q"] ?? "");

if ($q !== "") {

    $like = "%" . $q . "%";

    $stmt = $conn->prepare("
        SELECT *
        FROM books
        WHERE book_id LIKE ?
           OR title LIKE ?
           OR author LIKE ?
           OR isbn LIKE ?
           OR category LIKE ?
        ORDER BY id DESC"
        );

    $stmt->bind_param(
        "sssss",
        $like,
        $like,
        $like,
        $like,
        $like
    );

    $stmt->execute();
    $books = $stmt->get_result();

} else {

    $books = $conn->query("
        SELECT *
        FROM books
        ORDER BY id DESC
    ");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Books</title>

    <link rel="stylesheet" href="../../assets/css/style.css">

</head>


<body>

<?php include "../../includes/navbar.php"; ?>

<div class="container">

    <div class="page-head">

        <h1>Books</h1>

        <a class="btn primary" href="add.php">
            + Add Book
        </a>

    </div>


    

    <form class="search-form" method="GET">

        <input
            type="text"
            name="q"
            value="<?= htmlspecialchars($q) ?>"
            placeholder="Search by Book ID, title, author, ISBN or category">

        <button type="submit" class="btn">
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
                    <th>ISBN</th>
                    <th>Category</th>
                    <th>Quantity</th>
                    <th>Available</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>

            <?php if ($books->num_rows > 0): ?>

                <?php while ($b = $books->fetch_assoc()): ?>
                    <tr>
                        <td>
                            <?= htmlspecialchars($b["book_id"]) ?>
                        </td>
                        <td>
                            <?= htmlspecialchars($b["title"]) ?>
                        </td>
                        <td>
                            <?= htmlspecialchars($b["author"]) ?>
                        </td>
                        <td>
                            <?= htmlspecialchars($b["isbn"] ?: "-") ?>
                        </td>
                        <td>
                            <?= htmlspecialchars($b["category"] ?: "-") ?>
                        </td>
                        <td>
                            <?= (int)$b["quantity"] ?>
                        </td>
                        <td>
                            <?= (int)$b["available_quantity"] ?>
                        </td>
                        <td>
                            <a class="btn small" href="edit.php?id=<?= (int)$b["id"] ?>">Edit</a>

                            <a class="btn small danger" href="delete.php?id=<?= (int)$b["id"] ?>"
                                onclick="return confirm('Are you sure you want to delete this book?')">Delete</a>
                        </td>
                    </tr>

                <?php endwhile; ?>

            <?php else: ?>

                <tr>
                    <td colspan="8" style="text-align:center;">
                        <?php if ($q !== ""): ?>
                            No books found for:
                            <strong><?= htmlspecialchars($q) ?></strong>

                        <?php else: ?>

                            No books available.

                        <?php endif; ?>
                    </td>
                </tr>

            <?php endif; ?>

            </tbody>
        </table>

    </div>

</div>
</body>

</html>