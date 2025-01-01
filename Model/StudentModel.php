<?php
include_once '../Model/conx.php'; 

class StudentModel {
    private $db;

    public function __construct() {
        $database = new Database();  // Créer une instance de la classe Database
        $this->db = $database->getConnection();  // Obtenir la connexion via getConnection()
    }

    // Vérifier si le CIN existe déjà
    public function checkStudentExists($cin) {
        $query = "SELECT COUNT(*) FROM student WHERE cin = :cin";  // Requête pour vérifier si le CIN existe
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':cin', $cin);
        $stmt->execute();

        return $stmt->fetchColumn() > 0;  // Si le nombre est supérieur à 0, le CIN existe
    }

    // Sauvegarder ou mettre à jour les fichiers de l'étudiant
    public function saveOrUpdateStudentFiles($cin, $code_class, $bac_img, $id_card_img, $birth_img) {
        // Vérifier si l'étudiant existe déjà
        if ($this->checkStudentExists($cin)) {
            // Si le CIN existe, faire une mise à jour
            $query = "UPDATE student SET code_class = :code_class, bac_img = :bac_img, id_card_img = :id_card_img, birth_img = :birth_img WHERE cin = :cin";
            $stmt = $this->db->prepare($query);

            // Lier les valeurs
            $stmt->bindParam(':cin', $cin);
            $stmt->bindParam(':code_class', $code_class);
            $stmt->bindParam(':bac_img', $bac_img, PDO::PARAM_LOB);
            $stmt->bindParam(':id_card_img', $id_card_img, PDO::PARAM_LOB);
            $stmt->bindParam(':birth_img', $birth_img, PDO::PARAM_LOB);

            // Exécuter la requête et retourner false pour indiquer qu'il s'agit d'une mise à jour
            return $stmt->execute() ? false : true;
        } else {
            // Si le CIN n'existe pas, faire une insertion
            $query = "INSERT INTO student (cin, code_class, bac_img, id_card_img, birth_img)
                      VALUES (:cin, :code_class, :bac_img, :id_card_img, :birth_img)";
            $stmt = $this->db->prepare($query);

            // Lier les valeurs
            $stmt->bindParam(':cin', $cin);
            $stmt->bindParam(':code_class', $code_class);
            $stmt->bindParam(':bac_img', $bac_img, PDO::PARAM_LOB); 
            $stmt->bindParam(':id_card_img', $id_card_img, PDO::PARAM_LOB);
            $stmt->bindParam(':birth_img', $birth_img, PDO::PARAM_LOB);

            // Exécuter la requête et retourner true pour indiquer qu'il s'agit d'un ajout
            return $stmt->execute() ? true : false;
        }
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
    // Méthode pour créer un dossier pour la classe et l'associer à la table `student`
public function createClassFolder($code_class) {
    $folderPath = '../uploads/classes/' . $code_class; // Spécifier le chemin du dossier

    // Vérifier si le dossier existe déjà
    if (!file_exists($folderPath)) {
        // Si le dossier n'existe pas, le créer
        if (!mkdir($folderPath, 0777, true)) {
            return false; // Retourne false si le dossier ne peut pas être créé
        }
    }
    return true; // Retourne true si le dossier a été créé ou existe déjà
}

// Méthode pour ajouter ou mettre à jour les étudiants dans la base de données avec leurs dossiers
public function saveStudentWithFolder($cin, $code_class, $bac_img, $id_card_img, $birth_img) {
    // Créer le dossier de la classe si nécessaire
    if ($this->createClassFolder($code_class)) {
        // Enregistrer les fichiers dans la base de données
        return $this->saveOrUpdateStudentFiles($cin, $code_class, $bac_img, $id_card_img, $birth_img);
    } else {
        return false; // Si le dossier n'a pas pu être créé, retourner false
    }
}

}
?>
