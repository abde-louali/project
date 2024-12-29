<?php
// Inclure les fichiers nécessaires (connexion à la base de données, modèle, etc.)
session_start();
include_once('Header.php');
if (!isset($_SESSION["username"])) {
    header("location: Login.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Ajouter un Dossier de Classe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>
    <div class="container">
        <h1 class="display-4">Ajouter un Dossier de Classe</h1>
        
        <?php if (isset($message)): ?>
            <div class="alert alert-info">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <form action="../Controller/AdminController.php" method="POST">
            <div class="mb-3">
                <label for="folder_name" class="form-label">Nom du Dossier</label>
                <input type="text" name="folder_name" id="folder_name" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary" name="create_folder">Créer un Dossier</button>
        </form>

        <hr>

        <h3>Dossiers existants :</h3>
        <div class="row">
            <?php
            if (isset($folders)) {
                foreach ($folders as $folder) {
                    echo "<div class='col-md-3'>
                            <div class='folder-item'>
                                <i class='folder-icon'>📁</i>" . basename($folder) . "
                            </div>
                          </div>";
                }
            } else {
                echo "<p>Aucun dossier trouvé.</p>";
            }
            ?>
        </div>
    </div>
</body>
</html>
