document.addEventListener('DOMContentLoaded', () => {
    const form = document.querySelector('[data-salidas-filter-form]');

    if (!form) {
        return;
    }

    const input = form.querySelector('input[name="buscar"]');
    const clearButton = form.querySelector('[data-salidas-clear]');
    const results = document.querySelector('[data-live-results]');
    if (!results) return;

    let timer = null;
    let controller = null;

    const makeUrl = () => {
        const url = new URL(form.action, window.location.origin);
        url.search = '';
        const data = new FormData(form);

        for (const [key, value] of data.entries()) {
            const text = String(value).trim();
            if (text !== '') url.searchParams.set(key, text);
        }

        return url;
    };

    const updateClearButton = (url) => {
        if (clearButton) {
            clearButton.classList.toggle('hidden', !url.searchParams.get('buscar'));
        }
    };

    const loadResults = async (url, addHistory = false) => {
        if (controller) controller.abort();

        controller = new AbortController();
        form.setAttribute('aria-busy', 'true');
        results.setAttribute('aria-busy', 'true');

        try {
            const response = await fetch(url, {
                headers: {
                    Accept: 'text/html',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                signal: controller.signal,
            });

            if (!response.ok) throw new Error(`HTTP ${response.status}`);

            const html = await response.text();
            const documentResponse = new DOMParser().parseFromString(html, 'text/html');
            const newResults = documentResponse.querySelector('[data-live-results]');

            if (!newResults) throw new Error('No se encontró el bloque de resultados.');

            results.innerHTML = newResults.innerHTML;

            if (addHistory) {
                window.history.pushState({}, '', url);
            } else {
                window.history.replaceState({}, '', url);
            }

            updateClearButton(url);
        } catch (error) {
            if (error.name !== 'AbortError') {
                console.error('No se pudieron actualizar las salidas:', error);
            }
        } finally {
            form.removeAttribute('aria-busy');
            results.removeAttribute('aria-busy');
        }
    };

    const search = () => loadResults(makeUrl());

    if (input) {
        input.addEventListener('input', () => {
            window.clearTimeout(timer);
            timer = window.setTimeout(search, 350);
        });
    }

    form.addEventListener('submit', (event) => {
        event.preventDefault();
        window.clearTimeout(timer);
        search();
    });

    if (clearButton) {
        clearButton.addEventListener('click', () => {
            window.clearTimeout(timer);
            if (input) input.value = '';
            search();
        });
    }

    results.addEventListener('click', (event) => {
        const link = event.target.closest('[data-live-pagination] a[href]');
        if (!link) return;

        event.preventDefault();
        loadResults(new URL(link.href), true);
    });

    window.addEventListener('popstate', () => {
        const url = new URL(window.location.href);
        if (input) input.value = url.searchParams.get('buscar') || '';
        updateClearButton(url);
        loadResults(url);
    });

});
