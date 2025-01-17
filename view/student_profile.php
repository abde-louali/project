<?php
session_start();
require_once '../Controller/AdminController.php';
require_once '../Controller/FileController.php';

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: Login.php');
    exit();
}

$adminController = new AdminController();
$fileController = new FileController();

// Récupérer le CIN depuis l'URL
$cin = isset($_GET['cin']) ? $_GET['cin'] : '';

// Récupérer les informations de l'étudiant
$student = $adminController->getStudentByCin($cin);

if (!$student) {
    header('Location: student_details.php?error=student_not_found');
    exit();
}

function getDocumentPath($student, $docType) {
    $folderName = $student['cin'] . "_" . str_replace(' ', '_', $student['s_fname']) . "_" . str_replace(' ', '_', $student['s_lname']);
    $path = "../uploads/" . str_replace(' ', '_', $student['filier_name']) . "/" . 
            $student['code_class'] . "/" . $folderName . "/" . $docType;
    error_log("Checking path: " . $path);
    return $path;
}

// Debug: Afficher les informations de l'étudiant
error_log("Student info:");
error_log(print_r($student, true));

// Récupérer les chemins des documents en utilisant le FileController
$documents = [
    'bac_img' => $fileController->findDocument($fileController->getDocumentPath($student, 'bac_img')),
    'birth_img' => $fileController->findDocument($fileController->getDocumentPath($student, 'birth_img')),
    'id_card_img' => $fileController->findDocument($fileController->getDocumentPath($student, 'id_card_img'))
];

// Debug: Afficher les chemins des documents
error_log("Document paths:");
foreach ($documents as $type => $path) {
    error_log("$type path: " . ($path ?: "Not found"));
    if ($path) {
        error_log("File exists check: " . (file_exists($path) ? "Yes" : "No"));
    }
}

// Fonction pour obtenir l'icône appropriée selon le type de document
function getDocumentIcon($path) {
    if (!$path) return 'bi-file-earmark-x';
    $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
    return $extension === 'pdf' ? 'bi-file-earmark-pdf' : 'bi-file-earmark-image';
}

// Ajouter après session_start()
if (isset($_SESSION['folder_message'])) {
    $message = $_SESSION['folder_message'];
    $messageType = strpos($message, 'succès') !== false ? 'success' : 'danger';
    echo '<div class="alert alert-' . $messageType . ' alert-dismissible fade show" role="alert">
            ' . htmlspecialchars($message) . '
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>';
    unset($_SESSION['folder_message']);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Étudiant</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4f46e5;
            --primary-light: #818cf8;
            --success-color: #059669;
            --danger-color: #dc2626;
            --background-color: #f1f5f9;
            --card-background: #ffffff;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --border-color: #e2e8f0;
        }

        body {
            background-color: var(--background-color);
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
        }

        .main-container {
            padding: 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .info-section {
            background: var(--card-background);
            border-radius: 16px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .info-section:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        .section-title {
            color: var(--text-primary);
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 2rem;
            padding-bottom: 0.75rem;
            border-bottom: 2px solid var(--primary-light);
            display: inline-block;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5rem;
            margin-top: 1.5rem;
        }

        .info-item {
            background: linear-gradient(145deg, #ffffff, #f3f4f6);
            border-radius: 16px;
            padding: 1.5rem;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.8);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }

        .info-item:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }

        .info-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: linear-gradient(to bottom, var(--primary-color), var(--primary-light));
            border-radius: 4px;
        }

        .info-label {
            color: var(--text-secondary);
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            margin-bottom: 0.5rem;
            display: block;
        }

        .info-value {
            color: var(--text-primary);
            font-size: 1.2rem;
            font-weight: 600;
            display: block;
        }

        .documents-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 2rem;
            margin-top: 2rem;
        }

        .document-card {
            background: var(--card-background);
            border-radius: 12px;
            padding: 1.5rem;
            text-align: center;
            border: 2px solid var(--border-color);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .document-card:hover {
            transform: translateY(-5px);
            border-color: var(--primary-light);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .document-preview {
            width: 100%;
            height: 200px;
            border: 1px solid #e5e7eb;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
            cursor: pointer;
            background-color: #f8fafc;
            flex-direction: column;
        }
        
        .document-preview i {
            font-size: 3rem;
            color: #6b7280;
            margin-bottom: 1rem;
        }
        
        .document-preview.pdf i {
            color: #ef4444;
        }
        
        .document-name {
            font-size: 0.875rem;
            color: #4b5563;
            margin-top: 0.5rem;
        }

        .document-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--text-primary);
            margin: 1rem 0;
        }

        .status-badge {
            display: inline-block;
            padding: 0.5rem 1rem;
            border-radius: 9999px;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .status-badge.uploaded {
            background-color: #dcfce7;
            color: #059669;
        }

        .status-badge.not-uploaded {
            background-color: #fee2e2;
            color: #dc2626;
        }

        .modal-content {
            border: none;
            border-radius: 16px;
            overflow: hidden;
        }

        .modal-header {
            background: linear-gradient(to right, var(--primary-color), var(--primary-light));
            color: white;
            padding: 1.5rem;
            border: none;
        }

        .modal-title {
            font-weight: 600;
            font-size: 1.25rem;
        }

        .modal-body {
            padding: 2rem;
        }

        .modal-body.pdf-preview {
            height: 80vh;
            padding: 0;
        }

        .pdf-embed {
            width: 100%;
            height: 100%;
            border: none;
        }

        .modal-img {
            max-width: 100%;
            height: auto;
        }

        .btn-close {
            background-color: white;
            opacity: 0.8;
            transition: opacity 0.3s ease;
        }

        .btn-close:hover {
            opacity: 1;
        }

        .back-button {
            margin-bottom: 2rem;
        }

        .back-button .btn {
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
            background: linear-gradient(to right, var(--primary-color), var(--primary-light));
            border: none;
            border-radius: 8px;
        }

        .back-button .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
        }

        @media (max-width: 768px) {
            .info-grid, .documents-grid {
                grid-template-columns: 1fr;
            }
            
            .main-container {
                padding: 1rem;
            }
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="main-container">
        <!-- Bouton retour -->
        <div class="back-button">
            <a href="javascript:history.back()" class="btn btn-primary">
                <i class="bi bi-arrow-left"></i> Retour
            </a>
        </div>

        <!-- Informations de l'étudiant -->
        <div class="info-section">
            <h3 class="section-title">Informations personnelles</h3>
            <div class="info-grid">
                <div class="info-item">
                    <span class="info-label">CIN</span>
                    <span class="info-value"><?php echo htmlspecialchars($student['cin']); ?></span>
                    <i class="bi bi-person-vcard info-icon"></i>
                </div>
                <div class="info-item">
                    <span class="info-label">Nom</span>
                    <span class="info-value"><?php echo htmlspecialchars($student['s_lname']); ?></span>
                    <i class="bi bi-person info-icon"></i>
                </div>
                <div class="info-item">
                    <span class="info-label">Prénom</span>
                    <span class="info-value"><?php echo htmlspecialchars($student['s_fname']); ?></span>
                    <i class="bi bi-person-badge info-icon"></i>
                </div>
                <div class="info-item">
                    <span class="info-label">Classe</span>
                    <span class="info-value"><?php echo htmlspecialchars($student['code_class']); ?></span>
                    <i class="bi bi-mortarboard info-icon"></i>
                </div>
                <div class="info-item" style="grid-column: 1 / -1">
                    <span class="info-label">Filière</span>
                    <span class="info-value"><?php echo htmlspecialchars($student['filier_name']); ?></span>
                    <i class="bi bi-book info-icon"></i>
                </div>
            </div>
        </div>

        <!-- Documents -->
        <div class="info-section">
            <h3 class="section-title">Documents téléchargés</h3>
            <div class="documents-grid">
                <?php foreach ($documents as $type => $path): ?>
                    <div class="document-card">
                        <div class="document-preview <?php echo $path && pathinfo($path, PATHINFO_EXTENSION) === 'pdf' ? 'pdf' : ''; ?>"
                             <?php if ($path && file_exists($path)): ?>
                             data-bs-toggle="modal" 
                             data-bs-target="#documentModal"
                             data-document-path="<?php echo htmlspecialchars($path); ?>"
                             data-document-type="<?php echo pathinfo($path, PATHINFO_EXTENSION) === 'pdf' ? 'pdf' : 'image'; ?>"
                             <?php endif; ?>>
                            <?php if ($path && file_exists($path)): ?>
                                <?php
                                $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
                                if (in_array($ext, ['jpg', 'jpeg', 'png'])):
                                ?>
                                    <img src="<?php echo htmlspecialchars($path); ?>" alt="Aperçu du document" style="max-height: 150px; width: auto;">
                                <?php else: ?>
                                    <i class="bi <?php echo $fileController->getDocumentIcon($path); ?>"></i>
                                <?php endif; ?>
                            <?php else: ?>
                                <i class="bi bi-file-earmark-x"></i>
                                <span class="text-muted">Document non disponible</span>
                            <?php endif; ?>
                        </div>
                        <h3 class="document-title">
                            <?php
                            switch($type) {
                                case 'bac_img': echo 'Baccalauréat'; break;
                                case 'birth_img': echo 'Acte de naissance'; break;
                                case 'id_card_img': echo 'Carte d\'identité'; break;
                            }
                            ?>
                        </h3>
                        <span class="status-badge <?php echo ($path && file_exists($path)) ? 'uploaded' : 'not-uploaded'; ?>">
                            <?php echo ($path && file_exists($path)) ? 'Téléchargé' : 'Non téléchargé'; ?>
                        </span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Modal pour la prévisualisation -->
    <div class="modal fade" id="documentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Aperçu du document</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Le contenu sera inséré dynamiquement -->
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.querySelectorAll('.document-preview[data-document-path]').forEach(preview => {
        preview.addEventListener('click', function() {
            const path = this.getAttribute('data-document-path');
            const type = this.getAttribute('data-document-type');
            const modalBody = document.querySelector('#documentModal .modal-body');
            
            if (!path) return;

            modalBody.innerHTML = '';
            if (type === 'pdf') {
                modalBody.classList.add('pdf-preview');
                const embed = document.createElement('embed');
                embed.setAttribute('src', path);
                embed.classList.add('pdf-embed');
                modalBody.appendChild(embed);
            } else {
                modalBody.classList.remove('pdf-preview');
                const img = document.createElement('img');
                img.setAttribute('src', path);
                img.classList.add('modal-img');
                modalBody.appendChild(img);
            }
        });
    });
    </script>
</body>
</html>
