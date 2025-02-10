<?php
session_start();
include '../Controller/StudentController.php'; // Inclure le contrôleur

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['username'])) {
    header('location: Login.php'); // Si l'utilisateur n'est pas connecté, rediriger vers la page de connexion
    exit;
} else {
    // Récupérer les informations de l'étudiant à partir du CIN
    $cin = $_SESSION['username'];
    $studentController = new StudentController();
    $studentInfo = $studentController->getStudentInfo($cin);

    
    if ($studentInfo) {
        $name = $studentInfo['s_fname'];
        $prenom = $studentInfo['s_lname'];
        $code_class = $studentInfo['code_class'];
        $filier_name = $studentInfo['filier_name'];  // Récupérer la filière
    } else {
        echo "Aucune information trouvée.";
        exit;
    }

    // Déterminer le message de bienvenue selon l'heure
    $heure = date("H");
    if ($heure < 12) {
        $message_bienvenue = "Bonjour  $prenom  $name !";
    } elseif ($heure < 18) {
        $message_bienvenue = "Bon après-midi $prenom $name !";
    } else {
        $message_bienvenue = "Bonsoir $prenom $name !";
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
                // Récupérer le statut et le message depuis l'URL
                $status = isset($_GET['status']) ? $_GET['status'] : '';
                $message = isset($_GET['message']) ? $_GET['message'] : '';

                // Affichage des messages de statut
                if ($status === 'success') {
                    echo "<p style='color: green;'>$message</p>"; // Message de succès de mise à jour
                } elseif ($status === 'error') {
                    echo "<p style='color: red;'>$message</p>"; // Message d'erreur
                }
            ?>

<form action="../Controller/StudentController.php" method="POST" enctype="multipart/form-data">
                <!-- Champ CIN -->
                <div class="input-group">
                    <label for="cin">CIN</label>
                    <input type="text" id="cin" value="<?php echo htmlspecialchars($cin); ?>" disabled>
                    <input type="hidden" name="cin" value="<?php echo htmlspecialchars($cin); ?>">
                </div>

                <!-- Champ Filière -->
                <div class="input-group">
                    <label for="filier_name">Filière</label>
                    <input type="text" id="filier_name" value="<?php echo htmlspecialchars($filier_name); ?>" disabled>
                    <input type="hidden" name="filier_name" value="<?php echo htmlspecialchars($filier_name); ?>">
                </div>

                <!-- Champ Groupe -->
                <div class="input-group">
                    <label for="code_class">Groupe</label>
                    <input type="text" id="code_class" value="<?php echo htmlspecialchars($code_class); ?>" disabled>
                    <input type="hidden" name="code_class" value="<?php echo htmlspecialchars($code_class); ?>">
                </div>

                <!-- Champs pour les fichiers -->
                <div class="input-group">
                    <label for="bac_img">Image du Bac</label>
                    <input type="file" name="bac_img" id="bac_img" accept="image/*,application/pdf" required>
                </div>
                <div class="input-group">
                    <label for="id_card_img">Carte d'identité</label>
                    <input type="file" name="id_card_img" id="id_card_img" accept="image/*,application/pdf" required>
                </div>
                <div class="input-group">
                    <label for="birth_img">Acte de naissance</label>
                    <input type="file" name="birth_img" id="birth_img" accept="image/*,application/pdf" required>
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
