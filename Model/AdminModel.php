<?php

include_once '../Model/conx.php';

class AdminModel {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    // Get all unique filières
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
}

