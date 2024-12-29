<?php
include_once '../Model/StudentModel.php'; // Inclure le modèle

class StudentController {
    private $studentModel;

    public function __construct() {
        $this->studentModel = new StudentModel(); // Créer une instance du modèle
    }

    // Récupérer les informations de l'étudiant
    public function getStudentInfo($cin) {
        return $this->studentModel->getStudentInfo($cin);
    }

    // Sauvegarder ou mettre à jour les fichiers
    public function saveStudentFiles($cin, $code_class, $bac_img, $id_card_img, $birth_img) {
        return $this->studentModel->saveOrUpdateStudentFiles($cin, $code_class, $bac_img, $id_card_img, $birth_img);
    }
}

// Traiter la requête POST pour sauvegarder les fichiers
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['cin'], $_POST['group'], $_FILES['bac_img'], $_FILES['id_card_img'], $_FILES['birth_img'])) {
        // Récupérer les valeurs
        $cin = $_POST['cin'];
        $code_class = $_POST['group'];

        // Lire les fichiers envoyés
        $bac_img = file_get_contents($_FILES['bac_img']['tmp_name']);
        $id_card_img = file_get_contents($_FILES['id_card_img']['tmp_name']);
        $birth_img = file_get_contents($_FILES['birth_img']['tmp_name']);

        // Sauvegarder ou mettre à jour les fichiers
        $studentController = new StudentController();
        $isNew = $studentController->saveStudentFiles($cin, $code_class, $bac_img, $id_card_img, $birth_img);

        // Rediriger avec un message de succès ou d'erreur
        if ($isNew) {
            header('Location: ../view/profile.php?status=added');
        } else {
            header('Location: ../view/profile.php?status=updated');
        }
    } else {
        // Si des champs sont manquants, afficher un message d'erreur
        header('Location: ../view/profile.php?status=error');
    }
}
?>
