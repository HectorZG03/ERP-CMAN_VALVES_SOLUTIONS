document.addEventListener('DOMContentLoaded', () => {
    'use strict';

    const formulario = document.getElementById('salidaForm');
    const selectorSolicitud = document.getElementById(
        'solicitud_material_id'
    );
    const contenedor = document.getElementById('productos-container');
    const botonRecargar = document.getElementById('agregar-producto');
    const botonLimpiar = document.getElementById('limpiar-formulario');
    const botonEnviar = formulario?.querySelector(
        'button[type="submit"]'
    );

    if (
        !formulario ||
        !selectorSolicitud ||
        !contenedor ||
        !botonEnviar
    ) {
        return;
    }

    const solicitudUrlTemplate =
        formulario.dataset.solicitudUrlTemplate;

    let controladorSolicitud = null;
    let productoIndex = 0;
    let cargandoSolicitud = false;

    const formatoNumero = new Intl.NumberFormat('es-MX', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    });

    const panelSolicitud = document.createElement('div');

    panelSolicitud.id = 'detalle-solicitud';
    panelSolicitud.className = 'hidden mb-4 rounded-lg border p-4';

    contenedor.parentNode.insertBefore(
        panelSolicitud,
        contenedor
    );

    if (botonRecargar) {
        botonRecargar.textContent = 'Recargar materiales';
        botonRecargar.disabled = true;
        botonRecargar.classList.add(
            'opacity-50',
            'cursor-not-allowed'
        );
    }

    function moneda(valor) {
        return `$${formatoNumero.format(Number(valor) || 0)}`;
    }

    function convertirNumero(valor) {
        const numero = Number(valor);

        return Number.isFinite(numero) ? numero : 0;
    }

    function establecerEstadoBotonRecargar(habilitado) {
        if (!botonRecargar) {
            return;
        }

        botonRecargar.disabled = !habilitado;

        botonRecargar.classList.toggle(
            'opacity-50',
            !habilitado
        );

        botonRecargar.classList.toggle(
            'cursor-not-allowed',
            !habilitado
        );
    }

    function mostrarPanel(tipo, titulo, datos = []) {
        const estilos = {
            informacion: [
                'bg-blue-50',
                'border-blue-200',
                'text-blue-800',
                'dark:bg-blue-900/20',
                'dark:border-blue-800',
                'dark:text-blue-200',
            ],
            correcto: [
                'bg-green-50',
                'border-green-200',
                'text-green-800',
                'dark:bg-green-900/20',
                'dark:border-green-800',
                'dark:text-green-200',
            ],
            advertencia: [
                'bg-yellow-50',
                'border-yellow-200',
                'text-yellow-800',
                'dark:bg-yellow-900/20',
                'dark:border-yellow-800',
                'dark:text-yellow-200',
            ],
            error: [
                'bg-red-50',
                'border-red-200',
                'text-red-800',
                'dark:bg-red-900/20',
                'dark:border-red-800',
                'dark:text-red-200',
            ],
        };

        Object.values(estilos).flat().forEach((clase) => {
            panelSolicitud.classList.remove(clase);
        });

        panelSolicitud.classList.add(...estilos[tipo]);
        panelSolicitud.innerHTML = '';

        const encabezado = document.createElement('h3');

        encabezado.className = 'font-semibold';
        encabezado.textContent = titulo;

        panelSolicitud.appendChild(encabezado);

        if (datos.length > 0) {
            const listado = document.createElement('div');

            listado.className =
                'mt-2 grid grid-cols-1 md:grid-cols-2 gap-2 text-sm';

            datos.forEach((dato) => {
                const elemento = document.createElement('p');
                const etiqueta = document.createElement('strong');

                etiqueta.textContent = `${dato.etiqueta}: `;
                elemento.appendChild(etiqueta);
                elemento.appendChild(
                    document.createTextNode(dato.valor || 'N/A')
                );

                listado.appendChild(elemento);
            });

            panelSolicitud.appendChild(listado);
        }

        panelSolicitud.classList.remove('hidden');
    }

    function ocultarPanel() {
        panelSolicitud.classList.add('hidden');
        panelSolicitud.innerHTML = '';
    }

    function limpiarProductos() {
        contenedor.innerHTML = '';
        productoIndex = 0;
        calcularTotales();
    }

    function construirUrlSolicitud(solicitudId) {
        return solicitudUrlTemplate.replace(
            '__SOLICITUD__',
            encodeURIComponent(solicitudId)
        );
    }

    async function cargarSolicitud() {
        const solicitudId = selectorSolicitud.value;

        limpiarProductos();

        if (!solicitudId) {
            ocultarPanel();
            establecerEstadoBotonRecargar(false);
            actualizarEstadoBotonEnviar(false);
            return;
        }

        if (controladorSolicitud) {
            controladorSolicitud.abort();
        }

        controladorSolicitud = new AbortController();
        cargandoSolicitud = true;

        establecerEstadoBotonRecargar(false);
        actualizarEstadoBotonEnviar(false);

        mostrarPanel(
            'informacion',
            'Consultando solicitud aprobada...'
        );

        try {
            const respuesta = await fetch(
                construirUrlSolicitud(solicitudId),
                {
                    method: 'GET',
                    headers: {
                        Accept: 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    signal: controladorSolicitud.signal,
                }
            );

            if (!respuesta.ok) {
                throw new Error(
                    `No fue posible consultar la solicitud. HTTP ${respuesta.status}`
                );
            }

            const solicitud = await respuesta.json();

            if (
                String(selectorSolicitud.value) !==
                String(solicitudId)
            ) {
                return;
            }

            mostrarPanel(
                solicitud.completada
                    ? 'advertencia'
                    : 'correcto',
                solicitud.completada
                    ? 'Solicitud completamente entregada'
                    : `Solicitud #${solicitud.id}`,
                [
                    {
                        etiqueta: 'Solicitante',
                        valor: solicitud.solicitante?.nombre,
                    },
                    {
                        etiqueta: 'Número de empleado',
                        valor:
                            solicitud.solicitante
                                ?.numero_empleado,
                    },
                    {
                        etiqueta: 'Destino',
                        valor: solicitud.destino,
                    },
                    {
                        etiqueta: 'Fecha de solicitud',
                        valor: solicitud.fecha_solicitud,
                    },
                    {
                        etiqueta: 'Correo',
                        valor: solicitud.solicitante?.email,
                    },
                    {
                        etiqueta: 'Operador asignado',
                        valor: solicitud.operador?.nombre,
                    },
                ]
            );

            if (solicitud.completada) {
                actualizarEstadoBotonEnviar(false);
                establecerEstadoBotonRecargar(true);
                return;
            }

            solicitud.productos.forEach((producto) => {
                agregarProductoSolicitud(producto);
            });

            renumerarProductos();
            calcularTotales();
            establecerEstadoBotonRecargar(true);
        } catch (error) {
            if (error.name === 'AbortError') {
                return;
            }

            console.error(
                'Error al consultar la solicitud:',
                error
            );

            mostrarPanel(
                'error',
                'No fue posible cargar la solicitud'
            );

            limpiarProductos();
        } finally {
            cargandoSolicitud = false;

            if (selectorSolicitud.value) {
                establecerEstadoBotonRecargar(true);
            }

            calcularTotales();
        }
    }

    function agregarProductoSolicitud(producto) {
        const existencia = Math.max(
            0,
            Math.trunc(convertirNumero(producto.existencia))
        );

        const cantidadPendiente = Math.max(
            0,
            Math.trunc(
                convertirNumero(producto.cantidad_pendiente)
            )
        );

        const cantidadMaxima = Math.min(
            existencia,
            cantidadPendiente
        );

        if (cantidadMaxima < 1) {
            agregarAdvertenciaSinStock(
                producto,
                cantidadPendiente
            );
            return;
        }

        const index = productoIndex++;
        const precio = Math.max(
            0,
            convertirNumero(producto.precio_unitario)
        );

        const productoDiv = document.createElement('div');

        productoDiv.className =
            'producto-item bg-gray-50 dark:bg-gray-700 p-4 rounded-lg border border-gray-200 dark:border-gray-600 transition-colors duration-200';

        productoDiv.dataset.index = index;
        productoDiv.dataset.precio = String(precio);
        productoDiv.dataset.existencia = String(existencia);
        productoDiv.dataset.pendiente = String(
            cantidadPendiente
        );

        productoDiv.innerHTML = `
            <div class="flex items-start gap-4">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 bg-red-500 dark:bg-red-600 rounded-lg flex items-center justify-center">
                        <span class="numero-producto text-white font-medium"></span>
                    </div>
                </div>

                <div class="flex-1">
                    <div class="grid grid-cols-1 lg:grid-cols-4 gap-4">
                        <div class="lg:col-span-2">
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Material
                            </p>

                            <p class="producto-nombre font-semibold text-gray-900 dark:text-white"></p>

                            <p class="producto-detalles mt-1 text-xs text-gray-600 dark:text-gray-400"></p>

                            <input type="hidden"
                                class="inventario-id"
                                name="productos[${index}][inventario_id]"
                                value="${Number(producto.inventario_id)}">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Cantidad a entregar *
                            </label>

                            <input type="number"
                                class="cantidad-input block w-full bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-gray-900 dark:text-white text-sm p-2"
                                name="productos[${index}][cantidad]"
                                min="1"
                                max="${cantidadMaxima}"
                                value="${cantidadMaxima}"
                                required>

                            <p class="stock-disponible ok"></p>
                        </div>

                        <div>
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Precio unitario
                            </p>

                            <div class="bg-gray-100 dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-md p-2">
                                <span class="precio-unitario text-gray-900 dark:text-white"></span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3 grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm bg-white dark:bg-gray-600 p-3 rounded">
                        <div>
                            <span class="text-gray-600 dark:text-gray-400">
                                Subtotal:
                            </span>
                            <span class="subtotal-producto font-semibold text-gray-900 dark:text-white ml-2">
                                $0.00
                            </span>
                        </div>

                        <div>
                            <span class="text-gray-600 dark:text-gray-400">
                                IVA (16%):
                            </span>
                            <span class="iva-producto font-semibold text-gray-900 dark:text-white ml-2">
                                $0.00
                            </span>
                        </div>

                        <div>
                            <span class="text-gray-600 dark:text-gray-400">
                                Total:
                            </span>
                            <span class="total-producto font-semibold text-red-600 dark:text-red-400 ml-2">
                                $0.00
                            </span>
                        </div>
                    </div>
                </div>

                <div class="flex-shrink-0">
                    <button type="button"
                        class="eliminar-producto remove-producto bg-red-500 hover:bg-red-600 text-white p-2 rounded transition-colors duration-200"
                        title="No entregar este material ahora">
                        <svg class="w-5 h-5"
                            fill="currentColor"
                            viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                clip-rule="evenodd">
                            </path>
                        </svg>
                    </button>
                </div>
            </div>
        `;

        const codigo = producto.codigo
            ? `Código: ${producto.codigo}`
            : 'Sin código';

        productoDiv.querySelector(
            '.producto-nombre'
        ).textContent = producto.nombre_producto;

        productoDiv.querySelector(
            '.producto-detalles'
        ).textContent = [
            codigo,
            `Categoría: ${producto.categoria || 'N/A'}`,
            `Medida: ${producto.medida || 'N/A'}`,
            `Solicitado: ${producto.cantidad_solicitada}`,
            `Entregado: ${producto.cantidad_entregada}`,
            `Pendiente: ${cantidadPendiente}`,
        ].join(' | ');

        productoDiv.querySelector(
            '.stock-disponible'
        ).textContent =
            `Existencia: ${existencia} | Máximo: ${cantidadMaxima}`;

        productoDiv.querySelector(
            '.precio-unitario'
        ).textContent = moneda(precio);

        productoDiv
            .querySelector('.cantidad-input')
            .addEventListener('input', () => {
                actualizarProducto(productoDiv);
            });

        productoDiv
            .querySelector('.eliminar-producto')
            .addEventListener('click', () => {
                productoDiv.remove();
                renumerarProductos();
                calcularTotales();
            });

        contenedor.appendChild(productoDiv);
        actualizarProducto(productoDiv);
    }

    function agregarAdvertenciaSinStock(
        producto,
        cantidadPendiente
    ) {
        const advertencia = document.createElement('div');

        advertencia.className =
            'bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4 text-sm text-yellow-800 dark:text-yellow-200';

        advertencia.textContent =
            `${producto.nombre_producto}: quedan ` +
            `${cantidadPendiente} unidad(es) pendientes, ` +
            'pero actualmente no hay existencia disponible.';

        contenedor.appendChild(advertencia);
    }

    function actualizarProducto(productoDiv) {
        const cantidadInput =
            productoDiv.querySelector('.cantidad-input');

        const cantidad = Math.max(
            0,
            Math.trunc(convertirNumero(cantidadInput.value))
        );

        const precio = convertirNumero(
            productoDiv.dataset.precio
        );

        const subtotal = cantidad * precio;
        const iva = subtotal * 0.16;
        const total = subtotal + iva;

        productoDiv.querySelector(
            '.subtotal-producto'
        ).textContent = moneda(subtotal);

        productoDiv.querySelector(
            '.iva-producto'
        ).textContent = moneda(iva);

        productoDiv.querySelector(
            '.total-producto'
        ).textContent = moneda(total);

        calcularTotales();
    }

    function renumerarProductos() {
        const productos = contenedor.querySelectorAll(
            '.producto-item'
        );

        productos.forEach((productoDiv, index) => {
            productoDiv.querySelector(
                '.numero-producto'
            ).textContent = index + 1;

            productoDiv.querySelector(
                '.inventario-id'
            ).name = `productos[${index}][inventario_id]`;

            productoDiv.querySelector(
                '.cantidad-input'
            ).name = `productos[${index}][cantidad]`;
        });

        productoIndex = productos.length;
    }

    function calcularTotales() {
        const productos = contenedor.querySelectorAll(
            '.producto-item'
        );

        let subtotalGeneral = 0;
        let ivaGeneral = 0;
        let totalGeneral = 0;
        let formularioValido = productos.length > 0;

        productos.forEach((productoDiv) => {
            const cantidadInput =
                productoDiv.querySelector('.cantidad-input');

            const cantidad = Math.trunc(
                convertirNumero(cantidadInput.value)
            );

            const precio = convertirNumero(
                productoDiv.dataset.precio
            );

            if (
                cantidad < 1 ||
                !cantidadInput.checkValidity()
            ) {
                formularioValido = false;
            }

            subtotalGeneral += cantidad * precio;
            ivaGeneral += cantidad * precio * 0.16;
            totalGeneral += cantidad * precio * 1.16;
        });

        document.getElementById(
            'total-productos'
        ).textContent = productos.length;

        document.getElementById(
            'subtotal-total'
        ).textContent = moneda(subtotalGeneral);

        document.getElementById(
            'iva-total'
        ).textContent = moneda(ivaGeneral);

        document.getElementById(
            'total-general'
        ).textContent = moneda(totalGeneral);

        actualizarEstadoBotonEnviar(
            formularioValido &&
            Boolean(selectorSolicitud.value) &&
            !cargandoSolicitud
        );
    }

    function actualizarEstadoBotonEnviar(habilitado) {
        botonEnviar.disabled = !habilitado;

        botonEnviar.classList.toggle(
            'opacity-50',
            !habilitado
        );

        botonEnviar.classList.toggle(
            'cursor-not-allowed',
            !habilitado
        );
    }

    function limpiarFormulario() {
        if (
            !confirm(
                '¿Está seguro de que desea limpiar el formulario?'
            )
        ) {
            return;
        }

        if (controladorSolicitud) {
            controladorSolicitud.abort();
        }

        formulario.reset();
        selectorSolicitud.value = '';

        limpiarProductos();
        ocultarPanel();
        establecerEstadoBotonRecargar(false);
        actualizarEstadoBotonEnviar(false);
    }

    function validarFormulario(event) {
        const productos = contenedor.querySelectorAll(
            '.producto-item'
        );

        if (!selectorSolicitud.value) {
            event.preventDefault();

            alert('Debe seleccionar una solicitud aprobada.');
            selectorSolicitud.focus();
            return;
        }

        if (productos.length === 0) {
            event.preventDefault();

            alert(
                'Debe seleccionar al menos un material para entregar.'
            );

            return;
        }

        for (const productoDiv of productos) {
            const cantidadInput =
                productoDiv.querySelector('.cantidad-input');

            if (!cantidadInput.checkValidity()) {
                event.preventDefault();

                cantidadInput.reportValidity();
                cantidadInput.focus();
                return;
            }
        }

        const total = document.getElementById(
            'total-general'
        ).textContent;

        if (
            !confirm(
                `¿Confirmar la salida de materiales por ${total}?`
            )
        ) {
            event.preventDefault();
            return;
        }

        botonEnviar.disabled = true;
        botonEnviar.textContent = 'Registrando salida...';
    }

    selectorSolicitud.addEventListener(
        'change',
        cargarSolicitud
    );

    if (botonRecargar) {
        botonRecargar.addEventListener(
            'click',
            cargarSolicitud
        );
    }

    if (botonLimpiar) {
        botonLimpiar.addEventListener(
            'click',
            limpiarFormulario
        );
    }

    formulario.addEventListener(
        'submit',
        validarFormulario
    );

    limpiarProductos();
    actualizarEstadoBotonEnviar(false);

    if (selectorSolicitud.value) {
        cargarSolicitud();
    }
});