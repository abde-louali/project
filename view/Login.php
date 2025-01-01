<?php
session_start();
include '../Controller/UserController.php';

// Redirect if the user is already logged in
if (isset($_SESSION['username'])) {
    if ($_SESSION['user_type'] === 'admin') {
        header('Location: Admin.php');
    } else {
        header('Location: Profile.php');
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $userController = new UserController();
    $userController->login($username, $password); // Handles redirection
}
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion | OFPPT</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
    <div class="login-container">
        <div class="logo-container">
            <img src="../assets/img/ofppt_logo.png" alt="Logo OFPPT" class="logo">
        </div>
        <h2>Connexion</h2>
        <form action="Login.php" method="POST">
            <?php if (isset($error_message)): ?>
                <div class="error-message"><?php echo $error_message; ?></div>
            <?php endif; ?>
            <div class="input-group">
                <label for="username">Nom d'utilisateur</label>
                <input type="text" id="username" name="username" required placeholder="Enter CIN">
            </div>
            <div class="input-group">
                <label for="password">Mot de passe</label>
                <input type="password" id="password" name="password" required placeholder="Enter Mot de passe">
            </div>
            <button type="submit" class="btn-submit">Se connecter</button>
        </form>
    </div>
</body>
</html>
