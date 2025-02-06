<?php
session_start();
require_once 'functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $code = filter_var($_POST['code'], FILTER_SANITIZE_STRING);
    
    $conn = connectDB();
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND access_code = ?");
    $stmt->bind_param("ss", $email, $code);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['email'] = $email;
        
        // Generate new code for next login
        $new_code = generateCode();
        updateUserCode($email, $new_code);
        
        header('Location: dashboard.php');
    } else {
        header('Location: index.php?message=Invalid credentials');
    }
    
    $stmt->close();
    $conn->close();
} else {
    header('Location: index.php');
}
exit(); 