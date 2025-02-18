// darkmode.js - Save this in your assets/js folder
document.addEventListener('DOMContentLoaded', function() {
    // Check if dark mode is enabled in localStorage
    const isDarkMode = localStorage.getItem('darkMode') === 'enabled';
    
    // Apply dark mode if enabled
    if (isDarkMode) {
      document.body.classList.add('dark-mode');
    }
    
    // Function to toggle dark mode
    window.toggleDarkMode = function() {
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