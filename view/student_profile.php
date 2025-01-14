<?php
// student_profile.php
session_start();

// Vérifier si l'utilisateur est connecté et est un administrateur
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: ../View/Login.php');
    exit;
}

// Inclure la connexion à la base de données
include_once '../Model/conx.php';

// Récupérer le CIN, la classe, et la filière depuis la chaîne de requête
if (isset($_GET['cin']) && isset($_GET['class']) && isset($_GET['filiere'])) {
    $cin = $_GET['cin'];
    $code_class = $_GET['class'];
    $filier_name = $_GET['filiere'];

    // Récupérer les détails de l'étudiant
    $database = new Database();
    $conn = $database->getConnection();

    $query = "
        SELECT s_fname, s_lname, cin, code_class, filier_name, bac_img, birth_img, id_card_img
        FROM Student 
        WHERE cin = :cin 
          AND code_class = :code_class 
          AND filier_name = :filier_name
    ";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':cin', $cin);
    $stmt->bindParam(':code_class', $code_class);
    $stmt->bindParam(':filier_name', $filier_name);
    $stmt->execute();

    $student = $stmt->fetch(PDO::FETCH_ASSOC);
} else {
    die("Requête invalide.");
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil de l'étudiant</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="container mt-4">
        <h2 class="mb-4">Profil de l'étudiant</h2>
        
        <?php if ($student): ?>
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Nom : <?php echo htmlspecialchars($student['s_lname']); ?>, Prénom : <?php echo htmlspecialchars($student['s_fname']); ?></h5>
                    <p class="card-text">CIN : <?php echo htmlspecialchars($student['cin']); ?></p>
                    <p class="card-text">Classe : <?php echo htmlspecialchars($student['code_class']); ?></p>
                    <p class="card-text">Filière : <?php echo htmlspecialchars($student['filier_name']); ?></p>
                    <!-- Afficher les images si nécessaire -->
                    <div class="mt-3">
                        <h6>Documents téléchargés :</h6>
                        <ul>
                            <li>Image du BAC : <?php echo $student['bac_img'] ? 'Téléchargé' : 'Non téléchargé'; ?></li>
                            <li>Image de l'acte de naissance : <?php echo $student['birth_img'] ? 'Téléchargé' : 'Non téléchargé'; ?></li>
                            <li>Image de la carte d'identité : <?php echo $student['id_card_img'] ? 'Téléchargé' : 'Non téléchargé'; ?></li>
                        </ul>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-danger">
                Étudiant non trouvé.
            </div>
        <?php endif; ?>
        <a href="student_details.php?cin=<?php echo htmlspecialchars($student['cin']); ?>&class=<?php echo htmlspecialchars($student['code_class']); ?>&filiere=<?php echo htmlspecialchars($student['filier_name']); ?>" class="btn btn-primary mt-3">Retour à la liste des étudiants</a>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
