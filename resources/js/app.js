import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

// For Navbar
// resources/js/app.js
document.addEventListener('DOMContentLoaded', function () {
    const button = document.getElementById('mobile-menu-button');
    const menu = document.getElementById('mobile-menu');

    if (button && menu) {
        button.addEventListener('click', function () {
            // สลับการแสดงผล
            if (menu.style.display === 'none') {
                menu.style.display = 'block';
            } else {
                menu.style.display = 'none';
            }

            // (ไม่บังคับ) สลับไอคอน Hamburger/Close
            const iconOpen = button.querySelector('svg:first-child');
            const iconClose = button.querySelector('svg:last-child');

            iconOpen.classList.toggle('hidden');
            iconClose.classList.toggle('hidden');
        });
    }
});