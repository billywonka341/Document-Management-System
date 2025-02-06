<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $entered_code = $_POST['code'] ?? '';
    $stored_code = $_SESSION['verification_code'] ?? '';
    $expiry_time = $_SESSION['code_expiry'] ?? 0;
    
    if (empty($stored_code)) {
        $error = "No verification code found. Please request a new code.";
    } elseif (time() > $expiry_time) {
        $error = "Code has expired. Please request a new code.";
        unset($_SESSION['verification_code']);
        unset($_SESSION['code_expiry']);
    } elseif ($entered_code === $stored_code) {
        echo "Code is correct. Logging in...";
        $_SESSION['logged_in'] = true;
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Invalid code. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Code</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f8f9fa;
        }
        .verify-card {
            max-width: 400px;
            width: 90%;
            padding: 2rem;
            box-shadow: 0 0 15px rgba(0,0,0,.1);
            border-radius: 10px;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="card verify-card">
        <div class="card-body">
            <h3 class="text-center mb-4">Verify Code</h3>
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            <form method="POST">
                <div class="mb-3">
                    <label for="code" class="form-label">Enter Verification Code</label>
                    <input type="text" class="form-control" id="code" name="code" required>
                </div>
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">Verify Code</button>
                    <a href="index.php" class="btn btn-light">Request New Code</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 