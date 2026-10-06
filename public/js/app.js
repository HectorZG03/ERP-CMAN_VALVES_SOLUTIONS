/**
 * ============================================================
 * ERP CMAN
 * JavaScript global del layout
 * ============================================================
 *
 * Este archivo contiene únicamente funcionalidades generales
 * utilizadas por el layout principal.
 *
 * No colocar aquí lógica específica de Dashboard, Solicitudes,
 * Salidas, Inventario, etc.
 */


/**
 * ============================================================
 * INICIALIZACIÓN
 * ============================================================
 */

document.addEventListener('DOMContentLoaded', () => {
    initializeTheme();
    initializeSystemThemeListener();
});


/**
 * ============================================================
 * TEMA
 * ============================================================
 */

/**
 * Aplica el tema visual al documento.
 *
 * @param {string} theme
 * @returns {void}
 */
function applyTheme(theme) {
    const html = document.documentElement;

    if (theme === 'dark') {
        html.classList.add('dark');
    } else {
        html.classList.remove('dark');
    }
}


/**
 * Inicializa el tema guardado por el usuario.
 *
 * Si no existe una preferencia guardada,
 * utiliza la configuración del sistema.
 *
 * @returns {void}
 */
function initializeTheme() {
    const savedTheme = localStorage.getItem('theme');

    const systemTheme = window.matchMedia(
        '(prefers-color-scheme: dark)'
    ).matches
        ? 'dark'
        : 'light';

    const theme = savedTheme || systemTheme;

    applyTheme(theme);

    initializeThemeToggle();
}


/**
 * Inicializa el botón para cambiar entre
 * modo claro y modo oscuro.
 *
 * @returns {void}
 */
function initializeThemeToggle() {
    const themeToggle = document.getElementById('theme-toggle');

    if (!themeToggle) {
        return;
    }

    themeToggle.addEventListener('click', toggleTheme);
}


/**
 * Cambia entre modo claro y modo oscuro.
 *
 * @returns {void}
 */
function toggleTheme() {
    const html = document.documentElement;

    const currentTheme = html.classList.contains('dark')
        ? 'dark'
        : 'light';

    const newTheme = currentTheme === 'dark'
        ? 'light'
        : 'dark';

    applyTheme(newTheme);

    localStorage.setItem('theme', newTheme);
}


/**
 * ============================================================
 * CAMBIOS DEL TEMA DEL SISTEMA
 * ============================================================
 */

/**
 * Escucha cambios en la configuración de tema
 * del sistema operativo.
 *
 * Si el usuario ya seleccionó manualmente un tema,
 * se respeta esa selección.
 *
 * @returns {void}
 */
function initializeSystemThemeListener() {
    const mediaQuery = window.matchMedia(
        '(prefers-color-scheme: dark)'
    );

    mediaQuery.addEventListener('change', (event) => {

        // El usuario seleccionó manualmente un tema.
        if (localStorage.getItem('theme')) {
            return;
        }

        applyTheme(
            event.matches ? 'dark' : 'light'
        );
    });
}