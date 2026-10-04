<?php

date_default_timezone_set("Asia/Colombo");

require_once __DIR__ . "/../config/mail.php";

echo "Starting email test..." . PHP_EOL;

try {

    sendLibraryEmail(
        "manomanoja111@gmail.com",
        "Library Management System - SMTP Test",
        "
        <h2>SMTP Test Successful</h2>
        <p>This is a test email from the Library Management System.</p>
        <p>PHPMailer + Gmail SMTP is working correctly.</p>
        "
    );

    echo "EMAIL SENT SUCCESSFULLY." . PHP_EOL;

} catch (Throwable $e) {

    echo "EMAIL FAILED." . PHP_EOL;
    echo $e->getMessage() . PHP_EOL;
}
