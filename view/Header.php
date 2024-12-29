<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Admin Header</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        body {
            background-color: #f4f7fc; /* Light background color for the page */
        }
        .navbar {
            background-color: #4e73df; /* Primary blue color for the navbar */
        }
        .navbar-brand, .nav-link {
            color: white !important; /* White text for the links */
        }
        .navbar-toggler-icon {
            background-color: white;
        }
        .navbar-nav .nav-item .nav-link:hover {
            background-color: #2e59d9; /* Darker blue for hover effect */
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="./AdminP.php" style="font-style: italic;">Admin Dashboard</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="./Admin.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Add Class</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="./classes.php">Classes</a>
                    </li>
                  
                </ul>
            </div>
        </div>
    </nav>

   
    <div class="container-fluid mt-5">
      
    </div>

</body>
</html>
