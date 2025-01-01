<?php
// AdminController.php
require_once '../Model/StudentModel.php';

class AdminController {

    public function createFolder() {
        // Vérification de la soumission du formulaire
        if (isset($_POST['create_folder'])) {
            $folder_name = preg_replace('/[^a-zA-Z0-9_-]/', '', $_POST['folder_name']);
            $target_dir = __DIR__ . "/../classes/";  // Dossier de stockage

            // Vérifier si le dossier existe déjà
            if (file_exists($target_dir . $folder_name)) {
                $message = "Le dossier '$folder_name' existe déjà!";
            } else {
                // Créer le dossier
                if (mkdir($target_dir . $folder_name, 0777, true)) {
                    // Insérer les données dans la base de données
                    $studentModel = new StudentModel();
                    $studentModel->addFolderToDB($folder_name);

                    $message = "Dossier '$folder_name' créé avec succès!";
                } else {
                    $message = "Erreur lors de la création du dossier!";
                }
            }
        }

        // Charger la vue avec les messages et les dossiers existants
        $this->loadView($message);
    }

    // Méthode pour charger la vue avec les dossiers existants
    private function loadView($message = "") {
        // Inclure le header pour que l'en-tête s'affiche
        include_once('../view/Header.php');

        // Récupérer la liste des dossiers dans 'classes/'
        $target_dir = __DIR__ . "/../classes/";
        $folders = array_filter(glob($target_dir . '*'), 'is_dir');

        // Inclure la vue avec les variables nécessaires
        include('../view/ajouterCla.php');
    }
}

// Instancier le contrôleur et appeler la méthode appropriée
$adminController = new AdminController();
$adminController->createFolder();
