<?php
session_start();

if (!isset($_SESSION["username"])) {
    header("location: Login.php");
    exit();
}
$heure = date("H");
    if ($heure < 12) {
        $message_bienvenue = "Bonjour  !";
    } else {
        $message_bienvenue = "Bonsoir  !";
    }
include "./Header.php"; 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="../assets/css/style1.css">
    <script src="../assets/js/scripts.js"></script>
</head>
<body>
<?php
// Vérifier si le statut et le message sont passés dans l'URL
if (isset($_GET['status']) && isset($_GET['message'])) {
    $status = $_GET['status'];
    $message = $_GET['message'];

    // Affichage d'une image et du message d'erreur ou de succès
    if ($status == "error") {
        echo '<div class="alert alert-danger">';
        echo '<img src="assets/images/error.png" alt="Erreur" style="width: 50px; height: 50px;"> ';
        echo htmlspecialchars($message) . '</div>';
    } elseif ($status == "success") {
        echo '<div class="alert alert-success">';
        echo '<img src="assets/images/success.png" alt="Succès" style="width: 50px; height: 50px;"> ';
        echo htmlspecialchars($message) . '</div>';
    }
}
?>

<div class="container dashboard-container">
        <h1><?php echo $message_bienvenue; ?></h1>
        
        <div class="teacher-info">
            <h2>Votre Tableau de Bord Administrateur</h2>
            <p>Bienvenue dans votre espace administrateur personnalisé. Ici, vous pouvez gérer les étudiants, consulter leurs progrès et effectuer d'autres tâches.</p>
            <p>Explorez le menu ci-dessus pour commencer.</p>
        </div>

        <!-- Formulaire de modification du mot de passe -->
        <div class="teacher-info">
            <h2>Modifier le Mot de Passe</h2>
            <form action="../Controller/changePasswordController.php" method="POST" onsubmit="return validatePassword()">
                <div class="mb-3 position-relative">
                    <label for="currentPassword" class="form-label">Mot de passe actuel</label>
                    <input type="password" class="form-control" id="currentPassword" name="currentPassword" required>
                    <span class="toggle-password" onclick="togglePasswordVisibility('currentPassword', this)">👁</span>
                </div>
                <div class="mb-3 position-relative">
                    <label for="newPassword" class="form-label">Nouveau mot de passe</label>
                    <input type="password" class="form-control" id="newPassword" name="newPassword" required>
                    <span class="toggle-password" onclick="togglePasswordVisibility('newPassword', this)">👁</span>
                </div>
                <div class="mb-3 position-relative">
                    <label for="confirmPassword" class="form-label">Confirmer le nouveau mot de passe</label>
                    <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" required>
                    <span class="toggle-password" onclick="togglePasswordVisibility('confirmPassword', this)">👁</span>
                </div>
                <button type="submit" class="btn btn-primary">Modifier</button>
            </form>
        </div>

        <!-- Formulaire de déconnexion -->
        <div class="mt-4">
            <form action="../Controller/UserController.php?action=logout" method="POST">
                <button type="submit" class="btn-logout">Se déconnecter</button>
            </form>
        </div>
</div>

<!-- Validation du mot de passe en JavaScript -->


</body>
</html>
