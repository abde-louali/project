<?php
session_start();

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
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    <link rel="stylesheet" href="../assets/css/darkmood.css">
    <script src="../assets/js/darkmood.js"></script>
</head>

<body class="bg-gray-100 min-h-screen p-8 font-sans">
    <?php if (isset($_GET['status']) && isset($_GET['message'])): ?>
        <div class="max-w-7xl mx-auto mb-6">
            <div class="alert alert-<?php echo $_GET['status'] == 'success' ? 'bg-green-500' : 'bg-red-500'; ?> text-white px-6 py-4 rounded-lg shadow-md flex justify-between items-center">
                <?php echo htmlspecialchars($_GET['message']); ?>
                <button type="button" class="text-white" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        </div>
    <?php endif; ?>

    <div class="max-w-7xl mx-auto">
        <div class="bg-gradient-to-r from-blue-600 to-blue-800 text-white p-12 rounded-xl shadow-lg mb-10 relative overflow-hidden">
            <div class="flex justify-between items-start">
                <div>
                    <div class="text-4xl font-bold mb-4"><?php echo $message_bienvenue; ?></div>
                    <div class="text-xl opacity-95 font-light">
                        <span class="bg-white text-blue-800 px-3 py-1 rounded-full text-sm font-medium"><?php echo $message_role; ?></span>
                        Bienvenue dans votre espace administrateur
                    </div>
                </div>
                <div class="dark-mode-toggle" onclick="toggleDarkMode()">
                    <i id="darkModeIcon" class="bi bi-moon text-white text-xl"></i>
                </div>
            </div>
            <form action="../Controller/UserController.php?action=logout" method="POST" class="m-0">
                <button type="submit" class="absolute bottom-6 right-6 bg-gradient-to-r from-red-600 to-red-800 text-white px-4 py-2 rounded-lg flex items-center gap-2 shadow-md hover:shadow-lg transition-shadow">
                    <i class="bi bi-box-arrow-right"></i> Se déconnecter
                </button>
            </form>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mt-8">
            <a href="./ajouterCla.php" class="bg-white rounded-xl p-8 text-center shadow-md hover:shadow-lg transition-shadow">
                <div class="text-blue-600 text-5xl mb-6">
                    <i class="bi bi-person-plus"></i>
                </div>
                <div class="text-2xl font-semibold text-gray-800 mb-4">Ajouter Stagiaires</div>
                <div class="text-gray-600">Ajoutez de nouveaux stagiaires à la base de données</div>
            </a>

            <a href="./classes.php" class="bg-white rounded-xl p-8 text-center shadow-md hover:shadow-lg transition-shadow">
                <div class="text-blue-600 text-5xl mb-6">
                    <i class="bi bi-people"></i>
                </div>
                <div class="text-2xl font-semibold text-gray-800 mb-4">Gestion des Classes</div>
                <div class="text-gray-600">Gérez les classes et leurs stagiaires</div>
            </a>

            <a href="./AdminProfile.php" class="bg-white rounded-xl p-8 text-center shadow-md hover:shadow-lg transition-shadow">
                <div class="text-blue-600 text-5xl mb-6">
                    <i class="bi bi-person-circle"></i>
                </div>
                <div class="text-2xl font-semibold text-gray-800 mb-4">Mon Profil</div>
                <div class="text-gray-600">Consultez et modifiez vos informations</div>
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Add dark mode JavaScript -->
    <script src="../assets/js/darkmode.js"></script>
</body>

</html>