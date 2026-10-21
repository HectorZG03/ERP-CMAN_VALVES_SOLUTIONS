document.addEventListener('DOMContentLoaded', () => {
    const root = document.getElementById('personal-index');
    const form = document.getElementById('personal-search-form');

    if (!root || !form) return;

    const searchInput = document.getElementById('personal-search');
    const filterInput = document.getElementById('personal-filter-input');
    const resultsRegion = document.getElementById('personal-results-region');
    const indexUrl = root.dataset.indexUrl;

    if (!searchInput || !filterInput || !resultsRegion || !indexUrl) return;

    let debounceTimer = null;
    let activeRequest = null;

    const activeStyles = {
        all: 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-200',
        activo: 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-200',
        baja: 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-200',
        suspendido: 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-200',
    };

    const inactiveStyles =
        'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600';

    function updateFilterStyles(selectedFilter) {
        document.querySelectorAll('[data-status-filter]').forEach((link) => {
            const isSelected = link.dataset.statusFilter === selectedFilter;
            const status = link.dataset.statusFilter;

            link.className =
                'status-filter rounded-full px-3 py-1 text-xs font-semibold transition ' +
                (isSelected ? activeStyles[status] : inactiveStyles);

            link.setAttribute('aria-current', isSelected ? 'true' : 'false');
        });
    }

    function makeUrl({ search = searchInput.value, filter = filterInput.value, page = null } = {}) {
        const url = new URL(indexUrl, window.location.origin);
        const params = new URLSearchParams();

        if (search.trim()) params.set('search', search.trim());
        if (filter && filter !== 'all') params.set('filter', filter);
        if (page) params.set('page', page);

        url.search = params.toString();
        return url;
    }

    async function loadResults(url, updateHistory = true) {
        activeRequest?.abort();
        activeRequest = new AbortController();

        root.classList.add('opacity-60', 'pointer-events-none');

        try {
            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'Accept': 'text/html',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin',
                signal: activeRequest.signal,
            });

            if (!response.ok) {
                throw new Error(`La búsqueda respondió con estado ${response.status}.`);
            }

            const html = await response.text();
            const parsedDocument = new DOMParser().parseFromString(html, 'text/html');
            const newResults = parsedDocument.querySelector('#personal-results-region');

            if (!newResults) {
                throw new Error('No se encontró la sección de resultados en la respuesta.');
            }

            resultsRegion.innerHTML = newResults.innerHTML;

            const responseUrl = new URL(response.url);
            searchInput.value = responseUrl.searchParams.get('search') || '';
            filterInput.value = responseUrl.searchParams.get('filter') || 'all';
            updateFilterStyles(filterInput.value);

            if (updateHistory) {
                window.history.replaceState({}, '', responseUrl);
            }
        } catch (error) {
            if (error.name !== 'AbortError') {
                console.error('No se pudo actualizar el directorio de personal:', error);
                form.submit();
            }
        } finally {
            root.classList.remove('opacity-60', 'pointer-events-none');
        }
    }

    form.addEventListener('submit', (event) => {
        event.preventDefault();
        window.clearTimeout(debounceTimer);
        loadResults(makeUrl());
    });

    searchInput.addEventListener('input', () => {
        window.clearTimeout(debounceTimer);
        debounceTimer = window.setTimeout(() => {
            loadResults(makeUrl());
        }, 350);
    });

    document.querySelectorAll('[data-status-filter]').forEach((link) => {
        link.addEventListener('click', (event) => {
            event.preventDefault();

            filterInput.value = link.dataset.statusFilter || 'all';
            updateFilterStyles(filterInput.value);
            loadResults(makeUrl());
        });
    });

    root.addEventListener('click', (event) => {
        const paginationLink = event.target.closest('#personal-pagination a');

        if (!paginationLink) return;

        event.preventDefault();
        loadResults(new URL(paginationLink.href), true);
    });

    window.addEventListener('popstate', () => {
        const url = new URL(window.location.href);

        searchInput.value = url.searchParams.get('search') || '';
        filterInput.value = url.searchParams.get('filter') || 'all';
        updateFilterStyles(filterInput.value);
        loadResults(url, false);
    });

    updateFilterStyles(filterInput.value || 'all');
});