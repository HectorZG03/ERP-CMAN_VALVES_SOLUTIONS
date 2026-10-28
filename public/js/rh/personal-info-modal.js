document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('personal-info-modal');
    const panel = document.getElementById('personal-info-panel');

    if (!modal || !panel) {
        return;
    }

    let lastTrigger = null;
    let closeTimer = null;

    const fields = {
        employeeId: 'info-employee-id',
        status: 'info-status',
        area: 'info-area',
        department: 'info-department',
        position: 'info-position',
        hireDate: 'info-hire-date',
        email: 'info-email',
        phone: 'info-phone',
        curp: 'info-curp',
        rfc: 'info-rfc',
        nss: 'info-nss',
        sex: 'info-sex',
        birthDate: 'info-birth-date',
        nationality: 'info-nationality',
        civilStatus: 'info-civil-status',
        address: 'info-address',
    };

    const formatDate = (value) => {
        if (!value) return '';

        // Convierte fechas ISO, como 1990-05-21, a 21/05/1990.
        const match = value.match(/^(\d{4})-(\d{2})-(\d{2})/);

        if (match) {
            return `${match[3]}/${match[2]}/${match[1]}`;
        }

        return value;
    };

    const openModal = (trigger) => {
        window.clearTimeout(closeTimer);
        lastTrigger = trigger;

        const data = trigger.dataset;
        const name = data.name || 'Colaborador';

        const nameElement = document.getElementById('info-personal-name');
        if (nameElement) {
            nameElement.textContent = name;
        }

        Object.entries(fields).forEach(([dataKey, elementId]) => {
            const element = document.getElementById(elementId);

            if (!element) return;

            let value = data[dataKey] || '';

            if (dataKey === 'birthDate') {
                value = formatDate(value);
            }

            element.textContent = value.trim() || 'No registrado';
        });

        modal.classList.remove('invisible', 'pointer-events-none', 'opacity-0');
        modal.classList.add('visible', 'pointer-events-auto', 'opacity-100');
        modal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('overflow-hidden');

        window.requestAnimationFrame(() => {
            panel.classList.remove('translate-y-2', 'scale-95', 'opacity-0');
            panel.classList.add('translate-y-0', 'scale-100', 'opacity-100');
        });

        const closeButton = modal.querySelector('.close-personal-info-modal');
        window.setTimeout(() => closeButton?.focus(), 100);
    };

    const closeModal = () => {
        if (modal.getAttribute('aria-hidden') === 'true') {
            return;
        }

        modal.classList.remove('opacity-100');
        modal.classList.add('opacity-0');

        panel.classList.remove('translate-y-0', 'scale-100', 'opacity-100');
        panel.classList.add('translate-y-2', 'scale-95', 'opacity-0');

        modal.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('overflow-hidden');

        closeTimer = window.setTimeout(() => {
            modal.classList.remove('visible', 'pointer-events-auto');
            modal.classList.add('invisible', 'pointer-events-none');
            lastTrigger?.focus();
        }, 200);
    };

    // Delegación de eventos: funciona también después de que AJAX actualice la tabla.
    document.addEventListener('click', (event) => {
        const trigger = event.target.closest('.open-personal-info-modal');

        if (trigger) {
            openModal(trigger);
            return;
        }

        if (event.target.closest('.close-personal-info-modal')) {
            closeModal();
            return;
        }

        if (event.target === modal) {
            closeModal();
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && modal.getAttribute('aria-hidden') === 'false') {
            closeModal();
        }
    });
});