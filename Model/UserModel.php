<?php
include_once 'conx.php';

class UserModel {
    private $db;

    public function __construct() {
        $this->db = (new Database())->getConnection();
    }

    public function login($username, $password) {
        // Check for user in the students table (cin is both the username and password)
        $queryStudent = "SELECT cin AS username, code_class AS group_name FROM classes WHERE cin = :username AND cin = :password";
        $stmtStudent = $this->db->prepare($queryStudent);
        $stmtStudent->execute([
            ':username' => $username,
            ':password' => $password
        ]);

        $student = $stmtStudent->fetch(PDO::FETCH_ASSOC);

        if ($student) {
            $student['user_type'] = 'student'; 
            return $student;
        }
        $queryAdmin = "SELECT username FROM `ADMIN` WHERE username = :username AND PASSWORD = :password";
        $stmtAdmin = $this->db->prepare($queryAdmin);
        $stmtAdmin->execute([
            ':username' => $username,  
            ':password' => $password   
        ]);

        $admin = $stmtAdmin->fetch(PDO::FETCH_ASSOC);

        if ($admin) {
            $admin['user_type'] = 'admin'; 
            return $admin;
        }

      
        return null;
    }
}
?>
