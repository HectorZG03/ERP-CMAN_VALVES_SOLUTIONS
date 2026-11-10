(() => {
    const dialog = document.getElementById('erp-confirm-dialog');
    const panel = document.getElementById('erp-confirm-panel');

    if (!dialog || !panel) return;

    const title = document.getElementById('erp-confirm-title');
    const message = document.getElementById('erp-confirm-message');
    const icon = document.getElementById('erp-confirm-icon');
    const cancelButton = document.getElementById('erp-confirm-cancel');
    const acceptButton = document.getElementById('erp-confirm-accept');
    const queue = [];
    let active = null;
    let previousFocus = null;
    let closingTimer = null;

    const variants = {
        warning: 'bg-amber-500 text-white hover:bg-amber-600 focus-visible:ring-amber-500',
        danger: 'bg-red-600 text-white hover:bg-red-700 focus-visible:ring-red-500',
        success: 'bg-emerald-600 text-white hover:bg-emerald-700 focus-visible:ring-emerald-500',
        primary: 'bg-slate-900 text-white hover:bg-slate-700 focus-visible:ring-slate-500 dark:bg-slate-700 dark:hover:bg-slate-600',
    };
    const variantClasses = Object.values(variants).flatMap((classes) => classes.split(' '));

    function setVariant(variant) {
        const selected = variants[variant] || variants.warning;
        acceptButton.classList.remove(...variantClasses);
        acceptButton.classList.add(...selected.split(' '));

        icon.className = 'flex h-11 w-11 shrink-0 items-center justify-center rounded-full';
        if (variant === 'danger') {
            icon.classList.add('bg-red-100', 'text-red-700', 'dark:bg-red-900/40', 'dark:text-red-300');
        } else if (variant === 'success') {
            icon.classList.add('bg-emerald-100', 'text-emerald-700', 'dark:bg-emerald-900/40', 'dark:text-emerald-300');
        } else {
            icon.classList.add('bg-amber-100', 'text-amber-700', 'dark:bg-amber-900/40', 'dark:text-amber-300');
        }
    }

    function showNext() {
        if (active || queue.length === 0) return;

        active = queue.shift();
        previousFocus = document.activeElement;
        title.textContent = active.options.title || 'Confirmar acción';
        message.textContent = active.message;
        cancelButton.querySelector('[data-button-label]').textContent = active.options.cancelText || 'Cancelar';
        acceptButton.querySelector('[data-button-label]').textContent = active.options.confirmText || 'Confirmar';
        setVariant(active.options.variant || 'warning');

        dialog.classList.remove('hidden');
        dialog.classList.add('flex');
        dialog.setAttribute('aria-hidden', 'false');
        window.requestAnimationFrame(() => {
            dialog.classList.remove('opacity-0');
            panel.classList.remove('translate-y-2', 'scale-95', 'opacity-0');
            panel.classList.add('translate-y-0', 'scale-100', 'opacity-100');
            cancelButton.focus();
        });
    }

    function finish(confirmed) {
        if (!active || active.closing) return;

        const completed = active;
        active.closing = true;
        dialog.classList.add('opacity-0');
        panel.classList.remove('translate-y-0', 'scale-100', 'opacity-100');
        panel.classList.add('translate-y-2', 'scale-95', 'opacity-0');
        dialog.setAttribute('aria-hidden', 'true');

        window.clearTimeout(closingTimer);
        closingTimer = window.setTimeout(() => {
            dialog.classList.add('hidden');
            dialog.classList.remove('flex');
            active = null;
            completed.resolve(confirmed);
            if (previousFocus instanceof HTMLElement && previousFocus.isConnected) previousFocus.focus();
            previousFocus = null;
            showNext();
        }, 160);
    }

    window.erpConfirm = (text, options = {}) => new Promise((resolve) => {
        queue.push({ message: String(text ?? ''), options, resolve });
        showNext();
    });

    cancelButton.addEventListener('click', () => finish(false));
    acceptButton.addEventListener('click', () => finish(true));

    dialog.addEventListener('click', (event) => {
        if (event.target === dialog) finish(false);
    });

    document.addEventListener('keydown', (event) => {
        if (!active) return;
        if (event.key === 'Escape') {
            event.preventDefault();
            finish(false);
            return;
        }

        if (event.key === 'Tab') {
            event.preventDefault();
            (event.shiftKey ? acceptButton : cancelButton).focus();
        }
    });

    const bypassForms = new WeakSet();
    const pendingForms = new WeakSet();
    document.addEventListener('submit', (event) => {
        const form = event.target;
        if (!(form instanceof HTMLFormElement) || !form.dataset.confirm) return;

        if (bypassForms.has(form)) {
            bypassForms.delete(form);
            pendingForms.delete(form);
            delete form.dataset.confirmApproved;
            return;
        }

        // Deja que primero se ejecuten las validaciones propias de cada vista.
        if (event.defaultPrevented) return;
        if (pendingForms.has(form)) {
            event.preventDefault();
            return;
        }

        event.preventDefault();
        pendingForms.add(form);
        const submitter = event.submitter;
        window.erpConfirm(form.dataset.confirm, {
            title: form.dataset.confirmTitle || 'Confirmar acción',
            confirmText: form.dataset.confirmText || 'Confirmar',
            variant: form.dataset.confirmVariant || 'warning',
        }).then((confirmed) => {
            pendingForms.delete(form);
            if (!confirmed || !form.isConnected) return;
            bypassForms.add(form);
            form.dataset.confirmApproved = 'true';
            if (submitter && submitter.form === form) form.requestSubmit(submitter);
            else form.requestSubmit();
        });
    });
})();
