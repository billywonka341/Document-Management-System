<?php
session_start();

// Function to get all documents
function getDocuments() {
    $documents = array();
    $upload_dir = './uploads/';
    
    if (is_dir($upload_dir)) {
        $files = scandir($upload_dir);
        
        foreach ($files as $file) {
            if ($file != '.' && $file != '..') {
                $file_path = $upload_dir . $file;
                $documents[] = array(
                    'name' => $file,
                    'size' => filesize($file_path),
                    'date' => date("Y-m-d H:i:s", filemtime($file_path)),
                    'type' => pathinfo($file_path, PATHINFO_EXTENSION)
                );
            }
        }
    }
    
    return $documents;
}

// Get documents
$documents = getDocuments();

// Set headers for CSV download
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="documents_report_' . date('Y-m-d') . '.csv"');

// Create CSV file
$output = fopen('php://output', 'w');

// Add UTF-8 BOM for proper Excel display
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// Add headers
fputcsv($output, array('Document Name', 'Type', 'Size (KB)', 'Upload Date'));

// Add data
foreach ($documents as $doc) {
    fputcsv($output, array(
        $doc['name'],
        strtoupper($doc['type']),
        number_format($doc['size'] / 1024, 2),
        $doc['date']
    ));
}

fclose($output);
exit();
?> 