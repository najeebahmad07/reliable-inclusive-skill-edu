/**
 * RISE - Theme Toggle (Dark/Light)
 * ==================================
 */

(function() {
    'use strict';

    const THEME_KEY = 'rise_theme';

    function getStoredTheme() {
        return localStorage.getItem(THEME_KEY) || 'light';
    }

    function setTheme(theme) {
        document.documentElement.setAttribute('data-theme', theme);
        localStorage.setItem(THEME_KEY, theme);
        updateToggleIcon(theme);
        updateBootstrapTheme(theme);
    }

    function updateToggleIcon(theme) {
        const btn = document.getElementById('themeToggleBtn');
        if (!btn) return;
        if (theme === 'dark') {
            btn.innerHTML = '<i class="fas fa-sun"></i>';
            btn.title = 'Switch to Light Mode';
        } else {
            btn.innerHTML = '<i class="fas fa-moon"></i>';
            btn.title = 'Switch to Dark Mode';
        }
    }

    function updateBootstrapTheme(theme) {
        document.documentElement.setAttribute('data-bs-theme', theme);
    }

    function toggleTheme() {
        const current = getStoredTheme();
        const next = current === 'dark' ? 'light' : 'dark';
        setTheme(next);
    }

    // Initialize on DOM ready
    document.addEventListener('DOMContentLoaded', function() {
        setTheme(getStoredTheme());

        const btn = document.getElementById('themeToggleBtn');
        if (btn) {
            btn.addEventListener('click', toggleTheme);
        }
    });
})();