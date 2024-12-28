<?php
session_start();
include '../Controller/UserController.php';

// Rediriger si l'utilisateur est déjà connecté
if (isset($_SESSION['username'])) {
    header('Location: Profile.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $userController = new UserController();
    $user = $userController->login($username, $password);

    if ($user) {
        $_SESSION['username'] = $user['cin']; // Enregistrer le CIN dans la session
        header('Location: Profile.php');
        exit;
    } else {
        $error_message = "Identifiants incorrects.";
    }
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
                <label for="username">CIN</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="input-group">
                <label for="password">Mot de passe</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn-submit">Se connecter</button>
        </form>
    </div>
</body>
</html>
