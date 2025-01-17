<?php

require_once('../Model/AdminModel.php');

class AdminController {
    private $model;

    public function __construct() {
        $this->model = new AdminModel();
        $this->handleFolderCreation();
    }

    public function displayFilieres() {
        return $this->model->getAllFilieres();
    }

    public function getStudentsByClass($class) {
        return $this->model->getStudentsByClass($class);
    }

    public function getStudentDocuments($cin) {
        return $this->model->getStudentDocuments($cin);
    }

    public function updateProfile() {
        session_start();
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_profile') {
            if (!isset($_SESSION['username'])) {
                header('Location: ../view/Login.php');
                exit();
            }

            $currentUsername = $_SESSION['username'];
            $data = [
                'username' => trim($_POST['username']),
                'cin' => trim($_POST['cin']),
                'first_name' => trim($_POST['first_name']),
                'last_name' => trim($_POST['last_name'])
            ];

            // Vérifier que tous les champs sont remplis
            foreach ($data as $field => $value) {
                if (empty($value)) {
                    header('Location: ../view/AdminProfile.php?status=error&message=Tous les champs sont obligatoires');
                    exit();
                }
            }

            // Mettre à jour les informations
            $result = $this->model->updateAdminInfo($currentUsername, $data);

            if ($result === false) {
                // Vérifier si c'est à cause d'un nom d'utilisateur ou CIN existant
                if ($this->model->checkUsernameExists($data['username'], $currentUsername)) {
                    header('Location: ../view/AdminProfile.php?status=error&message=Ce nom d\'utilisateur existe déjà');
                    exit();
                }
                if ($this->model->checkCinExists($data['cin'], $currentUsername)) {
                    header('Location: ../view/AdminProfile.php?status=error&message=Ce CIN existe déjà');
                    exit();
                }
                header('Location: ../view/AdminProfile.php?status=error&message=Erreur lors de la mise à jour du profil');
                exit();
            }

            // Mise à jour réussie
            $_SESSION['username'] = $data['username']; // Mettre à jour le nom d'utilisateur dans la session
            header('Location: ../view/AdminProfile.php?status=success&message=Profil mis à jour avec succès');
            exit();
        }
    }

    public function getStudentByCin($cin) {
        return $this->model->getStudentByCin($cin);
    }

    public function handleFolderCreation() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create_folders') {
            session_start();
            $class = $_POST['class'];
            $filiere = $_POST['filiere'];
            $cin = $_POST['cin'];
            
            try {
                $student = $this->getStudentByCin($cin);
                if (!$student) {
                    throw new Exception("Étudiant non trouvé");
                }

                // Chemin absolu du dossier uploads
                $baseDir = dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'uploads';
                error_log("Base directory: " . $baseDir);

                // Créer le dossier uploads s'il n'existe pas
                if (!file_exists($baseDir)) {
                    if (!mkdir($baseDir, 0777, true)) {
                        throw new Exception("Impossible de créer le dossier uploads");
                    }
                    chmod($baseDir, 0777);
                }

                // Créer la structure des dossiers
                $filiereDir = $baseDir . DIRECTORY_SEPARATOR . str_replace(' ', '_', $filiere);
                $classDir = $filiereDir . DIRECTORY_SEPARATOR . $class;
                $studentDir = $classDir . DIRECTORY_SEPARATOR . $cin . '_' . str_replace(' ', '_', $student['s_fname']) . '_' . str_replace(' ', '_', $student['s_lname']);

                error_log("Creating directories:");
                error_log("Filiere dir: " . $filiereDir);
                error_log("Class dir: " . $classDir);
                error_log("Student dir: " . $studentDir);

                // Créer les dossiers avec gestion d'erreurs
                if (!file_exists($filiereDir)) {
                    if (!mkdir($filiereDir, 0777, true)) {
                        throw new Exception("Impossible de créer le dossier de la filière");
                    }
                    chmod($filiereDir, 0777);
                }
                
                if (!file_exists($classDir)) {
                    if (!mkdir($classDir, 0777, true)) {
                        throw new Exception("Impossible de créer le dossier de la classe");
                    }
                    chmod($classDir, 0777);
                }
                
                if (!file_exists($studentDir)) {
                    if (!mkdir($studentDir, 0777, true)) {
                        throw new Exception("Impossible de créer le dossier de l'étudiant");
                    }
                    chmod($studentDir, 0777);
                }

                // Vérifier que les dossiers ont été créés
                if (!is_dir($studentDir)) {
                    throw new Exception("Le dossier de l'étudiant n'a pas été créé correctement");
                }

                $_SESSION['folder_message'] = "Les dossiers ont été créés avec succès.";
        } catch (Exception $e) {
                error_log("Error creating folders: " . $e->getMessage());
                $_SESSION['folder_message'] = "Erreur lors de la création des dossiers : " . $e->getMessage();
            }

            // Rediriger vers la page précédente
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit();
        }
    }
}

// Handle incoming requests
$controller = new AdminController();
if (isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'update_profile':
            $controller->updateProfile();
            break;
    }
}
?>

