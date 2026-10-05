<?php
require_once "../../config/db.php";
require_once "../../includes/auth.php";


if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.php");
    exit();
}


$id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    $_SESSION["hold_admin_error"] = "Invalid hold.";
    header("Location: index.php");
    exit();
}

$conn->begin_transaction();

try {
    $holdStmt = $conn->prepare(
        "SELECT id, book_id, status, expiry_time
         FROM book_holds
         WHERE id=?
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
        throw new Exception("This hold has already been cancelled.");
    }

    if ($hold["status"] === "BORROWED") {
        throw new Exception("This hold has already been converted to a borrowing.");
    }

    if ($hold["status"] === "EXPIRED") {
        throw new Exception("This hold has expired.");
    }

    $newStatus = (strtotime($hold["expiry_time"]) <= time())
        ? "EXPIRED"
        : "CANCELLED";

    $bookStmt = $conn->prepare(
        "SELECT quantity, available_quantity
         FROM books
         WHERE id=?
         FOR UPDATE"
    );
    $bookStmt->bind_param("i", $hold["book_id"]);
    $bookStmt->execute();
    $book = $bookStmt->get_result()->fetch_assoc();

    if (!$book) {
        throw new Exception("Book not found.");
    }

    $newAvailable = min(
        (int)$book["quantity"],
        (int)$book["available_quantity"] + 1
    );

    $updateBook = $conn->prepare(
        "UPDATE books SET available_quantity=? WHERE id=?"
    );
    $updateBook->bind_param("ii", $newAvailable, $hold["book_id"]);
    $updateBook->execute();

    $updateHold = $conn->prepare(
        "UPDATE book_holds
         SET status=?
         WHERE id=? AND status='ACTIVE'"
    );
    $updateHold->bind_param("si", $newStatus, $id);
    $updateHold->execute();

    if ($updateHold->affected_rows !== 1) {
        throw new Exception("This hold is no longer active.");
    }

    $conn->commit();

    $_SESSION["hold_admin_message"] =
        $newStatus === "EXPIRED"
            ? "This hold had already expired and its reserved copy was restored."
            : "Hold cancelled successfully.";

} catch (Throwable $e) {
    $conn->rollback();
    $_SESSION["hold_admin_error"] = $e->getMessage();
}

header("Location: index.php");
exit();
