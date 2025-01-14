<?php

session_start();
require_once('../Model/AdminModel.php');

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION["username"])) {
    header("Location: ../view/Login.php");
    exit();
}

$model = new AdminModel();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $currentPassword = $_POST['currentPassword'];
    $newPassword = $_POST['newPassword'];
    $confirmPassword = $_POST['confirmPassword'];
    $username = $_SESSION["username"];

    // Vérification si les mots de passe correspondent
    if ($newPassword !== $confirmPassword) {
        header("Location: ../view/Admin.php?status=error&message=Les mots de passe ne correspondent pas");
        exit();
    }

    // Vérification de la sécurité du mot de passe (minimum 8 caractères, une majuscule, une minuscule, un chiffre, et un caractère spécial)
    $passwordPattern = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*(),.?\":{}|<>]).{8,}$/";
    if (!preg_match($passwordPattern, $newPassword)) {
        header("Location: ../view/Admin.php?status=error&message=Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial.");
        exit();
    }

    // Vérification du mot de passe actuel
    $user = $model->getPassword($username);
    if (!$user || $user['PASSWORD'] !== $currentPassword) {
        header("Location: ../view/Admin.php?status=error&message=Mot de passe actuel incorrect");
        exit();
    }

    // Mise à jour du mot de passe
    $updated = $model->updatePassword($username, $newPassword);
    $message = $updated ? "Mot de passe modifié avec succès" : "Erreur lors de la mise à jour du mot de passe";
    header("Location: ../view/Admin.php?status=" . ($updated ? 'success' : 'error') . "&message=" . $message);
    exit();
}
