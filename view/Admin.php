<?php
session_start();

if (!isset($_SESSION["username"])) {
    header("location: Login.php");
    exit();
}

include "./Header.php"; 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        body {
            background-color: #f8f9fc; /* Light background for contrast */
        }
        .dashboard-container {
            margin-top: 50px;
        }
        .teacher-info {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }
        .teacher-info h2 {
            font-size: 28px;
            color: #333;
            margin-bottom: 15px;
        }
        .teacher-info p {
            font-size: 18px;
            color: #666;
            margin-bottom: 20px;
        }
        .btn-logout {
            background-color: #e74a3b;
            color: white;
            font-size: 16px;
            border-radius: 5px;
            padding: 10px 20px;
            text-decoration: none;
        }
        .btn-logout:hover {
            background-color: #c0392b;
        }
    </style>
</head>
<body>
    <div class="container dashboard-container">
        <h1 class="display-4">Hello, Admin</h1>
        
        <div class="teacher-info">
            <h2>Your Admin Dashboard</h2>
            <p>Welcome to your personalized admin area. Here, you can manage students, view progress, and perform other tasks.</p>
            <p>Explore the menu above to get started.</p>
        </div>

        <div class="mt-4">
     
            <form action="../Controller/UserController.php?action=logout" method="POST">
                <button type="submit" class="btn-logout">Log out</button>
            </form>
        </div>
    </div>

   
</body>
</html>
