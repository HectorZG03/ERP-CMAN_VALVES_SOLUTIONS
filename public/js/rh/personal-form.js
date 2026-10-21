document.addEventListener('DOMContentLoaded', () => {
    const areaSelect = document.getElementById('area_id');
    const departamentoSelect = document.getElementById('departamento_id');
    const puestoSelect = document.getElementById('puesto_id');
    const catalogsElement = document.getElementById('personal-catalogs');

    if (!areaSelect || !departamentoSelect || !puestoSelect || !catalogsElement) {
        return;
    }

    let areas;

    try {
        areas = JSON.parse(catalogsElement.textContent);
    } catch (error) {
        console.error('No se pudieron cargar los catálogos de Recursos Humanos.', error);
        return;
    }

    const initialDepartamentoId = departamentoSelect.dataset.selected || '';
    const initialPuestoId = puestoSelect.dataset.selected || '';

    const resetSelect = (select, placeholder) => {
        select.innerHTML = '';
        const option = document.createElement('option');
        option.value = '';
        option.textContent = placeholder;
        select.appendChild(option);
        select.disabled = true;
    };

    const appendOptions = (select, items, selectedId, placeholder) => {
        resetSelect(select, placeholder);

        items.forEach((item) => {
            const option = document.createElement('option');
            option.value = item.id;
            option.textContent = item.nombre;

            if (String(item.id) === String(selectedId)) {
                option.selected = true;
            }

            select.appendChild(option);
        });

        select.disabled = items.length === 0;
    };

    const selectedArea = () =>
        areas.find((area) => String(area.id) === String(areaSelect.value));

    const updateDepartamentos = (selectedId = '') => {
        const area = selectedArea();
        const departamentos = area?.departamentos ?? [];

        appendOptions(
            departamentoSelect,
            departamentos,
            selectedId,
            area ? 'Selecciona un departamento' : 'Primero selecciona un área'
        );

        updatePuestos('');
    };

    const updatePuestos = (selectedId = '') => {
        const area = selectedArea();
        const departamento = area?.departamentos?.find(
            (item) => String(item.id) === String(departamentoSelect.value)
        );
        const puestos = departamento?.puestos ?? [];

        appendOptions(
            puestoSelect,
            puestos,
            selectedId,
            departamento ? 'Selecciona un puesto' : 'Primero selecciona un departamento'
        );
    };

    areaSelect.addEventListener('change', () => updateDepartamentos());
    departamentoSelect.addEventListener('change', () => updatePuestos());

    if (areaSelect.value) {
        updateDepartamentos(initialDepartamentoId);

        if (departamentoSelect.value) {
            updatePuestos(initialPuestoId);
        }
    }
});