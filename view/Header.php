<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>En-tête Administrateur</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        body {
            background-color: #f4f7fc; /* Couleur de fond claire pour la page */
        }
        .navbar {
            background-color: #4e73df; /* Couleur bleue principale pour la barre de navigation */
        }
        .navbar-brand, .nav-link {
            color: white !important; /* Texte blanc pour les liens */
        }
        .navbar-toggler-icon {
            background-color: white;
        }
        .navbar-nav .nav-item .nav-link:hover {
            background-color: #2e59d9; /* Bleu plus foncé pour l'effet hover */
            border-radius: 5px;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <!-- Logo agrandi dans la barre de navigation -->
        <a class="navbar-brand" href="./AdminP.php">
            <img src="../assets/img/ofppt_logo.png" alt="Logo OFPPT" width="50" height="50" class="d-inline-block align-text-top"> Admin Dashboard
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="./Admin.php">Accueil</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="./ajouterCla.php">Ajouter une Classe</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="./classes.php">Classes</a>
                </li>
            </ul>
        </div>
    </div>
</nav>


    <div class="container-fluid mt-5">
        <!-- Contenu principal -->
    </div>

</body>
</html>
