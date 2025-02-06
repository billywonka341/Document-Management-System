<?php
session_start();
session_destroy(); // Clear any existing sessions
session_start(); // Start a new session
require_once 'config.php';
require_once 'functions.php';

// Include PHPMailer classes
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';

    // Check if the email is allowed
    if (in_array($email, $allowed_emails)) {
        // Generate a verification code
        $code = sprintf("%06d", mt_rand(0, 999999));
        $_SESSION['verification_code'] = $code;
        $_SESSION['code_expiry'] = time() + (10 * 60); // 10 minutes expiry
        $_SESSION['email'] = $email;

        // Send the verification code via email
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = SMTP_HOST;  // Use the constant from config.php
            $mail->SMTPAuth = true;
            $mail->Username = SMTP_USERNAME; // Use the constant from config.php
            $mail->Password = SMTP_PASSWORD; // Use the constant from config.php
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // Use SMTPS for port 465
            $mail->Port = SMTP_PORT; // Use the constant from config.php

            $mail->setFrom(SMTP_USERNAME, 'Document System');
            $mail->addAddress($email);
            $mail->Subject = 'Your Verification Code';
            $mail->Body = "Your verification code is: <b>$code</b>";

            $mail->send();
            $success = "Code sent to your email. Please check your inbox.";
        } catch (Exception $e) {
            $error = "Failed to send code. Mailer Error: {$mail->ErrorInfo}";
        }
    } else {
        $error = "This email address is not allowed.";
    }
}
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Verification Code</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Dark mode styles */
        [data-bs-theme="dark"] {
            --bs-body-bg: #1a1d20;
            --bs-body-color: #e1e1e1;
        }
        
        [data-bs-theme="dark"] .card {
            background: #242729;
            border-color: #2c3035;
        }
        
        [data-bs-theme="dark"] .form-control {
            background-color: #242729;
            border-color: #2c3035;
            color: #e1e1e1;
        }
        
        [data-bs-theme="dark"] .form-control:focus {
            background-color: #242729;
            border-color: #0d6efd;
            color: #e1e1e1;
        }
        
        [data-bs-theme="dark"] .text-muted {
            color: #a1a1a1 !important;
        }
        
        .theme-toggle {
            position: fixed;
            top: 1rem;
            right: 1rem;
            z-index: 1000;
        }
        
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: var(--bs-body-bg);
        }
        
        .login-card {
            max-width: 400px;
            width: 90%;
            margin: 0 auto;
            padding: 2rem;
            box-shadow: 0 0 15px rgba(0,0,0,.1);
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <button class="btn btn-outline-primary theme-toggle" id="themeToggle">
        <i class="fas fa-moon"></i>
    </button>

    <div class="container">
        <div class="card login-card">
            <div class="card-body">
                <h3 class="text-center mb-4">Request Verification Code</h3>
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
                    <form method="POST">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Send Code</button>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const themeToggle = document.getElementById('themeToggle');
        const html = document.documentElement;
        const icon = themeToggle.querySelector('i');
        
        // Check for saved theme preference
        const savedTheme = localStorage.getItem('theme');
        if (savedTheme) {
            html.setAttribute('data-bs-theme', savedTheme);
            updateIcon(savedTheme === 'dark');
        }
        
        themeToggle.addEventListener('click', function() {
            const currentTheme = html.getAttribute('data-bs-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            
            html.setAttribute('data-bs-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            updateIcon(newTheme === 'dark');
        });
        
        function updateIcon(isDark) {
            icon.className = isDark ? 'fas fa-sun' : 'fas fa-moon';
        }
    });

    // Prevent form resubmission on page refresh
    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }
    </script>
</body>
</html> 