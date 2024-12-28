<?php
include_once '../Model/StudentModel.php'; // Inclure le modèle

class StudentController {
    private $studentModel;

    public function __construct() {
        $this->studentModel = new StudentModel(); // Créer une instance du modèle
    }

    public function getStudentInfo($cin) {
        return $this->studentModel->getStudentInfo($cin);
    }

    public function saveStudentFiles($cin, $code_class, $bac_img, $id_card_img, $birth_img) {
        return $this->studentModel->saveStudentFiles($cin, $code_class, $bac_img, $id_card_img, $birth_img);
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['cin'], $_POST['group'], $_FILES['bac_img'], $_FILES['id_card_img'], $_FILES['birth_img'])) {
        // Récupérer les valeurs
        $cin = $_POST['cin'];
        $code_class = $_POST['group'];

        // Lire les fichiers envoyés
        $bac_img = file_get_contents($_FILES['bac_img']['tmp_name']);
        $id_card_img = file_get_contents($_FILES['id_card_img']['tmp_name']);
        $birth_img = file_get_contents($_FILES['birth_img']['tmp_name']);

        // Sauvegarder les fichiers
        $studentController = new StudentController();
        $success = $studentController->saveStudentFiles($cin, $code_class, $bac_img, $id_card_img, $birth_img);

        if ($success) {
            header('Location: ../view/profile.php?update=success');
        } else {
            die("Erreur lors de la mise à jour.");
        }
    } else {
        die("Veuillez remplir tous les champs et sélectionner les fichiers.");
    }
}
?>
