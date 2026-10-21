document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('search');
    const filterInput = document.getElementById('filter-input');
    const perPageSelect = document.getElementById('per-page');
    const inventoryTable = document.getElementById('inventory-table');
    const resultCounter = document.getElementById('result-counter');
    const searchForm = document.getElementById('search-form');
    const paginationContainer = document.getElementById('pagination-container');
    const clearSearchButton = document.getElementById('clear-search');
    const clearFiltersButton = document.getElementById('clear-filters');

    if (!searchInput || !filterInput || !perPageSelect || !inventoryTable ||
        !resultCounter || !searchForm || !paginationContainer) {
        return;
    }

    const allowedPerPage = [15, 25, 50, 100];
    let searchTimeout = null;
    let activeRequest = null;
    let requestSequence = 0;
    let currentSearch = searchInput.value.trim();
    let currentFilter = filterInput.value || 'all';
    let currentPage = Math.max(1, Number(new URL(window.location.href).searchParams.get('page')) || 1);
    let currentPerPage = allowedPerPage.includes(Number(perPageSelect.value))
        ? Number(perPageSelect.value)
        : 15;

    const csrfToken = searchForm.dataset.csrfToken;
    const ajaxUrl = searchForm.dataset.ajaxUrl;

    function updateClearControls() {
        if (clearSearchButton) clearSearchButton.hidden = currentSearch.length === 0;
        if (clearFiltersButton) {
            clearFiltersButton.hidden = currentFilter === 'all' && currentSearch.length === 0;
        }
    }

    function updateUrl(page) {
        const url = new URL(window.location.href);
        url.searchParams.set('filter', currentFilter);
        url.searchParams.set('per_page', String(currentPerPage));
        if (currentSearch) url.searchParams.set('search', currentSearch);
        else url.searchParams.delete('search');
        if (page > 1) url.searchParams.set('page', String(page));
        else url.searchParams.delete('page');

        window.history.replaceState({}, '', url.pathname + url.search + url.hash);
        updateClearControls();
    }

    function setLoading() {
        inventoryTable.innerHTML =
            '<tr><td colspan="7" class="px-6 py-12 text-center text-sm text-gray-500 dark:text-gray-400">' +
            '<span class="inline-flex items-center gap-3">' +
            '<span class="h-5 w-5 animate-spin rounded-full border-2 border-gray-300 border-t-blue-600 dark:border-gray-600 dark:border-t-blue-400"></span>' +
            'Cargando productos...</span></td></tr>';
    }

    function setError() {
        inventoryTable.innerHTML =
            '<tr><td colspan="7" class="px-6 py-12 text-center text-sm text-red-600 dark:text-red-400">' +
            'No fue posible cargar los productos. Intenta nuevamente.</td></tr>';
        resultCounter.textContent = 'Error al cargar los resultados';
        paginationContainer.replaceChildren();
    }

    function makePageButton(label, page, options) {
        options = options || {};
        const button = document.createElement('button');
        button.type = 'button';
        button.dataset.page = String(page);
        button.textContent = label;
        button.setAttribute('aria-label', options.ariaLabel || label);
        button.disabled = Boolean(options.disabled);
        if (options.active) button.setAttribute('aria-current', 'page');

        button.className = 'inline-flex min-w-9 items-center justify-center rounded-md border px-3 py-1.5 text-sm transition ' +
            (options.active
                ? 'border-blue-600 bg-blue-600 font-semibold text-white dark:border-blue-500 dark:bg-blue-500'
                : 'border-gray-300 bg-white text-gray-700 hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-40 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700');
        return button;
    }

    function renderPagination(page, lastPage) {
        paginationContainer.replaceChildren();
        if (!lastPage || lastPage <= 1) return;

        const nav = document.createElement('nav');
        nav.className = 'flex flex-wrap items-center justify-center gap-1';
        nav.setAttribute('aria-label', 'Paginación de inventario');
        nav.append(makePageButton('‹', Math.max(1, page - 1), {
            disabled: page <= 1,
            ariaLabel: 'Página anterior',
        }));

        const start = Math.max(1, page - 2);
        const end = Math.min(lastPage, page + 2);
        if (start > 1) {
            nav.append(makePageButton('1', 1, { active: page === 1 }));
            if (start > 2) {
                const dots = document.createElement('span');
                dots.className = 'px-2 text-gray-400';
                dots.textContent = '…';
                nav.append(dots);
            }
        }

        for (let number = start; number <= end; number += 1) {
            if ((number === 1 && start > 1) || (number === lastPage && end < lastPage)) continue;
            nav.append(makePageButton(String(number), number, { active: number === page }));
        }

        if (end < lastPage) {
            if (end < lastPage - 1) {
                const dots = document.createElement('span');
                dots.className = 'px-2 text-gray-400';
                dots.textContent = '…';
                nav.append(dots);
            }
            nav.append(makePageButton(String(lastPage), lastPage, { active: page === lastPage }));
        }

        nav.append(makePageButton('›', Math.min(lastPage, page + 1), {
            disabled: page >= lastPage,
            ariaLabel: 'Página siguiente',
        }));
        paginationContainer.append(nav);
    }

    function syncFilterCards() {
        const styles = {
            all: ['border-blue-500', 'ring-1', 'ring-blue-500/20', 'dark:border-blue-400'],
            'with-stock': ['border-green-500', 'ring-1', 'ring-green-500/20', 'dark:border-green-400'],
            'without-stock': ['border-red-500', 'ring-1', 'ring-red-500/20', 'dark:border-red-400'],
        };
        const ids = {
            all: 'filter-all',
            'with-stock': 'filter-stock',
            'without-stock': 'filter-no-stock',
        };
        const inactive = ['border-gray-200', 'dark:border-gray-700'];

        Object.entries(ids).forEach(([filter, id]) => {
            const card = document.getElementById(id);
            if (!card) return;
            Object.values(styles).flat().concat(inactive).forEach((className) => {
                card.classList.remove(className);
            });
            card.classList.add(...(filter === currentFilter ? styles[filter] : inactive));
        });
    }

    async function loadProducts(page) {
        if (!ajaxUrl || !csrfToken) {
            setError();
            return;
        }

        if (activeRequest) activeRequest.abort();
        activeRequest = new AbortController();
        const requestId = ++requestSequence;
        currentPage = Math.max(1, Number(page) || 1);
        setLoading();

        try {
            const response = await fetch(ajaxUrl, {
                method: 'POST',
                signal: activeRequest.signal,
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify({
                    search: currentSearch,
                    filter: currentFilter,
                    per_page: currentPerPage,
                    page: currentPage,
                }),
            });

            if (!response.ok) throw new Error('HTTP ' + response.status);
            const data = await response.json();
            if (requestId !== requestSequence) return;
            if (!data || typeof data.html !== 'string') {
                throw new Error('La respuesta del servidor no tiene el formato esperado.');
            }

            inventoryTable.innerHTML = data.html;
            resultCounter.textContent = data.total > 0
                ? 'Mostrando ' + data.from + '–' + data.to + ' de ' + data.total + ' productos'
                : 'No hay productos para mostrar';
            currentPage = Number(data.current_page) || 1;
            renderPagination(currentPage, Number(data.last_page) || 1);
            syncFilterCards();
            updateUrl(currentPage);
        } catch (error) {
            if (error.name === 'AbortError') return;
            console.error('Error al cargar inventario:', error);
            if (requestId === requestSequence) setError();
        }
    }

    function scheduleSearch() {
        currentSearch = searchInput.value.trim();
        currentPage = 1;
        updateClearControls();
        window.clearTimeout(searchTimeout);
        searchTimeout = window.setTimeout(() => loadProducts(1), 350);
    }

    searchForm.addEventListener('submit', (event) => {
        event.preventDefault();
        currentSearch = searchInput.value.trim();
        currentPage = 1;
        window.clearTimeout(searchTimeout);
        loadProducts(1);
    });

    searchInput.addEventListener('input', scheduleSearch);
    searchInput.addEventListener('keydown', (event) => {
        if (event.key !== 'Enter') return;
        event.preventDefault();
        window.clearTimeout(searchTimeout);
        currentSearch = searchInput.value.trim();
        loadProducts(1);
    });

    if (clearSearchButton) {
        clearSearchButton.addEventListener('click', () => {
            searchInput.value = '';
            currentSearch = '';
            currentPage = 1;
            loadProducts(1);
            searchInput.focus();
        });
    }

    if (clearFiltersButton) {
        clearFiltersButton.addEventListener('click', () => {
            currentFilter = 'all';
            currentSearch = '';
            currentPage = 1;
            filterInput.value = 'all';
            searchInput.value = '';
            loadProducts(1);
        });
    }

    perPageSelect.addEventListener('change', () => {
        const requested = Number(perPageSelect.value);
        currentPerPage = allowedPerPage.includes(requested) ? requested : 15;
        currentPage = 1;
        loadProducts(1);
    });

    paginationContainer.addEventListener('click', (event) => {
        const button = event.target.closest('button[data-page]');
        if (button) {
            if (!button.disabled) loadProducts(Number(button.dataset.page));
            return;
        }

        // Intercepta los enlaces del paginador inicial de Laravel.
        const link = event.target.closest('a[href]');
        if (!link) return;
        const targetUrl = new URL(link.href, window.location.href);
        if (targetUrl.origin !== window.location.origin) return;
        const page = Number(targetUrl.searchParams.get('page'));
        if (!Number.isInteger(page) || page < 1) return;

        event.preventDefault();
        loadProducts(page);
    });

    [
        ['filter-all', 'all'],
        ['filter-stock', 'with-stock'],
        ['filter-no-stock', 'without-stock'],
    ].forEach(([id, filter]) => {
        const card = document.getElementById(id);
        if (!card) return;
        card.addEventListener('click', (event) => {
            event.preventDefault();
            currentFilter = filter;
            filterInput.value = filter;
            currentPage = 1;
            loadProducts(1);
        });
    });

    updateClearControls();
});

