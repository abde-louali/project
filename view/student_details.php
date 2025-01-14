<?php
// student_details.php
session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: ../View/Login.php');
    exit;
}

// Include the database connection
include_once '../Model/conx.php';

// Get the selected class and filière from the query string
if (isset($_GET['class']) && isset($_GET['filiere'])) {
    $code_class = $_GET['class'];
    $filier_name = $_GET['filiere'];

    // Fetch students who have uploaded bac_img, birth_img, and id_card_img
    $database = new Database();
    $conn = $database->getConnection();

    $query = "
        SELECT s.cin 
        FROM Student s
        WHERE s.code_class = :code_class 
          AND s.filier_name = :filier_name
          AND s.bac_img IS NOT NULL
          AND s.birth_img IS NOT NULL
          AND s.id_card_img IS NOT NULL
    ";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':code_class', $code_class);
    $stmt->bindParam(':filier_name', $filier_name);
    $stmt->execute();

    $students = $stmt->fetchAll(PDO::FETCH_COLUMN);
} else {
    die("Invalid request.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="container mt-4">
        <h2 class="mb-4">Students in <?php echo htmlspecialchars($code_class); ?> (<?php echo htmlspecialchars($filier_name); ?>)</h2>
        
        <?php if (!empty($students)): ?>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>CIN</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($students as $cin): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($cin); ?></td>
                            <td>
                                <a href="student_profile.php?cin=<?php echo urlencode($cin); ?>&class=<?php echo urlencode($code_class); ?>&filiere=<?php echo urlencode($filier_name); ?>" class="btn btn-primary">
                                    View Profile
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="alert alert-info">
                No students found for this class who have uploaded all required documents.
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>