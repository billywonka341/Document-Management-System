<?php
session_start();

if (!isset($_SESSION['logged_in'])) {
    die('Unauthorized access');
}

if (isset($_GET['file'])) {
    $filename = basename($_GET['file']); // Sanitize filename
    $filepath = './uploads/' . $filename;
    
    // Validate file path to prevent directory traversal
    $realpath = realpath($filepath);
    $uploadsDir = realpath('./uploads/');
    
    if ($realpath === false || strpos($realpath, $uploadsDir) !== 0) {
        die('Invalid file path');
    }
    
    if (file_exists($filepath)) {
        $mime_type = mime_content_type($filepath);
        
        // Set proper headers based on file type
        header('Content-Type: ' . $mime_type);
        header('Content-Length: ' . filesize($filepath));
        
        // For PDFs and images, display in browser
        if ($mime_type === 'application/pdf' || strpos($mime_type, 'image/') === 0) {
            header('Content-Disposition: inline; filename="' . $filename . '"');
        } else {
            // For other files, force download
            header('Content-Disposition: attachment; filename="' . $filename . '"');
        }
        
        // Output file contents
        readfile($filepath);
        exit;
    }
}

// If file not found or invalid
header("HTTP/1.0 404 Not Found");
echo "File not found"; 