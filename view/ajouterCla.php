<?php
session_start();
include_once('Header.php');
if (!isset($_SESSION["username"])) {
    header("location: Login.php");
    exit();
}

// Inclure la bibliothèque PhpSpreadsheet
require '../vendor/autoload.php'; 

use PhpOffice\PhpSpreadsheet\IOFactory;

$message = ""; // Message de retour

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['excel_file'])) {
    $file = $_FILES['excel_file']['tmp_name'];
    $extension = pathinfo($_FILES['excel_file']['name'], PATHINFO_EXTENSION);
    
    // Vérification de l'extension et de la taille du fichier
    if ($_FILES['excel_file']['error'] === UPLOAD_ERR_OK) {
        if ($extension == 'xlsx' || $extension == 'xls') {
            // Charger le fichier Excel
            try {
                $spreadsheet = IOFactory::load($file);
                $sheet = $spreadsheet->getActiveSheet();
                $data = $sheet->toArray();

                // Connexion à la base de données
                include_once('../Model/conx.php');
                $db = (new Database())->getConnection();

                // Insérer les données dans la base de données
                $query = "INSERT INTO classes (code_class, filier_name, cin, s_fname, s_lname, age) 
                          VALUES (:code_class, :filier_name, :cin, :s_fname, :s_lname, :age)";
                $stmt = $db->prepare($query);

                // Parcourir les lignes du fichier Excel et les insérer dans la base de données
                foreach ($data as $row) {
                    // Vérifier que toutes les valeurs nécessaires sont présentes
                    if (!empty($row[0]) && !empty($row[1]) && !empty($row[2]) && !empty($row[3]) && !empty($row[4]) && !empty($row[5])) {
                        $stmt->bindParam(':code_class', $row[0]);
                        $stmt->bindParam(':filier_name', $row[1]);
                        $stmt->bindParam(':cin', $row[2]);
                        $stmt->bindParam(':s_fname', $row[3]);
                        $stmt->bindParam(':s_lname', $row[4]);
                        $stmt->bindParam(':age', $row[5], PDO::PARAM_INT); // Assurez-vous que l'âge est un entier

                        $stmt->execute();
                    }
                }

                $message = "Données importées avec succès !";
            } catch (Exception $e) {
                $message = "Erreur lors du traitement du fichier Excel : " . $e->getMessage();
            }
        } else {
            $message = "Veuillez télécharger un fichier Excel valide (xlsx ou xls).";
        }
    } else {
        $message = "Erreur lors du téléchargement du fichier. Code d'erreur : " . $_FILES['excel_file']['error'];
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Importer un Dossier de Classe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h1>Importer liste des stagiaires</h1>
        
        <?php if (!empty($message)): ?>
            <div class="alert alert-info">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <form  method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="excel_file" class="form-label">Fichier Excel à importer</label>
                <input type="file" class="form-control" name="excel_file" id="excel_file" accept=".xlsx,.xls" required>
            </div>
            <button type="submit" class="btn btn-primary">Importer</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
