<?php
session_start();

if (!isset($_SESSION["username"])) {
    header("location: ../view/Login.php");
    exit();
}

require_once(dirname(__DIR__) . '/Model/AdminModel.php');

// Connexion à la base de données
try {
    $db = new PDO("mysql:host=127.0.0.1;dbname=ista_project", "root", "");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

$model = new AdminModel($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $currentPassword = $_POST['currentPassword'];
    $newPassword = $_POST['newPassword'];
    $confirmPassword = $_POST['confirmPassword'];
    $username = $_SESSION["username"];

    // Vérification si les mots de passe correspondent
    if ($newPassword !== $confirmPassword) {
        header("location: ../view/Admin.php?message=Les mots de passe ne correspondent pas");
        exit();
    }

    // Vérification de la sécurité du mot de passe (minimum 8 caractères, une majuscule, une minuscule, un chiffre, et un caractère spécial)
    $passwordPattern = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*(),.?\":{}|<>]).{8,}$/";
    if (!preg_match($passwordPattern, $newPassword)) {
        header("location: ../view/Admin.php?message=Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial.");
        exit();
    }

    // Vérification du mot de passe actuel
    $user = $model->getPassword($username);
    if (!$user || $user['PASSWORD'] !== $currentPassword) {
        header("location: ../view/Admin.php?message=Mot de passe actuel incorrect");
        exit();
    } else {
        // Mise à jour du mot de passe
        $updated = $model->updatePassword($username, $newPassword);
        $message = $updated ? "Mot de passe modifié avec succès" : "Erreur lors de la mise à jour";
        header("location: ../view/Admin.php?message=" . $message);
        exit();
    }
}
?>
