<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once 'config.php';

require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $code = sprintf("%06d", mt_rand(0, 999999));
        $_SESSION['verification_code'] = $code;
        $_SESSION['code_expiry'] = time() + (10 * 60); // 10 minutes expiry
        $_SESSION['email'] = $email;

        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = SMTP_HOST;  // Use the constant from config.php
            $mail->SMTPAuth = true;
            $mail->Username = SMTP_USERNAME; // Use the constant from config.php
            $mail->Password = SMTP_PASSWORD; // Use the constant from config.php
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // Use SMTPS for port 465
            $mail->Port = SMTP_PORT; // Use the constant from config.php

            // Recipients
            $mail->setFrom(SMTP_USERNAME, 'Document System');
            $mail->addAddress($email);

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Your Verification Code';
            $mail->Body = "Your verification code is: <b>$code</b>";

            // Send the email
            if ($mail->send()) {
                $success = "Code sent to your email. Please check your inbox.";
            } else {
                throw new Exception("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
            }
        } catch (Exception $e) {
            $error = "Error: " . $e->getMessage();
            error_log("Email Error: " . $e->getMessage()); // Log the error for debugging
        }
    } else {
        $error = "Please enter a valid email address.";
    }
}
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Code</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            background-color: var(--bs-body-bg);
        }
        .card {
            margin-top: -100px;
        }
        .loading {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 9999;
        }
        .loading-content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: white;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="loading" id="loadingSpinner">
        <div class="loading-content">
            <div class="spinner-border text-light mb-2" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <div>Sending code...</div>
        </div>
    </div>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="card shadow">
                    <div class="card-body p-4">
                        <h4 class="text-center mb-4">Request Verification Code</h4>
                        
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>
                        
                        <?php if ($success): ?>
                            <div class="alert alert-success"><?php echo $success; ?></div>
                            <form action="verify.php" method="POST">
                                <div class="mb-3">
                                    <label for="code" class="form-label">Enter Code</label>
                                    <input type="text" class="form-control" id="code" name="code" required>
                                </div>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">Verify Code</button>
                                </div>
                            </form>
                        <?php else: ?>
                            <form method="POST" id="requestForm">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email Address</label>
                                    <input type="email" class="form-control" id="email" name="email" required>
                                </div>
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary">Send Code</button>
                                    <a href="index.php" class="btn btn-light">Back to Login</a>
                                </div>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.getElementById('requestForm').addEventListener('submit', function() {
        document.getElementById('loadingSpinner').style.display = 'block';
    });
    </script>
</body>
</html> 