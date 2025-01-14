<?php
// student_profile.php
session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: ../View/Login.php');
    exit;
}

// Include the database connection
include_once '../Model/conx.php';

// Get the selected CIN, class, and filière from the query string
if (isset($_GET['cin']) && isset($_GET['class']) && isset($_GET['filiere'])) {
    $cin = $_GET['cin'];
    $code_class = $_GET['class'];
    $filier_name = $_GET['filiere'];

    // Fetch student details
    $database = new Database();
    $conn = $database->getConnection();

    $query = "
        SELECT * 
        FROM Student 
        WHERE cin = :cin 
          AND code_class = :code_class 
          AND filier_name = :filier_name
    ";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':cin', $cin);
    $stmt->bindParam(':code_class', $code_class);
    $stmt->bindParam(':filier_name', $filier_name);
    $stmt->execute();

    $student = $stmt->fetch(PDO::FETCH_ASSOC);
} else {
    die("Invalid request.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="container mt-4">
        <h2 class="mb-4">Student Profile</h2>
        
        <?php if ($student): ?>
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">CIN: <?php echo htmlspecialchars($student['cin']); ?></h5>
                    <p class="card-text">Class: <?php echo htmlspecialchars($student['code_class']); ?></p>
                    <p class="card-text">Filière: <?php echo htmlspecialchars($student['filier_name']); ?></p>
                    <!-- Display images if needed -->
                    <div class="mt-3">
                        <h6>Uploaded Documents:</h6>
                        <ul>
                            <li>BAC Image: <?php echo $student['bac_img'] ? 'Uploaded' : 'Not Uploaded'; ?></li>
                            <li>Birth Image: <?php echo $student['birth_img'] ? 'Uploaded' : 'Not Uploaded'; ?></li>
                            <li>ID Card Image: <?php echo $student['id_card_img'] ? 'Uploaded' : 'Not Uploaded'; ?></li>
                        </ul>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-danger">
                Student not found.
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>