<?php
include_once '../Model/conx.php'; // Connexion à la base de données

class StudentModel {
    private $db;

    public function __construct() {
        try {
            $database = new Database();
            $this->db = $database->getConnection();
        } catch (PDOException $e) {
            die("Erreur de connexion à la base de données : " . $e->getMessage());
        }
    }

    // Vérifier si le CIN existe dans la table student
    public function checkStudentExists($cin, $code_class, $filier_name) {
        try {
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
        $query = "SELECT * FROM classes WHERE cin = :cin";  // Interroger la table `classes`
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':cin', $cin);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getStudentDetails($cin, $code_class) {
        try {
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
        if ($this->checkStudentExists($cin, $code_class, $filier_name)) {
            $query = "UPDATE student 
                      SET bac_img = :bac_img, id_card_img = :id_card_img, birth_img = :birth_img
                      WHERE cin = :cin AND code_class = :code_class AND filier_name = :filier_name";
            $stmt = $this->db->prepare($query);
        } else {
            $query = "INSERT INTO student (cin, s_fname, s_lname, id_card_img, bac_img, birth_img, code_class, filier_name) 
                      VALUES (:cin, :s_fname, :s_lname, :id_card_img, :bac_img, :birth_img, :code_class, :filier_name)";
            $stmt = $this->db->prepare($query);
        }

        // Lier les paramètres aux valeurs
        $stmt->bindParam(':cin', $cin);
        $stmt->bindParam(':code_class', $code_class);
        $stmt->bindParam(':filier_name', $filier_name);
        $stmt->bindParam(':s_fname', $s_fname);
        $stmt->bindParam(':s_lname', $s_lname);
        $stmt->bindParam(':bac_img', $bac_img, PDO::PARAM_LOB);
        $stmt->bindParam(':id_card_img', $id_card_img, PDO::PARAM_LOB);
        $stmt->bindParam(':birth_img', $birth_img, PDO::PARAM_LOB);

        return $stmt->execute();
    }
}
?>
