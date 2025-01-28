<?php
// Enable comprehensive error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Extensive logging
error_log("======= START flasktest.php =======");
error_log("SERVER: " . print_r($_SERVER, true));
error_log("POST DATA: " . print_r($_POST, true));
error_log("GET DATA: " . print_r($_GET, true));

// Receive path from POST with multiple fallback checks
$folder_path = $_POST['uploads_path'] ?? $_GET['uploads_path'] ?? null;

// Comprehensive path validation
if (empty($folder_path)) {
    error_log("CRITICAL: No path received");
    http_response_code(400);
    die(json_encode([
        'status' => 'error',
        'message' => 'No folder path provided',
        'received_data' => $_POST
    ]));
}

// Log received path details
error_log("Received Folder Path: $folder_path");
error_log("Path Exists: " . (is_dir($folder_path) ? 'Yes' : 'No'));

// Additional path existence check
if (!is_dir($folder_path)) {
    error_log("ERROR: Folder does not exist");
    http_response_code(404);
    die(json_encode([
        'status' => 'error',
        'message' => "Folder does not exist: $folder_path"
    ]));
}

// Prepare zip file
$zip_file_path = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'folder_to_send.zip';
error_log("Zip File Path: $zip_file_path");

$zip = new ZipArchive();
if ($zip->open($zip_file_path, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {     
    $files = new RecursiveIteratorIterator(         
        new RecursiveDirectoryIterator($folder_path),         
        RecursiveIteratorIterator::LEAVES_ONLY     
    );      

    foreach ($files as $file) {         
        if (!$file->isDir()) {             
            $file_path = $file->getRealPath();             
            $relative_path = substr($file_path, strlen($folder_path) + 1);             
            $zip->addFile($file_path, $relative_path);         
        }     
    }      

    $zip->close(); 
} else {     
    error_log("CRITICAL: Failed to create zip file");
    die("Failed to create zip file."); 
}  

$url = 'http://localhost:81/validate';
error_log("Validation URL: $url");

$ch = curl_init();  
curl_setopt($ch, CURLOPT_URL, $url); 
curl_setopt($ch, CURLOPT_POST, 1); 
curl_setopt($ch, CURLOPT_POSTFIELDS, [     
    'file' => new CURLFile($zip_file_path, 'application/zip', 'folder_to_send.zip') 
]); 
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: multipart/form-data']);  

$response = curl_exec($ch); 
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);  

error_log("Validation Response Code: $httpCode");
error_log("Validation Response: $response");

if (curl_errno($ch)) {     
    error_log('CURL Error: ' . curl_error($ch)); 
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => curl_error($ch)]); 
} else {     
    echo json_encode([
        'status' => 'success', 
        'http_code' => $httpCode,
        'server_response' => $response
    ]); 
}  

curl_close($ch); 
unlink($zip_file_path);

error_log("======= END flasktest.php =======");
?>