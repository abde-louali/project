<?php
include_once '../Model/conx.php';

class StudentModel
{
    private $db;
    private $maxRetries = 3;
    private $retryDelay = 1; // seconds

    public function __construct()
    {
        $this->initializeConnection();
    }

    private function initializeConnection()
    {
        try {
            $database = new Database();
            $this->db = $database->getConnection();
            $this->configureConnection();
        } catch (PDOException $e) {
            error_log("Initial database connection error: " . $e->getMessage());
            throw new Exception("Erreur de connexion à la base de données");
        }
    }

    private function configureConnection()
    {
        if ($this->db) {
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            $this->db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

            // Configure session variables for larger data
            $this->executeSafely("SET SESSION wait_timeout=300");
            $this->executeSafely("SET SESSION interactive_timeout=300");
            $this->executeSafely("SET SESSION max_allowed_packet=16777216"); // 16MB
        }
    }

    private function reconnect()
    {
        $this->db = null; // Close existing connection
        $this->initializeConnection();
    }

    private function executeSafely($query, $params = [], $retryCount = 0)
    {
        try {
            if (!$this->db) {
                $this->initializeConnection();
            }

            if ($params) {
                $stmt = $this->db->prepare($query);
                $result = $stmt->execute($params);
                return $stmt;
            } else {
                return $this->db->exec($query);
            }
        } catch (PDOException $e) {
            if (
                $retryCount < $this->maxRetries &&
                (strpos($e->getMessage(), 'server has gone away') !== false ||
                    strpos($e->getMessage(), 'Lost connection') !== false)
            ) {

                error_log("Connection lost, attempting reconnect. Attempt " . ($retryCount + 1));
                sleep($this->retryDelay);
                $this->reconnect();
                return $this->executeSafely($query, $params, $retryCount + 1);
            }
            throw $e;
        }
    }

    public function saveOrUpdateStudentFiles($cin, $code_class, $filier_name, $s_fname, $s_lname, $bac_img, $id_card_img, $birth_img)
    {
        try {
            $this->db->beginTransaction();

            // Check if student exists
            $exists = $this->checkStudentExists($cin, $code_class, $filier_name);

            if ($exists) {
                $query = "UPDATE student 
                         SET bac_img = :bac_img, 
                             id_card_img = :id_card_img, 
                             birth_img = :birth_img
                         WHERE cin = :cin 
                         AND code_class = :code_class 
                         AND filier_name = :filier_name";
                $params = [
                    ':cin' => $cin,
                    ':code_class' => $code_class,
                    ':filier_name' => $filier_name,
                    ':bac_img' => $bac_img,
                    ':id_card_img' => $id_card_img,
                    ':birth_img' => $birth_img
                ];
            } else {
                $query = "INSERT INTO student (cin, s_fname, s_lname, id_card_img, bac_img, birth_img, 
                         code_class, filier_name) 
                         VALUES (:cin, :s_fname, :s_lname, :id_card_img, :bac_img, :birth_img, 
                         :code_class, :filier_name)";
                $params = [
                    ':cin' => $cin,
                    ':s_fname' => $s_fname,
                    ':s_lname' => $s_lname,
                    ':code_class' => $code_class,
                    ':filier_name' => $filier_name,
                    ':bac_img' => $bac_img,
                    ':id_card_img' => $id_card_img,
                    ':birth_img' => $birth_img
                ];
            }

            $stmt = $this->executeSafely($query, $params);
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            if ($this->db && $this->db->inTransaction()) {
                try {
                    $this->db->rollBack();
                } catch (PDOException $rollbackException) {
                    error_log("Rollback failed: " . $rollbackException->getMessage());
                }
            }
            error_log("Error in saveOrUpdateStudentFiles: " . $e->getMessage());
            throw new Exception("Erreur lors de la sauvegarde des fichiers");
        }
    }

    public function checkStudentExists($cin, $code_class, $filier_name)
    {
        $query = "SELECT COUNT(*) FROM student WHERE cin = :cin AND code_class = :code_class AND filier_name = :filier_name";
        $params = [
            ':cin' => $cin,
            ':code_class' => $code_class,
            ':filier_name' => $filier_name
        ];

        $stmt = $this->executeSafely($query, $params);
        return $stmt->fetchColumn() > 0;
    }

    public function getStudentInfo($cin)
    {
        $query = "SELECT * FROM classes WHERE cin = :cin";
        $stmt = $this->executeSafely($query, [':cin' => $cin]);
        return $stmt->fetch();
    }

    public function getStudentDetails($cin, $code_class)
    {
        $query = "SELECT s_fname, s_lname, filier_name FROM classes WHERE cin = :cin AND code_class = :code_class";
        $stmt = $this->executeSafely($query, [
            ':cin' => $cin,
            ':code_class' => $code_class
        ]);
        return $stmt->fetch();
    }
}
