<?php
include_once '../Model/conx.php'; // Connexion à la base de données

class StudentModel {
    private $db;

    public function __construct() {
        $this->connectToDatabase();
    }

    // Reconnect to the database if the connection is lost
    private function connectToDatabase() {
        try {
            $database = new Database();
            $this->db = $database->getConnection();
        } catch (PDOException $e) {
            die("Erreur de connexion à la base de données : " . $e->getMessage());
        }
    }

    // Check if the connection is still alive
    private function checkConnection() {
        try {
            $this->db->query('SELECT 1');
        } catch (PDOException $e) {
            // Reconnect if the connection is lost
            $this->connectToDatabase();
        }
    }

    // Vérifier si le CIN existe dans la table student
    public function checkStudentExists($cin, $code_class, $filier_name) {
        try {
            $this->checkConnection(); // Ensure the connection is alive
            $query = "SELECT COUNT(*) FROM student WHERE cin = :cin AND code_class = :code_class AND filier_name = :filier_name";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':cin', $cin, PDO::PARAM_STR);
            $stmt->bindParam(':code_class', $code_class, PDO::PARAM_STR);
            $stmt->bindParam(':filier_name', $filier_name, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            error_log("Erreur lors de la vérification du CIN : " . $e->getMessage());
            return false;
        }
    }

    public function getStudentInfo($cin) {
        try {
            $this->checkConnection(); // Ensure the connection is alive
            $query = "SELECT * FROM classes WHERE cin = :cin";  // Interroger la table classes
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':cin', $cin);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération des informations de l'étudiant : " . $e->getMessage());
            return false;
        }
    }

    public function getStudentDetails($cin, $code_class) {
        try {
            $this->checkConnection(); // Ensure the connection is alive
            $query = "SELECT s_fname, s_lname, filier_name FROM classes WHERE cin = :cin AND code_class = :code_class";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':cin', $cin);
            $stmt->bindParam(':code_class', $code_class);
            $stmt->execute();
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($result) {
                return $result;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération des détails de l'étudiant : " . $e->getMessage());
            return false;
        }
    }

    public function saveOrUpdateStudentFiles($cin, $code_class, $filier_name, $s_fname, $s_lname, $bac_img, $id_card_img, $birth_img) {
        try {
            if ($this->checkStudentExists($cin, $code_class, $filier_name)) {
                // Requête UPDATE
                $query = "UPDATE student 
                          SET bac_img = :bac_img, id_card_img = :id_card_img, birth_img = :birth_img
                          WHERE cin = :cin AND code_class = :code_class AND filier_name = :filier_name";
                $stmt = $this->db->prepare($query);
    
                // Lier uniquement les paramètres nécessaires pour l'UPDATE
                $stmt->bindParam(':cin', $cin);
                $stmt->bindParam(':code_class', $code_class);
                $stmt->bindParam(':filier_name', $filier_name);
                $stmt->bindParam(':bac_img', $bac_img, PDO::PARAM_LOB);
                $stmt->bindParam(':id_card_img', $id_card_img, PDO::PARAM_LOB);
                $stmt->bindParam(':birth_img', $birth_img, PDO::PARAM_LOB);
            } else {
                // Requête INSERT
                $query = "INSERT INTO student (cin, s_fname, s_lname, id_card_img, bac_img, birth_img, code_class, filier_name) 
                          VALUES (:cin, :s_fname, :s_lname, :id_card_img, :bac_img, :birth_img, :code_class, :filier_name)";
                $stmt = $this->db->prepare($query);
    
                // Lier tous les paramètres nécessaires pour l'INSERT
                $stmt->bindParam(':cin', $cin);
                $stmt->bindParam(':s_fname', $s_fname);
                $stmt->bindParam(':s_lname', $s_lname);
                $stmt->bindParam(':id_card_img', $id_card_img, PDO::PARAM_LOB);
                $stmt->bindParam(':bac_img', $bac_img, PDO::PARAM_LOB);
                $stmt->bindParam(':birth_img', $birth_img, PDO::PARAM_LOB);
                $stmt->bindParam(':code_class', $code_class);
                $stmt->bindParam(':filier_name', $filier_name);
            }
    
            // Exécuter la requête
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erreur lors de la sauvegarde ou mise à jour des fichiers de l'étudiant : " . $e->getMessage());
            return false;
        }
    }
    
}
?>
