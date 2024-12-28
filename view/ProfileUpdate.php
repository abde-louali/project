<?php
session_start();
include '../Controller/StudentController.php';

if (!isset($_SESSION['username'])) {
    header('Location: Login.php');
    exit;
}

$cin = $_SESSION['username'];
$studentController = new StudentController();
$studentInfo = $studentController->getStudentInfo($cin);

if ($studentInfo) {
    $name = $studentInfo['s_fname'];
    $prenom = $studentInfo['s_lname'];
    $group = $studentInfo['code_class'];
} else {
    echo "Aucune information trouvée.";
    exit;
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mettre à jour le profil | OFPPT</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
    <div class="profile-update-container">
        <header>
            <img src="../assets/img/ofppt_logo.png" alt="OFPPT Logo" class="logo">
            <h1>Mettre à jour votre profil</h1>
        </header>

        <form action="process_profile.php" method="POST" enctype="multipart/form-data">
            <div class="input-group">
                <label for="bac_img">Ajouter l'image du Bac</label>
                <input type="file" name="bac_img" id="bac_img">
            </div>

            <div class="input-group">
                <label for="id_card_img">Ajouter l'image de la carte d'identité</label>
                <input type="file" name="id_card_img" id="id_card_img">
            </div>

            <div class="input-group">
                <label for="birth_img">Ajouter l'image de l'acte de naissance</label>
                <input type="file" name="birth_img" id="birth_img">
            </div>

            <button type="submit" class="btn-submit">Mettre à jour</button>
        </form>

        <form action="../Controller/UserController.php" method="POST">
            <button type="submit" class="btn-logout">Se déconnecter</button>
        </form>
    </div>
</body>
</html>
