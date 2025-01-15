<?php
include_once 'conx.php';

// Répertoire de base pour créer les dossiers
$baseDirectory = '../uploads'; 
if (!is_dir($baseDirectory)) {
    mkdir($baseDirectory, 0777, true);
}

// Connexion à la base de données
$database = new Database();
$conn = $database->getConnection();

try {
    // Récupérer les informations des étudiants, y compris les images
    $query = "SELECT s_fname, s_lname, cin, bac_img, birth_img, id_card_img, code_class, filier_name FROM student";
    $stmt = $conn->prepare($query);
    $stmt->execute();

    $foldersCreated = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $filiere = $row['filier_name'];
        $classe = $row['code_class'];
        $cin = $row['cin'];
        $nom = $row['s_lname'];
        $prenom = $row['s_fname'];

        // Nom du dossier du stagiaire : cin_nom
        $folderName = $cin . "_" . $nom;
        $folderPath = "$baseDirectory/$filiere/$classe/$folderName";

        // Vérifier si le dossier existe déjà, sinon le créer
        if (!is_dir($folderPath)) {
            mkdir($folderPath, 0777, true);
            $foldersCreated[] = $folderPath;
        }

        // Récupérer et sauvegarder les fichiers (photo bac, acte de naissance, carte d'identité)
        $files = [
            'bac_img' => $row['bac_img'],
            'birth_img' => $row['birth_img'],
            'id_card_img' => $row['id_card_img']
        ];

        foreach ($files as $fileType => $fileData) {
            if ($fileData) {
                // Décoder l'image et la sauvegarder dans le dossier approprié
                $fileName = $fileType . ".jpg"; // Vous pouvez personnaliser l'extension en fonction du type de fichier
                $filePath = "$folderPath/$fileName";

                // Sauvegarder le fichier
                file_put_contents($filePath, $fileData);
            }
        }
    }

    // Retourner la réponse en JSON pour indiquer que l'opération a réussi
    echo json_encode([
        'status' => 'success',
        'folders' => $foldersCreated
    ]);
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'error' => $e->getMessage()
    ]);
}
?>
