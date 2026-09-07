(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {
        const app = document.getElementById('valepp-create-app');

        if (!app) {
            return;
        }

        const form = document.getElementById('valepp-form');
        const searchInput = document.getElementById('buscar-solicitud');
        const dateFilter = document.getElementById('fecha-solicitud-filtro');
        const searchButton = document.getElementById('buscar-solicitudes-btn');
        const clearButton = document.getElementById('limpiar-busqueda-btn');
        const searchStatus = document.getElementById('estado-busqueda');
        const resultsContainer = document.getElementById('resultados-solicitudes');
        const requestIdInput = document.getElementById('solicitud_material_id');
        const personSelect = document.getElementById('personal_id');
        const valeDateInput = document.getElementById('fecha_solicitud');
        const summaryFolio = document.getElementById('resumen-folio');
        const summaryDestination = document.getElementById('resumen-destino');
        const emptyDetails = document.getElementById('detalle-solicitud-vacio');
        const detailsContent = document.getElementById('detalle-solicitud-contenido');
        const detailsRows = document.getElementById('detalle-solicitud-filas');
        const hiddenDetails = document.getElementById('detalles-hidden');
        const assignmentSummary = document.getElementById('resumen-asignacion');
        const assignmentError = document.getElementById('error-asignacion');
        const saveButton = document.getElementById('guardar-vale-btn');

        const searchUrl = app.dataset.searchUrl;
        const detailUrlTemplate = app.dataset.detailUrlTemplate;
        const oldRequestId = Number(app.dataset.oldSolicitudId || 0);
        const oldAssignments = parseOldAssignments(app.dataset.oldDetalles);

        let selectedRequestId = oldRequestId || null;
        let searchTimer = null;
        let searchSequence = 0;
        let detailSequence = 0;

        function parseOldAssignments(rawValue) {
            if (!rawValue) {
                return new Map();
            }

            try {
                const details = JSON.parse(rawValue);
                const assignments = new Map();

                if (!Array.isArray(details)) {
                    return assignments;
                }

                details.forEach(function (detail) {
                    const detailId = Number(
                        detail.solicitud_material_detalle_id
                    );
                    const quantity = Number(detail.cantidad);

                    if (detailId > 0 && quantity > 0) {
                        assignments.set(detailId, quantity);
                    }
                });

                return assignments;
            } catch (error) {
                return new Map();
            }
        }

        function escapeHtml(value) {
            const element = document.createElement('div');
            element.textContent = value == null ? '' : String(value);

            return element.innerHTML;
        }

        function normalizeStatus(status) {
            if (status === 'aprobado') {
                return {
                    label: 'Aprobada',
                    classes: 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300',
                };
            }

            return {
                label: 'Pendiente',
                classes: 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-300',
            };
        }

        function showSearchStatus(message, type) {
            const colorClasses = type === 'error'
                ? [
                    'border-red-200',
                    'bg-red-50',
                    'text-red-700',
                    'dark:border-red-800',
                    'dark:bg-red-900/30',
                    'dark:text-red-300',
                ]
                : [
                    'border-blue-200',
                    'bg-blue-50',
                    'text-blue-700',
                    'dark:border-blue-800',
                    'dark:bg-blue-900/30',
                    'dark:text-blue-300',
                ];

            searchStatus.className = 'rounded-lg border px-4 py-3 text-sm';
            searchStatus.classList.add(...colorClasses);
            searchStatus.textContent = message;
        }

        function hideSearchStatus() {
            searchStatus.classList.add('hidden');
            searchStatus.textContent = '';
        }

        function renderSearchLoading() {
            resultsContainer.innerHTML = `
                <div class="rounded-lg border border-dashed border-gray-300 px-5 py-10 text-center dark:border-gray-600">
                    <p class="font-medium text-gray-700 dark:text-gray-300">
                        Consultando solicitudes EPP…
                    </p>
                </div>
            `;
        }

        function renderNoResults() {
            resultsContainer.innerHTML = `
                <div class="rounded-lg border border-dashed border-gray-300 px-5 py-10 text-center dark:border-gray-600">
                    <p class="font-medium text-gray-700 dark:text-gray-300">
                        No se encontraron solicitudes disponibles
                    </p>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Revise el ID, la fecha o los términos utilizados.
                    </p>
                </div>
            `;
        }

        function renderSearchResults(requests) {
            resultsContainer.innerHTML = '';

            if (!Array.isArray(requests) || requests.length === 0) {
                renderNoResults();
                return;
            }

            requests.forEach(function (request) {
                const status = normalizeStatus(request.estatus);
                const isSelected = Number(request.id) === selectedRequestId;
                const button = document.createElement('button');

                button.type = 'button';
                button.dataset.solicitudId = request.id;
                button.className = [
                    'block w-full rounded-lg border p-4 text-left transition',
                    isSelected
                        ? 'border-orange-500 bg-orange-50 ring-1 ring-orange-500 dark:bg-orange-900/20'
                        : 'border-gray-200 bg-white hover:border-orange-300 hover:bg-orange-50/50 dark:border-gray-700 dark:bg-gray-800 dark:hover:border-orange-700 dark:hover:bg-orange-900/10',
                ].join(' ');

                button.innerHTML = `
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="font-bold text-gray-900 dark:text-white">
                                Solicitud ${escapeHtml(request.folio)}
                            </p>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                ${escapeHtml(request.fecha || 'Sin fecha')} · ${escapeHtml(request.solicitante || 'Sin solicitante')}
                            </p>
                        </div>
                        <span class="rounded-full px-2.5 py-1 text-xs font-semibold ${status.classes}">
                            ${status.label}
                        </span>
                    </div>

                    <p class="mt-3 truncate text-sm font-medium text-gray-700 dark:text-gray-300">
                        ${escapeHtml(request.destino || 'Sin destino')}
                    </p>

                    <div class="mt-3 grid grid-cols-3 gap-2 text-center">
                        <div class="rounded bg-gray-100 px-2 py-2 dark:bg-gray-700">
                            <p class="text-xs text-gray-500 dark:text-gray-400">Solicitado</p>
                            <p class="font-semibold text-gray-900 dark:text-white">${Number(request.cantidad_solicitada || 0)}</p>
                        </div>
                        <div class="rounded bg-gray-100 px-2 py-2 dark:bg-gray-700">
                            <p class="text-xs text-gray-500 dark:text-gray-400">Asignado</p>
                            <p class="font-semibold text-gray-900 dark:text-white">${Number(request.cantidad_asignada || 0)}</p>
                        </div>
                        <div class="rounded bg-orange-100 px-2 py-2 dark:bg-orange-900/30">
                            <p class="text-xs text-orange-600 dark:text-orange-300">Disponible</p>
                            <p class="font-bold text-orange-700 dark:text-orange-300">${Number(request.cantidad_disponible || 0)}</p>
                        </div>
                    </div>
                `;

                button.addEventListener('click', function () {
                    loadRequest(Number(request.id));
                });

                resultsContainer.appendChild(button);
            });
        }

        async function searchRequests() {
            const currentSequence = ++searchSequence;
            const parameters = new URLSearchParams();
            const query = searchInput.value.trim();
            const date = dateFilter.value;

            if (query !== '') {
                parameters.set('q', query);
            }

            if (date !== '') {
                parameters.set('fecha', date);
            }

            renderSearchLoading();
            hideSearchStatus();

            try {
                const response = await fetch(
                    `${searchUrl}?${parameters.toString()}`,
                    {
                        headers: {
                            Accept: 'application/json',
                        },
                    }
                );

                if (!response.ok) {
                    throw new Error('No fue posible consultar las solicitudes EPP.');
                }

                const data = await response.json();

                if (currentSequence !== searchSequence) {
                    return;
                }

                renderSearchResults(data.solicitudes || []);
            } catch (error) {
                if (currentSequence !== searchSequence) {
                    return;
                }

                resultsContainer.innerHTML = '';
                showSearchStatus(
                    error.message || 'Ocurrió un error durante la búsqueda.',
                    'error'
                );
            }
        }

        function renderDetailsLoading() {
            emptyDetails.classList.remove('hidden');
            detailsContent.classList.add('hidden');
            emptyDetails.innerHTML = `
                <p class="font-medium text-gray-700 dark:text-gray-300">
                    Cargando equipos de la solicitud…
                </p>
            `;
            detailsRows.innerHTML = '';
            hiddenDetails.innerHTML = '';
            updateAssignmentState();
        }

        function renderDetailsError(message) {
            selectedRequestId = null;
            requestIdInput.value = '';
            summaryFolio.textContent = 'Sin seleccionar';
            summaryDestination.textContent = '—';
            detailsContent.classList.add('hidden');
            emptyDetails.classList.remove('hidden');
            emptyDetails.innerHTML = `
                <p class="font-medium text-red-700 dark:text-red-300">
                    ${escapeHtml(message)}
                </p>
            `;
            detailsRows.innerHTML = '';
            hiddenDetails.innerHTML = '';
            updateAssignmentState();
        }

        function renderRequestDetails(request) {
            const details = Array.isArray(request.detalles)
                ? request.detalles
                : [];

            summaryFolio.textContent = request.folio || `#${request.id}`;
            summaryDestination.textContent = request.destino || 'Sin destino';
            summaryDestination.title = request.destino || 'Sin destino';
            detailsRows.innerHTML = '';

            if (details.length === 0) {
                renderDetailsError(
                    'La solicitud ya no tiene cantidades disponibles para asignar.'
                );
                return;
            }

            details.forEach(function (detail) {
                const available = Number(detail.cantidad_disponible || 0);
                const oldQuantity = Number(oldAssignments.get(Number(detail.id)) || 0);
                const initialQuantity = Math.min(
                    Math.max(oldQuantity, 0),
                    available
                );
                const row = document.createElement('tr');

                row.className = 'hover:bg-orange-50/40 dark:hover:bg-orange-900/10';
                row.innerHTML = `
                    <td class="px-4 py-3">
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">
                            ${escapeHtml(detail.nombre_producto || 'Producto sin nombre')}
                        </p>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                            ${escapeHtml(detail.economico || 'Sin económico')} · ${escapeHtml(detail.categoria || 'SEGURIDAD')}
                        </p>
                    </td>
                    <td class="px-4 py-3 text-center text-sm text-gray-600 dark:text-gray-300">
                        ${escapeHtml(detail.medida || '—')}
                    </td>
                    <td class="px-4 py-3 text-center text-sm font-semibold text-gray-700 dark:text-gray-300">
                        ${Number(detail.cantidad_solicitada || 0)}
                    </td>
                    <td class="px-4 py-3 text-center text-sm text-gray-600 dark:text-gray-300">
                        ${Number(detail.cantidad_asignada || 0)}
                    </td>
                    <td class="px-4 py-3 text-center text-sm font-bold text-orange-700 dark:text-orange-300">
                        ${available}
                    </td>
                    <td class="px-4 py-3 text-center">
                        <input
                            type="number"
                            min="0"
                            max="${available}"
                            step="1"
                            value="${initialQuantity}"
                            data-assignment-input
                            data-detail-id="${Number(detail.id)}"
                            class="w-24 rounded-lg border-gray-300 bg-white text-center text-sm font-semibold text-gray-900 shadow-sm focus:border-orange-500 focus:ring-orange-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                            aria-label="Cantidad de ${escapeHtml(detail.nombre_producto || 'equipo')}"
                        >
                    </td>
                `;

                const quantityInput = row.querySelector('[data-assignment-input]');
                quantityInput.addEventListener('input', function () {
                    normalizeQuantity(this);
                    updateAssignmentState();
                });

                detailsRows.appendChild(row);
            });

            emptyDetails.classList.add('hidden');
            detailsContent.classList.remove('hidden');
            oldAssignments.clear();
            updateAssignmentState();
        }

        function normalizeQuantity(input) {
            const max = Number(input.max || 0);
            let value = Number(input.value || 0);

            if (!Number.isFinite(value)) {
                value = 0;
            }

            value = Math.floor(value);
            value = Math.max(0, Math.min(value, max));
            input.value = value;
        }

        async function loadRequest(requestId) {
            if (!requestId) {
                return;
            }

            const currentSequence = ++detailSequence;
            selectedRequestId = requestId;
            requestIdInput.value = requestId;
            renderDetailsLoading();
            renderSearchResultsSelection();

            const url = detailUrlTemplate.replace(
                '__SOLICITUD__',
                encodeURIComponent(requestId)
            );

            try {
                const response = await fetch(url, {
                    headers: {
                        Accept: 'application/json',
                    },
                });

                if (!response.ok) {
                    let message = 'No fue posible cargar la solicitud seleccionada.';

                    try {
                        const errorData = await response.json();
                        message = errorData.message || message;
                    } catch (error) {
                        // La respuesta no contiene JSON.
                    }

                    throw new Error(message);
                }

                const data = await response.json();

                if (currentSequence !== detailSequence) {
                    return;
                }

                renderRequestDetails(data.solicitud || {});
            } catch (error) {
                if (currentSequence !== detailSequence) {
                    return;
                }

                renderDetailsError(
                    error.message || 'Ocurrió un error al cargar la solicitud.'
                );
            }
        }

        function renderSearchResultsSelection() {
            resultsContainer
                .querySelectorAll('[data-solicitud-id]')
                .forEach(function (button) {
                    const isSelected = Number(button.dataset.solicitudId)
                        === selectedRequestId;

                    button.classList.toggle('border-orange-500', isSelected);
                    button.classList.toggle('bg-orange-50', isSelected);
                    button.classList.toggle('ring-1', isSelected);
                    button.classList.toggle('ring-orange-500', isSelected);
                    button.classList.toggle('border-gray-200', !isSelected);
                });
        }

        function updateAssignmentState() {
            const inputs = Array.from(
                detailsRows.querySelectorAll('[data-assignment-input]')
            );
            const assignments = inputs
                .map(function (input) {
                    normalizeQuantity(input);

                    return {
                        detailId: Number(input.dataset.detailId),
                        quantity: Number(input.value || 0),
                    };
                })
                .filter(function (assignment) {
                    return assignment.detailId > 0
                        && assignment.quantity > 0;
                });

            hiddenDetails.innerHTML = '';

            assignments.forEach(function (assignment, index) {
                const detailInput = document.createElement('input');
                const quantityInput = document.createElement('input');

                detailInput.type = 'hidden';
                detailInput.name = `detalles[${index}][solicitud_material_detalle_id]`;
                detailInput.value = assignment.detailId;

                quantityInput.type = 'hidden';
                quantityInput.name = `detalles[${index}][cantidad]`;
                quantityInput.value = assignment.quantity;

                hiddenDetails.append(detailInput, quantityInput);
            });

            const totalQuantity = assignments.reduce(function (total, assignment) {
                return total + assignment.quantity;
            }, 0);

            assignmentSummary.textContent = totalQuantity === 1
                ? '1 equipo asignado'
                : `${totalQuantity} equipos asignados`;

            const hasRequiredData = Boolean(
                selectedRequestId
                && personSelect.value
                && valeDateInput.value
                && totalQuantity > 0
            );

            saveButton.disabled = !hasRequiredData;

            if (totalQuantity > 0) {
                assignmentError.classList.add('hidden');
                assignmentError.textContent = '';
            }
        }

        searchButton.addEventListener('click', searchRequests);

        clearButton.addEventListener('click', function () {
            searchInput.value = '';
            dateFilter.value = '';
            searchRequests();
        });

        searchInput.addEventListener('input', function () {
            window.clearTimeout(searchTimer);
            searchTimer = window.setTimeout(searchRequests, 350);
        });

        searchInput.addEventListener('keydown', function (event) {
            if (event.key === 'Enter') {
                event.preventDefault();
                window.clearTimeout(searchTimer);
                searchRequests();
            }
        });

        dateFilter.addEventListener('change', searchRequests);
        personSelect.addEventListener('change', updateAssignmentState);
        valeDateInput.addEventListener('change', updateAssignmentState);

        form.addEventListener('submit', function (event) {
            updateAssignmentState();

            const hasAssignments = hiddenDetails.querySelector(
                'input[name$="[cantidad]"]'
            );

            if (!selectedRequestId || !hasAssignments) {
                event.preventDefault();
                assignmentError.textContent = 'Seleccione una solicitud y asigne al menos un equipo.';
                assignmentError.classList.remove('hidden');
                return;
            }

            saveButton.disabled = true;
            saveButton.textContent = 'Registrando…';
        });

        if (oldRequestId > 0) {
            loadRequest(oldRequestId);
        }

        searchRequests();
        updateAssignmentState();
    });
})();
