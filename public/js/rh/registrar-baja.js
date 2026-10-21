document.addEventListener('DOMContentLoaded', () => {
    const root = document.getElementById('personal-index');
    const modal = document.getElementById('baja-modal');
    const panel = document.getElementById('baja-modal-panel');

    if (!root || !modal || !panel) return;

    const personalIdInput = document.getElementById('baja-personal-id');
    const personalName = document.getElementById('baja-modal-person');
    const employeeId = document.getElementById('baja-modal-employee');

    // Saca el modal del contenedor del listado y lo posiciona respecto a la ventana.
    document.body.appendChild(modal);

    let closeTimer = null;

    function openModal(button = null) {
        if (button) {
            personalIdInput.value = button.dataset.personalId || '';
            personalName.textContent = button.dataset.personalName || 'Colaborador seleccionado';
            employeeId.textContent = button.dataset.employeeId || '';
        }

        window.clearTimeout(closeTimer);

        modal.classList.remove('invisible', 'pointer-events-none', 'opacity-0');
        modal.classList.add('opacity-100');
        modal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('overflow-hidden');

        requestAnimationFrame(() => {
            panel.classList.remove('translate-y-2', 'scale-95', 'opacity-0');
            panel.classList.add('translate-y-0', 'scale-100', 'opacity-100');
        });

        window.setTimeout(() => {
            modal.querySelector('input[name="fecha_baja"]')?.focus();
        }, 200);
    }

    function closeModal() {
        panel.classList.remove('translate-y-0', 'scale-100', 'opacity-100');
        panel.classList.add('translate-y-2', 'scale-95', 'opacity-0');

        modal.classList.remove('opacity-100');
        modal.classList.add('opacity-0');
        modal.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('overflow-hidden');

        closeTimer = window.setTimeout(() => {
            modal.classList.add('invisible', 'pointer-events-none');
        }, 200);
    }

    // Delegación: las filas pueden cambiar cuando se actualizan resultados por AJAX.
    root.addEventListener('click', (event) => {
        const openButton = event.target.closest('.open-baja-modal');

        if (openButton) {
            openModal(openButton);
        }
    });

    // El modal está en body, así que sus eventos se escuchan directamente en él.
    modal.addEventListener('click', (event) => {
        if (event.target === modal || event.target.closest('.close-baja-modal')) {
            closeModal();
        }
    });

    document.addEventListener('keydown', (event) => {
        const modalOpen = modal.getAttribute('aria-hidden') === 'false';

        if (event.key === 'Escape' && modalOpen) {
            closeModal();
        }
    });

    // Reabre el modal si Laravel regresó al listado con errores de validación.
    if (modal.dataset.reopen === '1') {
        openModal();
    }
});