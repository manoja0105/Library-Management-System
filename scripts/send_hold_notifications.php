
<?php

date_default_timezone_set("Asia/Colombo");

require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../config/mail.php";

$logFile = __DIR__ . "/hold_notifications.log";

function writeLog($message)
{
    global $logFile;

    file_put_contents(
        $logFile,
        "[" . date("Y-m-d H:i:s") . "] " . $message . PHP_EOL,
        FILE_APPEND
    );
}

writeLog("========================================");
writeLog("Notification script started.");
writeLog("Current time: " . date("Y-m-d H:i:s"));


$sql = "
    SELECT
        h.id,
        h.email,
        h.hold_time,
        h.expiry_time,
        h.notification_sent,
        s.name AS student_name,
        s.index_number,
        b.book_id AS display_book_id,
        b.title
    FROM book_holds h

    INNER JOIN students s
        ON h.student_id = s.id

    INNER JOIN books b
        ON h.book_id = b.id

    WHERE h.status = 'ACTIVE'

      AND h.notification_sent = 0

      AND h.hold_time <= DATE_SUB(NOW(), INTERVAL 5 MINUTE)

    ORDER BY h.hold_time ASC
";

$result = $conn->query($sql);

if (!$result) {

    writeLog(
        "DATABASE ERROR: " .
        $conn->error
    );

    writeLog("Notification script finished.");
    writeLog("========================================");

    exit();
}

$count = $result->num_rows;

writeLog(
    "Eligible holds found: " .
    $count
);

if ($count === 0) {

    writeLog(
        "No ACTIVE hold requires a reminder."
    );

    writeLog("Notification script finished.");
    writeLog("========================================");

    exit();
}

while ($hold = $result->fetch_assoc()) {

    writeLog(
        "Processing Hold ID: " .
        $hold["id"] .
        " | Email: " .
        $hold["email"] .
        " | Hold time: " .
        $hold["hold_time"] .
        " | Expiry: " .
        $hold["expiry_time"]
    );

    try {

        
        $studentName = htmlspecialchars(
            $hold["student_name"],
            ENT_QUOTES,
            "UTF-8"
        );

        $bookId = htmlspecialchars(
            $hold["display_book_id"],
            ENT_QUOTES,
            "UTF-8"
        );

        $bookTitle = htmlspecialchars(
            $hold["title"],
            ENT_QUOTES,
            "UTF-8"
        );

        $expiryTime = htmlspecialchars(
            $hold["expiry_time"],
            ENT_QUOTES,
            "UTF-8"
        );

        $subject =
            "Library Book Hold Reminder";

        $body = "
        <!DOCTYPE html>
        <html>
        <body>

        <h2>Library Book Hold Reminder</h2>

        <p>
            Dear {$studentName},
        </p>

        <p>
            This is a reminder that you currently
            have the following book on hold:
        </p>

        <p>
            <strong>Book ID:</strong>
            {$bookId}
        </p>

        <p>
            <strong>Book:</strong>
            {$bookTitle}
        </p>

        <p>
            <strong>Hold expires at:</strong>
            {$expiryTime}
        </p>

        <p>
            Please borrow the book before the
            hold expires.
        </p>

        <p>
            Thank you.
        </p>

        </body>
        </html>
        ";

       
        sendLibraryEmail(
            $hold["email"],
            $subject,
            $body
        );

       
        $update = $conn->prepare("
            UPDATE book_holds
            SET
                notification_sent = 1,
                notification_sent_at = NOW()
            WHERE id = ?
              AND notification_sent = 0
              AND status = 'ACTIVE'
        ");

        if (!$update) {

            throw new Exception(
                "Prepare notification update failed: " .
                $conn->error
            );
        }

        $update->bind_param(
            "i",
            $hold["id"]
        );

        if (!$update->execute()) {

            throw new Exception(
                "Notification update failed: " .
                $update->error
            );
        }

        if ($update->affected_rows === 1) {

            writeLog(
                "EMAIL SENT SUCCESSFULLY. " .
                "Hold ID: " .
                $hold["id"] .
                " | Email: " .
                $hold["email"]
            );

        } else {

            writeLog(
                "Email sent, but database status " .
                "was not updated for Hold ID: " .
                $hold["id"]
            );
        }

        $update->close();

    } catch (Throwable $e) {

        writeLog(
            "EMAIL FAILED. " .
            "Hold ID: " .
            $hold["id"] .
            " | Error: " .
            $e->getMessage()
        );
    }
}

writeLog("Notification script finished.");
writeLog("========================================");

