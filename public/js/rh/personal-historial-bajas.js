document.addEventListener('DOMContentLoaded', () => {
    const root = document.getElementById('personal-bajas-history');

    if (!root) return;

    const searchInput = document.getElementById('historial-bajas-busqueda');
    const statusInput = document.getElementById('historial-bajas-estado');
    const fromInput = document.getElementById('historial-bajas-desde');
    const toInput = document.getElementById('historial-bajas-hasta');
    const clearButton = document.getElementById('historial-bajas-limpiar');
    const resultCount = document.getElementById('historial-bajas-resultados');
    const noResults = document.getElementById('historial-bajas-sin-resultados');
    const rows = [...root.querySelectorAll('[data-historial-baja]')];

    if (!searchInput || !statusInput || !fromInput || !toInput || !resultCount) {
        return;
    }

    function normalize(value) {
        return String(value || '')
            .toLocaleLowerCase('es')
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .trim();
    }

    function applyFilters() {
        const search = normalize(searchInput.value);
        const selectedStatus = statusInput.value;
        const fromDate = fromInput.value;
        const toDate = toInput.value;

        let visibleCount = 0;

        rows.forEach((row) => {
            const rowSearch = normalize(row.dataset.busqueda);
            const rowStatus = row.dataset.estado || '';
            const rowDate = row.dataset.fecha || '';

            const matchesSearch = !search || rowSearch.includes(search);
            const matchesStatus = selectedStatus === 'todas' || rowStatus === selectedStatus;
            const matchesFrom = !fromDate || (rowDate && rowDate >= fromDate);
            const matchesTo = !toDate || (rowDate && rowDate <= toDate);

            const visible = matchesSearch && matchesStatus && matchesFrom && matchesTo;

            row.hidden = !visible;

            if (visible) {
                visibleCount++;
            }
        });

        resultCount.textContent = String(visibleCount);

        if (noResults) {
            noResults.classList.toggle('hidden', visibleCount !== 0);
        }
    }

    searchInput.addEventListener('input', applyFilters);
    statusInput.addEventListener('change', applyFilters);
    fromInput.addEventListener('change', applyFilters);
    toInput.addEventListener('change', applyFilters);

    clearButton?.addEventListener('click', () => {
        searchInput.value = '';
        statusInput.value = 'todas';
        fromInput.value = '';
        toInput.value = '';
        applyFilters();
        searchInput.focus();
    });

    applyFilters();
});