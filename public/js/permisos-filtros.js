document.addEventListener('DOMContentLoaded', () => {
    const form = document.querySelector('[data-permisos-filter-form]');
    const results = document.querySelector('[data-permisos-results]');
    if (!form || !results) return;

    const search = form.querySelector('[name="buscar"]');
    const scope = form.querySelector('[name="alcance"]');
    const perPage = form.querySelector('[name="per_page"]');
    const status = form.querySelector('[data-permisos-status]');
    const errorMessage = form.querySelector('[data-permisos-filter-error]');
    let timer = null;
    let controller = null;
    let requestId = 0;

    const makeUrl = () => {
        const url = new URL(form.action, window.location.origin);
        url.search = '';
        for (const [key, value] of new FormData(form)) {
            const text = String(value).trim();
            if (text !== '') url.searchParams.set(key, text);
        }
        return url;
    };

    const cancelPending = () => {
        window.clearTimeout(timer);
        requestId += 1;
        controller?.abort();
        results.setAttribute('aria-busy', 'false');
    };

    const loadResults = async (url, historyMode = 'replace') => {
        cancelPending();
        const currentId = requestId;
        controller = new AbortController();
        results.setAttribute('aria-busy', 'true');
        status.textContent = 'Cargando permisos…';
        errorMessage.classList.add('hidden');

        try {
            const response = await fetch(url, {
                headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin',
                signal: controller.signal,
            });
            if (response.status === 401 || response.redirected) {
                throw new Error('Tu sesión ha expirado. Recarga la página para continuar.');
            }

            const data = await response.json();
            if (!response.ok || typeof data.html !== 'string') {
                const validation = response.status === 422 && data.errors
                    ? Object.values(data.errors).flat().join(' ')
                    : '';
                throw new Error(validation || 'No fue posible actualizar los permisos. Intenta nuevamente.');
            }
            if (currentId !== requestId) return;

            results.innerHTML = data.html;
            status.textContent = `${data.total} ${data.total === 1 ? 'permiso encontrado' : 'permisos encontrados'}.`;
            if (historyMode === 'push') window.history.pushState({}, '', url);
            else if (historyMode === 'replace') window.history.replaceState({}, '', url);
        } catch (error) {
            if (error.name === 'AbortError' || currentId !== requestId) return;
            status.textContent = '';
            errorMessage.textContent = `${error.message || 'Ocurrió un error al consultar.'} Se conservan los resultados anteriores.`;
            errorMessage.classList.remove('hidden');
        } finally {
            if (currentId === requestId) results.setAttribute('aria-busy', 'false');
        }
    };

    search.addEventListener('input', () => {
        cancelPending();
        timer = window.setTimeout(() => loadResults(makeUrl()), 300);
    });

    form.addEventListener('submit', (event) => {
        event.preventDefault();
        loadResults(makeUrl(), 'push');
    });

    [scope, perPage].filter(Boolean).forEach((select) => {
        select.addEventListener('change', () => loadResults(makeUrl(), 'push'));
    });

    form.querySelector('[data-permisos-clear]').addEventListener('click', () => {
        search.value = '';
        if (scope) scope.value = 'todos';
        perPage.value = '15';
        loadResults(makeUrl(), 'push');
    });

    results.addEventListener('click', (event) => {
        const link = event.target.closest('[data-permisos-pagination] a[href]');
        if (!link || event.ctrlKey || event.metaKey || event.shiftKey || event.altKey || event.button > 0) return;
        event.preventDefault();
        loadResults(new URL(link.href), 'push');
    });

    window.addEventListener('popstate', () => {
        const url = new URL(window.location.href);
        search.value = url.searchParams.get('buscar') || '';
        if (scope) scope.value = url.searchParams.get('alcance') || 'todos';
        perPage.value = url.searchParams.get('per_page') || '15';
        loadResults(url, null);
    });
});
