<?php

session_start();


if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: ../View/Login.php');
    exit;
}

include_once '../Controller/AdminController.php';


$adminController = new AdminController();
$filieres = $adminController->displayFilieres();
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Filières</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4e73df;
            --primary-dark: #224abe;
            --secondary-color: #858796;
            --success-color: #1cc88a;
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
            margin-top: 2rem;
            box-shadow: 0 4px 15px rgba(78, 115, 223, 0.15);
        }

        .page-title {
            font-size: 1.8rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .page-subtitle {
            opacity: 0.9;
            font-size: 1rem;
        }

        .filiere-card {
            background: white;
            border-radius: 12px;
            border: none;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            height: 100%;
        }

        .filiere-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
        }

        .filiere-icon {
            font-size: 2.5rem;
            color: var(--primary-color);
            margin-bottom: 1rem;
        }

        .filiere-title {
            color: #2d3748;
            font-size: 1.4rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .filiere-description {
            color: var(--secondary-color);
            margin-bottom: 1.5rem;
        }

        .view-classes-btn {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            border: none;
            padding: 0.8rem 1.5rem;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .view-classes-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(78, 115, 223, 0.2);
        }

        /* Style pour le modal */
        .modal-content {
            border-radius: 15px;
            border: none;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .modal-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            color: white;
            border-radius: 15px 15px 0 0;
            padding: 1.5rem;
        }

        .modal-title {
            font-weight: 600;
        }

        .modal-body {
            padding: 2rem;
        }

        .class-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 1rem;
            padding: 1rem 0;
        }

        .class-item {
            background: #f8f9fc;
            padding: 1rem;
            border-radius: 10px;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .class-item:hover {
            background: white;
            transform: translateY(-3px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .class-icon {
            font-size: 2rem;
            color: var(--primary-color);
            margin-bottom: 0.5rem;
        }

        .class-name {
            font-weight: 500;
            color: #2d3748;
            margin: 0;
        }

        @media (max-width: 768px) {
            body {
                padding: 1rem;
            }

            .container {
                margin-top: 1.5rem;
            }

            .page-header {
                margin-top: 1.5rem;
                padding: 1.5rem;
            }

            .page-title {
                font-size: 1.5rem;
            }

            .class-grid {
                grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            }
        }
    </style>
    <link rel="stylesheet" href="../assets/css/darkmood.css">
    <script src="../assets/js/darkmood.js"></script>
</head>

<body>
    <?php include 'header.php'; ?>

    <div class="container">
        <div class="page-header">
            <h1 class="page-title">Gestion des Filières</h1>
            <p class="page-subtitle">Consultez et gérez les classes par filière</p>
        </div>

        <div class="row g-4">
            <?php if (!empty($filieres)): ?>
                <?php foreach ($filieres as $filiere): ?>
                    <div class="col-md-4">
                        <div class="filiere-card">
                            <div class="card-body p-4">
                                <div class="filiere-icon">
                                    <i class="bi bi-mortarboard"></i>
                                </div>
                                <h5 class="filiere-title"><?php echo htmlspecialchars($filiere); ?></h5>
                                <p class="filiere-description">Accédez aux classes et aux informations des étudiants</p>
                                <button class="btn btn-primary view-classes-btn" data-filiere="<?php echo htmlspecialchars($filiere); ?>" data-bs-toggle="modal" data-bs-target="#classesModal">
                                    <i class="bi bi-eye me-2"></i>Voir les classes
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="alert alert-info">
                        Aucune filière trouvée dans la base de données.
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal pour afficher les classes -->
    <div class="modal fade" id="classesModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-collection me-2"></i>
                        Classes de <span id="filiereName"></span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="classesList" class="class-grid">
                        <!-- Les classes seront chargées ici dynamiquement -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.view-classes-btn').on('click', function() {
                const filiere = $(this).data('filiere');
                $('#filiereName').text(filiere);

                $.ajax({
                    url: 'fetch_classes.php',
                    type: 'GET',
                    data: {
                        filiere: filiere
                    },
                    success: function(response) {
                        // Transformer la réponse en HTML pour la grille de classes
                        const classes = JSON.parse(response);
                        let classesHtml = '';

                        classes.forEach(function(className) {
                            classesHtml += `
                                <div class="class-item" onclick="window.location.href='student_details.php?class=${encodeURIComponent(className)}&filiere=${encodeURIComponent(filiere)}'">
                                    <div class="class-icon">
                                        <i class="bi bi-people"></i>
                                    </div>
                                    <h6 class="class-name">${className}</h6>
                                </div>
                            `;
                        });

                        $('#classesList').html(classesHtml);
                    },
                    error: function() {
                        $('#classesList').html('<div class="alert alert-danger">Erreur lors du chargement des classes.</div>');
                    }
                });
            });
        });
    </script>
</body>

</html>