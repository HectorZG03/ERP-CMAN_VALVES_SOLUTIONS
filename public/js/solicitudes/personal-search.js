document.addEventListener('DOMContentLoaded', () => {
    const combo = document.getElementById('personal-combobox-solicitud');
    const searchInput = document.getElementById('buscar-personal-solicitud');
    const personalIdInput = document.getElementById('personal_id');
    const results = document.getElementById('resultados-personal-solicitud');
    const status = document.getElementById('estado-personal-solicitud');
    const clearButton = document.getElementById('limpiar-personal-solicitud');

    if (!combo || !searchInput || !personalIdInput || !results || !status || !clearButton) {
        return;
    }

    const minimumCharacters = 2;
    const debounceDelay = 300;

    let debounceTimer = null;
    let activeRequest = null;
    let currentResults = [];
    let activeIndex = -1;

    const showStatus = (message) => {
        status.textContent = message;
    };

    const clearResults = () => {
        results.replaceChildren();
        results.classList.add('hidden');
        searchInput.setAttribute('aria-expanded', 'false');
        currentResults = [];
        activeIndex = -1;
    };

    const updateClearButton = () => {
        const hasSelection = Boolean(personalIdInput.value);

        clearButton.classList.toggle('hidden', !hasSelection);
        clearButton.classList.toggle('inline-flex', hasSelection);
    };

    const clearSelection = ({ focus = false } = {}) => {
        personalIdInput.value = '';
        searchInput.value = '';
        updateClearButton();
        clearResults();
        showStatus('Escribe al menos dos caracteres para buscar.');

        personalIdInput.dispatchEvent(new Event('change', { bubbles: true }));

        if (focus) {
            searchInput.focus();
        }
    };

    const buildLabel = (person) => {
        return [
            person.nombre,
            person.employee_id,
            person.puesto,
            person.area,
        ].filter(Boolean).join(' · ');
    };

    const selectPerson = (person) => {
        personalIdInput.value = String(person.id);
        searchInput.value = buildLabel(person);
        updateClearButton();
        clearResults();

        showStatus(`Colaborador seleccionado: ${person.nombre}.`);
        personalIdInput.dispatchEvent(new Event('change', { bubbles: true }));
    };

    const setActiveOption = (index) => {
        const options = Array.from(results.querySelectorAll('[role="option"]'));

        if (!options.length) {
            activeIndex = -1;
            return;
        }

        activeIndex = (index + options.length) % options.length;

        options.forEach((option, optionIndex) => {
            const isActive = optionIndex === activeIndex;

            option.setAttribute('aria-selected', String(isActive));
            option.classList.toggle('bg-blue-50', isActive);
            option.classList.toggle('dark:bg-gray-700', isActive);
        });

        options[activeIndex].scrollIntoView({ block: 'nearest' });
    };

    const renderResults = (people) => {
        results.replaceChildren();
        currentResults = Array.isArray(people) ? people : [];
        activeIndex = -1;

        if (currentResults.length === 0) {
            results.classList.add('hidden');
            searchInput.setAttribute('aria-expanded', 'false');
            showStatus('No se encontraron colaboradores activos con esa búsqueda.');
            return;
        }

        currentResults.forEach((person, index) => {
            const option = document.createElement('button');
            option.type = 'button';
            option.id = `personal-solicitud-opcion-${index}`;
            option.setAttribute('role', 'option');
            option.setAttribute('aria-selected', 'false');
            option.className =
                'block w-full rounded-md px-3 py-2.5 text-left transition '
                + 'hover:bg-blue-50 focus:bg-blue-50 focus:outline-none '
                + 'dark:hover:bg-gray-700 dark:focus:bg-gray-700';

            const name = document.createElement('span');
            name.className = 'block text-sm font-semibold text-gray-900 dark:text-white';
            name.textContent = person.nombre || 'Nombre no disponible';

            const details = document.createElement('span');
            details.className = 'mt-0.5 block text-xs text-gray-500 dark:text-gray-400';
            details.textContent = [
                person.employee_id || 'Sin número de empleado',
                person.puesto || 'Sin puesto',
                person.area || 'Sin área',
            ].join(' · ');

            option.append(name, details);

            option.addEventListener('click', () => {
                selectPerson(person);
            });

            option.addEventListener('keydown', (event) => {
                if (event.key === 'ArrowDown') {
                    event.preventDefault();
                    setActiveOption(index + 1);
                    results.querySelectorAll('[role="option"]')[activeIndex]?.focus();
                } else if (event.key === 'ArrowUp') {
                    event.preventDefault();

                    if (index === 0) {
                        searchInput.focus();
                        return;
                    }

                    setActiveOption(index - 1);
                    results.querySelectorAll('[role="option"]')[activeIndex]?.focus();
                } else if (event.key === 'Escape') {
                    event.preventDefault();
                    clearResults();
                    searchInput.focus();
                }
            });

            results.appendChild(option);
        });

        results.classList.remove('hidden');
        searchInput.setAttribute('aria-expanded', 'true');
        showStatus(`${currentResults.length} coincidencia(s).`);
    };

    const searchPeople = async () => {
        const term = searchInput.value.trim();

        if (activeRequest) {
            activeRequest.abort();
            activeRequest = null;
        }

        clearResults();

        if (term.length < minimumCharacters) {
            showStatus(
                term.length === 0
                    ? 'Escribe al menos dos caracteres para buscar.'
                    : 'Escribe un carácter más para buscar.'
            );
            return;
        }

        const url = new URL(searchInput.dataset.searchUrl, window.location.origin);
        url.searchParams.set('q', term);

        activeRequest = new AbortController();
        showStatus('Buscando colaboradores...');

        try {
            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin',
                signal: activeRequest.signal,
            });

            if (!response.ok) {
                throw new Error('No se pudo completar la búsqueda.');
            }

            const people = await response.json();
            renderResults(people);
        } catch (error) {
            if (error.name === 'AbortError') {
                return;
            }

            clearResults();
            showStatus('Ocurrió un error al buscar. Intenta nuevamente.');
        }
    };

    searchInput.addEventListener('input', () => {
        window.clearTimeout(debounceTimer);

        // Si cambia el texto de una selección, exige elegir otra coincidencia.
        if (personalIdInput.value) {
            personalIdInput.value = '';
            updateClearButton();
            personalIdInput.dispatchEvent(new Event('change', { bubbles: true }));
        }

        if (activeRequest) {
            activeRequest.abort();
            activeRequest = null;
        }

        debounceTimer = window.setTimeout(searchPeople, debounceDelay);
    });

    searchInput.addEventListener('keydown', (event) => {
        const options = results.querySelectorAll('[role="option"]');

        if (event.key === 'ArrowDown' && options.length > 0) {
            event.preventDefault();
            setActiveOption(0);
            options[activeIndex].focus();
        } else if (event.key === 'Escape') {
            clearResults();
        }
    });

    clearButton.addEventListener('click', () => {
        clearSelection({ focus: true });
    });

    document.addEventListener('click', (event) => {
        if (!combo.contains(event.target)) {
            clearResults();
        }
    });

    const form = combo.closest('form');

    form?.addEventListener('reset', () => {
        window.setTimeout(() => {
            updateClearButton();
            clearResults();
            showStatus(
                personalIdInput.value
                    ? 'Se conservó el colaborador previamente seleccionado.'
                    : 'Escribe al menos dos caracteres para buscar.'
            );
        }, 0);
    });

    updateClearButton();

    showStatus(
        personalIdInput.value
            ? 'Se conservó el colaborador previamente seleccionado.'
            : 'Escribe al menos dos caracteres para buscar.'
    );
});