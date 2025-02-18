<?php
if (!isset($_SESSION["username"])) {
    header("location: Login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>En-tête Administrateur</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --header-height: 60px;
            --primary-color: #4e73df;
            --primary-dark: #224abe;
            --text-light: #f8f9fc;
        }

        body {
            background-color: #f4f7fc;
            padding-top: 0;
            margin: 0;
        }

        .navbar {
            height: var(--header-height);
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 0.5rem 1rem;
            position: relative;
            z-index: 1030;
        }

        .navbar-brand {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-weight: 600;
            font-size: 1.25rem;
            color: var(--text-light) !important;
            transition: opacity 0.2s;
            padding: 0;
        }

        .navbar-brand:hover {
            opacity: 0.9;
        }

        .navbar-brand img {
            width: 40px;
            height: 40px;
            object-fit: contain;
        }

        .navbar-nav {
            gap: 0.5rem;
        }

        .nav-item {
            display: flex;
            align-items: center;
        }

        .nav-link {
            color: var(--text-light) !important;
            padding: 0.5rem 1rem !important;
            border-radius: 6px;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 500;
            position: relative;
        }

        .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
            transform: translateY(-1px);
        }

        .nav-link.active {
            background-color: rgba(255, 255, 255, 0.2);
        }

        .nav-link i {
            font-size: 1.1rem;
        }

        .dropdown-menu {
            border: none;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            padding: 0.5rem;
            min-width: 200px;
            margin-top: 0.5rem;
        }

        .dropdown-item {
            padding: 0.75rem 1rem;
            border-radius: 6px;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.2s;
        }

        .dropdown-item:hover {
            background-color: #f0f4ff;
            transform: translateX(3px);
        }

        .dropdown-item.text-danger:hover {
            background-color: #fff5f5;
        }

        .navbar-toggler {
            border: none;
            padding: 0.5rem;
            color: var(--text-light);
        }

        .navbar-toggler:focus {
            box-shadow: none;
        }

        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba(255, 255, 255, 1)' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }

        @media (max-width: 991.98px) {
            .navbar-collapse {
                background: var(--primary-dark);
                padding: 1rem;
                border-radius: 8px;
                margin-top: 0.5rem;
            }

            .nav-link {
                padding: 0.75rem 1rem !important;
            }

            .dropdown-menu {
                background: transparent;
                box-shadow: none;
                border-left: 2px solid rgba(255, 255, 255, 0.1);
                margin-left: 1rem;
            }

            .dropdown-item {
                color: var(--text-light);
            }

            .dropdown-item:hover {
                background-color: rgba(255, 255, 255, 0.1);
                color: var(--text-light);
            }
        }
    </style>

    <link rel="stylesheet" href="../assets/css/darkmood.css">
    <script src="../assets/js/darkmood.js"></script>
</head>

<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand" href="./Admin.php">
                <img src="../assets/img/ofppt_logo.png" alt="Logo OFPPT">
                <span>OFPPT</span>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'Admin.php' ? 'active' : ''; ?>" href="./Admin.php">
                            <i class="bi bi-house"></i> Accueil
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'ajouterCla.php' ? 'active' : ''; ?>" href="./ajouterCla.php">
                            <i class="bi bi-person-plus"></i> Ajouter stagiaires
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'classes.php' ? 'active' : ''; ?>" href="./classes.php">
                            <i class="bi bi-people"></i> Classes
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="profileDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-circle"></i>
                            <?php echo htmlspecialchars($_SESSION['username']); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item" href="./AdminProfile.php">
                                    <i class="bi bi-person"></i> Mon Profile
                                </a>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <form action="../Controller/UserController.php?action=logout" method="POST" style="margin: 0;">
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="bi bi-box-arrow-right"></i> Se déconnecter
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>