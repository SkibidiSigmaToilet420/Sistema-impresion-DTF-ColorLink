document.addEventListener('DOMContentLoaded', function() {
    const themeToggle = document.getElementById('themeToggle');
    let currentTheme = localStorage.getItem('theme') || 'dark';

    applyTheme(currentTheme);

    if (themeToggle) {
        themeToggle.addEventListener('click', function(e) {
            e.preventDefault();
            currentTheme = currentTheme === 'dark' ? 'light' : 'dark';
            applyTheme(currentTheme);
            localStorage.setItem('theme', currentTheme);
        });
    }

    function applyTheme(theme) {
        document.documentElement.setAttribute('data-theme', theme);

        // Cambiar fondo del body
        document.body.style.backgroundColor = theme === 'dark' ? '#0a192f' : '#f8f9fa';

        // Cambiar fondo de los inputs, selects y textareas en todas las páginas
        const inputs = document.querySelectorAll('input, select, textarea');
        inputs.forEach(input => {
            if (theme === 'light') {
                input.classList.remove('bg-dark', 'text-light');
                input.classList.add('bg-light', 'text-dark');
            } else {
                input.classList.remove('bg-light', 'text-dark');
                input.classList.add('bg-dark', 'text-light');
            }
        });

        // Cambiar estilos específicos para admin.html
        if (window.location.pathname.includes('admin.html')) {
            const navbar = document.querySelector('.navbar');
            const cards = document.querySelectorAll('.card');
            const tables = document.querySelectorAll('.table');

            if (theme === 'light') {
                // Navbar
                if (navbar) {
                    navbar.classList.remove('navbar-dark', 'bg-dark-blue');
                    navbar.classList.add('navbar-light', 'bg-light');
                    navbar.style.boxShadow = '0 2px 4px rgba(0,0,0,.1)';
                }
                // Cards
                cards.forEach(card => {
                    card.classList.remove('bg-dark-blue', 'bg-navy');
                    card.classList.add('bg-white');
                    card.style.boxShadow = '0 2px 8px rgba(0,0,0,.1)';
                });
                // Tables
                tables.forEach(table => {
                    table.classList.remove('table-dark');
                    table.classList.add('table-light');
                });
            } else {
                // Navbar
                if (navbar) {
                    navbar.classList.remove('navbar-light', 'bg-light');
                    navbar.classList.add('navbar-dark', 'bg-dark-blue');
                    navbar.style.boxShadow = '0 2px 4px rgba(0,0,0,.3)';
                }
                // Cards
                cards.forEach(card => {
                    card.classList.remove('bg-white', 'table-light');
                    card.classList.add('bg-dark-blue', 'bg-navy');
                    card.style.boxShadow = '0 2px 8px rgba(0,0,0,.3)';
                });
                // Tables
                tables.forEach(table => {
                    table.classList.remove('table-light');
                    table.classList.add('table-dark');
                });
            }
        }

        // Actualizar ícono del toggle en todas las páginas
        if (themeToggle) {
            const icon = themeToggle.querySelector('i');
            if (icon) {
                icon.className = theme === 'dark' ? 'fa-solid fa-sun' : 'fa-solid fa-moon';
            }
        }
    }
});