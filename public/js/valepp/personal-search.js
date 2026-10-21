document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('buscar-personal');
    const personalIdInput = document.getElementById('personal_id');
    const combo = document.getElementById('personal-combobox');
    const optionsList = document.getElementById('personal-options');
    const clearButton = document.getElementById('limpiar-personal');
    const resultsStatus = document.getElementById('personal-search-status');
    const noResults = document.getElementById('personal-no-results');

    if (!searchInput || !personalIdInput || !combo || !optionsList || !clearButton || !resultsStatus || !noResults) {
        return;
    }

    const options = Array.from(optionsList.querySelectorAll('[data-personal-option]'));
    let filteredOptions = [];
    let activeIndex = -1;

    const normalize = (value) => String(value || '')
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .toLocaleLowerCase('es')
        .trim();

    const selectedOption = () => options.find(
        (option) => option.dataset.personalId === personalIdInput.value
    );

    const updateClearButton = () => {
        clearButton.classList.toggle('hidden', !personalIdInput.value);
        clearButton.classList.toggle('inline-flex', Boolean(personalIdInput.value));
    };

    const openOptions = () => {
        optionsList.classList.remove('hidden');
        searchInput.setAttribute('aria-expanded', 'true');
    };

    const closeOptions = () => {
        optionsList.classList.add('hidden');
        searchInput.setAttribute('aria-expanded', 'false');
        activeIndex = -1;
    };

    const filterOptions = (query = searchInput.value) => {
        const term = normalize(query);
        const hasQuery = term.length > 0;
        filteredOptions = [];

        options.forEach((option) => {
            const text = normalize(`${option.dataset.search || ''} ${option.dataset.label || ''}`);
            const matches = hasQuery && text.includes(term);
            option.hidden = !matches;
            option.setAttribute('aria-selected', String(option.dataset.personalId === personalIdInput.value));

            if (matches) filteredOptions.push(option);
        });

        noResults.classList.toggle('hidden', !hasQuery || filteredOptions.length > 0);
        noResults.textContent = options.length === 0
            ? 'No hay personal activo disponible.'
            : 'No se encontraron colaboradores con esa búsqueda.';

        if (options.length === 0) {
            resultsStatus.textContent = 'No hay personal activo disponible para seleccionar.';
        } else if (!hasQuery) {
            resultsStatus.textContent = 'Escribe para buscar un colaborador.';
        } else if (filteredOptions.length === 0) {
            resultsStatus.textContent = 'No se encontraron colaboradores con esa búsqueda.';
        } else {
            resultsStatus.textContent = `${filteredOptions.length} coincidencia(s).`;
        }

        activeIndex = -1;
    };

    const selectOption = (option) => {
        if (!option) return;

        personalIdInput.value = option.dataset.personalId || '';
        searchInput.value = option.dataset.label || '';
        option.setAttribute('aria-selected', 'true');
        updateClearButton();
        closeOptions();
        resultsStatus.textContent = `Seleccionado: ${option.dataset.label || ''}`;
        personalIdInput.dispatchEvent(new Event('change', { bubbles: true }));
    };

    const clearSelection = () => {
        personalIdInput.value = '';
        searchInput.value = '';
        updateClearButton();
        filterOptions('');
        closeOptions();
        searchInput.focus();
        personalIdInput.dispatchEvent(new Event('change', { bubbles: true }));
    };

    searchInput.addEventListener('focus', () => {
        const selected = selectedOption();
        const hasSelectedLabel = selected
            && normalize(searchInput.value) === normalize(selected.dataset.label);
        const query = hasSelectedLabel ? '' : searchInput.value;

        filterOptions(query);
        if (normalize(query)) {
            openOptions();
        } else {
            closeOptions();
        }

        if (hasSelectedLabel) searchInput.select();
    });

    searchInput.addEventListener('input', () => {
        const selected = selectedOption();

        if (selected && normalize(searchInput.value) !== normalize(selected.dataset.label)) {
            personalIdInput.value = '';
            updateClearButton();
            personalIdInput.dispatchEvent(new Event('change', { bubbles: true }));
        }

        if (normalize(searchInput.value)) {
            openOptions();
        } else {
            closeOptions();
        }
        filterOptions(searchInput.value);
    });

    optionsList.addEventListener('click', (event) => {
        const option = event.target.closest('[data-personal-option]');
        if (option) selectOption(option);
    });

    optionsList.addEventListener('mousemove', (event) => {
        activeIndex = filteredOptions.indexOf(event.target.closest('[data-personal-option]'));
    });

    searchInput.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            closeOptions();
            searchInput.value = selectedOption()?.dataset.label || '';
            return;
        }

        if (event.key === 'ArrowDown' || event.key === 'ArrowUp') {
            event.preventDefault();
            if (optionsList.classList.contains('hidden')) openOptions();
            if (!filteredOptions.length) return;

            const direction = event.key === 'ArrowDown' ? 1 : -1;
            activeIndex = activeIndex === -1
                ? (direction === 1 ? 0 : filteredOptions.length - 1)
                : (activeIndex + direction + filteredOptions.length) % filteredOptions.length;
            filteredOptions[activeIndex].focus();
            return;
        }

        if (event.key === 'Enter' && !optionsList.classList.contains('hidden')) {
            const option = filteredOptions[activeIndex]
                || (filteredOptions.length === 1 ? filteredOptions[0] : null);
            if (option) {
                event.preventDefault();
                selectOption(option);
            }
        }
    });

    optionsList.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            closeOptions();
            searchInput.focus();
            return;
        }

        if (event.key === 'ArrowDown' || event.key === 'ArrowUp') {
            event.preventDefault();
            const direction = event.key === 'ArrowDown' ? 1 : -1;
            activeIndex = (filteredOptions.indexOf(event.target) + direction + filteredOptions.length)
                % filteredOptions.length;
            filteredOptions[activeIndex]?.focus();
        }
    });

    clearButton.addEventListener('click', clearSelection);

    document.addEventListener('click', (event) => {
        if (!combo.contains(event.target)) closeOptions();
    });

    const initialSelection = selectedOption();
    if (initialSelection) searchInput.value = initialSelection.dataset.label || '';

    updateClearButton();
    filterOptions('');
    closeOptions();
});
