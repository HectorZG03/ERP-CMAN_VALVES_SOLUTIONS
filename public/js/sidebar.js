(() => {
    const storageKey = 'erp-sidebar-collapsed';
    const root = document.documentElement;

    document.addEventListener('DOMContentLoaded', () => {
        const button = document.getElementById('sidebar-toggle');
        const icon = document.getElementById('sidebar-toggle-icon');
        const sidebar = document.getElementById('app-sidebar');

        if (!button || !icon || !sidebar) {
            return;
        }

        const collapsed = () => root.classList.contains('sidebar-collapsed');

        const updateButton = () => {
            const isCollapsed = collapsed();
            const label = isCollapsed
                ? 'Expandir menú lateral'
                : 'Contraer menú lateral';

            button.setAttribute('aria-expanded', String(!isCollapsed));
            button.setAttribute('aria-label', label);
            button.setAttribute('title', label);

            icon.classList.toggle('fa-angles-left', !isCollapsed);
            icon.classList.toggle('fa-angles-right', isCollapsed);
        };

        // Conserva nombres accesibles y tooltips para los enlaces cuyos textos
        // quedan ocultos cuando el menú está contraído.
        sidebar.querySelectorAll('nav a, form button').forEach((element) => {
            const text = element.querySelector('span')?.textContent.trim();

            if (text) {
                element.setAttribute('aria-label', text);
                element.setAttribute('title', text);
            }
        });

        button.addEventListener('click', () => {
            root.classList.toggle('sidebar-collapsed');

            try {
                localStorage.setItem(storageKey, String(collapsed()));
            } catch (error) {
                // El cambio visual funciona durante esta página aunque no se pueda guardar.
            }

            updateButton();
        });

        updateButton();
    });
})();