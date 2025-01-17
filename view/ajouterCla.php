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

// Récupérer la liste des classes
include_once('../Model/conx.php');
$db = (new Database())->getConnection();
$query = "SELECT * FROM classes ORDER BY filier_name, code_class";
$stmt = $db->prepare($query);
$stmt->execute();
$classes = $stmt->fetchAll(PDO::FETCH_ASSOC);

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

                // Préparer la requête pour insérer ou ignorer les doublons
                $query = "INSERT INTO classes (code_class, filier_name, cin, s_fname, s_lname, age) 
                          VALUES (:code_class, :filier_name, :cin, :s_fname, :s_lname, :age)
                          ON DUPLICATE KEY UPDATE 
                          s_fname = VALUES(s_fname), 
                          s_lname = VALUES(s_lname), 
                          age = VALUES(age)";
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
                        $stmt->bindParam(':age', $row[5], PDO::PARAM_INT);

                        $stmt->execute();
                    }
                }

                $message = "Données importées avec succès !";
            } catch (Exception $e) {
                if ($e->getCode() == 23000) {
                    $message = "Certains enregistrements existent déjà dans la base.";
                } else {
                    $message = "Erreur lors du traitement du fichier Excel : " . $e->getMessage();
                }
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
    <link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4a90e2;
            --secondary-color: #f8f9fa;
            --accent-color: #28a745;
            --danger-color: #dc3545;
            --text-color: #333;
        }

        body {
            background-color: #f5f6f8;
            color: var(--text-color);
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
        }

        .container {
            max-width: 1200px;
            margin: 2rem auto;
        }

        .page-title {
            color: var(--primary-color);
            font-weight: 600;
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid var(--primary-color);
        }

        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.08);
            margin-bottom: 2rem;
            transition: transform 0.2s;
        }

        .card:hover {
            transform: translateY(-2px);
        }

        .card-header {
            background-color: var(--secondary-color);
            border-bottom: 1px solid rgba(0, 0, 0, 0.08);
            padding: 1rem 1.5rem;
        }

        .card-title {
            color: var(--primary-color);
            font-weight: 600;
            margin: 0;
        }

        .card-body {
            padding: 1.5rem;
        }

        .form-control {
            border-radius: 8px;
            border: 1px solid #ddd;
            padding: 0.75rem;
            transition: border-color 0.2s;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(74, 144, 226, 0.25);
        }

        .btn {
            border-radius: 8px;
            padding: 0.5rem 1.5rem;
            font-weight: 500;
            transition: all 0.2s;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border: none;
        }

        .btn-primary:hover {
            background-color: darken(var(--primary-color), 10%);
            transform: translateY(-1px);
        }

        .table {
            border-collapse: separate;
            border-spacing: 0;
            width: 100%;
            margin-bottom: 1rem;
        }

        .table th {
            background-color: var(--secondary-color);
            font-weight: 600;
            padding: 1rem;
            white-space: nowrap;
        }

        .table td {
            padding: 1rem;
            vertical-align: middle;
        }

        .btn-group-sm .btn {
            padding: 0.25rem 0.5rem;
            margin: 0 2px;
        }

        .alert {
            border-radius: 8px;
            padding: 1rem 1.5rem;
            margin-bottom: 1.5rem;
        }

        /* DataTables personnalisation */
        .dataTables_wrapper {
            padding: 1rem;
            border-radius: 12px;
            background: white;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.08);
        }

        .dataTables_filter input {
            border-radius: 8px;
            border: 1px solid #ddd;
            padding: 0.5rem;
        }

        .dt-buttons {
            margin-bottom: 1rem;
        }

        .dt-button {
            background-color: var(--secondary-color) !important;
            border: 1px solid #ddd !important;
            border-radius: 6px !important;
            padding: 0.375rem 1rem !important;
            margin-right: 0.5rem !important;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="page-title">Gestion des Classes</h1>
        
        <?php if (!empty($message)): ?>
            <div class="alert alert-info">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title">Importer la liste des stagiaires</h5>
            </div>
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="excel_file" class="form-label">Fichier Excel à importer</label>
                        <input type="file" class="form-control" name="excel_file" id="excel_file" accept=".xlsx,.xls" required>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-upload me-2"></i>Importer
                    </button>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Liste des Classes</h5>
            </div>
            <div class="card-body">
                <table id="classeTable" class="table table-striped">
                    <thead>
                        <tr>
                            <th>CIN</th>
                            <th>Nom</th>
                            <th>Prénom</th>
                            <th>Filière</th>
                            <th>Classe</th>
                            <th>Age</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($classes as $classe): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($classe['cin']); ?></td>
                            <td><?php echo htmlspecialchars($classe['s_lname']); ?></td>
                            <td><?php echo htmlspecialchars($classe['s_fname']); ?></td>
                            <td><?php echo htmlspecialchars($classe['filier_name']); ?></td>
                            <td><?php echo htmlspecialchars($classe['code_class']); ?></td>
                            <td><?php echo htmlspecialchars($classe['age']); ?></td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button type="button" 
                                            class="btn btn-danger" 
                                            onclick="deleteStudent('<?php echo $classe['cin']; ?>')"
                                            title="Supprimer">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

    <script>
    $(document).ready(function() {
        $('#classeTable').DataTable({
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'copy',
                    text: 'Copier'
                },
                {
                    extend: 'csv',
                    text: 'CSV'
                },
                {
                    extend: 'excel',
                    text: 'Excel'
                },
                {
                    extend: 'pdf',
                    text: 'PDF'
                },
                {
                    extend: 'print',
                    text: 'Imprimer'
                }
            ],
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/fr-FR.json'
            },
            pageLength: 10,
            order: [[3, 'asc'], [4, 'asc']],
            responsive: true
        });
    });

    function deleteStudent(cin) {
        if (confirm('Êtes-vous sûr de vouloir supprimer cet étudiant ?')) {
            $.ajax({
                url: 'delete_student.php',
                type: 'POST',
                data: { cin: cin },
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert('Erreur lors de la suppression : ' + response.message);
                    }
                },
                error: function() {
                    alert('Erreur lors de la communication avec le serveur');
                }
            });
        }
    }
    </script>
</body>
</html>
