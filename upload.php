<?php
session_start();
require_once 'config.php';
require_once 'functions.php';

// Define the allowed email for upload actions
$allowed_email = auth_email;

// Check if the user is allowed to upload documents
$is_allowed_user = isset($_SESSION['email']) && $_SESSION['email'] === $allowed_email;

if (!$is_allowed_user) {
    echo "<div class='alert alert-danger'>You are not authorized to upload documents.</div>";
    exit();
}

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['document'])) {
    $upload_dir = './uploads/';
    $file_name = basename($_FILES['document']['name']);
    $target_file = $upload_dir . $file_name;

    // Move the uploaded file to the uploads directory
    if (move_uploaded_file($_FILES['document']['tmp_name'], $target_file)) {
        echo "<div class='alert alert-success'>File uploaded successfully.</div>";
        header('Location: dashboard.php');
    } else {
        echo "<div class='alert alert-danger'>Error uploading file.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #e9ecef;
            font-family: 'Arial', sans-serif;
        }
        .container {
            max-width: 600px;
            margin: 50px auto;
            padding: 30px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #007bff;
        }
        .form-label {
            font-weight: bold;
        }
        .btn-custom {
            width: 100%;
            border-radius: 5px;
            transition: background-color 0.3s, transform 0.2s;
        }
        .btn-custom:hover {
            background-color: #0056b3;
            transform: translateY(-2px);
        }
        footer {
            text-align: center;
            margin-top: 20px;
            font-size: 0.9em;
            color: #6c757d;
        }
        .btn-outline-secondary {
            border-color: #007bff;
            color: #007bff;
        }
        .btn-outline-secondary:hover {
            background-color: #007bff;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2><i class="fas fa-upload"></i> Upload Document</h2>
        <form action="" method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="document" class="form-label">Select Document</label>
                <input type="file" class="form-control" name="document" id="document" required>
            </div>
            <button type="submit" class="btn btn-primary btn-custom">Upload</button>
        </form>
        <div class="mt-3">
            <a href="dashboard.php" class="btn btn-secondary btn-custom">Dashboard</a>
            <a href="documents.php" class="btn btn-outline-secondary btn-custom">Documents</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 