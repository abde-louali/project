// darkmode.js - Save this in your assets/js folder
document.addEventListener('DOMContentLoaded', function () {
  // Check if dark mode is enabled in localStorage
  const isDarkMode = localStorage.getItem('darkMode') === 'enabled';

  // Apply dark mode if enabled
  if (isDarkMode) {
    document.body.classList.add('dark-mode');
  }

  // Function to toggle dark mode
  window.toggleDarkMode = function () {
    const isDarkMode = document.body.classList.toggle('dark-mode');
    if (isDarkMode) {
      localStorage.setItem('darkMode', 'enabled');
      if (document.getElementById('darkModeIcon')) {
        document.getElementById('darkModeIcon').classList.replace('bi-moon', 'bi-sun');
      }
    } else {
      localStorage.setItem('darkMode', 'disabled');
      if (document.getElementById('darkModeIcon')) {
        document.getElementById('darkModeIcon').classList.replace('bi-sun', 'bi-moon');
      }
    }
  }

  // Update icon on page load
  if (document.getElementById('darkModeIcon')) {
    if (isDarkMode) {
      document.getElementById('darkModeIcon').classList.replace('bi-moon', 'bi-sun');
    } else {
      document.getElementById('darkModeIcon').classList.replace('bi-sun', 'bi-moon');
    }
  }
});

// Fonction pour basculer le mode sombre
function toggleTheme() {
  const currentTheme = document.documentElement.getAttribute('data-theme');
  const newTheme = currentTheme === 'dark' ? 'light' : 'dark';

  document.documentElement.setAttribute('data-theme', newTheme);
  localStorage.setItem('theme', newTheme);

  // Mettre à jour l'icône du bouton
  const themeIcon = document.getElementById('themeIcon');
  if (themeIcon) {
    themeIcon.className = newTheme === 'dark' ? 'bi bi-moon-fill' : 'bi bi-sun-fill';
  }
}

// Fonction pour initialiser le thème
function initTheme() {
  // Vérifier si un thème est déjà enregistré
  const savedTheme = localStorage.getItem('theme') || 'light';
  document.documentElement.setAttribute('data-theme', savedTheme);

  // Mettre à jour l'icône initiale
  const themeIcon = document.getElementById('themeIcon');
  if (themeIcon) {
    themeIcon.className = savedTheme === 'dark' ? 'bi bi-moon-fill' : 'bi bi-sun-fill';
  }
}

// Initialiser le thème au chargement de la page
document.addEventListener('DOMContentLoaded', initTheme);