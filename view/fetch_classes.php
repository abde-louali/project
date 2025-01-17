<?php
require_once '../Model/conx.php';

if (isset($_GET['filiere'])) {
    $filiere = $_GET['filiere'];
    
    try {
        $db = (new Database())->getConnection();
        $query = "SELECT DISTINCT code_class FROM classes WHERE filier_name = :filiere ORDER BY code_class";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':filiere', $filiere);
        $stmt->execute();
        
        $classes = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        // Retourner les classes au format JSON
        echo json_encode($classes);
    } catch (PDOException $e) {
        echo json_encode(['error' => 'Erreur lors de la récupération des classes']);
    }
} else {
    echo json_encode(['error' => 'Filière non spécifiée']);
}
?>