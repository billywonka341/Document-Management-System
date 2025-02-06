<?php
require_once 'config.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Allowed email list (Not in Use, Only Config.php will be used)
const ALLOWED_EMAILS = [
    'user1@example.com',
    'user2@example.com',
    'user3@example.com'
    // Add more emails as needed
];

function generateCode() {
    return sprintf("%06d", mt_rand(0, 999999));
}

function isEmailAllowed($email) {
    return in_array(strtolower($email), array_map('strtolower', ALLOWED_EMAILS));
}

//Not in Use, Follow the Index.php Function
function sendAccessCode($email, $code) {
    $mail = new PHPMailer(true);
    
    try {
        $mail->isSMTP();
        $mail->Host = SMTP_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = SMTP_USER;
        $mail->Password = SMTP_PASS;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = SMTP_PORT;

        $mail->setFrom(SMTP_USER, 'Document System');
        $mail->addAddress($email);
        $mail->isHTML(true);
        $mail->Subject = 'Your Document Access Code';
        $mail->Body = "Your access code is: <b>{$code}</b>";

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

function updateUserCode($email, $code) {
    $conn = connectDB();
    $stmt = $conn->prepare("INSERT INTO users (email, access_code) VALUES (?, ?) ON DUPLICATE KEY UPDATE access_code = ?");
    $stmt->bind_param("sss", $email, $code, $code);
    $stmt->execute();
    $stmt->close();
    $conn->close();
} 