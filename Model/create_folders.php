<?php
session_start();
require_once 'conx.php';
header('Content-Type: application/json');

// Vérification de la session
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    echo json_encode([
        'status' => 'error',
        'error' => 'Session expirée ou utilisateur non autorisé'
    ]);
    exit;
}

// Répertoire de base pour créer les dossiers
$baseDirectory = dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'uploads';

// Récupérer les paramètres de la requête
$filiere = isset($_POST['filiere']) ? trim($_POST['filiere']) : '';
$class = isset($_POST['class']) ? trim($_POST['class']) : '';

if (empty($filiere) || empty($class)) {
    echo json_encode([
        'status' => 'error',
        'error' => 'Paramètres filiere et class manquants.'
    ]);
    exit;
}

try {
    // Nettoyer les noms pour les dossiers
    $filiereName = str_replace(' ', '_', strtoupper($filiere));
    $className = str_replace(' ', '_', strtoupper($class));

    // Vérifier et créer les dossiers de la filière et de la classe
    $filierePath = $baseDirectory . DIRECTORY_SEPARATOR . $filiereName;
    $classPath = $filierePath . DIRECTORY_SEPARATOR . $className;

    $foldersCreated = [];
    $filesSaved = [];
    $errors = [];

    // Créer le dossier de base s'il n'existe pas
    if (!is_dir($baseDirectory)) {
        if (!@mkdir($baseDirectory, 0777, true)) {
            throw new Exception("Impossible de créer le dossier uploads");
        }
        chmod($baseDirectory, 0777);
        $foldersCreated[] = 'uploads';
    }

    // Créer le dossier de la filière s'il n'existe pas
    if (!is_dir($filierePath)) {
        if (!@mkdir($filierePath, 0777, true)) {
            throw new Exception("Impossible de créer le dossier de la filière");
        }
        chmod($filierePath, 0777);
        $foldersCreated[] = $filiereName;
    }

    // Créer le dossier de la classe s'il n'existe pas
    if (!is_dir($classPath)) {
        if (!@mkdir($classPath, 0777, true)) {
            throw new Exception("Impossible de créer le dossier de la classe");
        }
        chmod($classPath, 0777);
        $foldersCreated[] = $className;
    }

    // Connexion à la base de données
    $database = new Database();
    $conn = $database->getConnection();

    // Récupérer les informations des étudiants pour cette filière et classe
    $query = "SELECT s.s_fname, s.s_lname, s.cin, s.bac_img, s.birth_img, s.id_card_img 
             FROM student s 
             WHERE s.filier_name = :filiere AND s.code_class = :class";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':filiere', $filiere);
    $stmt->bindParam(':class', $class);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $studentName = $row['cin'] . '_' . str_replace(' ', '_', $row['s_lname']);
        $studentPath = $classPath . DIRECTORY_SEPARATOR . $studentName;

        // Créer le dossier de l'étudiant s'il n'existe pas
        if (!is_dir($studentPath)) {
            if (!@mkdir($studentPath, 0777, true)) {
                $errors[] = "Impossible de créer le dossier pour " . $row['s_fname'] . ' ' . $row['s_lname'];
                continue;
            }
            chmod($studentPath, 0777);
            $foldersCreated[] = $studentName;
        }

        // Traiter les documents
        $documents = [
            'bac_img' => $row['bac_img'],
            'birth_img' => $row['birth_img'],
            'id_card_img' => $row['id_card_img']
        ];

        foreach ($documents as $type => $content) {
            if (!empty($content)) {
                // Détecter le type de fichier
                $extension = detectFileType($content);
                if ($extension) {
                    $filePath = $studentPath . DIRECTORY_SEPARATOR . $type . '.' . $extension;
                    
                    // Sauvegarder le fichier
                    if (@file_put_contents($filePath, $content) !== false) {
                        chmod($filePath, 0666);
                        $filesSaved[] = "$type.$extension pour " . $row['s_fname'] . ' ' . $row['s_lname'];
                    } else {
                        $errors[] = "Impossible de sauvegarder le fichier $type pour " . $row['s_fname'] . ' ' . $row['s_lname'];
                    }
                } else {
                    // Si le type de fichier n'est pas détecté, créer un fichier vide .pdf
                    $filePath = $studentPath . DIRECTORY_SEPARATOR . $type . '.pdf';
                    if (!file_exists($filePath)) {
                        if (@file_put_contents($filePath, '') !== false) {
                            chmod($filePath, 0666);
                            $filesSaved[] = "$type.pdf (vide) pour " . $row['s_fname'] . ' ' . $row['s_lname'];
                        } else {
                            $errors[] = "Impossible de créer le fichier vide $type pour " . $row['s_fname'] . ' ' . $row['s_lname'];
                        }
                    }
                }
            } else {
                // Si pas de contenu, créer un fichier vide .pdf
                $filePath = $studentPath . DIRECTORY_SEPARATOR . $type . '.pdf';
                if (!file_exists($filePath)) {
                    if (@file_put_contents($filePath, '') !== false) {
                        chmod($filePath, 0666);
                        $filesSaved[] = "$type.pdf (vide) pour " . $row['s_fname'] . ' ' . $row['s_lname'];
                    } else {
                        $errors[] = "Impossible de créer le fichier vide $type pour " . $row['s_fname'] . ' ' . $row['s_lname'];
                    }
                }
            }
        }
    }

    // Retourner la réponse en JSON
    echo json_encode([
        'status' => empty($errors) ? 'success' : 'error',
        'folders' => $foldersCreated,
        'files' => $filesSaved,
        'errors' => $errors
    ]);

} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'error' => $e->getMessage(),
        'errors' => [$e->getMessage()]
    ]);
}

/**
 * Détecte le type de fichier à partir de son contenu
 * @param string $content Le contenu du fichier
 * @return string|null L'extension du fichier ou null si non détecté
 */
function detectFileType($content) {
    // Vérifier les signatures de fichiers
    $signatures = [
        // PDF
        ['%PDF', 'pdf'],
        // JPEG
        ["\xFF\xD8\xFF", 'jpg'],
        // PNG
        ["\x89PNG\r\n\x1a\n", 'png']
    ];

    foreach ($signatures as $sig) {
        if (strpos($content, $sig[0]) === 0) {
            return $sig[1];
        }
    }

    return null;
}
?>