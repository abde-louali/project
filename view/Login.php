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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        /* Styles précédents inchangés jusqu'à input-wrapper */
        body {
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
        }

        .login-container {
            background: white;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 380px;
            text-align: center;
        }

        .logo-container {
            margin-bottom: 1.5rem;
        }

        .logo-container img {
            width: 80px;
            height: auto;
            margin-bottom: 0.5rem;
        }

        h2 {
            color: #333;
            font-size: 24px;
            margin-bottom: 2rem;
            font-weight: 500;
        }

        .input-group {
            margin-bottom: 1.5rem;
            text-align: left;
        }

        .input-group label {
            display: block;
            color: #333;
            margin-bottom: 0.5rem;
            font-size: 14px;
        }

        .input-group input {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            box-sizing: border-box;
            font-size: 14px;
            background-color: #f8f9fa;
        }

        .input-group input:focus {
            outline: none;
            border-color: #4a90e2;
            background-color: white;
        }

        .input-group input::placeholder {
            color: #999;
        }

        .btn-submit {
            width: 100%;
            padding: 12px;
            background-color: #4361ee;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .btn-submit:hover {
            background-color: #3a52d8;
        }

        .error-message {
            background-color: #fee2e2;
            color: #dc2626;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 1rem;
            font-size: 14px;
            text-align: left;
        }

        /* Style modifié pour les wrappers d'input */
        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-wrapper input {
            padding-left: 35px;
            padding-right: 35px; /* Espace pour l'icône de visibilité */
        }

        .input-wrapper::before {
            content: '';
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            width: 16px;
            height: 16px;
            background-size: contain;
            background-repeat: no-repeat;
            opacity: 0.5;
        }

        .input-wrapper.username::before {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%23666666'%3E%3Cpath d='M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z'/%3E%3C/svg%3E");
        }

        .input-wrapper.password::before {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%23666666'%3E%3Cpath d='M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z'/%3E%3C/svg%3E");
        }

        /* Nouveau style pour le bouton de visibilité */
        .toggle-password {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            border: none;
            background: none;
            cursor: pointer;
            padding: 0;
            color: #666;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: color 0.2s;
        }

        .toggle-password:hover {
            color: #4361ee;
        }

        .toggle-password i {
            font-size: 1.2rem;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo-container">
            <img src="../assets/img/ofppt_logo.png" alt="Logo OFPPT">
            <h2>Connexion</h2>
        </div>
        
        <form action="Login.php" method="POST">
            <?php if (isset($error_message)): ?>
                <div class="error-message">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>
            
            <div class="input-group">
                <label for="username">Nom d'utilisateur</label>
                <div class="input-wrapper username">
                    <input type="text" id="username" name="username" required 
                           placeholder="Entrez votre CIN">
                </div>
            </div>
            
            <div class="input-group">
                <label for="password">Mot de passe</label>
                <div class="input-wrapper password">
                    <input type="password" id="password" name="password" required 
                           placeholder="Entrez votre mot de passe">
                    <button type="button" class="toggle-password" aria-label="Afficher/Masquer le mot de passe">
                        <i class="bi bi-eye-slash"></i>
                    </button>
                </div>
            </div>
            
            <button type="submit" class="btn-submit">Se connecter</button>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const togglePassword = document.querySelector('.toggle-password');
            const passwordInput = document.querySelector('#password');
            
            togglePassword.addEventListener('click', function() {
                // Change le type de l'input
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                
                // Change l'icône
                const icon = this.querySelector('i');
                icon.classList.toggle('bi-eye');
                icon.classList.toggle('bi-eye-slash');
            });
        });
    </script>
</body>
</html>