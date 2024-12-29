<?php
session_start();
include '../Controller/StudentController.php'; // Inclure le contrôleur

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['username'])) {
    header('location:Login.php'); // Si l'utilisateur n'est pas connecté, rediriger vers la page de connexion
    exit;
} else {
    // Récupérer les informations de l'étudiant à partir du CIN
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

    // Déterminer le message de bienvenue selon l'heure
    $heure = date("H");
    if ($heure < 12) {
        $message_bienvenue = "Bonjour $name $prenom !";
    } else {
        $message_bienvenue = "Bonsoir $name $prenom !";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Étudiant | OFPPT</title>
    <link rel="stylesheet" href="../assets/css/styles.css"> <!-- Lien vers votre fichier CSS -->
</head>
<body>
    <div class="profile-container">
        <header class="header">
            <img src="../assets/img/ofppt_logo.png" alt="OFPPT Logo" class="logo">
            <h1><?php echo $message_bienvenue; ?></h1>
        </header>

        <div class="profile-box">
            <?php
            // Afficher le message de statut
            if (isset($_GET['status'])) {
                if ($_GET['status'] === 'added') {
                    echo '<p class="success-message">Les informations ont été ajoutées avec succès.</p>';
                } elseif ($_GET['status'] === 'updated') {
                    echo '<p class="success-message">Les informations ont été modifiées avec succès.</p>';
                } elseif ($_GET['status'] === 'error') {
                    echo '<p class="error-message">Erreur lors de l\'ajout ou de la modification des informations. Veuillez réessayer.</p>';
                }
            }
            ?>

            <form action="../Controller/StudentController.php" method="POST" enctype="multipart/form-data">
                <!-- Champ CIN -->
                <div class="input-group">
                    <label for="cin">CIN</label>
                    <input type="text" id="cin" name="cin_display" value="<?php echo htmlspecialchars($cin ?? 'Non défini'); ?>" disabled>
                    <input type="hidden" name="cin" value="<?php echo htmlspecialchars($cin); ?>">
                </div>

                <!-- Champ Groupe -->
                <div class="input-group">
                    <label for="group">Groupe</label>
                    <input type="text" id="group" name="group_display" value="<?php echo htmlspecialchars($group ?? 'Non défini'); ?>" disabled>
                    <input type="hidden" name="group" value="<?php echo htmlspecialchars($group); ?>">
                </div>

                <!-- Fichiers uploadés -->
                <div class="input-group">
                    <label for="bac_img">Ajouter l'image du Bac</label>
                    <input type="file" name="bac_img" id="bac_img" required>
                </div>

                <div class="input-group">
                    <label for="id_card_img">Ajouter l'image de la carte d'identité</label>
                    <input type="file" name="id_card_img" id="id_card_img" required>
                </div>

                <div class="input-group">
                    <label for="birth_img">Ajouter l'image de l'acte de naissance</label>
                    <input type="file" name="birth_img" id="birth_img" required>
                </div>

                <button type="submit" class="btn-submit">Envoyer</button>
            </form>

            <form action="../Controller/UserController.php?action=logout" method="POST">
                <button type="submit" class="btn-logout">Se déconnecter</button>
            </form>
        </div>
    </div>
</body>
</html>
