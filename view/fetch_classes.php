<?php
// fetch_classes.php
session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    die("Unauthorized access.");
}

// Include the database connection
include_once '../Model/conx.php';

// Get the selected filière from the query string
if (isset($_GET['filiere'])) {
    $filiere = $_GET['filiere'];

    // Fetch distinct classes for the selected filière
    $database = new Database();
    $conn = $database->getConnection();

    $query = "SELECT DISTINCT code_class FROM classes WHERE filier_name = :filiere";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':filiere', $filiere);
    $stmt->execute();

    $classes = $stmt->fetchAll(PDO::FETCH_COLUMN);

    // Return the classes as buttons
    if (!empty($classes)) {
        foreach ($classes as $class) {
            echo '<button class="btn btn-outline-primary m-2 class-btn" data-class="' . htmlspecialchars($class) . '">' . htmlspecialchars($class) . '</button>';
        }
    } else {
        echo '<p>No classes found for this filière.</p>';
    }
} else {
    echo '<p>Invalid request.</p>';
}
?>