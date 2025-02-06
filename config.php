<?php
//DB Setup can be empty(Optional)
define('DB_HOST', 'localhost');//Not Required
define('DB_USER', 'your_username');//Not Required
define('DB_PASS', 'your_password');//Not Required
define('DB_NAME', 'your_dbName');//Not Required

// Email configuration(Must)
define('SMTP_HOST', 'smtp.hostexample.com');
define('SMTP_USERNAME', 'your_smtp_username');
define('SMTP_PASSWORD', 'your_smtp_password');
define('SMTP_PORT', portnumber);
define('SMTP_SECURE', 'ssl');

// Allowed Email(Must)
define('auth_email', 'user@example.com'); //Enter the Allowed Email, on which you want to perform delete and upload feature.

// Allowed email addresses(Must)
$allowed_emails = ['user@example.com', 'user2@example.com']; //Allowed Emails to Login.
?> 