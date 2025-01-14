<?php
include_once '../Model/StudentModel.php';

class StudentController {
    private $studentModel;

    public function __construct() {
        $this->studentModel = new StudentModel();
    }

    public function getStudentDetails($cin, $code_class) {
        return $this->studentModel->getStudentDetails($cin, $code_class);
    }

    public function getStudentInfo($cin) {
        return $this->studentModel->getStudentInfo($cin);
    }

    public function saveOrUpdateStudentFiles($cin, $code_class, $s_fname, $s_lname, $filier_name, $bac_img, $id_card_img, $birth_img) {
        return $this->studentModel->saveOrUpdateStudentFiles($cin, $code_class, $filier_name, $s_fname, $s_lname, $bac_img, $id_card_img, $birth_img);
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['cin'], $_POST['code_class'], $_POST['filier_name'], $_FILES['bac_img'], $_FILES['id_card_img'], $_FILES['birth_img'])) {
        $cin = htmlspecialchars($_POST['cin']);
        $code_class = htmlspecialchars($_POST['code_class']);
        $filier_name = htmlspecialchars($_POST['filier_name']);
        
        // Récupérer les informations de l'étudiant (s_fname et s_lname) en utilisant le CIN et le code de la classe
        $studentController = new StudentController();
        $studentInfo = $studentController->getStudentInfo($cin);

        if ($studentInfo) {
            $s_fname = $studentInfo['s_fname'];
            $s_lname = $studentInfo['s_lname'];

            // Récupérer les images des fichiers
            $bac_img = file_get_contents($_FILES['bac_img']['tmp_name']);
            $id_card_img = file_get_contents($_FILES['id_card_img']['tmp_name']);
            $birth_img = file_get_contents($_FILES['birth_img']['tmp_name']);

            // Sauvegarder ou mettre à jour les fichiers
            $success = $studentController->saveOrUpdateStudentFiles($cin, $code_class, $s_fname, $s_lname, $filier_name, $bac_img, $id_card_img, $birth_img);

            if ($success) {
                header('Location: ../view/profile.php?status=success&message=Fichiers ajoutés ou mis à jour avec succès');
            } else {
                header('Location: ../view/profile.php?status=error&message=Erreur lors de la sauvegarde des fichiers');
            }
        } else {
            header('Location: ../view/profile.php?status=error&message=Aucune information trouvée pour ce CIN');
        }
    } else {
        header('Location: ../view/profile.php?status=error&message=Paramètres manquants');
    }
    exit;
}
?>
