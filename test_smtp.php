<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

$mail = new PHPMailer(true);

try {
    $mail->SMTPDebug = 2; // Enable verbose debug output
    $mail->isSMTP(); // Set mailer to use SMTP
    $mail->Host = SMTP_HOST;  // Use the constant from config.php
    $mail->SMTPAuth = true;
    $mail->Username = SMTP_USERNAME; // Use the constant from config.php
    $mail->Password = SMTP_PASSWORD; // Use the constant from config.php
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // Use SMTPS for port 465
    $mail->Port = SMTP_PORT; // Use the constant from config.php
    

    $mail->setFrom('documents@example.com', 'Test'); // Use the same email as Username
    $mail->addAddress('Test@document.com'); // Replace with a test recipient

    $mail->Subject = 'Test Email';
    $mail->Body    = 'This is a test email.';

    $mail->send();
    echo 'Email sent successfully.';
} catch (Exception $e) {
    echo "Email could not be sent. Mailer Error: {$mail->ErrorInfo}";
} 