<?php
// student_details.php
session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: ../View/Login.php');
    exit;
}

include_once '../Controller/AdminController.php';
$adminController = new AdminController();

// Récupérer les paramètres de l'URL
$class = isset($_GET['class']) ? $_GET['class'] : '';
$filiere = isset($_GET['filiere']) ? $_GET['filiere'] : '';

// Récupérer les étudiants de la classe
$students = $adminController->getStudentsByClass($class);
$studentCount = count($students);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Students in <?php echo htmlspecialchars($class); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    <link rel="stylesheet" href="../assets/css/darkmood.css">
    <script src="../assets/js/darkmood.js"></script>
    <style>
        :root {
            --primary-color: #4e73df;
            --primary-dark: #224abe;
            --secondary-color: #858796;
            --success-color: #1cc88a;
            --warning-color: #f6c23e;
        }

        body {
            background-color: #f8f9fc;
            min-height: 100vh;
            padding: 2rem;
        }

        .container {
            max-width: 1200px;
            margin-top: 2rem;
        }

        .page-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            color: white;
            padding: 2rem;
            border-radius: 15px;
            margin-bottom: 2rem;
            box-shadow: 0 4px 15px rgba(78, 115, 223, 0.15);
            position: relative;
        }

        .stats-card {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: rgba(255, 255, 255, 0.1);
            padding: 1rem 1.5rem;
            border-radius: 10px;
            backdrop-filter: blur(5px);
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .stats-icon {
            font-size: 2rem;
            color: white;
        }

        .stats-info {
            display: flex;
            flex-direction: column;
            color: white;
        }

        .stats-label {
            font-size: 0.9rem;
            opacity: 0.9;
        }

        .stats-value {
            font-size: 1.5rem;
            font-weight: 600;
        }

        .table {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .table th {
            background: var(--primary-color);
            color: white;
            font-weight: 500;
            border: none;
            padding: 1rem;
        }

        .table td {
            padding: 1rem;
            vertical-align: middle;
        }

        .status-badge {
            padding: 0.4rem 1rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
        }

        .status-verified {
            background: var(--success-color);
            color: white;
        }

        .status-pending {
            background: var(--warning-color);
            color: #000;
        }

        .btn-view {
            padding: 0.5rem 1rem;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .btn-view:hover {
            transform: translateY(-2px);
        }
    </style>
</head>

<body>
    <?php include 'header.php'; ?>

    <div class="container">
        <div class="page-header">
            <h1>les stagiaires de <?php echo htmlspecialchars($class); ?> (<?php echo htmlspecialchars($filiere); ?>)</h1>
            <div class="stats-card">
                <div class="stats-icon">
                    <i class="bi bi-people-fill"></i>
                </div>
                <div class="stats-info">
                    <span class="stats-label">Total Étudiants</span>
                    <span class="stats-value"><?php echo $studentCount; ?></span>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Liste des Stagiaires</h2>
            <div class="btn-group">
                <button id="createFoldersBtn" class="btn btn-primary me-2">
                    <i class="bi bi-folder-plus"></i> Créer les dossiers
                </button>
                <button id="verifyFoldersBtn" class="btn btn-secondary" disabled>
                    <i class="bi bi-check-circle"></i> Vérifier les dossiers
                </button>
            </div>
        </div>

        <div class="modal fade" id="statusModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Statut de l'opération</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div id="statusMessage"></div>
                        <div id="statusDetails" class="mt-3">
                            <div id="successList" class="text-success"></div>
                            <div id="errorList" class="text-danger"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>CIN</th>
                        <th>Nom Complet</th>

                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($students as $student): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($student['cin']); ?></td>
                            <td><?php echo htmlspecialchars($student['s_fname'] . ' ' . $student['s_lname']); ?></td>

                            <td>
                                <a href="student_profile.php?cin=<?php echo urlencode($student['cin']); ?>"
                                    class="btn btn-primary btn-view">
                                    <i class="bi bi-eye me-1"></i>
                                    Voir Profil
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const createFoldersBtn = document.getElementById('createFoldersBtn');
            const verifyFoldersBtn = document.getElementById('verifyFoldersBtn');
            const statusModal = new bootstrap.Modal(document.getElementById('statusModal'));

            createFoldersBtn.addEventListener('click', async function() {
                try {
                    // Désactiver le bouton pendant le traitement
                    createFoldersBtn.disabled = true;
                    createFoldersBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Création en cours...';

                    // Récupérer la classe et la filière depuis l'URL
                    const urlParams = new URLSearchParams(window.location.search);
                    const class_code = urlParams.get('class');
                    const filiere = urlParams.get('filiere');

                    if (!class_code || !filiere) {
                        throw new Error('Paramètres manquants dans l\'URL');
                    }

                    // Préparer les données
                    const formData = new URLSearchParams();
                    formData.append('class', class_code);
                    formData.append('filiere', filiere);

                    // Envoyer la requête AJAX
                    const response = await fetch('../Model/create_folders.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: formData.toString()
                    });

                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }

                    const data = await response.json();

                    // Mettre à jour le modal avec le statut
                    const statusMessage = document.getElementById('statusMessage');
                    const successList = document.getElementById('successList');
                    const errorList = document.getElementById('errorList');

                    // Réinitialiser les listes
                    successList.innerHTML = '';
                    errorList.innerHTML = '';

                    if (data.status === 'success') {
                        statusMessage.className = 'alert alert-success';
                        statusMessage.textContent = 'Les dossiers ont été créés avec succès';
                        // Activer le bouton de vérification
                        verifyFoldersBtn.disabled = false;
                    } else {
                        statusMessage.className = 'alert alert-danger';
                        statusMessage.textContent = 'Une erreur est survenue lors de la création des dossiers';

                        if (data.error) {
                            errorList.innerHTML = `<div class="text-danger"><i class="bi bi-exclamation-circle"></i> ${data.error}</div>`;
                        }
                    }

                    // Afficher le modal
                    statusModal.show();

                } catch (error) {
                    console.error('Error:', error);
                    const statusMessage = document.getElementById('statusMessage');
                    const errorList = document.getElementById('errorList');

                    statusMessage.className = 'alert alert-danger';
                    statusMessage.textContent = 'Une erreur est survenue lors de la création des dossiers';
                    errorList.innerHTML = `<div class="text-danger"><i class="bi bi-exclamation-circle"></i> ${error.message}</div>`;

                    statusModal.show();
                } finally {
                    // Réactiver le bouton et restaurer son texte
                    createFoldersBtn.disabled = false;
                    createFoldersBtn.innerHTML = '<i class="bi bi-folder-plus"></i> Créer les dossiers';
                }
            });

            verifyFoldersBtn.addEventListener('click', function() {
                const urlParams = new URLSearchParams(window.location.search);
                const class_code = urlParams.get('class');
                const filiere = urlParams.get('filiere');

                window.location.href = `verify_folders.php?class=${encodeURIComponent(class_code)}&filiere=${encodeURIComponent(filiere)}`;
            });
        });
    </script>
</body>

</html>