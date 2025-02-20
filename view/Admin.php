<?php
session_start();

// Only handle theme toggling - no changes to your existing logic
if (isset($_POST['toggle_theme'])) {
    if (!isset($_SESSION['theme']) || $_SESSION['theme'] === 'light') {
        $_SESSION['theme'] = 'dark';
    } else {
        $_SESSION['theme'] = 'light';
    }
    // Redirect to the same page to prevent form resubmission
    header("Location: " . $_SERVER['PHP_SELF'] . (isset($_SERVER['QUERY_STRING']) && !empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : ''));
    exit();
}

// Set default theme if not set
if (!isset($_SESSION['theme'])) {
    $_SESSION['theme'] = 'light';
}

// Your existing code starts here - unchanged
if (!isset($_SESSION["username"])) {
    header("location: Login.php");
    exit();
}

include_once '../Model/AdminModel.php';
$adminModel = new AdminModel();
$adminInfo = $adminModel->getAdminInfo($_SESSION["username"]);

$heure = date("H");
if ($heure < 12) {
    $message_bienvenue = "Bonjour " . htmlspecialchars($adminInfo['first_name']) . " " . htmlspecialchars($adminInfo['last_name']) . " !";
} elseif ($heure < 18) {
    $message_bienvenue = "Bon après-midi " . htmlspecialchars($adminInfo['first_name']) . " " . htmlspecialchars($adminInfo['last_name']) . " !";
} else {
    $message_bienvenue = "Bonsoir " . htmlspecialchars($adminInfo['first_name']) . " " . htmlspecialchars($adminInfo['last_name']) . " !";
}

$message_role = "Administrateur";
?>

<!DOCTYPE html>
<html lang="fr" data-theme="<?php echo $_SESSION['theme']; ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4e73df;
            --primary-dark: #224abe;
            --secondary-color: #858796;
            --success-color: #1cc88a;
            --danger-color: #e74a3b;

            /* Light theme variables */
            --bg-color: #f8f9fc;
            --text-color: #2d3748;
            --card-bg: white;
            --card-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            --border-color: rgba(0, 0, 0, 0.05);
        }

        /* Dark theme variables */
        [data-theme="dark"] {
            --bg-color: #1a202c;
            --text-color: #e2e8f0;
            --card-bg: #2d3748;
            --card-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            --border-color: rgba(255, 255, 255, 0.05);
            --secondary-color: #a0aec0;
        }

        body {
            background-color: var(--bg-color);
            min-height: 100vh;
            padding: 2rem;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            transition: background-color 0.3s ease, color 0.3s ease;
            color: var(--text-color);
        }

        .dashboard-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .welcome-section {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            color: white;
            padding: 3rem;
            border-radius: 15px;
            margin-bottom: 2.5rem;
            box-shadow: 0 8px 24px rgba(78, 115, 223, 0.15);
            position: relative;
            overflow: hidden;
        }

        .welcome-section::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
            background: linear-gradient(45deg, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0) 100%);
            pointer-events: none;
        }

        .welcome-message {
            font-size: 2.75rem;
            font-weight: 700;
            margin-bottom: 1rem;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .welcome-subtitle {
            font-size: 1.3rem;
            opacity: 0.95;
            font-weight: 300;
            letter-spacing: 0.5px;
        }

        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
            padding: 0.5rem;
        }

        .action-card {
            background: var(--card-bg);
            border-radius: 15px;
            padding: 2rem;
            text-align: center;
            transition: all 0.3s ease;
            box-shadow: var(--card-shadow);
            cursor: pointer;
            text-decoration: none;
            color: inherit;
            border: 1px solid var(--border-color);
            position: relative;
            overflow: hidden;
        }

        .action-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-color), var(--primary-dark));
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .action-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
            color: inherit;
        }

        .action-card:hover::before {
            opacity: 1;
        }

        .action-icon {
            font-size: 3rem;
            color: var(--primary-color);
            margin-bottom: 1.5rem;
            transition: transform 0.3s ease;
        }

        .action-card:hover .action-icon {
            transform: scale(1.1);
        }

        .action-title {
            font-size: 1.4rem;
            font-weight: 600;
            color: var(--text-color);
            margin-bottom: 1rem;
        }

        .action-description {
            color: var(--secondary-color);
            font-size: 1rem;
            line-height: 1.6;
        }

        .logout-button {
            position: absolute;
            bottom: 1.5rem;
            right: 1.5rem;
            padding: 0.5rem 1.2rem;
            background: linear-gradient(135deg, var(--danger-color) 0%, #dc3545 100%);
            color: white;
            border: none;
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.9rem;
            box-shadow: 0 2px 8px rgba(231, 74, 59, 0.2);
        }

        .logout-button:hover {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(231, 74, 59, 0.25);
        }

        .logout-button i {
            font-size: 1rem;
        }

        .alert {
            max-width: 1200px;
            margin: 0 auto 1.5rem auto;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            background-color: var(--card-bg);
            color: var(--text-color);
        }

        /* Theme toggle styling */
        .theme-toggle {
            position: absolute;
            top: 1.5rem;
            right: 1.5rem;
            background: rgba(255, 255, 255, 0.2);
            border: none;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            color: white;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            z-index: 10;
        }

        .theme-toggle:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
        }

        .theme-toggle i {
            font-size: 1.2rem;
        }

        @media (max-width: 768px) {
            body {
                padding: 1rem;
            }

            .welcome-message {
                font-size: 2rem;
            }

            .welcome-section {
                padding: 2rem;
            }

            .action-card {
                padding: 1.5rem;
            }

            .quick-actions {
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            .logout-button {
                padding: 0.4rem 1rem;
                font-size: 0.85rem;
                bottom: 1rem;
                right: 1rem;
            }

            .theme-toggle {
                top: 1rem;
                right: 1rem;
                width: 35px;
                height: 35px;
            }
        }
    </style>
</head>

<body>
    <?php if (isset($_GET['status']) && isset($_GET['message'])): ?>
        <div class="alert alert-<?php echo $_GET['status'] == 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($_GET['message']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="dashboard-container">
        <div class="welcome-section">
            <div class="welcome-message"><?php echo $message_bienvenue; ?></div>
            <div class="welcome-subtitle">
                <span class="role-badge"><?php echo $message_role; ?></span>
                Bienvenue dans votre espace administrateur
            </div>

            <!-- Theme toggle button - only on Admin.php -->
            <form method="POST" style="margin: 0;">
                <button type="submit" name="toggle_theme" class="theme-toggle" title="<?php echo $_SESSION['theme'] === 'dark' ? 'Passer en mode clair' : 'Passer en mode sombre'; ?>">
                    <i class="bi <?php echo $_SESSION['theme'] === 'dark' ? 'bi-sun' : 'bi-moon'; ?>"></i>
                </button>
            </form>

            <form action="../Controller/UserController.php?action=logout" method="POST" style="margin: 0;">
                <button type="submit" class="logout-button">
                    <i class="bi bi-box-arrow-right"></i> Se déconnecter
                </button>
            </form>
        </div>

        <div class="quick-actions">
            <a href="./ajouterCla.php" class="action-card">
                <div class="action-icon">
                    <i class="bi bi-person-plus"></i>
                </div>
                <div class="action-title">Ajouter Stagiaires</div>
                <div class="action-description">Ajoutez de nouveaux stagiaires à la base de données</div>
            </a>

            <a href="./classes.php" class="action-card">
                <div class="action-icon">
                    <i class="bi bi-people"></i>
                </div>
                <div class="action-title">Gestion des Classes</div>
                <div class="action-description">Gérez les classes et leurs stagiaires</div>
            </a>

            <a href="./AdminProfile.php" class="action-card">
                <div class="action-icon">
                    <i class="bi bi-person-circle"></i>
                </div>
                <div class="action-title">Mon Profil</div>
                <div class="action-description">Consultez et modifiez vos informations</div>
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>