(() => {
    const dialog = document.getElementById('profile-photo-viewer');
    if (!dialog || dialog.dataset.initialized === 'true') return;

    dialog.dataset.initialized = 'true';

    const image = document.getElementById('profile-photo-viewer-image');
    const title = document.getElementById('profile-photo-viewer-title');
    const status = document.getElementById('profile-photo-viewer-status');
    const content = document.getElementById('profile-photo-viewer-content');
    const closeButton = document.getElementById('profile-photo-viewer-close');

    let previousFocus = null;
    let previousOverflow = '';
    let requestId = 0;
    let startedOutside = false;

    const close = () => {
        if (dialog.open) dialog.close();
    };

    const isOutside = (event) => {
        if (event.target !== dialog) return false;

        const rect = dialog.getBoundingClientRect();

        return (
            event.clientX < rect.left ||
            event.clientX > rect.right ||
            event.clientY < rect.top ||
            event.clientY > rect.bottom
        );
    };

    const open = (trigger) => {
        if (dialog.open) return;

        const source = trigger.dataset.photoSrc;
        if (!source) return;

        const name = trigger.dataset.photoName || 'Fotografía del colaborador';
        const currentRequest = ++requestId;

        previousFocus = trigger;
        previousOverflow = document.body.style.overflow;

        title.textContent = name;
        image.alt = `Fotografía de ${name}`;
        image.hidden = true;
        image.removeAttribute('src');

        status.textContent = 'Cargando fotografía…';
        status.hidden = false;
        content.setAttribute('aria-busy', 'true');

        dialog.showModal();
        document.body.style.overflow = 'hidden';

        const preview = new Image();

        preview.onload = () => {
            if (currentRequest !== requestId || !dialog.open) return;

            image.src = preview.src;
            image.hidden = false;
            status.hidden = true;
            content.setAttribute('aria-busy', 'false');
        };

        preview.onerror = () => {
            if (currentRequest !== requestId || !dialog.open) return;

            image.hidden = true;
            status.textContent = 'No fue posible cargar la fotografía.';
            status.hidden = false;
            content.setAttribute('aria-busy', 'false');
        };

        preview.src = source;
    };

    // También funciona con fotografías insertadas mediante AJAX.
    document.addEventListener('click', (event) => {
        const trigger = event.target.closest('[data-photo-preview]');
        if (!trigger) return;

        event.preventDefault();
        open(trigger);
    });

    closeButton.addEventListener('click', close);

    dialog.addEventListener('pointerdown', (event) => {
        startedOutside = isOutside(event);
    });

    dialog.addEventListener('click', (event) => {
        if (startedOutside && isOutside(event)) close();
        startedOutside = false;
    });

    // Evita que Escape cierre también otro modal situado debajo.
    dialog.addEventListener('keydown', (event) => {
        event.stopPropagation();

        if (event.key === 'Escape') {
            event.preventDefault();
            close();
        }
    }, true);

    dialog.addEventListener('cancel', (event) => {
        event.preventDefault();
        close();
    });

    dialog.addEventListener('close', () => {
        requestId += 1;
        startedOutside = false;

        image.hidden = true;
        image.removeAttribute('src');
        content.setAttribute('aria-busy', 'false');

        // Conserva el bloqueo que pudiera tener otra ventana abierta.
        document.body.style.overflow = previousOverflow;

        if (previousFocus instanceof HTMLElement && previousFocus.isConnected) {
            previousFocus.focus({ preventScroll: true });
        }

        previousFocus = null;
    });
})();