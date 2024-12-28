<?php
include_once '../Model/conx.php'; // Inclure la classe Database

class StudentModel {
    private $db;

    public function __construct() {
        $database = new Database();  // Créer une instance de la classe Database
        $this->db = $database->getConnection();  // Obtenir la connexion via getConnection()
    }

    // Récupérer les informations de l'étudiant depuis la table `classes`
    public function getStudentInfo($cin) {
        $query = "SELECT * FROM classes WHERE cin = :cin";  // Interroger la table `classes`
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':cin', $cin);
        $stmt->execute();

        // Retourner les informations de l'étudiant
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Ajouter les images dans la table `student`
    public function saveStudentFiles($cin, $code_class, $bac_img, $id_card_img, $birth_img) {
        $query = "INSERT INTO student (cin, code_class, bac_img, id_card_img, birth_img)
                  VALUES (:cin, :code_class, :bac_img, :id_card_img, :birth_img)";
        $stmt = $this->db->prepare($query);

        // Lier les valeurs
        $stmt->bindParam(':cin', $cin);
        $stmt->bindParam(':code_class', $code_class);
        $stmt->bindParam(':bac_img', $bac_img, PDO::PARAM_LOB); // Utilisez PDO::PARAM_LOB pour les fichiers binaires
        $stmt->bindParam(':id_card_img', $id_card_img, PDO::PARAM_LOB);
        $stmt->bindParam(':birth_img', $birth_img, PDO::PARAM_LOB);

        // Exécuter la requête
        return $stmt->execute();
    }
}
?>
