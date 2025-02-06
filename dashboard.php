<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once 'functions.php';


if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: index.php"); // Redirect to login if not logged in
    exit();
}

// $conn = connectDB();

// Define the upload directory
$upload_dir = './uploads/';

// Define the allowed email for upload and delete actions, check config.php for setup
$allowed_email = auth_email;

// Check if the user is allowed to upload/delete documents
$is_allowed_user = isset($_SESSION['email']) && $_SESSION['email'] === $allowed_email;

if (isset($_SESSION['email'])) {
    echo '<div class="alert alert-info d-flex align-items-center" role="alert">';
    echo '<i class="fas fa-user-circle me-2" style="font-size: 1.5rem;"></i>'; // User icon
    echo '<div>';
    echo 'Logged in as: <strong>' . htmlspecialchars($_SESSION['email']) . '</strong>';
    echo '</div>';
    echo '</div>';
} else {
    echo '<div class="alert alert-warning" role="alert">';
    echo 'No user is logged in.';
    echo '</div>';
}

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['document']) && $is_allowed_user) {
    $file_name = basename($_FILES['document']['name']);
    $target_file = $upload_dir . $file_name;

    // Move the uploaded file to the uploads directory
    if (move_uploaded_file($_FILES['document']['tmp_name'], $target_file)) {
        echo "<div class='alert alert-success'>File uploaded successfully.</div>";
    } else {
        echo "<div class='alert alert-danger'>Error uploading file.</div>";
    }
}

// Handle file deletion
if (isset($_GET['delete'])) {
    $file_to_delete = $upload_dir . basename($_GET['delete']);
    if ($is_allowed_user) {
        if (file_exists($file_to_delete)) {
            unlink($file_to_delete);
            echo "<div class='alert alert-success'>File deleted successfully.</div>";
        } else {
            echo "<div class='alert alert-danger'>File not found.</div>";
        }
    } else {
        echo "<div class='alert alert-danger'>You are not authorized to delete this document.</div>";
    }
}


// Function to get all documents from uploads directory
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
                    'type' => pathinfo($file_path, PATHINFO_EXTENSION),
                    'path' => $file_path
                );
            }
        }
    }
    
    // Sort by date modified (newest first)
    usort($documents, function($a, $b) {
        return strtotime($b['date']) - strtotime($a['date']);
    });
    
    return $documents;
}

$documents = getDocuments();

echo "<!-- Current directory: " . getcwd() . " -->";

error_reporting(E_ALL);
ini_set('display_errors', 1);

if (isset($_SESSION['email'])) {
    $user_email = $_SESSION['email'];
}
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document Management Dashboard</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Dark mode styles */
        [data-bs-theme="dark"] {
            --bs-body-bg: #1a1d20;
            --bs-body-color: #e1e1e1;
        }
        
        [data-bs-theme="dark"] .sidebar {
            background: #111315;
        }
        
        [data-bs-theme="dark"] .card {
            background: #242729;
            border-color: #2c3035;
        }
        
        [data-bs-theme="dark"] .card-header {
            background: #242729 !important;
            border-bottom-color: #2c3035;
        }
        
        [data-bs-theme="dark"] .main-content {
            background: #1a1d20;
        }
        
        [data-bs-theme="dark"] .table {
            --bs-table-color: #e1e1e1;
            --bs-table-bg: #242729;
            --bs-table-border-color: #2c3035;
        }
        
        [data-bs-theme="dark"] .btn-info {
            background-color: #0dcaf0;
            border-color: #0dcaf0;
            color: #000;
        }
        
        [data-bs-theme="dark"] .btn-info:hover {
            background-color: #31d2f2;
            border-color: #25cff2;
            color: #000;
        }
        
        [data-bs-theme="dark"] .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
            color: #fff;
        }
        
        [data-bs-theme="dark"] .btn-secondary:hover {
            background-color: #5c636a;
            border-color: #565e64;
            color: #fff;
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
        
        .sidebar {
            min-height: 100vh;
            background: #2c3e50;
            color: white;
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,.8);
            padding: 15px 25px;
            transition: 0.3s;
        }
        .sidebar .nav-link:hover {
            background: rgba(255,255,255,.1);
            color: white;
        }
        .sidebar .nav-link.active {
            background: #3498db;
            color: white;
        }
        .main-content {
            background: #f8f9fa;
        }
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,.1);
            transition: transform 0.3s;
        }
        .card:hover {
            transform: translateY(-5px);
        }
        .stat-card {
            border-left: 4px solid;
        }
        .document-card {
            border-left: 4px solid #3498db;
        }
        .status-pending { color: #f39c12; }
        .status-approved { color: #2ecc71; }
        .status-rejected { color: #e74c3c; }
    </style>
</head>
<body>

<button class="btn btn-outline-primary theme-toggle" id="themeToggle">
    <i class="fas fa-moon"></i>
</button>

<a href="logout.php" class="btn btn-outline-danger position-fixed" style="top: 1rem; right: 4rem;">
    <i class="fas fa-sign-out-alt"></i>
</a>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 col-lg-2 px-0 sidebar">
            <div class="p-4">
                <h4>Document System</h4>
            </div>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link active" href="dashboard.php"><i class="fas fa-home me-2"></i> Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="documents.php"><i class="fas fa-file-alt me-2"></i> Documents</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="upload.php"><i class="fas fa-upload me-2"></i> Upload Document</a>
                </li>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="col-md-9 col-lg-10 main-content p-4">
            <h2 class="mb-4">Document Management Overview</h2>
            
            <!-- Stats Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card stat-card" style="border-left-color: #3498db;">
                        <div class="card-body">
                            <h5 class="card-title text-muted">Total Documents</h5>
                            <h2><?php echo count($documents); ?></h2>
                            <p class="mb-0 text-primary"><i class="fas fa-file"></i> All Documents</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Documents -->
            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">Documents List</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>File Name</th>
                                            <th>Type</th>
                                            <th>Size</th>
                                            <th>Upload Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($documents)): ?>
                                        <tr>
                                            <td colspan="5" class="text-center">No documents found</td>
                                        </tr>
                                        <?php else: ?>
                                            <?php foreach ($documents as $doc): ?>
                                            <tr>
                                                <td>
                                                    <i class="fas fa-file me-2"></i>
                                                    <?php echo htmlspecialchars($doc['name']); ?>
                                                </td>
                                                <td>
                                                    <span class="badge bg-secondary">
                                                        <?php echo strtoupper(htmlspecialchars($doc['type'])); ?>
                                                    </span>
                                                </td>
                                                <td><?php echo number_format($doc['size'] / 1024, 2); ?> KB</td>
                                                <td><?php echo $doc['date']; ?></td>
                                                <td>
                                                    <a href="view_document.php?file=<?php echo urlencode($doc['name']); ?>" 
                                                       class="btn btn-sm btn-primary" 
                                                       target="_blank">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="download_document.php?file=<?php echo urlencode($doc['name']); ?>" 
                                                       class="btn btn-sm btn-info">
                                                        <i class="fas fa-download"></i>
                                                    </a>
                                                    <a href="#" 
                                                       class="btn btn-sm btn-danger" 
                                                       onclick="handleDelete('<?php echo urlencode($doc['name']); ?>', <?php echo json_encode($is_allowed_user); ?>); return false;">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header bg-white">
                            <h5 class="card-title mb-0">Quick Actions</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <a href="documents.php" class="btn btn-info">
                                    <i class="fas fa-search me-2"></i>Search Documents
                                </a>
                                <a href="export_documents.php" class="btn btn-secondary">
                                    <i class="fas fa-file-export me-2"></i>Export Report
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS and dependencies -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const themeToggle = document.getElementById('themeToggle');
    const html = document.documentElement;
    const icon = themeToggle.querySelector('i');
    
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

function handleDelete(fileName, isAllowed) {
    if (isAllowed) {
        if (confirm('Are you sure you want to delete this document?')) {
            window.location.href = '?delete=' + fileName;
        }
    } else {
        alert('You are not authorized to delete this document.');
    }
}
</script>
</body>
</html> 