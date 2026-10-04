<?php

require_once __DIR__ . "/../config/db.php";



$sql = "
    SELECT id, book_id
    FROM book_holds
    WHERE status = 'ACTIVE'
      AND expiry_time <= NOW()
";

$result = $conn->query($sql);

if (!$result) {
    die("Database query failed: " . $conn->error . PHP_EOL);
}

$count = 0;

while ($hold = $result->fetch_assoc()) {

    $holdId = (int)$hold['id'];
    $bookId = (int)$hold['book_id'];

    $conn->begin_transaction();

    try {

       
        $stmt = $conn->prepare("
            SELECT status
            FROM book_holds
            WHERE id = ?
            FOR UPDATE
        ");

        if (!$stmt) {
            throw new Exception($conn->error);
        }

        $stmt->bind_param("i", $holdId);
        $stmt->execute();

        $check = $stmt->get_result();
        $current = $check->fetch_assoc();

        $stmt->close();

       
        if (!$current || $current['status'] !== 'ACTIVE') {
            $conn->rollback();
            continue;
        }

       
        $stmt = $conn->prepare("
            UPDATE book_holds
            SET status = 'EXPIRED'
            WHERE id = ?
              AND status = 'ACTIVE'
        ");

        if (!$stmt) {
            throw new Exception($conn->error);
        }

        $stmt->bind_param("i", $holdId);
        $stmt->execute();

        $affected = $stmt->affected_rows;

        $stmt->close();

        
        if ($affected === 1) {

            $stmt = $conn->prepare("
                UPDATE books
                SET available_quantity = available_quantity + 1
                WHERE id = ?
            ");

            if (!$stmt) {
                throw new Exception($conn->error);
            }

            $stmt->bind_param("i", $bookId);
            $stmt->execute();

            $stmt->close();

            $conn->commit();

            $count++;

            echo "Hold #{$holdId} expired successfully." . PHP_EOL;

        } else {

            $conn->rollback();
        }

    } catch (Throwable $e) {

        $conn->rollback();

        echo "Error processing hold #{$holdId}: "
             . $e->getMessage()
             . PHP_EOL;
    }
}

echo "Expiry script completed. Expired holds: {$count}" . PHP_EOL;