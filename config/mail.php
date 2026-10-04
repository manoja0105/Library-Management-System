<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . "/../vendor/autoload.php";

function sendLibraryEmail(
    string $to,
    string $subject,
    string $body
): bool {

    $mail = new PHPMailer(true);

    try {

        $mail->isSMTP();

        $mail->Host = "smtp.gmail.com";

        $mail->SMTPAuth = true;

        $mail->Username = "manojathevarajah@gmail.com";

        $mail->Password = "yveytzxzwibizfik";

        $mail->SMTPSecure =
            PHPMailer::ENCRYPTION_STARTTLS;

        $mail->Port = 587;

        $mail->setFrom(
            "manojathevarajah@gmail.com",
            "Library Management System"
        );

        $mail->addAddress($to);

        $mail->isHTML(true);

        $mail->Subject = $subject;

        $mail->Body = $body;

        $mail->send();

        return true;

    } catch (Exception $e) {

        throw new Exception(
            "Mailer Error: " .
            $mail->ErrorInfo
        );
    }
}