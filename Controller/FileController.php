<?php
class FileController {
    private $uploadDir = '../uploads/';

    public function __construct() {
        if (!file_exists($this->uploadDir)) {
            mkdir($this->uploadDir, 0777, true);
        }
    }

    public function getDocumentPath($student, $docType) {
        $filiere = str_replace(' ', '_', $student['filier_name']);
        $class = str_replace(' ', '_', $student['code_class']);
        $folderName = $student['cin'] . "_" . str_replace(' ', '_', $student['s_lname']);
        
        $path = $this->uploadDir . $filiere . "/" . $class . "/" . $folderName . "/" . $docType;
        
        error_log("Generated document path: " . $path);
        
        return $path;
    }

    
    public function findDocument($basePath) {
        if (!$basePath) {
            error_log("Base path is empty");
            return false;
        }

        $extensions = ['pdf', 'jpg', 'jpeg', 'png'];
        foreach ($extensions as $ext) {
            $path = $basePath . '.' . $ext;
            error_log("Checking file: " . $path);
            if (file_exists($path)) {
                error_log("File found: " . $path);
                return $path;
            }
        }
        error_log("No file found for base path: " . $basePath);
        return false;
    }

    public function getDocumentIcon($path) {
        if (!$path) return 'bi-file-earmark-x';
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        return $extension === 'pdf' ? 'bi-file-earmark-pdf' : 'bi-file-earmark-image';
    }

    public function uploadFile($file, $filiere, $class, $cin, $type) {
        try {
            $targetDir = $this->uploadDir . $filiere . '/' . $class . '/' . $cin . '_' . $type;
            if (!file_exists($targetDir)) {
                mkdir($targetDir, 0777, true);
            }

            $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $targetFile = $targetDir . '/' . $type . '.' . $fileExtension;

            if (move_uploaded_file($file['tmp_name'], $targetFile)) {
                return ['status' => 'success', 'path' => $targetFile];
            }
            return ['status' => 'error', 'message' => 'Erreur lors du téléchargement du fichier'];
        } catch (Exception $e) {
            error_log("File upload error: " . $e->getMessage());
            return ['status' => 'error', 'message' => 'Erreur système lors du téléchargement'];
        }
    }

    public function deleteFile($path) {
        if (file_exists($path)) {
            return unlink($path);
        }
        return false;
    }

    public function getFilePath($filiere, $class, $cin, $type) {
        $pattern = $this->uploadDir . $filiere . '/' . $class . '/' . $cin . '_' . $type . '/' . $type . '.*';
        $files = glob($pattern);
        return !empty($files) ? $files[0] : null;
    }
}
?> 