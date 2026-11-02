document.addEventListener('DOMContentLoaded', function () {
    const htmlRoot = document.documentElement;
    const themeToggleBtn = document.getElementById('theme-toggle-btn');
    const themeIcon = document.getElementById('theme-icon');

    const savedTheme = localStorage.getItem('alamo_citizen_theme') || 'light';
    applyTheme(savedTheme);

    if (themeToggleBtn) {
        themeToggleBtn.addEventListener('click', function () {
            const currentTheme = htmlRoot.getAttribute('data-theme');
            const nextTheme = currentTheme === 'light' ? 'dark' : 'light';
            applyTheme(nextTheme);
            localStorage.setItem('alamo_citizen_theme', nextTheme);
        });
    }

    function applyTheme(theme) {
        htmlRoot.setAttribute('data-theme', theme);
        if (!themeIcon) return;

        if (theme === 'dark') {
            themeIcon.classList.remove('fa-moon');
            themeIcon.classList.add('fa-sun');
        } else {
            themeIcon.classList.remove('fa-sun');
            themeIcon.classList.add('fa-moon');
        }
    }
});