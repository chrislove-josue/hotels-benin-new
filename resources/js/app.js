import './bootstrap-app';

// Petites interactions front pour la vitrine (menu mobile, etc.)
document.addEventListener('DOMContentLoaded', () => {
    const toggle = document.getElementById('mobile-menu-toggle');
    const menu = document.getElementById('mobile-menu');

    if (toggle && menu) {
        toggle.addEventListener('click', () => {
            menu.classList.toggle('hidden');
        });
    }
});
