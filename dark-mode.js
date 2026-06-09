/* DARK MODE ADDITION START - THEME TOGGLE FUNCTIONALITY */

(function() {
  // Initialize theme from localStorage or system preference
  function initializeTheme() {
    const savedTheme = localStorage.getItem('motoride-theme');
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    const isDarkMode = savedTheme ? savedTheme === 'dark' : prefersDark;
    
    if (isDarkMode) {
      document.documentElement.classList.add('dark-mode');
    } else {
      document.documentElement.classList.remove('dark-mode');
    }
  }
  
  // Initialize on page load (before DOM renders)
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeTheme);
  } else {
    initializeTheme();
  }
  
  // Also run immediately for faster theme application
  initializeTheme();
})();

// Toggle theme function
function toggleDarkMode() {
  const isDarkMode = document.documentElement.classList.toggle('dark-mode');
  const toggleButtons = document.querySelectorAll('.theme-toggle');
  
  toggleButtons.forEach(button => {
    if (isDarkMode) {
      button.classList.add('dark-active');
    } else {
      button.classList.remove('dark-active');
    }
  });
  
  // Save preference to localStorage
  localStorage.setItem('motoride-theme', isDarkMode ? 'dark' : 'light');
  
  // Dispatch custom event for any other scripts that need to know
  window.dispatchEvent(new CustomEvent('themeChange', { 
    detail: { isDarkMode } 
  }));
}

// Initialize toggle button state on page load
document.addEventListener('DOMContentLoaded', function() {
  const toggleButtons = document.querySelectorAll('.theme-toggle');
  const isDarkMode = document.documentElement.classList.contains('dark-mode');
  
  toggleButtons.forEach(button => {
    if (isDarkMode) {
      button.classList.add('dark-active');
    } else {
      button.classList.remove('dark-active');
    }
  });
});

/* DARK MODE ADDITION END */
