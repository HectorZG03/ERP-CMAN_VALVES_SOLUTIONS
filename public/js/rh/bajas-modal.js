document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('baja-modal');
    const panel = document.getElementById('baja-modal-panel');
    const searchInput = document.getElementById('buscar-personal-baja');
    const resultsContainer = document.getElementById('resultados-personal-baja');
    const searchStatus = document.getElementById('estado-busqueda-personal-baja');
    const personalIdInput = document.getElementById('baja-personal-id');
    const selectedName = document.getElementById('baja-modal-person');
    const selectedEmployee = document.getElementById('baja-modal-employee');
    const form = modal?.querySelector('form');

    if (
        !modal ||
        !panel ||
        !searchInput ||
        !resultsContainer ||
        !searchStatus ||
        !personalIdInput ||
        !selectedName ||
        !selectedEmployee ||
        !form
    ) {
        return;
    }

    const minimumCharacters = 2;
    const debounceDelay = 300;

    let debounceTimer = null;
    let activeRequest = null;

    const showStatus = (message) => {
        searchStatus.textContent = message;
        searchStatus.classList.remove('hidden');
    };

    const clearStatus = () => {
        searchStatus.textContent = '';
        searchStatus.classList.add('hidden');
    };

    const clearResults = () => {
        resultsContainer.replaceChildren();
        resultsContainer.classList.add('hidden');
    };

    const clearSelection = () => {
        personalIdInput.value = '';
        selectedName.textContent = 'Selecciona un colaborador de la lista.';
        selectedEmployee.textContent = '';
    };

    const openModal = () => {
        modal.classList.remove('invisible', 'pointer-events-none', 'opacity-0');
        modal.classList.add('visible', 'pointer-events-auto', 'opacity-100');
        modal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('overflow-hidden');

        window.requestAnimationFrame(() => {
            panel.classList.remove('translate-y-2', 'scale-95', 'opacity-0');
            panel.classList.add('translate-y-0', 'scale-100', 'opacity-100');
        });

        window.setTimeout(() => searchInput.focus(), 100);
    };

    const closeModal = () => {
        modal.classList.remove('opacity-100');
        modal.classList.add('opacity-0');
        panel.classList.remove('translate-y-0', 'scale-100', 'opacity-100');
        panel.classList.add('translate-y-2', 'scale-95', 'opacity-0');

        window.setTimeout(() => {
            modal.classList.remove('visible', 'pointer-events-auto');
            modal.classList.add('invisible', 'pointer-events-none');
            modal.setAttribute('aria-hidden', 'true');
            document.body.classList.remove('overflow-hidden');
        }, 200);
    };

    const formatDetails = (person) => {
        return [
            person.employee_id,
            person.puesto,
            person.departamento,
            person.area,
        ].filter(Boolean).join(' · ');
    };

    const selectPerson = (person) => {
        personalIdInput.value = person.id;
        selectedName.textContent = person.nombre || 'Colaborador seleccionado';
        selectedEmployee.textContent = formatDetails(person);

        searchInput.value = person.nombre || '';
        clearResults();
        clearStatus();
    };

    const renderResults = (people) => {
        clearResults();

        if (!people.length) {
            showStatus('No se encontraron colaboradores activos con esos datos.');
            return;
        }

        people.forEach((person) => {
            const option = document.createElement('button');

            option.type = 'button';
            option.className =
                'block w-full border-b border-gray-100 px-4 py-3 text-left transition last:border-b-0 hover:bg-red-50 focus:bg-red-50 focus:outline-none dark:border-gray-600 dark:hover:bg-gray-600 dark:focus:bg-gray-600';
            option.setAttribute('role', 'option');

            const name = document.createElement('span');
            name.className = 'block text-sm font-semibold text-gray-900 dark:text-white';
            name.textContent = person.nombre || 'Nombre no disponible';

            const details = document.createElement('span');
            details.className = 'mt-1 block text-xs text-gray-500 dark:text-gray-300';
            details.textContent = formatDetails(person) || 'Sin datos laborales disponibles';

            option.append(name, details);
            option.addEventListener('click', () => selectPerson(person));

            resultsContainer.appendChild(option);
        });

        resultsContainer.classList.remove('hidden');
        clearStatus();
    };

    const searchPeople = async () => {
        const term = searchInput.value.trim();

        clearSelection();
        clearResults();

        if (term.length < minimumCharacters) {
            if (term.length > 0) {
                showStatus('Escribe al menos dos caracteres para buscar.');
            } else {
                clearStatus();
            }

            return;
        }

        const url = new URL(searchInput.dataset.searchUrl, window.location.origin);
        url.searchParams.set('q', term);

        if (activeRequest) {
            activeRequest.abort();
        }

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
            renderResults(Array.isArray(people) ? people : []);
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

        if (activeRequest) {
            activeRequest.abort();
            activeRequest = null;
        }

        debounceTimer = window.setTimeout(searchPeople, debounceDelay);
    });

    searchInput.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            clearResults();
        }
    });

    document.querySelectorAll('.open-baja-modal').forEach((button) => {
        button.addEventListener('click', openModal);
    });

    document.querySelectorAll('.close-baja-modal').forEach((button) => {
        button.addEventListener('click', closeModal);
    });

    modal.addEventListener('click', (event) => {
        if (event.target === modal) {
            closeModal();
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && !modal.classList.contains('invisible')) {
            closeModal();
        }
    });

    form.addEventListener('submit', (event) => {
        if (!personalIdInput.value) {
            event.preventDefault();
            showStatus('Selecciona un colaborador de los resultados antes de registrar la baja.');
            searchInput.focus();
        }
    });

    if (modal.dataset.reopen === '1') {
        // Si la validación devuelve al formulario, hay que volver a seleccionar al colaborador.
        clearSelection();
        openModal();
    }
});