<?php
session_start();

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
                    'type' => pathinfo($file_path, PATHINFO_EXTENSION)
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
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Documents</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
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
        
        [data-bs-theme="dark"] .main-content {
            background: #1a1d20;
        }
        
        [data-bs-theme="dark"] .document-card {
            background: #242729;
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
        .document-card {
            transition: transform 0.3s;
        }
        .document-card:hover {
            transform: translateY(-5px);
        }
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
                    <a class="nav-link" href="dashboard.php"><i class="fas fa-home me-2"></i> Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="documents.php"><i class="fas fa-file-alt me-2"></i> Documents</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="upload.php"><i class="fas fa-upload me-2"></i> Upload Document</a>
                </li>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="col-md-9 col-lg-10 main-content p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>All Documents</h2>
            </div>


            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control" id="searchDocument" placeholder="Search documents...">
                    </div>
                </div>
            </div>

            <div class="row" id="documentsContainer">
                <?php if (empty($documents)): ?>
                    <div class="col-12">
                        <div class="alert alert-info">
                            No documents found.
                        </div>
                    </div>
                <?php else: ?>
                    <?php foreach ($documents as $doc): ?>
                        <div class="col-md-4 mb-4 document-item">
                            <div class="card document-card h-100">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-3">
                                        <?php
                                        $icon = 'fa-file';
                                        switch(strtolower($doc['type'])) {
                                            case 'pdf': $icon = 'fa-file-pdf'; break;
                                            case 'doc':
                                            case 'docx': $icon = 'fa-file-word'; break;
                                            case 'xls':
                                            case 'xlsx': $icon = 'fa-file-excel'; break;
                                            case 'txt': $icon = 'fa-file-lines'; break;
                                        }
                                        ?>
                                        <i class="fas <?php echo $icon; ?> fa-2x me-3 text-primary"></i>
                                        <h5 class="card-title mb-0"><?php echo htmlspecialchars($doc['name']); ?></h5>
                                    </div>
                                    <p class="card-text">
                                        <small class="text-muted">
                                            Size: <?php echo number_format($doc['size'] / 1024, 2); ?> KB<br>
                                            Uploaded: <?php echo $doc['date']; ?>
                                        </small>
                                    </p>
                                    <div class="btn-group w-100">
                                        <a href="view_document.php?file=<?php echo urlencode($doc['name']); ?>" 
                                           class="btn btn-outline-primary" 
                                           target="_blank">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                        <a href="download_document.php?file=<?php echo urlencode($doc['name']); ?>" 
                                           class="btn btn-outline-success">
                                            <i class="fas fa-download"></i> Download
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
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
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchDocument');
    const documentsContainer = document.getElementById('documentsContainer');
    const documentItems = document.getElementsByClassName('document-item');
    
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        let hasResults = false;
        
        Array.from(documentItems).forEach(item => {
            const documentName = item.querySelector('.card-title').textContent.toLowerCase();
            
            if (documentName.includes(searchTerm)) {
                item.style.display = '';
                hasResults = true;
            } else {
                item.style.display = 'none';
            }
        });
        
        // Show "no results" message if no documents match
        const existingNoResults = documentsContainer.querySelector('.no-results');
        if (!hasResults) {
            if (!existingNoResults) {
                const noResults = document.createElement('div');
                noResults.className = 'col-12 no-results';
                noResults.innerHTML = `
                    <div class="alert alert-info">
                        No documents found matching "${searchTerm}"
                    </div>
                `;
                documentsContainer.appendChild(noResults);
            }
        } else if (existingNoResults) {
            existingNoResults.remove();
        }
    });
});
</script>
</body>
</html> 