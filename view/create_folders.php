<?php
session_start();
require_once '../Controller/AdminController.php';

header('Content-Type: application/json');

// Activer l'affichage des erreurs pour le débogage
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Log de début d'exécution
error_log("Starting create_folders.php execution");

// Vérification de la session
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    error_log("Session check failed: user_type=" . ($_SESSION['user_type'] ?? 'not set'));
    echo json_encode([
        'success' => false,
        'message' => 'Accès non autorisé',
        'details' => ['errors' => ['Session expirée ou utilisateur non autorisé']]
    ]);
    exit();
}

// Vérification des paramètres POST
error_log("POST parameters received: " . print_r($_POST, true));

if (!isset($_POST['class']) || !isset($_POST['filiere'])) {
    error_log("Missing required parameters");
    echo json_encode([
        'success' => false,
        'message' => 'Paramètres manquants',
        'details' => ['errors' => ['La classe et la filière sont requises']]
    ]);
    exit();
}

try {
    error_log("Initializing AdminController");
    $adminController = new AdminController();
    
    $class = trim($_POST['class']);
    $filiere = trim($_POST['filiere']);
    
    error_log("Creating folders for class: $class, filiere: $filiere");
    
    // Appeler la méthode de création des dossiers
    $result = $adminController->model->createStudentFolders($class, $filiere);
    
    error_log("Folder creation result: " . print_r($result, true));
    
    // Formater la réponse
    $response = [
        'success' => !empty($result['success']) || !empty($result['updated']),
        'message' => !empty($result['success']) || !empty($result['updated']) 
            ? 'Dossiers créés avec succès' 
            : 'Aucun dossier créé',
        'details' => [
            'success' => $result['success'] ?? [],
            'errors' => $result['errors'] ?? [],
            'updated' => $result['updated'] ?? []
        ]
    ];
    
    // Si aucun succès et des erreurs, marquer comme échec
    if (empty($result['success']) && empty($result['updated']) && !empty($result['errors'])) {
        $response['success'] = false;
        $response['message'] = 'Erreur lors de la création des dossiers';
    }
    
    echo json_encode($response);
    
} catch (Exception $e) {
    error_log("Error in create_folders.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Erreur lors de la création des dossiers',
        'details' => [
            'errors' => [$e->getMessage()]
        ]
    ]);
}
?> 