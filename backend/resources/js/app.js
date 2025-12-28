import './bootstrap';
import '../css/app.css';

document.addEventListener('DOMContentLoaded', () => {
    const toggleButton = document.querySelector('[data-mobile-toggle]');
    const mobileMenu = document.querySelector('[data-mobile-menu]');

    if (toggleButton && mobileMenu) {
        toggleButton.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });
    }

    // Render Lucide icons loaded from CDN
    if (window.lucide?.createIcons) {
        window.lucide.createIcons();
    }
});
