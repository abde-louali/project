<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION["username"]) || $_SESSION["user_type"] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Non autorisé']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['cin'])) {
    echo json_encode(['success' => false, 'message' => 'Paramètres invalides']);
    exit();
}

include_once('../Model/conx.php');
try {
    $db = (new Database())->getConnection();
    
    // Supprimer l'étudiant
    $query = "DELETE FROM classes WHERE cin = :cin";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':cin', $_POST['cin']);
    
    if ($stmt->execute()) {
        // Supprimer les documents associés
        $uploadDir = '../uploads/';
        // Rechercher et supprimer le dossier de l'étudiant
        $pattern = $uploadDir . "*/*/" . $_POST['cin'] . "_*";
        $folders = glob($pattern, GLOB_ONLYDIR);
        
        foreach ($folders as $folder) {
            if (is_dir($folder)) {
                array_map('unlink', glob("$folder/*.*")); // Supprimer tous les fichiers
                rmdir($folder); // Supprimer le dossier
            }
        }
        
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erreur lors de la suppression']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?> 