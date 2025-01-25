<?php
// Create the directory structure if it doesn't exist
$base_path = __DIR__ . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'DEVELOPMENT_DIGITAL' . DIRECTORY_SEPARATOR . 'DD201';
if (!file_exists($base_path)) {
    mkdir($base_path, 0777, true);
    echo "Created directory: $base_path\n";
}

// Path to the folder you want to send
$folder_path = $base_path;
$url = 'http://localhost:81/validate';

if (!is_dir($folder_path)) {
    die("Error: Source folder not found: $folder_path");
}

// Create a unique temporary file name
$temp_dir = sys_get_temp_dir();
$zip_file_path = $temp_dir . DIRECTORY_SEPARATOR . uniqid('upload_') . '.zip';

// Make sure we have write permissions
if (file_exists($zip_file_path)) {
    @unlink($zip_file_path);
}

$zip = new ZipArchive();
if ($zip->open($zip_file_path, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
    die("Error: Failed to create zip file: $zip_file_path");
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
            echo "Adding file to zip: " . $relative_path . "\n";
            if (!$zip->addFile($file_path, $relative_path)) {
                throw new Exception("Failed to add file to zip: $file_path");
            }
            $fileCount++;
        }
    }
    
    if ($fileCount == 0) {
        die("Error: No files found in the directory to zip");
    }
    
    echo "Total files added to zip: " . $fileCount . "\n";
    
    // Close the ZIP file
    if (!$zip->close()) {
        throw new Exception("Failed to close ZIP file: " . $zip->getStatusString());
    }
} catch (Exception $e) {
    $zip->close();
    @unlink($zip_file_path);
    die("Error during zip creation: " . $e->getMessage());
}

// Initialize cURL
$ch = curl_init();

// Set cURL options
curl_setopt_array($ch, [
    CURLOPT_URL => $url,
    CURLOPT_POST => true,
    CURLOPT_TIMEOUT => 60, // Increased timeout to 60 seconds
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_POSTFIELDS => [
        'file' => new CURLFile($zip_file_path, 'application/zip', basename($zip_file_path))
    ],
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => ['Content-Type: multipart/form-data']
]);

// Execute cURL request
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if (curl_errno($ch)) {
    echo 'cURL Error: ' . curl_error($ch) . "\n";
} else {
    echo "<pre style='background-color: #f5f5f5; padding: 15px; border-radius: 5px; font-family: monospace;'>";
    echo $response;
    echo "</pre>";
}

// Clean up
curl_close($ch);

// Safely delete the temporary zip file
if (file_exists($zip_file_path)) {
    // Wait a brief moment to ensure file handles are closed
    usleep(100000); // 0.1 seconds
    @unlink($zip_file_path);
    if (file_exists($zip_file_path)) {
        echo "Warning: Could not delete temporary file: $zip_file_path\n";
    }
}
?>