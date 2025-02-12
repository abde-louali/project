<!DOCTYPE html>
<html lang="fr" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenue | OFPPT</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: '#4e73df',
                        'primary-dark': '#224abe',
                        secondary: '#858796',
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.5s ease-out',
                        'slide-up': 'slideUp 0.5s ease-out',
                        'bounce-slow': 'bounce 3s infinite',
                    },
                    keyframes: {
                        fadeIn: {
                            '0%': {
                                opacity: '0'
                            },
                            '100%': {
                                opacity: '1'
                            },
                        },
                        slideUp: {
                            '0%': {
                                transform: 'translateY(20px)',
                                opacity: '0'
                            },
                            '100%': {
                                transform: 'translateY(0)',
                                opacity: '1'
                            },
                        }
                    }
                }
            }
        }
    </script>
</head>

<body class="min-h-screen bg-gradient-to-br from-gray-50 to-blue-50 dark:from-gray-900 dark:to-gray-800 transition-colors duration-300">
    <!-- Dark Mode Toggle -->
    <button id="darkModeToggle" class="fixed top-4 right-4 p-2 rounded-full bg-white dark:bg-gray-800 shadow-lg hover:shadow-xl transition-all">
        <i class="bi bi-sun-fill text-yellow-500 dark:hidden text-2xl"></i>
        <i class="bi bi-moon-fill text-blue-300 hidden dark:block text-2xl"></i>
    </button>



    <div class="min-h-screen flex items-center justify-center p-8">
        <div class="max-w-7xl w-full bg-white dark:bg-gray-800 rounded-3xl shadow-xl overflow-hidden flex flex-col items-center p-12 animate-fade-in">
            <div class="text-center mb-8 animate-slide-up">
                <img src="assets/img/ofppt_logo.png" alt="Logo OFPPT" class="w-40 h-auto mb-4 mx-auto hover:scale-105 transition-transform">
                <h1 class="text-4xl md:text-5xl font-bold text-primary dark:text-white mb-4 relative">
                    Bienvenue à l'OFPPT
                    <span class="absolute -top-2 -right-2 w-2 h-2 bg-primary rounded-full animate-bounce-slow"></span>
                </h1>
                <p class="text-lg text-secondary dark:text-gray-300 mb-8 max-w-2xl mx-auto">
                    Plateforme de gestion des documents des stagiaires de l'Office de la Formation Professionnelle et de la Promotion du Travail
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12 w-full max-w-5xl">
                <div class="group bg-gray-50 dark:bg-gray-700 p-6 rounded-2xl text-center transform transition-all duration-300 hover:-translate-y-2 hover:shadow-2xl">
                    <div class="text-4xl text-primary dark:text-blue-400 mb-4 group-hover:scale-110 transition-transform">
                        <i class="bi bi-file-earmark-text"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-2">Gestion des Documents</h3>
                    <p class="text-secondary dark:text-gray-300">
                        Gérez facilement tous vos documents administratifs en un seul endroit
                    </p>
                </div>

                <div class="group bg-gray-50 dark:bg-gray-700 p-6 rounded-2xl text-center transform transition-all duration-300 hover:-translate-y-2 hover:shadow-2xl">
                    <div class="text-4xl text-primary dark:text-blue-400 mb-4 group-hover:scale-110 transition-transform">
                        <i class="bi bi-people"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-2">Suivi des Stagiaires</h3>
                    <p class="text-secondary dark:text-gray-300">
                        Suivez l'état des dossiers de chaque stagiaire en temps réel
                    </p>
                </div>

                <div class="group bg-gray-50 dark:bg-gray-700 p-6 rounded-2xl text-center transform transition-all duration-300 hover:-translate-y-2 hover:shadow-2xl">
                    <div class="text-4xl text-primary dark:text-blue-400 mb-4 group-hover:scale-110 transition-transform">
                        <i class="bi bi-shield-check"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-2">Sécurité Garantie</h3>
                    <p class="text-secondary dark:text-gray-300">
                        Vos données sont protégées avec les plus hauts standards de sécurité
                    </p>
                </div>
            </div>

            <a href="view/Login.php"
                class="group inline-flex items-center gap-2 px-8 py-4 bg-gradient-to-r from-primary to-primary-dark text-white text-lg font-semibold rounded-full 
                      transform transition-all duration-300 hover:-translate-y-1 hover:shadow-lg hover:scale-105">
                <i class="bi bi-box-arrow-in-right group-hover:translate-x-1 transition-transform"></i>
                Se Connecter
            </a>
        </div>
    </div>

    <script>
        // Dark Mode Toggle
        const darkModeToggle = document.getElementById('darkModeToggle');

        // Check for saved dark mode preference
        if (localStorage.getItem('darkMode') === 'enabled' ||
            (localStorage.getItem('darkMode') !== 'disabled' &&
                window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        }

        darkModeToggle.addEventListener('click', () => {
            document.documentElement.classList.toggle('dark');
            localStorage.setItem('darkMode',
                document.documentElement.classList.contains('dark') ? 'enabled' : 'disabled'
            );
        });

        // Smooth scroll
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });
    </script>
</body>

</html>