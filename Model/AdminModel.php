<?php
include_once '../Model/conx.php';

class AdminModel {
    private $db;

    public function __construct() {
        // Ici, on utilise getConnection pour obtenir une instance de PDO
        $this->db = (new Database())->getConnection();  // Obtenez la connexion PDO via getConnection
    }
    public function getAllFilieres() {
        try {
            $query = "SELECT DISTINCT filier_name FROM classes ORDER BY filier_name ASC";
            $stmt = $this->db->prepare($query);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_COLUMN);
        } catch (PDOException $e) {
            error_log("Error getting filières: " . $e->getMessage());
            return [];
        }
    }
    // Récupérer le mot de passe actuel de l'utilisateur
    public function getPassword($username) {
        $query = "SELECT PASSWORD FROM admin WHERE username = :username";
        $stmt = $this->db->prepare($query);  // La méthode prepare() fonctionne maintenant car $this->db est un objet PDO
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Mettre à jour le mot de passe de l'utilisateur
    public function updatePassword($username, $newPassword) {
        $query = "UPDATE admin SET PASSWORD = :newPassword WHERE username = :username";
        $stmt = $this->db->prepare($query);  // Toujours un objet PDO ici
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':newPassword', $newPassword);  // Sécuriser avec hash
        return $stmt->execute();
    }

    public function getAdminInfo($username) {
        try {
            $query = "SELECT username, first_name, last_name, cin FROM admin WHERE username = :username";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':username', $username);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting admin info: " . $e->getMessage());
            return false;
        }
    }

    public function checkUsernameExists($username, $currentUsername) {
        try {
            $query = "SELECT COUNT(*) FROM admin WHERE username = :username AND username != :currentUsername";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':currentUsername', $currentUsername);
            $stmt->execute();
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            error_log("Error checking username existence: " . $e->getMessage());
            return false;
        }
    }

    public function checkCinExists($cin, $currentUsername) {
        try {
            $query = "SELECT COUNT(*) FROM admin WHERE cin = :cin AND username != :currentUsername";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':cin', $cin);
            $stmt->bindParam(':currentUsername', $currentUsername);
            $stmt->execute();
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            error_log("Error checking CIN existence: " . $e->getMessage());
            return false;
        }
    }

    public function updateAdminInfo($username, $data) {
        try {
            $this->db->beginTransaction();

            // Vérifier si le nouveau nom d'utilisateur existe déjà
            if ($this->checkUsernameExists($data['username'], $username)) {
                $this->db->rollBack();
                return false;
            }

            // Vérifier si le CIN existe déjà
            if ($this->checkCinExists($data['cin'], $username)) {
                $this->db->rollBack();
                return false;
            }

            // Mettre à jour les informations de l'administrateur
            $query = "UPDATE admin SET 
                      username = :new_username,
                      cin = :cin,
                      first_name = :first_name,
                      last_name = :last_name
                      WHERE username = :current_username";
                      
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':new_username', $data['username']);
            $stmt->bindParam(':cin', $data['cin']);
            $stmt->bindParam(':first_name', $data['first_name']);
            $stmt->bindParam(':last_name', $data['last_name']);
            $stmt->bindParam(':current_username', $username);
            
            $result = $stmt->execute();
            
            if ($result) {
                $this->db->commit();
                error_log("Admin info updated successfully for user: " . $username);
                return true;
            } else {
                $this->db->rollBack();
                error_log("Failed to update admin info for user: " . $username);
                return false;
            }
        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log("Error updating admin info: " . $e->getMessage());
            return false;
        }
    }

    public function getStudentsByClass($class) {
        try {
            $query = "SELECT 
                        s.cin,
                        s.s_fname,
                        s.s_lname,
                        CASE 
                            WHEN s.bac_img IS NOT NULL 
                            AND s.birth_img IS NOT NULL 
                            AND s.id_card_img IS NOT NULL 
                            THEN 1 
                            ELSE 0 
                        END as verified
                    FROM student s
                    WHERE s.code_class = :class
                    ORDER BY s.s_lname, s.s_fname";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':class', $class);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting students by class: " . $e->getMessage());
            return [];
        }
    }

    public function getStudentDocuments($cin) {
        try {
            error_log("Fetching documents for CIN: " . $cin);
            
            $query = "SELECT bac_img, birth_img, id_card_img FROM student WHERE cin = :cin";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':cin', $cin);
            $stmt->execute();
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$result) {
                error_log("No documents found in database for CIN: " . $cin);
                return null;
            }

            // Log des données brutes pour debug
            error_log("Raw document data length: bac_img=" . strlen($result['bac_img'] ?? '') . 
                     ", birth_img=" . strlen($result['birth_img'] ?? '') . 
                     ", id_card_img=" . strlen($result['id_card_img'] ?? ''));

            // Vérifier le format des données pour chaque document
            foreach ($result as $type => $content) {
                if (!empty($content)) {
                    $sample = substr($content, 0, 50); // Prendre les 50 premiers caractères pour analyse
                    error_log("$type sample: " . bin2hex($sample));
                }
            }

            // Filtrer les documents vides
            $documents = [];
            foreach ($result as $type => $content) {
                if (!empty($content)) {
                    // Vérifier si le contenu commence par les marqueurs PDF
                    $isPdf = (substr($content, 0, 4) === '%PDF');
                    error_log("$type is PDF: " . ($isPdf ? 'yes' : 'no'));
                    
                    // Vérifier si c'est une image JPEG
                    $isJpeg = (substr($content, 0, 2) === "\xFF\xD8");
                    error_log("$type is JPEG: " . ($isJpeg ? 'yes' : 'no'));
                    
                    // Vérifier si c'est une image PNG
                    $isPng = (substr($content, 0, 8) === "\x89PNG\r\n\x1a\n");
                    error_log("$type is PNG: " . ($isPng ? 'yes' : 'no'));

                    $documents[$type] = $content;
                }
            }
            
            return !empty($documents) ? $documents : null;
            
        } catch (PDOException $e) {
            error_log("Database error while getting student documents: " . $e->getMessage());
            return null;
        }
    }

    public function getStudentByCin($cin) {
        try {
            $query = "SELECT 
                        s.cin,
                        s.s_fname,
                        s.s_lname,
                        s.code_class,
                        s.filier_name,
                        s.bac_img,
                        s.birth_img,
                        s.id_card_img
                    FROM student s
                    WHERE s.cin = :cin";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':cin', $cin);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting student by CIN: " . $e->getMessage());
            return false;
        }
    }

    public function uploadStudentDocument($cin, $documentType, $file) {
        try {
            // Récupérer les informations de l'étudiant
            $student = $this->getStudentByCin($cin);
            if (!$student) {
                throw new Exception("Étudiant non trouvé");
            }

            // Définir les types de documents autorisés
            $allowedTypes = ['bac_img', 'birth_img', 'id_card_img'];
            if (!in_array($documentType, $allowedTypes)) {
                throw new Exception("Type de document non valide");
            }

            // Vérifier le type de fichier
            $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $allowedExtensions = ['pdf', 'jpg', 'jpeg', 'png'];
            if (!in_array($fileExtension, $allowedExtensions)) {
                throw new Exception("Type de fichier non autorisé. Extensions autorisées : " . implode(', ', $allowedExtensions));
            }

            // Créer le chemin des dossiers
            $baseDir = 'uploads';
            $filiereDir = $baseDir . '/' . str_replace(' ', '_', $student['filier_name']);
            $classDir = $filiereDir . '/' . $student['code_class'];
            $studentDir = $classDir . '/' . $student['cin'] . '_' . str_replace(' ', '_', $student['s_fname']) . '_' . str_replace(' ', '_', $student['s_lname']);

            // Créer les dossiers s'ils n'existent pas
            foreach ([$baseDir, $filiereDir, $classDir, $studentDir] as $dir) {
                if (!file_exists($dir) && !mkdir($dir, 0777, true)) {
                    throw new Exception("Impossible de créer le dossier : " . $dir);
                }
            }

            // Construire le nom du fichier
            $fileName = $documentType . '.' . $fileExtension;
            $filePath = $studentDir . '/' . $fileName;

            // Supprimer l'ancien fichier s'il existe
            if (file_exists($filePath)) {
                unlink($filePath);
            }

            // Déplacer le nouveau fichier
            if (!move_uploaded_file($file['tmp_name'], $filePath)) {
                throw new Exception("Erreur lors du téléchargement du fichier");
            }

            // Mettre à jour le chemin dans la base de données
            $query = "UPDATE student SET $documentType = :path WHERE cin = :cin";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':path', $filePath);
            $stmt->bindParam(':cin', $cin);
            
            if (!$stmt->execute()) {
                // Si la mise à jour échoue, supprimer le fichier uploadé
                unlink($filePath);
                throw new Exception("Erreur lors de la mise à jour de la base de données");
            }

            return [
                'success' => true,
                'message' => 'Document téléchargé avec succès',
                'path' => $filePath
            ];

        } catch (Exception $e) {
            error_log("Error uploading document: " . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function deleteStudentDocument($cin, $documentType) {
        try {
            // Vérifier le type de document
            $allowedTypes = ['bac_img', 'birth_img', 'id_card_img'];
            if (!in_array($documentType, $allowedTypes)) {
                throw new Exception("Type de document non valide");
            }

            // Récupérer le chemin du fichier actuel
            $query = "SELECT $documentType FROM student WHERE cin = :cin";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':cin', $cin);
            $stmt->execute();
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($result && !empty($result[$documentType])) {
                $filePath = $result[$documentType];
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }

            // Mettre à jour la base de données
            $query = "UPDATE student SET $documentType = NULL WHERE cin = :cin";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':cin', $cin);
            
            if (!$stmt->execute()) {
                throw new Exception("Erreur lors de la mise à jour de la base de données");
            }

            return [
                'success' => true,
                'message' => 'Document supprimé avec succès'
            ];

        } catch (Exception $e) {
            error_log("Error deleting document: " . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function createStudentFolders($class, $filiere) {
        try {
            $results = [
                'success' => [],
                'errors' => [],
                'updated' => []
            ];

            // Vérifier les paramètres
            if (empty($class) || empty($filiere)) {
                throw new Exception("La classe et la filière sont requises");
            }

            // Récupérer les étudiants de la classe
            $students = $this->getStudentsByClass($class);
            if (empty($students)) {
                throw new Exception("Aucun étudiant trouvé dans cette classe");
            }

            error_log("Found " . count($students) . " students in class $class");

            // Définir le chemin de base
            $baseDir = dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'uploads';
            error_log("Base directory: $baseDir");

            // Nettoyer les noms pour les dossiers
            $filiereName = str_replace(' ', '_', strtoupper($filiere));
            $className = str_replace(' ', '_', strtoupper($class));

            // Créer les chemins
            $filiereDir = $baseDir . DIRECTORY_SEPARATOR . $filiereName;
            $classDir = $filiereDir . DIRECTORY_SEPARATOR . $className;

            error_log("Creating directory structure:");
            error_log("Filiere directory: $filiereDir");
            error_log("Class directory: $classDir");

            // Créer les dossiers de base avec gestion d'erreurs
            foreach ([$baseDir, $filiereDir, $classDir] as $dir) {
                if (!file_exists($dir)) {
                    error_log("Creating directory: $dir");
                    if (!@mkdir($dir, 0777, true)) {
                        $error = error_get_last();
                        throw new Exception("Erreur lors de la création du dossier $dir: " . $error['message']);
                    }
                    @chmod($dir, 0777);
                    $results['success'][] = "Dossier créé : " . basename($dir);
                } else {
                    $results['updated'][] = "Dossier existant : " . basename($dir);
                }
            }

            // Traiter chaque étudiant
            foreach ($students as $student) {
                try {
                    $studentName = $student['cin'] . '_' . str_replace(' ', '_', $student['s_lname']);
                    $studentDir = $classDir . DIRECTORY_SEPARATOR . $studentName;

                    error_log("Processing student: $studentName");
                    error_log("Student directory: $studentDir");

                    // Créer le dossier étudiant
                    if (!file_exists($studentDir)) {
                        if (!@mkdir($studentDir, 0777, true)) {
                            $error = error_get_last();
                            $results['errors'][] = "Erreur lors de la création du dossier pour {$student['s_fname']} {$student['s_lname']}: " . $error['message'];
                            continue;
                        }
                        @chmod($studentDir, 0777);
                        $results['success'][] = "Dossier créé pour {$student['s_fname']} {$student['s_lname']}";
                    } else {
                        $results['updated'][] = "Dossier existant pour {$student['s_fname']} {$student['s_lname']}";
                    }

                    // Créer les fichiers vides pour les documents
                    $documentTypes = ['bac_img', 'birth_img', 'id_card_img'];
                    foreach ($documentTypes as $type) {
                        $filePath = $studentDir . DIRECTORY_SEPARATOR . $type . '.pdf';
                        if (!file_exists($filePath)) {
                            if (file_put_contents($filePath, '') === false) {
                                $results['errors'][] = "Erreur lors de la création du fichier $type pour {$student['s_fname']} {$student['s_lname']}";
                            } else {
                                chmod($filePath, 0666);
                                $results['success'][] = "Fichier $type créé pour {$student['s_fname']} {$student['s_lname']}";
                            }
                        } else {
                            $results['updated'][] = "Fichier $type existant pour {$student['s_fname']} {$student['s_lname']}";
                        }
                    }
                } catch (Exception $e) {
                    error_log("Error processing student {$student['cin']}: " . $e->getMessage());
                    $results['errors'][] = "Erreur pour {$student['s_fname']} {$student['s_lname']}: " . $e->getMessage();
                }
            }

            return $results;

        } catch (Exception $e) {
            error_log("Error in createStudentFolders: " . $e->getMessage());
            return [
                'success' => [],
                'errors' => [$e->getMessage()],
                'updated' => []
            ];
        }
    }

    private function cleanFileName($name) {
        // Remplacer les caractères spéciaux et les espaces
        $clean = preg_replace('/[^a-zA-Z0-9_-]/', '_', $name);
        $clean = preg_replace('/_+/', '_', $clean); // Éviter les underscores multiples
        $clean = trim($clean, '_'); // Enlever les underscores au début et à la fin
        return $clean;
    }

    private function detectFileExtension($content) {
        // Détecter le type de fichier basé sur les signatures
        if (substr($content, 0, 4) === '%PDF') {
            return 'pdf';
        } elseif (substr($content, 0, 2) === "\xFF\xD8") {
            return 'jpg';
        } elseif (substr($content, 0, 8) === "\x89PNG\r\n\x1a\n") {
            return 'png';
        }
        return null;
    }
}
?>
