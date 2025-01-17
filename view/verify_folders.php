<?php
session_start();
require_once '../Controller/AdminController.php';

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: Login.php');
    exit();
}

$adminController = new AdminController();

if (isset($_GET['class']) && isset($_GET['filiere'])) {
    $class = $_GET['class'];
    $filiere = $_GET['filiere'];
    
    // Vérifier l'existence des dossiers et fichiers
    $baseDir = '../uploads/';
    $filiereDir = $baseDir . $filiere;
    $classDir = $filiereDir . '/' . $class;
    
    $students = $adminController->getStudentsByClass($class);
    $verificationResults = [];
    
    foreach ($students as $student) {
        $studentName = $student['s_fname'] . ' ' . $student['s_lname'];
        $studentDir = $classDir . '/' . $student['cin'] . '_' . $studentName;
        
        $studentResult = [
            'name' => $studentName,
            'cin' => $student['cin'],
            'directory' => file_exists($studentDir),
            'documents' => []
        ];
        
        // Vérifier les documents
        $documents = $adminController->getStudentDocuments($student['cin']);
        if ($documents) {
            foreach ($documents as $type => $path) {
                if ($path) {
                    $extension = pathinfo($path, PATHINFO_EXTENSION);
                    $destPath = $studentDir . '/' . $type . '.' . $extension;
                    $studentResult['documents'][$type] = file_exists($destPath);
                } else {
                    $studentResult['documents'][$type] = false;
                }
            }
        }
        
        $verificationResults[] = $studentResult;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vérification des Dossiers</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .verification-card {
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 1rem;
        }
        .status-icon {
            font-size: 1.2rem;
        }
        .status-success {
            color: #198754;
        }
        .status-error {
            color: #dc3545;
        }
    </style>
</head>
<body>
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Vérification des Dossiers - <?php echo htmlspecialchars($class); ?></h2>
            <a href="student_details.php?class=<?php echo urlencode($class); ?>&filiere=<?php echo urlencode($filiere); ?>" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Retour
            </a>
        </div>

        <?php if (!empty($verificationResults)): ?>
            <?php foreach ($verificationResults as $result): ?>
                <div class="card verification-card">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($result['name']); ?></h5>
                        <p class="card-text">CIN: <?php echo htmlspecialchars($result['cin']); ?></p>
                        
                        <div class="mt-3">
                            <div class="mb-2">
                                <span class="status-icon <?php echo $result['directory'] ? 'status-success' : 'status-error'; ?>">
                                    <i class="bi <?php echo $result['directory'] ? 'bi-check-circle-fill' : 'bi-x-circle-fill'; ?>"></i>
                                </span>
                                Dossier personnel
                            </div>
                            
                            <?php foreach ($result['documents'] as $type => $exists): ?>
                                <div class="mb-2">
                                    <span class="status-icon <?php echo $exists ? 'status-success' : 'status-error'; ?>">
                                        <i class="bi <?php echo $exists ? 'bi-check-circle-fill' : 'bi-x-circle-fill'; ?>"></i>
                                    </span>
                                    <?php 
                                    $docName = [
                                        'bac_img' => 'Baccalauréat',
                                        'birth_img' => 'Acte de naissance',
                                        'id_card_img' => 'Carte d\'identité'
                                    ][$type] ?? $type;
                                    echo htmlspecialchars($docName); 
                                    ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="alert alert-info">
                Aucun résultat de vérification disponible.
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 