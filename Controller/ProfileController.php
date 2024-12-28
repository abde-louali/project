<?php
include '../Model/StudentModel.php';

class ProfileController {

    // Mise à jour des informations de l'étudiant
    public function updateProfile($cin, $bac_img, $id_card_img, $birth_img) {
        $studentModel = new StudentModel();
        
        // Vérifier si les images sont téléchargées et gérer les chemins
        $bac_img_path = $this->uploadFile($bac_img);
        $id_card_img_path = $this->uploadFile($id_card_img);
        $birth_img_path = $this->uploadFile($birth_img);
        
        return $studentModel->updateStudentInfo($cin, $bac_img_path, $id_card_img_path, $birth_img_path);
    }

    // Fonction pour gérer le téléchargement des fichiers
    private function uploadFile($file) {
        if ($file['error'] === 0) {
            $target_dir = "../uploads/";
            $target_file = $target_dir . basename($file["name"]);
            if (move_uploaded_file($file["tmp_name"], $target_file)) {
                return $target_file;  // Retourne le chemin de l'image téléchargée
            }
        }
        return null;
    }
}
?>
