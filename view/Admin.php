<?php
session_start();

if (!isset($_SESSION["username"])) {
    header("location: Login.php");
    exit();
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
    <style>
        body {
            background-color: #f8f9fc; /* Light background for contrast */
        }
        .dashboard-container {
            margin-top: 50px;
        }
        .teacher-info {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }
        .teacher-info h2 {
            font-size: 28px;
            color: #333;
            margin-bottom: 15px;
        }
        .teacher-info p {
            font-size: 18px;
            color: #666;
            margin-bottom: 20px;
        }
        .btn-logout {
            background-color: #e74a3b;
            color: white;
            font-size: 16px;
            border-radius: 5px;
            padding: 10px 20px;
            text-decoration: none;
        }
        .btn-logout:hover {
            background-color: #c0392b;
        }
    </style>
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
        <h1 class="display-4">Bonjour, Administrateur</h1>
        
        <div class="teacher-info">
            <h2>Votre Tableau de Bord Administrateur</h2>
            <p>Bienvenue dans votre espace administrateur personnalisé. Ici, vous pouvez gérer les étudiants, consulter leurs progrès et effectuer d'autres tâches.</p>
            <p>Explorez le menu ci-dessus pour commencer.</p>
        </div>

        <!-- Formulaire de modification du mot de passe -->
        <div class="teacher-info">
            <h2>Modifier le Mot de Passe</h2>
            <form action="../Controller/changePasswordController.php" method="POST" onsubmit="return validatePassword()">
                <div class="mb-3">
                    <label for="currentPassword" class="form-label">Mot de passe actuel</label>
                    <input type="password" class="form-control" id="currentPassword" name="currentPassword" required>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="showCurrentPassword">
                        <label class="form-check-label" for="showCurrentPassword">Voir le mot de passe</label>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="newPassword" class="form-label">Nouveau mot de passe</label>
                    <input type="password" class="form-control" id="newPassword" name="newPassword" required>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="showNewPassword">
                        <label class="form-check-label" for="showNewPassword">Voir le mot de passe</label>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="confirmPassword" class="form-label">Confirmer le nouveau mot de passe</label>
                    <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" required>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="showConfirmPassword">
                        <label class="form-check-label" for="showConfirmPassword">Voir le mot de passe</label>
                    </div>
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
<script>
    // Fonction pour valider les mots de passe
    function validatePassword() {
        var newPassword = document.getElementById("newPassword").value;
        var confirmPassword = document.getElementById("confirmPassword").value;

        if (newPassword !== confirmPassword) {
            alert("Les mots de passe ne correspondent pas.");
            return false;
        }

        var passwordPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*(),.?\":{}|<>]).{8,}$/;
        if (!passwordPattern.test(newPassword)) {
            alert("Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial.");
            return false;
        }

        return true;
    }

    // Fonction pour afficher ou masquer les mots de passe
    document.getElementById("showCurrentPassword").addEventListener('change', function() {
        var currentPasswordField = document.getElementById("currentPassword");
        currentPasswordField.type = this.checked ? 'text' : 'password';
    });

    document.getElementById("showNewPassword").addEventListener('change', function() {
        var newPasswordField = document.getElementById("newPassword");
        newPasswordField.type = this.checked ? 'text' : 'password';
    });

    document.getElementById("showConfirmPassword").addEventListener('change', function() {
        var confirmPasswordField = document.getElementById("confirmPassword");
        confirmPasswordField.type = this.checked ? 'text' : 'password';
    });
</script>

</body>
</html>
