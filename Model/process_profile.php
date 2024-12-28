<?php
session_start();
include '../Controller/StudentController.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Vérifiez que les fichiers sont téléchargés
    if (isset($_FILES['bac_img']) && isset($_FILES['id_card_img']) && isset($_FILES['birth_img'])) {
        // Récupérer les fichiers
        $bac_img = file_get_contents($_FILES['bac_img']['tmp_name']);
        $id_card_img = file_get_contents($_FILES['id_card_img']['tmp_name']);
        $birth_img = file_get_contents($_FILES['birth_img']['tmp_name']);

        $cin = $_SESSION['username'];  // CIN de l'étudiant
        $code_class = $_POST['group']; // Code de la classe

        // Sauvegarder les fichiers
        $studentController = new StudentController();
        $studentController->saveStudentFiles($cin, $code_class, $bac_img, $id_card_img, $birth_img);

        echo "Fichiers enregistrés avec succès!";
    }
}
?>
