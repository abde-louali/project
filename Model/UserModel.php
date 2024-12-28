<?php
include_once 'conx.php';

class UserModel {
    private $db;

    public function __construct() {
        $this->db = (new Database())->getConnection();
    }

    public function login($username, $password) {
        $query = "SELECT cin, code_class FROM classes WHERE cin = :username ";
        $stmt = $this->db->prepare($query);
        $stmt->execute([
            ':username' => $username
        ]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
