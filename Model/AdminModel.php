<?php
class AdminModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getPassword($username) {
        $stmt = $this->db->prepare("SELECT PASSWORD FROM admin WHERE username = :username");
        $stmt->execute(['username' => $username]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updatePassword($username, $newPassword) {
        $stmt = $this->db->prepare("UPDATE admin SET PASSWORD = :newPassword WHERE username = :username");
        return $stmt->execute(['newPassword' => $newPassword, 'username' => $username]);
    }
}
?>
