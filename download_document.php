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
        // Get file extension
        $ext = strtolower(pathinfo($filepath, PATHINFO_EXTENSION));
        
        // Set appropriate content type based on file extension
        switch($ext) {
            case 'pdf':
                $content_type = 'application/pdf';
                break;
            case 'doc':
                $content_type = 'application/msword';
                break;
            case 'docx':
                $content_type = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
                break;
            case 'xls':
                $content_type = 'application/vnd.ms-excel';
                break;
            case 'xlsx':
                $content_type = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
                break;
            case 'txt':
                $content_type = 'text/plain';
                break;
            default:
                $content_type = 'application/octet-stream';
        }
        
        // Clear any previous output
        if(ob_get_level()) {
            ob_end_clean();
        }
        
        // Set headers
        header('Content-Description: File Transfer');
        header('Content-Type: ' . $content_type);
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filepath));
        
        // Output file
        readfile($filepath);
        exit();
    } else {
        die('File not found.');
    }
}

// If we get here, something went wrong (Ahhh Shit, Here We Go Again)
header("HTTP/1.0 404 Not Found");
echo "File not found";
?> 