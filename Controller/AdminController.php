<?php

include_once '../Model/AdminModel.php';

class AdminController {
    private $adminModel;

    public function __construct() {
        $this->adminModel = new AdminModel();
    }

    public function displayFilieres() {
        try {
            // Fetch filières from the model
            $filieres = $this->adminModel->getAllFilieres();

            // Pass the data to the view
            return $filieres; // Return the data instead of including the view here
        } catch (Exception $e) {
            error_log("Error in displayFilieres: " . $e->getMessage());
            header('Location: ../View/error.php?message=error_displaying_filieres');
            exit;
        }
    }
}

