<?php
require_once "../../config/db.php";
require_once "../../includes/auth.php";

$borrowings = $conn->query(
    "SELECT br.*,s.index_number,s.name student_name,b.book_id,b.title 
     FROM borrowings br 
     JOIN students s ON br.student_id=s.id 
     JOIN books b ON br.book_id=b.id 
     ORDER BY br.id DESC"
);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Borrowings</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>

<body>

<?php include "../../includes/navbar.php"; ?>

<div class="container">

    <div class="page-head">
        <h1>Borrowings</h1>

         <a class="btn primary" href="issue.php">
            + Issue Book </a>
    </div>

    <div class="table-wrap">

        <table>

            <tr>
                <th>Student</th>
                <th>Book</th>
                <th>Issue</th>
                <th>Due</th>
                <th>Return</th>
                <th>Status</th>
                <th>Action</th>
            </tr>

            <?php while ($r = $borrowings->fetch_assoc()): ?>

                <tr>
                    <td>
                        <?= htmlspecialchars($r["index_number"] . " - " . $r["student_name"]) ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($r["book_id"] . " - " . $r["title"]) ?>
                    </td>

                    <td>
                        <?= $r["issue_date"] ?>
                    </td>

                    <td>
                        <?= $r["due_date"] ?>
                    </td>

                    <td>
                        <?= $r["return_date"] ?? "-" ?>
                    </td>

                    <td>
                        <span class="badge">
                            <?= $r["status"] ?>
                        </span>
                    </td>

                    <td>
                        <?php if ($r["status"] === "Issued"): ?>

                            <a class="btn small" href="return.php?id=<?= $r["id"] ?>">
                                Return
                            </a>

                        <?php else: ?>

                            -

                        <?php endif; ?>
                    </td>
                </tr>

            <?php endwhile; ?>
        </table>
    </div>

</div>

</body>
</html>