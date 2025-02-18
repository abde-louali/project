<?php
session_start();

if (!isset($_SESSION["username"])) {
    header("location: Login.php");
    exit();
}

include_once '../Model/AdminModel.php';
$adminModel = new AdminModel();
$adminInfo = $adminModel->getAdminInfo($_SESSION["username"]);

include "./Header.php";
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Administrateur</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .profile-container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        .profile-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .profile-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #e5e7eb;
        }

        .profile-title-section {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .profile-icon {
            font-size: 2.5rem;
            color: #4e73df;
        }

        .profile-title {
            margin: 0;
            color: #2d3748;
            font-size: 1.5rem;
            font-weight: 600;
        }

        .edit-button {
            padding: 0.5rem 1rem;
            background-color: #4e73df;
            color: white;
            border: none;
            border-radius: 6px;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .edit-button:hover {
            background-color: #2e59d9;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .info-item {
            padding: 1rem;
            background: #f8fafc;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
        }

        .info-label {
            color: #6b7280;
            font-size: 0.875rem;
            margin-bottom: 0.5rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .info-value {
            color: #1f2937;
            font-size: 1.1rem;
            font-weight: 500;
        }

        .edit-form {
            display: none;
            margin-top: 1rem;
        }

        .edit-form.active {
            display: block;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #4a5568;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .form-group input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            font-size: 1rem;
            transition: all 0.2s;
        }

        .form-group input:focus {
            outline: none;
            border-color: #4e73df;
            box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.1);
        }

        .button-group {
            display: flex;
            gap: 1rem;
            margin-top: 1.5rem;
        }

        .save-button,
        .cancel-button {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 6px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
        }

        .save-button {
            background-color: #4e73df;
            color: white;
        }

        .cancel-button {
            background-color: #e2e8f0;
            color: #4a5568;
        }

        .save-button:hover {
            background-color: #2e59d9;
        }

        .cancel-button:hover {
            background-color: #cbd5e0;
        }

        .password-section {
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 2px solid #e5e7eb;
        }

        .password-title {
            color: #2d3748;
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
        }

        .form-floating {
            margin-bottom: 1rem;
        }

        .password-toggle {
            cursor: pointer;
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #6b7280;
        }

        .btn-submit {
            background: #4e73df;
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 6px;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .btn-submit:hover {
            background: #2e59d9;
        }

        @media (max-width: 640px) {
            .info-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
    <link rel="stylesheet" href="../assets/css/darkmood.css">
    <script src="../assets/js/darkmood.js"></script>
</head>

<body>
    <div class="profile-container">
        <?php if (isset($_GET['status']) && isset($_GET['message'])): ?>
            <div class="alert alert-<?php echo $_GET['status'] == 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($_GET['message']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="profile-card">
            <div class="profile-header">
                <div class="profile-title-section">
                    <i class="bi bi-person-circle profile-icon"></i>
                    <h1 class="profile-title">Profile Administrateur</h1>
                </div>
                <button type="button" class="edit-button" onclick="toggleEdit()">
                    <i class="bi bi-pencil"></i> Modifier
                </button>
            </div>

            <div id="info-display" class="info-grid">
                <div class="info-item">
                    <div class="info-label">Nom d'utilisateur</div>
                    <div class="info-value"><?php echo htmlspecialchars($adminInfo['username']); ?></div>
                </div>

                <div class="info-item">
                    <div class="info-label">CIN</div>
                    <div class="info-value"><?php echo htmlspecialchars($adminInfo['cin']); ?></div>
                </div>

                <div class="info-item">
                    <div class="info-label">Nom</div>
                    <div class="info-value"><?php echo htmlspecialchars($adminInfo['last_name']); ?></div>
                </div>

                <div class="info-item">
                    <div class="info-label">Prénom</div>
                    <div class="info-value"><?php echo htmlspecialchars($adminInfo['first_name']); ?></div>
                </div>
            </div>

            <form id="edit-form" action="../Controller/AdminController.php" method="POST" class="edit-form">
                <input type="hidden" name="action" value="update_profile">
                <div class="form-group">
                    <label for="username">Nom d'utilisateur</label>
                    <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($adminInfo['username']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="cin">CIN</label>
                    <input type="text" id="cin" name="cin" value="<?php echo htmlspecialchars($adminInfo['cin']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="last_name">Nom</label>
                    <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($adminInfo['last_name']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="first_name">Prénom</label>
                    <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($adminInfo['first_name']); ?>" required>
                </div>
                <div class="button-group">
                    <button type="submit" class="save-button">
                        <i class="bi bi-check2"></i> Enregistrer
                    </button>
                    <button type="button" class="cancel-button" onclick="toggleEdit()">
                        <i class="bi bi-x"></i> Annuler
                    </button>
                </div>
            </form>

            <div class="password-section">
                <h2 class="password-title">
                    <i class="bi bi-key me-2"></i>
                    Changer le mot de passe
                </h2>
                <form action="../Controller/changePasswordController.php" method="POST" onsubmit="return validatePassword()">
                    <div class="form-floating mb-3 position-relative">
                        <input type="password" class="form-control" id="currentPassword" name="currentPassword" placeholder="Mot de passe actuel" required>
                        <label for="currentPassword">Mot de passe actuel</label>
                        <i class="bi bi-eye password-toggle" onclick="togglePassword('currentPassword', this)"></i>
                    </div>

                    <div class="form-floating mb-3 position-relative">
                        <input type="password" class="form-control" id="newPassword" name="newPassword" placeholder="Nouveau mot de passe" required>
                        <label for="newPassword">Nouveau mot de passe</label>
                        <i class="bi bi-eye password-toggle" onclick="togglePassword('newPassword', this)"></i>
                    </div>

                    <div class="form-floating mb-4 position-relative">
                        <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" placeholder="Confirmer le mot de passe" required>
                        <label for="confirmPassword">Confirmer le nouveau mot de passe</label>
                        <i class="bi bi-eye password-toggle" onclick="togglePassword('confirmPassword', this)"></i>
                    </div>

                    <button type="submit" class="btn-submit">
                        <i class="bi bi-check2-circle me-2"></i>
                        Modifier le mot de passe
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function toggleEdit() {
            const infoDisplay = document.getElementById('info-display');
            const editForm = document.getElementById('edit-form');

            if (editForm.classList.contains('active')) {
                editForm.classList.remove('active');
                infoDisplay.style.display = 'grid';
            } else {
                editForm.classList.add('active');
                infoDisplay.style.display = 'none';
            }
        }

        function togglePassword(inputId, icon) {
            const input = document.getElementById(inputId);
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            }
        }

        function validatePassword() {
            const newPassword = document.getElementById('newPassword').value;
            const confirmPassword = document.getElementById('confirmPassword').value;

            if (newPassword !== confirmPassword) {
                alert('Les mots de passe ne correspondent pas');
                return false;
            }

            // Vérification de la complexité du mot de passe
            const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*(),.?":{}|<>]).{8,}$/;
            if (!passwordRegex.test(newPassword)) {
                alert('Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial');
                return false;
            }

            return true;
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>