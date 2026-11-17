(() => {
    const selector = '[data-uppercase]';

    const normalize = (field) => {
        if (!(field instanceof HTMLInputElement || field instanceof HTMLTextAreaElement)) return;
        if (!field.matches(selector)) return;
        if (field instanceof HTMLInputElement && field.type !== 'text') return;

        const original = field.value;
        const uppercase = original.toUpperCase();
        if (original === uppercase) return;

        const start = field.selectionStart;
        const end = field.selectionEnd;
        const direction = field.selectionDirection;
        field.value = uppercase;

        if (start !== null && end !== null) {
            field.setSelectionRange(
                original.slice(0, start).toUpperCase().length,
                original.slice(0, end).toUpperCase().length,
                direction
            );
        }
    };

    const normalizeTree = (root) => {
        if (!(root instanceof Element)) return;
        normalize(root);
        root.querySelectorAll(selector).forEach(normalize);
    };

    const initialize = () => {
        const forms = document.querySelectorAll('form[data-uppercase-form]');
        forms.forEach((form) => {
            normalizeTree(form);
            form.addEventListener('input', (event) => {
                if (!event.isComposing) normalize(event.target);
            }, true);
            form.addEventListener('compositionend', (event) => normalize(event.target), true);
            form.addEventListener('submit', () => normalizeTree(form), true);

            // Incluye materiales dinámicos y valores restaurados sin evento input.
            new MutationObserver((mutations) => {
                mutations.forEach((mutation) => mutation.addedNodes.forEach(normalizeTree));
            }).observe(form, { childList: true, subtree: true });
        });
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initialize, { once: true });
    } else {
        initialize();
    }
})();
