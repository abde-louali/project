<?php

session_start();


if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: ../View/Login.php');
    exit;
}

include_once '../Controller/AdminController.php';


$adminController = new AdminController();
$filieres = $adminController->displayFilieres(); 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Filières Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .filiere-card {
            transition: transform 0.2s;
            margin-bottom: 20px;
        }
        .filiere-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .class-btn {
            width: 100px; /* Adjust button width as needed */
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="container mt-4">
        <h2 class="mb-4">Filières Management</h2>
        
        <div class="row">
            <?php if (!empty($filieres)): ?>
                <?php foreach ($filieres as $filiere): ?>
                    <div class="col-md-4">
                        <div class="card filiere-card">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($filiere); ?></h5>
                                <p class="card-text">Click to view classes in this filière</p>
                                <!-- Button to trigger the modal -->
                                <button class="btn btn-primary view-classes-btn" data-filiere="<?php echo htmlspecialchars($filiere); ?>" data-bs-toggle="modal" data-bs-target="#classesModal">
                                    View Classes
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="alert alert-info">
                        No filières found in the database.
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="modal fade" id="classesModal" tabindex="-1" aria-labelledby="classesModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="classesModalLabel">Classes in <span id="filiereName"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                  
                    <div id="classesList"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

  
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
 
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
     
        $(document).ready(function () {
            $('.view-classes-btn').on('click', function () {
                const filiere = $(this).data('filiere'); // Get the selected filière
                $('#filiereName').text(filiere); 

                // Fetch classes for the selected filière using AJAX
                $.ajax({
                    url: 'fetch_classes.php', 
                    type: 'GET',
                    data: { filiere: filiere },
                    success: function (response) {
                        $('#classesList').html(response); 
                    },
                    error: function (xhr, status, error) {
                        $('#classesList').html('<p>Error loading classes.</p>'); 
                    }
                });
            });

            // Handle class button clicks (for future navigation)
            $(document).on('click', '.class-btn', function () {
    const selectedClass = $(this).data('class'); 
    const filiere = $('#filiereName').text(); 

    window.location.href = 'student_details.php?class=' + encodeURIComponent(selectedClass) + '&filiere=' + encodeURIComponent(filiere);
});
        });
    </script>
</body>
</html>