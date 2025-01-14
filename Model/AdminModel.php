<?php
include_once '../Model/conx.php';

class AdminModel {
    private $db;

    public function __construct() {
        // Ici, on utilise getConnection pour obtenir une instance de PDO
        $this->db = (new Database())->getConnection();  // Obtenez la connexion PDO via getConnection
    }
    public function getAllFilieres() {
        try {
            $query = "SELECT DISTINCT filier_name FROM classes ORDER BY filier_name ASC";
            $stmt = $this->db->prepare($query);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_COLUMN);
        } catch (PDOException $e) {
            error_log("Error getting filières: " . $e->getMessage());
            return [];
        }
    }
    // Récupérer le mot de passe actuel de l'utilisateur
    public function getPassword($username) {
        $query = "SELECT PASSWORD FROM admin WHERE username = :username";
        $stmt = $this->db->prepare($query);  // La méthode prepare() fonctionne maintenant car $this->db est un objet PDO
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Mettre à jour le mot de passe de l'utilisateur
    public function updatePassword($username, $newPassword) {
        $query = "UPDATE admin SET PASSWORD = :newPassword WHERE username = :username";
        $stmt = $this->db->prepare($query);  // Toujours un objet PDO ici
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':newPassword', $newPassword);  // Sécuriser avec hash
        return $stmt->execute();
    }
}
?>
