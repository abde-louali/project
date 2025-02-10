<div class="container">
    <div class="header">
        <h1>Document Verification Results</h1>
    </div>

    <div id="loading" class="loading">
        <div class="loading-spinner"></div>
        <p>Processing documents, please wait...</p>
    </div>

</div>
<?php
// Set execution time to 15 minutes
ini_set('max_execution_time', '900');
set_time_limit(900);

session_start();
require_once '../Controller/AdminController.php';

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: Login.php');
    exit();
}

$adminController = new AdminController();

if (isset($_GET['class']) && isset($_GET['filiere'])) {
    // Sanitize the input by replacing spaces with underscores
    $class = trim(str_replace(' ', '_', $_GET['class']));
    $filiere = trim(str_replace(' ', '_', $_GET['filiere']));
    
    // Get students data first
    $students = $adminController->getStudentsByClass($class, $filiere);
    
    if (empty($students)) {
        die("No students found for this class and filiere combination.");
    }

    // Create the directory structure if it doesn't exist
    $base_path = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR 
                . str_replace('_', ' ', $filiere) . DIRECTORY_SEPARATOR 
                . str_replace('_', ' ', $class);

    // Check if there's a variant of the path with different spacing
    $parent_dir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'uploads';
    $possible_dirs = glob($parent_dir . DIRECTORY_SEPARATOR . "*{$filiere}*" . DIRECTORY_SEPARATOR . "*{$class}*", GLOB_BRACE);
    
    if (!empty($possible_dirs)) {
        $base_path = $possible_dirs[0];
    } else if (!file_exists($base_path)) {
        mkdir($base_path, 0777, true);
    }

    $folder_path = $base_path;
    $url = 'http://localhost:81/validate';

    if (!is_dir($folder_path)) {
        die("Error: Source folder not found: $folder_path");
    }

    $files_list = array_diff(scandir($folder_path), array('.', '..'));
    if (empty($files_list)) {
        die("The directory is empty. Please upload files to '$filiere/$class' before verifying.");
    }

    // Create zip file silently
    $temp_dir = sys_get_temp_dir();
    $zip_file_path = $temp_dir . DIRECTORY_SEPARATOR . uniqid('upload_') . '.zip';

    if (file_exists($zip_file_path)) {
        @unlink($zip_file_path);
    }

    $zip = new ZipArchive();
    if ($zip->open($zip_file_path, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
        die("Error: Failed to create zip file");
    }

    try {
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($folder_path),
            RecursiveIteratorIterator::LEAVES_ONLY
        );

        $fileCount = 0;
        foreach ($files as $file) {
            if (!$file->isDir()) {
                $file_path = $file->getRealPath();
                $relative_path = substr($file_path, strlen($folder_path) + 1);
                if (!$zip->addFile($file_path, $relative_path)) {
                    throw new Exception("Failed to add file to zip");
                }
                $fileCount++;
            }
        }
        
        if ($fileCount == 0) {
            die("Error: No files found in the directory to zip");
        }
        
        if (!$zip->close()) {
            throw new Exception("Failed to close ZIP file");
        }
    } catch (Exception $e) {
        $zip->close();
        @unlink($zip_file_path);
        die("Error: " . $e->getMessage());
    }

    // Initialize cURL
    $ch = curl_init();

    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_POST => true,
        CURLOPT_TIMEOUT => 300,
        CURLOPT_CONNECTTIMEOUT => 60,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_POSTFIELDS => [
            'file' => new CURLFile($zip_file_path, 'application/zip', basename($zip_file_path))
        ],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => ['Content-Type: multipart/form-data']
    ]);

    $response = curl_exec($ch);
    
    if (curl_errno($ch)) {
        echo '<div class="error-message">';
        echo 'Error: Failed to process the verification. Please try again later.';
        echo '</div>';
    } else {
        $results = json_decode($response, true);
        
        echo '<table class="results-table">';
        echo '<thead>';
        echo '<tr>';
        echo '<th>CIN</th>';
        echo '<th>Student Name</th>';
        echo '<th>Status</th>';
        echo '<th>Verified Name</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';
        
        foreach ($results as $result) {
            $student = array_filter($students, function($s) use ($result) {
                return $s['cin'] === $result['cin'];
            });
            $student = reset($student);
            
            if ($student) {
                $studentName = $student['s_fname'] . ' ' . $student['s_lname'];
                
                $statusClass = $result['is_correct'] ? 'status-correct' : 'status-incorrect';
                $statusText = $result['is_correct'] ? '✅ Correct' : '❌ Incorrect';
                
                echo '<tr>';
                echo '<td>' . htmlspecialchars($result['cin']) . '</td>';
                echo '<td>' . htmlspecialchars($studentName) . '</td>';
                echo '<td><span class="status ' . $statusClass . '">' . $statusText . '</span></td>';
                echo '<td>' . ($result['verified_name'] ? htmlspecialchars($result['verified_name']) : 'N/A') . '</td>';
                echo '</tr>';
            }
        }
        
        echo '</tbody>';
        echo '</table>';
    }

    curl_close($ch);

    // Clean up
    if (file_exists($zip_file_path)) {
        usleep(100000);
        @unlink($zip_file_path);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document Verification</title>
    <style>
        .container {
            max-width: 1000px;
            margin: 40px auto;
            padding: 20px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #eee;
        }

        .header h1 {
            color: #2c3e50;
            font-size: 24px;
            margin: 0;
        }

        .results-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: #fff;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .results-table th {
            background: #f8f9fa;
            color: #2c3e50;
            padding: 15px;
            text-align: left;
            border-bottom: 2px solid #eee;
        }

        .results-table td {
            padding: 15px;
            border-bottom: 1px solid #eee;
            color: #2c3e50;
        }

        .results-table tr:hover {
            background-color: #f8f9fa;
        }

        .status {
            padding: 6px 12px;
            border-radius: 4px;
            font-weight: 500;
        }

        .status-correct {
            background-color: #e8f5e9;
            color: #2e7d32;
        }

        .status-incorrect {
            background-color: #ffebee;
            color: #c62828;
        }

        .loading {
            display: none;
            text-align: center;
            padding: 20px;
        }

        .loading-spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #3498db;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 20px auto;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .error-message {
            background-color: #ffebee;
            color: #c62828;
            padding: 15px;
            border-radius: 4px;
            margin: 20px 0;
            text-align: center;
        }

        .success-message {
            background-color: #e8f5e9;
            color: #2e7d32;
            padding: 15px;
            border-radius: 4px;
            margin: 20px 0;
            text-align: center;
        }
    </style>
</head>
<body>



<script>
document.addEventListener('DOMContentLoaded', function() {
    // Show loading state when processing starts
    document.getElementById('loading').style.display = 'block';
    
    // Hide loading state when results are shown
    if (document.querySelector('.results-table')) {
        document.getElementById('loading').style.display = 'none';
    }
});
</script>

</body>
</html>
    