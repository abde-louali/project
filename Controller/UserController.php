<?php
include_once '../Model/UserModel.php';

class UserController {
    public function login($username, $password) {
        $userModel = new UserModel();
        $user = $userModel->login($username, $password);

        if ($user) {
            session_start();
            $_SESSION['username'] = $user['username'];
            $_SESSION['group'] = $user['group_name'];
            $_SESSION['user_type'] = $user['user_type']; // Store user type in session

            // Redirect based on user type
            if ($user['user_type'] === 'admin') {
                header('Location: ../View/Admin.php?message=success'); // Admin page
            } else {
                header('Location: ../View/Profile.php?message=success'); // Student page
            }
        } else {
            header('Location: ../View/Login.php?message=error');
        }
        exit;
    }

    public function logout() {
        session_start();
        session_unset();
        session_destroy();
        header('Location: ../View/Login.php?message=logout');
        exit;
    }
}

// Vérification de l'action dans l'URL
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    $userController = new UserController();
    $userController->logout();
}
?>
