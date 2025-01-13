<?php
// index.php or classes.php (your main entry point)

require_once 'Controller/AdminController.php';

// Assuming you already have your database connection as $db
$controller = new AdminController($db);
$controller->displayFilieres();
?>