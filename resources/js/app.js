// bootstrap.js tidak dibutuhkan — aplikasi menggunakan form POST biasa,
// bukan AJAX. Alpine.js sudah cukup untuk interaktivitas UI.
import Alpine from 'alpinejs';

const applyTheme = (theme) => {
    document.documentElement.dataset.theme = theme;
    document.documentElement.classList.toggle('dark', theme === 'dark');
    localStorage.setItem('kost-theme', theme);
};

window.setTheme = applyTheme;
window.toggleTheme = () => applyTheme(
    document.documentElement.dataset.theme === 'dark' ? 'light' : 'dark'
);

applyTheme(localStorage.getItem('kost-theme') || 'light');

window.Alpine = Alpine;
Alpine.start();
