<?php
require_once "../../config/db.php";
require_once "../../includes/auth.php";

$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);
if (!$id || $id < 1) {
    header("Location: index.php");
    exit();
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    

    $dueDate = $_POST["due_date"] ?? "";

    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $dueDate)) {
        $error = "Please enter a valid due date.";
    } elseif ($dueDate < date("Y-m-d")) {
        $error = "Due date cannot be before today.";
    } else {
        $conn->begin_transaction();

        try {
            
            $holdStmt = $conn->prepare(
                "SELECT h.id, h.student_id, h.book_id, h.status, h.expiry_time,
                        s.name AS student_name,
                        b.title, b.book_id AS display_book_id
                 FROM book_holds h
                 JOIN students s ON h.student_id=s.id
                 JOIN books b ON h.book_id=b.id
                 WHERE h.id=?
                 LIMIT 1
                 FOR UPDATE"
            );
            $holdStmt->bind_param("i", $id);
            $holdStmt->execute();
            $hold = $holdStmt->get_result()->fetch_assoc();

            if (!$hold) {
                throw new Exception("Hold not found.");
            }

            if ($hold["status"] === "CANCELLED") {
                throw new Exception("This hold has been cancelled.");
            }

            if ($hold["status"] === "EXPIRED") {
                throw new Exception("This hold has expired.");
            }

            if ($hold["status"] === "BORROWED") {
                throw new Exception("This hold has already been converted to a borrowing.");
            }

            
            if (strtotime($hold["expiry_time"]) <= time()) {
                $bookStmt = $conn->prepare(
                    "SELECT quantity, available_quantity
                     FROM books WHERE id=? FOR UPDATE"
                );
                $bookStmt->bind_param("i", $hold["book_id"]);
                $bookStmt->execute();
                $book = $bookStmt->get_result()->fetch_assoc();

                $newAvailable = min(
                    (int)$book["quantity"],
                    (int)$book["available_quantity"] + 1
                );

                $updateBook = $conn->prepare(
                    "UPDATE books SET available_quantity=? WHERE id=?"
                );
                $updateBook->bind_param("ii", $newAvailable, $hold["book_id"]);
                $updateBook->execute();

                $expire = $conn->prepare(
                    "UPDATE book_holds SET status='EXPIRED'
                     WHERE id=? AND status='ACTIVE'"
                );
                $expire->bind_param("i", $id);
                $expire->execute();

                $conn->commit();
                throw new Exception("This hold has expired.");
            }

            
            $insert = $conn->prepare(
                "INSERT INTO borrowings
                    (student_id, book_id, issue_date, due_date, return_date, status)
                 VALUES (?, ?, CURDATE(), ?, NULL, 'Issued')"
            );
            $insert->bind_param(
                "iis",
                $hold["student_id"],
                $hold["book_id"],
                $dueDate
            );
            $insert->execute();

            $updateHold = $conn->prepare(
                "UPDATE book_holds
                 SET status='BORROWED'
                 WHERE id=? AND status='ACTIVE'"
            );
            $updateHold->bind_param("i", $id);
            $updateHold->execute();

            if ($updateHold->affected_rows !== 1) {
                throw new Exception("This hold is no longer active.");
            }

            $conn->commit();

            $_SESSION["hold_admin_message"] = "Book hold converted to borrowing successfully.";
            header("Location: index.php");
            exit();

        } catch (Throwable $e) {
            try {
                $conn->rollback();
            } catch (Throwable $ignored) {
            }
            $error = $e->getMessage();
        }
    }
}


$stmt = $conn->prepare(
    "SELECT h.*, s.name AS student_name, s.index_number,
            b.book_id AS display_book_id, b.title
     FROM book_holds h
     JOIN students s ON h.student_id=s.id
     JOIN books b ON h.book_id=b.id
     WHERE h.id=?"
);
$stmt->bind_param("i", $id);
$stmt->execute();
$hold = $stmt->get_result()->fetch_assoc();

if (!$hold) {
    exit("Hold not found.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Borrow From Hold</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>

<?php include "../../includes/navbar.php"; ?>

<div class="container">
    <div class="page-head">
        <h1>Borrow From Hold</h1>
        <a class="btn" href="index.php">← Back to Holds</a>
    </div>

    <?php if ($error): ?>
        <div class="alert error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="form-card">
        <p><strong>Student:</strong> <?= htmlspecialchars($hold["index_number"] . " - " . $hold["student_name"]) ?></p>
        <p><strong>Book:</strong> <?= htmlspecialchars($hold["display_book_id"] . " - " . $hold["title"]) ?></p>
        <p><strong>Hold Time:</strong> <?= htmlspecialchars(date("d M Y, h:i A", strtotime($hold["hold_time"]))) ?></p>
        <p><strong>Expiry Time:</strong> <?= htmlspecialchars(date("d M Y, h:i A", strtotime($hold["expiry_time"]))) ?></p>

        <?php if ($hold["status"] === "ACTIVE"): ?>
            <form method="POST">
               

                <label for="due_date">Due Date *</label>
                <input type="date"
                       id="due_date"
                       name="due_date"
                       min="<?= date("Y-m-d") ?>"
                       required>

                <button class="btn success" type="submit">
                    Confirm Borrow
                </button>

                <a class="btn" href="index.php">Cancel</a>
            </form>
        <?php else: ?>
            <div class="alert error">
                This hold is no longer active.
            </div>
            <a class="btn" href="index.php">Back to Holds</a>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
