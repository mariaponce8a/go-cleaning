document.addEventListener('DOMContentLoaded', () => {
    const themeToggler = document.querySelector('.theme-toggler');
    const lightModeIcon = document.querySelector('.theme-toggler .material-icons-sharp:first-child');
    const darkModeIcon = document.querySelector('.theme-toggler .material-icons-sharp:last-child');
    const menuBtn = document.getElementById('menu-btn');
    const closeBtn = document.getElementById('close-btn');
    const sidebar = document.querySelector('.sidebar');
    const topSection = document.querySelector('.top');
    const body = document.body;
    const logoText = document.querySelector('.logo h2');
    const toggleSidebar = () => {
        sidebar.classList.toggle('collapsed');
        topSection.classList.toggle('hidden');

    };

    // Función para aplicar el tema
    const applyTheme = (theme) => {
        if (theme === 'dark') {
            body.classList.add('dark-theme-variables');
            sidebar.classList.add('dark-theme-variables'); // Aplicar tema oscuro a la barra lateral
            topSection.classList.add('dark-theme-variables'); // Aplicar tema oscuro al encabezado
            lightModeIcon.classList.remove('active');
            darkModeIcon.classList.add('active');
        } else {
            body.classList.remove('dark-theme-variables');
            sidebar.classList.remove('dark-theme-variables'); // Quitar tema oscuro de la barra lateral
            topSection.classList.remove('dark-theme-variables'); // Quitar tema oscuro del encabezado
            lightModeIcon.classList.add('active');
            darkModeIcon.classList.remove('active');
        }
    };

    // Alternar el tema cuando se hace clic en el toggler
    themeToggler.addEventListener('click', () => {
        if (body.classList.contains('dark-theme-variables')) {
            applyTheme('light');
        } else {
            applyTheme('dark');
        }
    });

    // Aplicar tema por defecto
    applyTheme('light');

    // Función para ocultar el texto específico del logo
    closeBtn.addEventListener('click', () => {
        toggleSidebar();
    });

    menuBtn.addEventListener('click', () => {
        toggleSidebar();
    });
});