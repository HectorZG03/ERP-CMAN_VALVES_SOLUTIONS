document.addEventListener('DOMContentLoaded', () => {
    const config = window.valeppEditConfig;
    const app = document.getElementById('valepp-edit-app');
    const form = document.getElementById('valepp-edit-form');
    const rows = document.getElementById('valepp-edit-rows');
    const productSelect = document.getElementById('producto-disponible');
    const addButton = document.getElementById('agregar-producto-btn');
    const status = document.getElementById('valepp-edit-status');
    const saveButton = document.getElementById('guardar-vale-btn');

    if (!config || !app || !form || !rows || !productSelect || !addButton) {
        return;
    }

    const catalog = new Map();
    const selected = new Map();

    const showStatus = (message, isError = false) => {
        status.textContent = message;
        status.classList.remove('hidden', 'text-red-600', 'text-gray-500');
        status.classList.add(isError ? 'text-red-600' : 'text-gray-500');
    };

    const makeCell = (className = '') => {
        const cell = document.createElement('td');
        cell.className = `px-5 py-4 ${className}`.trim();
        return cell;
    };

    const refreshNames = () => {
        [...rows.querySelectorAll('tr')].forEach((row, index) => {
            row.querySelector('[data-detail-id]').name =
                `detalles[${index}][solicitud_material_detalle_id]`;
            row.querySelector('[data-quantity]').name =
                `detalles[${index}][cantidad]`;
        });
    };

    const refreshProductSelect = () => {
        const currentValue = productSelect.value;
        productSelect.replaceChildren();

        const placeholder = document.createElement('option');
        placeholder.value = '';
        placeholder.textContent = 'Selecciona un producto';
        productSelect.appendChild(placeholder);

        const available = [...catalog.values()].filter((item) => {
            return item.cantidad_disponible > 0 && !selected.has(String(item.id));
        });

        available.forEach((item) => {
            const option = document.createElement('option');
            option.value = String(item.id);
            option.textContent =
                `${item.nombre_producto} · disponibles: ${item.cantidad_disponible}`;
            productSelect.appendChild(option);
        });

        productSelect.disabled = available.length === 0;
        addButton.disabled = available.length === 0;

        if (available.some((item) => String(item.id) === currentValue)) {
            productSelect.value = currentValue;
        }

        if (available.length === 0) {
            placeholder.textContent = 'No hay otros productos disponibles';
        }
    };

    const addRow = (item, quantity) => {
        const id = String(item.id);

        if (selected.has(id)) {
            return;
        }

        const row = document.createElement('tr');
        row.dataset.detailId = id;

        const productCell = makeCell();
        const productName = document.createElement('p');
        productName.className = 'font-semibold text-gray-900 dark:text-white';
        productName.textContent = item.nombre_producto || 'Producto sin nombre';

        const productMeta = document.createElement('p');
        productMeta.className = 'mt-1 text-xs text-gray-500 dark:text-gray-400';
        productMeta.textContent = [
            item.economico || 'Sin económico',
            item.categoria || 'Sin categoría',
            item.medida || 'Sin unidad',
        ].join(' · ');

        productCell.append(productName, productMeta);

        const requestedCell = makeCell('text-center text-sm text-gray-700 dark:text-gray-300');
        requestedCell.textContent = item.cantidad_solicitada ?? '—';

        const maxCell = makeCell('text-center text-sm font-semibold text-orange-700 dark:text-orange-300');
        maxCell.textContent = item.cantidad_disponible ?? quantity;

        const quantityCell = makeCell('text-center');
        const hiddenId = document.createElement('input');
        hiddenId.type = 'hidden';
        hiddenId.dataset.detailId = '';
        hiddenId.value = id;

        const quantityInput = document.createElement('input');
        quantityInput.type = 'number';
        quantityInput.min = '1';
        quantityInput.max = String(item.cantidad_disponible ?? quantity);
        quantityInput.required = true;
        quantityInput.value = String(quantity);
        quantityInput.dataset.quantity = '';
        quantityInput.className =
            'w-24 rounded-lg border-gray-300 text-center text-sm focus:border-orange-500 focus:ring-orange-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white';

        quantityInput.addEventListener('input', () => {
            if (Number(quantityInput.value) > Number(quantityInput.max)) {
                showStatus(
                    `La cantidad máxima para ${item.nombre_producto} es ${quantityInput.max}.`,
                    true
                );
            } else {
                status.classList.add('hidden');
            }
        });

        quantityCell.appendChild(hiddenId);
        quantityCell.appendChild(quantityInput);

        const actionCell = makeCell('text-right');
        const removeButton = document.createElement('button');
        removeButton.type = 'button';
        removeButton.textContent = 'Quitar';
        removeButton.className =
            'rounded-lg border border-red-200 px-3 py-2 text-xs font-semibold text-red-700 hover:bg-red-50 dark:border-red-800 dark:text-red-300 dark:hover:bg-red-900/20';

        removeButton.addEventListener('click', () => {
            selected.delete(id);
            row.remove();
            refreshNames();
            refreshProductSelect();

            if (rows.children.length === 0) {
                showStatus('El vale debe conservar al menos un producto.', true);
            } else {
                status.classList.add('hidden');
            }
        });

        actionCell.appendChild(removeButton);
        row.append(productCell, requestedCell, maxCell, quantityCell, actionCell);
        rows.appendChild(row);
        selected.set(id, true);

        refreshNames();
        refreshProductSelect();
    };

    const loadCatalog = async () => {
        try {
            const url = new URL(config.detailUrl, window.location.origin);
            url.searchParams.set('valepp_id', config.valeppId);

            const response = await fetch(url, {
                headers: { Accept: 'application/json' },
                credentials: 'same-origin',
            });

            if (!response.ok) {
                throw new Error('No se pudieron cargar los productos de la solicitud.');
            }

            const data = await response.json();
            const details = data.solicitud?.detalles || [];

            details
                .filter((item) =>
                    String(item.categoria || '').trim().toUpperCase() === 'SEGURIDAD'
                )
                .forEach((item) => catalog.set(String(item.id), item));

            const assignments = Array.isArray(config.assignments)
                ? config.assignments
                : [];

            assignments.forEach((assignment) => {
                const id = String(assignment.solicitud_material_detalle_id);
                const currentItem = catalog.get(id);
                const oldItem = config.existingProducts?.[id];

                if (currentItem) {
                    addRow(currentItem, Number(assignment.cantidad));
                } else if (oldItem) {
                    // Deja visibles renglones antiguos para poder quitarlos o reducirlos.
                    addRow(
                        {
                            ...oldItem,
                            cantidad_solicitada: '—',
                            cantidad_disponible: Number(assignment.cantidad),
                        },
                        Number(assignment.cantidad)
                    );
                }
            });

            refreshProductSelect();

            if (rows.children.length === 0) {
                showStatus('Agrega al menos un producto disponible para guardar el vale.');
            } else {
                status.classList.add('hidden');
            }
        } catch (error) {
            showStatus(error.message || 'Error al cargar los productos.', true);
            productSelect.disabled = true;
            addButton.disabled = true;
        }
    };

    addButton.addEventListener('click', () => {
        const item = catalog.get(productSelect.value);

        if (!item) {
            showStatus('Selecciona un producto disponible.', true);
            return;
        }

        addRow(item, 1);
        status.classList.add('hidden');
    });

    form.addEventListener('submit', (event) => {
        if (rows.children.length === 0) {
            event.preventDefault();
            showStatus('El vale debe conservar al menos un producto.', true);
            return;
        }

        const invalidQuantity = [...rows.querySelectorAll('[data-quantity]')]
            .some((input) => !input.checkValidity());

        if (invalidQuantity) {
            event.preventDefault();
            showStatus('Revisa las cantidades: deben ser mayores a cero y no exceder el máximo disponible.', true);
        }
    });

    loadCatalog();
});