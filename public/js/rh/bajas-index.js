document.addEventListener('DOMContentLoaded', () => {
    document.addEventListener('submit', (event) => {
        const form = event.target.closest('[data-confirm-reactivation]');

        if (!form) return;

        const nombre = form.dataset.workerName || 'este colaborador';
        const confirmar = window.confirm(
            `¿Reactivar a ${nombre}? La baja se conservará en el historial como anulada.`
        );

        if (!confirmar) {
            event.preventDefault();
        }
    });
});
