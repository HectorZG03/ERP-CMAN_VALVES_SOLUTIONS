
document.addEventListener('DOMContentLoaded', () => {
    'use strict';

    const form = document.getElementById('entradaForm');

    if (!form) return;

    const container = document.getElementById('materiales-container');
    const emptyState = document.getElementById('materiales-vacio');

    const proveedorSearch = document.getElementById('proveedor_search');
    const proveedorId = document.getElementById('proveedor_id');
    const proveedorResultados = document.getElementById('proveedor_resultados');
    const proveedorSeleccionado = document.getElementById('proveedor_seleccionado');

    const agregarBtn = document.getElementById('agregarMaterialBtn');
    const limpiarBtn = document.getElementById('limpiarFormularioBtn');
    const registrarBtn = document.getElementById('registrarEntradaBtn');

    const buscarProveedoresUrl = form.dataset.buscarProveedores;
    const buscarProductosUrl = form.dataset.buscarProductos;

    const IVA = 0.16;
    const MAX_RESULTADOS = 20;

    let siguienteIndice = 0;
    let solicitudProveedor = null;
    let temporizadorProveedor = null;
    let proveedorEnfocado = false;

    const temporizadoresProductos = new WeakMap();
    const solicitudesProductos = new WeakMap();

    /**
     * Escapa caracteres para mostrar texto dentro de HTML.
     */
    function escaparHtml(valor) {
        return String(valor ?? '').replace(/[&<>"']/g, caracter => ({
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        })[caracter]);
    }

    /**
     * Formatea importes en pesos mexicanos.
     */
    function moneda(valor) {
        return new Intl.NumberFormat('es-MX', {
            style: 'currency',
            currency: 'MXN',
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }).format(Number(valor) || 0);
    }

    /**
     * Actualiza el estado vacío y el resumen.
     */
    function actualizarVista() {
        const materiales = obtenerMateriales();

        emptyState.classList.toggle('hidden', materiales.length > 0);

        document.getElementById('total-materiales').textContent =
            materiales.filter(tarjeta => {
                const productoId = tarjeta.querySelector(
                    '[data-campo="producto-id"]'
                );

                return productoId && productoId.value !== '';
            }).length;

        calcularTotales();
    }
    /**
     * Obtiene las tarjetas actuales.
     */
    function obtenerMateriales() {
        return Array.from(container.querySelectorAll('.material-item'));
    }

    /**
     * Construye una tarjeta de material.
     */
    function agregarMaterial() {
        const indice = siguienteIndice++;

        const tarjeta = document.createElement('article');
        tarjeta.className =
            'material-item rounded-xl border border-gray-200 bg-gray-50 p-4 ' +
            'dark:border-gray-600 dark:bg-gray-700/50';

        tarjeta.dataset.indice = indice;

        tarjeta.innerHTML = `
            <div class="mb-4 flex items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <span class="numero-material flex h-9 w-9 items-center justify-center
                                 rounded-lg bg-blue-100 font-semibold text-blue-700
                                 dark:bg-blue-900/50 dark:text-blue-300">
                        ${obtenerMateriales().length + 1}
                    </span>

                    <div>
                        <h3 class="font-semibold text-gray-900 dark:text-white">
                            Material
                        </h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            Selecciona un producto del inventario
                        </p>
                    </div>
                </div>

                <button
                    type="button"
                    data-accion="eliminar"
                    class="rounded-lg p-2 text-red-600 transition hover:bg-red-50
                           dark:text-red-400 dark:hover:bg-red-900/20"
                    aria-label="Eliminar material"
                    title="Eliminar material"
                >
                    <svg class="h-5 w-5" fill="none" stroke="currentColor"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              stroke-width="2"
                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862
                                 a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6
                                 M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3
                                 M4 7h16"/>
                    </svg>
                </button>
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
                <div class="relative md:col-span-2">
                    <label
                        for="producto-search-${indice}"
                        class="mb-2 block text-sm font-medium text-gray-700
                               dark:text-gray-300"
                    >
                        Producto <span class="text-red-500">*</span>
                    </label>

                    <input
                        type="text"
                        id="producto-search-${indice}"
                        data-campo="buscar-producto"
                        autocomplete="off"
                        placeholder="Buscar por nombre o categoría..."
                        aria-autocomplete="list"
                        class="block w-full rounded-lg border border-gray-300
                               bg-white px-3 py-2.5 text-sm text-gray-900
                               outline-none focus:border-blue-500 focus:ring-2
                               focus:ring-blue-500/20 dark:border-gray-500
                               dark:bg-gray-600 dark:text-white"
                    >

                    <input
                        type="hidden"
                        data-campo="producto-id"
                        name="materiales[${indice}][inventario_id]"
                        required
                    >

                    <div
                        data-campo="resultados-producto"
                        role="listbox"
                        class="absolute left-0 right-0 z-40 mt-1 hidden max-h-64
                               overflow-y-auto rounded-lg border border-gray-200
                               bg-white shadow-lg dark:border-gray-500
                               dark:bg-gray-800"
                    ></div>

                    <p
                        data-campo="producto-seleccionado"
                        class="mt-2 hidden text-xs text-green-700
                               dark:text-green-400"
                    ></p>
                </div>

                <div>
                    <label
                        class="mb-2 block text-sm font-medium text-gray-700
                               dark:text-gray-300"
                    >
                        Cantidad <span class="text-red-500">*</span>
                    </label>

                    <input
                        type="number"
                        data-campo="cantidad"
                        name="materiales[${indice}][cantidad]"
                        min="1"
                        step="1"
                        value="1"
                        required
                        class="block w-full rounded-lg border border-gray-300
                               bg-white px-3 py-2.5 text-sm text-gray-900
                               outline-none focus:border-blue-500 focus:ring-2
                               focus:ring-blue-500/20 dark:border-gray-500
                               dark:bg-gray-600 dark:text-white"
                    >
                </div>

                <div>
                    <label
                        class="mb-2 block text-sm font-medium text-gray-700
                               dark:text-gray-300"
                    >
                        Precio unitario <span class="text-red-500">*</span>
                    </label>

                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2
                                     text-sm text-gray-500 dark:text-gray-400">$</span>

                        <input
                            type="number"
                            data-campo="precio"
                            name="materiales[${indice}][precio_unitario]"
                            min="0"
                            step="0.01"
                            value="0"
                            required
                            class="block w-full rounded-lg border border-gray-300
                                   bg-white py-2.5 pl-7 pr-3 text-sm text-gray-900
                                   outline-none focus:border-blue-500 focus:ring-2
                                   focus:ring-blue-500/20 dark:border-gray-500
                                   dark:bg-gray-600 dark:text-white"
                        >
                    </div>
                </div>
            </div>

            <div class="mt-4 grid grid-cols-1 gap-3 rounded-lg bg-white p-3
                        text-sm dark:bg-gray-800 sm:grid-cols-3">
                <div>
                    <p class="text-gray-500 dark:text-gray-400">Subtotal</p>
                    <p data-campo="subtotal" class="mt-1 font-semibold
                       text-gray-900 dark:text-white">$0.00</p>
                </div>

                <div>
                    <p class="text-gray-500 dark:text-gray-400">IVA (16%)</p>
                    <p data-campo="iva" class="mt-1 font-semibold
                       text-gray-900 dark:text-white">$0.00</p>
                </div>

                <div>
                    <p class="text-gray-500 dark:text-gray-400">Total</p>
                    <p data-campo="total" class="mt-1 font-semibold
                       text-green-600 dark:text-green-400">$0.00</p>
                </div>
            </div>
        `;

        container.appendChild(tarjeta);

        actualizarNumeros();
        actualizarVista();

        tarjeta.querySelector('[data-campo="buscar-producto"]').focus();
    }

    /**
     * Renumera visualmente las tarjetas.
     */
    function actualizarNumeros() {
        obtenerMateriales().forEach((tarjeta, posicion) => {
            tarjeta.querySelector('.numero-material').textContent = posicion + 1;
        });
    }

    /**
     * Elimina una tarjeta.
     */
    function eliminarMaterial(tarjeta) {
        if (obtenerMateriales().length <= 1) {
            alert('Debe conservar al menos un material en la entrada.');
            return;
        }

        const input = tarjeta.querySelector('[data-campo="buscar-producto"]');
        const resultados = tarjeta.querySelector('[data-campo="resultados-producto"]');

        if (temporizadoresProductos.has(input)) {
            clearTimeout(temporizadoresProductos.get(input));
        }

        if (solicitudesProductos.has(input)) {
            solicitudesProductos.get(input).abort();
        }

        resultados.classList.add('hidden');
        tarjeta.remove();

        actualizarNumeros();
        actualizarVista();
    }

    /**
     * Calcula los importes individuales y generales.
     */
    function calcularTotales() {
        let subtotalGeneral = 0;
        let ivaGeneral = 0;
        let totalGeneral = 0;

        obtenerMateriales().forEach(tarjeta => {
            const cantidadInput = tarjeta.querySelector('[data-campo="cantidad"]');
            const precioInput = tarjeta.querySelector('[data-campo="precio"]');

            const cantidad = Math.max(0, Number(cantidadInput.value) || 0);
            const precio = Math.max(0, Number(precioInput.value) || 0);

            const subtotal = cantidad * precio;
            const iva = subtotal * IVA;
            const total = subtotal + iva;

            tarjeta.querySelector('[data-campo="subtotal"]').textContent = moneda(subtotal);
            tarjeta.querySelector('[data-campo="iva"]').textContent = moneda(iva);
            tarjeta.querySelector('[data-campo="total"]').textContent = moneda(total);

            subtotalGeneral += subtotal;
            ivaGeneral += iva;
            totalGeneral += total;
        });

        document.getElementById('subtotal-total').textContent = moneda(subtotalGeneral);
        document.getElementById('iva-total').textContent = moneda(ivaGeneral);
        document.getElementById('total-general').textContent = moneda(totalGeneral);
    }

    /**
     * Muestra resultados de búsqueda de productos.
     */
    function mostrarProductos(resultados, lista, tarjeta) {
        lista.replaceChildren();

        if (!Array.isArray(resultados) || resultados.length === 0) {
            const mensaje = document.createElement('p');
            mensaje.className = 'p-3 text-sm text-gray-500 dark:text-gray-400';
            mensaje.textContent = 'No se encontraron productos.';
            lista.appendChild(mensaje);
            lista.classList.remove('hidden');
            return;
        }

        resultados.slice(0, MAX_RESULTADOS).forEach(producto => {
            const opcion = document.createElement('button');

            opcion.type = 'button';
            opcion.setAttribute('role', 'option');
            opcion.className =
                'block w-full border-b border-gray-100 px-3 py-3 text-left ' +
                'last:border-0 hover:bg-blue-50 dark:border-gray-700 ' +
                'dark:hover:bg-gray-700';

            const nombre = document.createElement('span');
            nombre.className = 'block text-sm font-medium text-gray-900 dark:text-white';
            nombre.textContent = producto.nombre_producto || 'Producto sin nombre';

            const detalle = document.createElement('span');
            detalle.className = 'mt-1 block text-xs text-gray-500 dark:text-gray-400';

            const datos = [];

            if (producto.categoria) datos.push(producto.categoria);
            if (producto.medida) datos.push(`Unidad: ${producto.medida}`);

            datos.push(`Existencia: ${producto.existencia ?? 0}`);
            detalle.textContent = datos.join(' · ');

            opcion.append(nombre, detalle);

            opcion.addEventListener('click', () => {
                seleccionarProducto(producto, tarjeta);
                lista.classList.add('hidden');
            });

            lista.appendChild(opcion);
        });

        lista.classList.remove('hidden');
    }

    /**
     * Guarda el producto seleccionado en los campos del formulario.
     */
    function seleccionarProducto(producto, tarjeta) {
        const idInput = tarjeta.querySelector('[data-campo="producto-id"]');
        const busqueda = tarjeta.querySelector('[data-campo="buscar-producto"]');
        const seleccionado = tarjeta.querySelector('[data-campo="producto-seleccionado"]');
        const resultados = tarjeta.querySelector('[data-campo="resultados-producto"]');

        idInput.value = producto.id;
        busqueda.value = producto.nombre_producto || '';
        seleccionado.textContent =
            `Seleccionado · Existencia actual: ${producto.existencia ?? 0}` +
            (producto.medida ? ` · ${producto.medida}` : '');

        seleccionado.classList.remove('hidden');
        resultados.classList.add('hidden');

        calcularTotales();
    }

    /**
     * Consulta productos al servidor con límite y búsqueda.
     */
    async function buscarProductos(termino, tarjeta) {
        const input = tarjeta.querySelector('[data-campo="buscar-producto"]');
        const lista = tarjeta.querySelector('[data-campo="resultados-producto"]');

        if (solicitudesProductos.has(input)) {
            solicitudesProductos.get(input).abort();
        }

        const controlador = new AbortController();
        solicitudesProductos.set(input, controlador);

        lista.replaceChildren();

        const cargando = document.createElement('p');
        cargando.className = 'p-3 text-sm text-gray-500 dark:text-gray-400';
        cargando.textContent = 'Buscando productos...';
        lista.appendChild(cargando);
        lista.classList.remove('hidden');

        try {
            const url = new URL(buscarProductosUrl, window.location.origin);
            url.searchParams.set('search', termino);

            const respuesta = await fetch(url, {
                headers: { Accept: 'application/json' },
                signal: controlador.signal
            });

            if (!respuesta.ok) {
                throw new Error('No se pudo consultar el inventario.');
            }

            const productos = await respuesta.json();

            if (!input.isConnected || input.value.trim() !== termino) return;

            mostrarProductos(productos, lista, tarjeta);
        } catch (error) {
            if (error.name === 'AbortError') return;

            lista.replaceChildren();

            const mensaje = document.createElement('p');
            mensaje.className = 'p-3 text-sm text-red-600 dark:text-red-400';
            mensaje.textContent = 'Error al buscar productos. Intenta nuevamente.';
            lista.appendChild(mensaje);
            lista.classList.remove('hidden');

            console.error(error);
        }
    }

    /**
     * Consulta proveedores al servidor.
     */
    async function buscarProveedores(termino) {
        if (solicitudProveedor) {
            solicitudProveedor.abort();
        }

        const controlador = new AbortController();
        solicitudProveedor = controlador;

        proveedorResultados.replaceChildren();

        const cargando = document.createElement('p');
        cargando.className = 'p-3 text-sm text-gray-500 dark:text-gray-400';
        cargando.textContent = 'Buscando proveedores...';
        proveedorResultados.appendChild(cargando);
        proveedorResultados.classList.remove('hidden');

        try {
            const url = new URL(buscarProveedoresUrl, window.location.origin);
            url.searchParams.set('search', termino);

            const respuesta = await fetch(url, {
                headers: { Accept: 'application/json' },
                signal: controlador.signal
            });

            if (!respuesta.ok) {
                throw new Error('No se pudieron consultar los proveedores.');
            }

            const proveedores = await respuesta.json();

            if (proveedorSearch.value.trim() !== termino) return;

            mostrarProveedores(proveedores);
        } catch (error) {
            if (error.name === 'AbortError') return;

            proveedorResultados.replaceChildren();

            const mensaje = document.createElement('p');
            mensaje.className = 'p-3 text-sm text-red-600 dark:text-red-400';
            mensaje.textContent = 'Error al buscar proveedores. Intenta nuevamente.';
            proveedorResultados.appendChild(mensaje);
            proveedorResultados.classList.remove('hidden');

            console.error(error);
        }
    }

    /**
     * Renderiza resultados de proveedores.
     */
    function mostrarProveedores(proveedores) {
        proveedorResultados.replaceChildren();

        if (!Array.isArray(proveedores) || proveedores.length === 0) {
            const mensaje = document.createElement('p');
            mensaje.className = 'p-3 text-sm text-gray-500 dark:text-gray-400';
            mensaje.textContent = 'No se encontraron proveedores.';
            proveedorResultados.appendChild(mensaje);
            proveedorResultados.classList.remove('hidden');
            return;
        }

        proveedores.slice(0, MAX_RESULTADOS).forEach(proveedor => {
            const opcion = document.createElement('button');

            opcion.type = 'button';
            opcion.setAttribute('role', 'option');
            opcion.className =
                'block w-full border-b border-gray-100 px-3 py-3 text-left ' +
                'last:border-0 hover:bg-blue-50 dark:border-gray-700 ' +
                'dark:hover:bg-gray-700';

            const nombre = document.createElement('span');
            nombre.className = 'block text-sm font-medium text-gray-900 dark:text-white';
            nombre.textContent = proveedor.proveedor || 'Proveedor sin nombre';

            opcion.appendChild(nombre);

            opcion.addEventListener('click', () => {
                proveedorId.value = proveedor.id;
                proveedorSearch.value = proveedor.proveedor || '';
                proveedorSeleccionado.textContent =
                    `Proveedor seleccionado: ${proveedor.proveedor || ''}`;

                proveedorSeleccionado.classList.remove('hidden');
                proveedorResultados.classList.add('hidden');
            });

            proveedorResultados.appendChild(opcion);
        });

        proveedorResultados.classList.remove('hidden');
    }

    /**
     * Reinicia los campos del proveedor.
     */
    function limpiarProveedor() {
        proveedorId.value = '';
        proveedorSeleccionado.textContent = '';
        proveedorSeleccionado.classList.add('hidden');
    }

    /**
     * Reinicia el formulario sin cambiar la fecha por defecto.
     */
    function limpiarFormulario() {
        if (!confirm('¿Deseas limpiar los datos capturados y comenzar de nuevo?')) {
            return;
        }

        form.reset();
        limpiarProveedor();

        obtenerMateriales().forEach(tarjeta => {
            const input = tarjeta.querySelector('[data-campo="buscar-producto"]');

            if (temporizadoresProductos.has(input)) {
                clearTimeout(temporizadoresProductos.get(input));
            }

            if (solicitudesProductos.has(input)) {
                solicitudesProductos.get(input).abort();
            }
        });

        container.replaceChildren();
        siguienteIndice = 0;

        agregarMaterial();
        actualizarVista();
    }

    /**
     * Eventos del formulario y de las tarjetas.
     */
    agregarBtn.addEventListener('click', agregarMaterial);
    limpiarBtn.addEventListener('click', limpiarFormulario);

    container.addEventListener('input', event => {
        const tarjeta = event.target.closest('.material-item');
        if (!tarjeta) return;

        if (
            event.target.matches('[data-campo="cantidad"]') ||
            event.target.matches('[data-campo="precio"]')
        ) {
            calcularTotales();
        }

        if (event.target.matches('[data-campo="buscar-producto"]')) {
            const input = event.target;
            const idInput = tarjeta.querySelector('[data-campo="producto-id"]');
            const seleccionado = tarjeta.querySelector('[data-campo="producto-seleccionado"]');
            const lista = tarjeta.querySelector('[data-campo="resultados-producto"]');
            const termino = input.value.trim();

            idInput.value = '';
            seleccionado.classList.add('hidden');

            if (temporizadoresProductos.has(input)) {
                clearTimeout(temporizadoresProductos.get(input));
            }

            if (solicitudesProductos.has(input)) {
                solicitudesProductos.get(input).abort();
            }

            if (termino.length < 2) {
                lista.replaceChildren();
                lista.classList.add('hidden');
                return;
            }

            const temporizador = setTimeout(() => {
                buscarProductos(termino, tarjeta);
            }, 300);

            temporizadoresProductos.set(input, temporizador);
        }
    });

    container.addEventListener('click', event => {
        const boton = event.target.closest('[data-accion="eliminar"]');
        if (!boton) return;

        const tarjeta = boton.closest('.material-item');
        if (tarjeta) eliminarMaterial(tarjeta);
    });

    proveedorSearch.addEventListener('input', () => {
        limpiarProveedor();

        if (temporizadorProveedor) {
            clearTimeout(temporizadorProveedor);
        }

        if (solicitudProveedor) {
            solicitudProveedor.abort();
        }

        const termino = proveedorSearch.value.trim();

        if (termino.length < 2) {
            proveedorResultados.replaceChildren();
            proveedorResultados.classList.add('hidden');
            return;
        }

        temporizadorProveedor = setTimeout(() => {
            buscarProveedores(termino);
        }, 300);
    });

    proveedorSearch.addEventListener('focus', () => {
        proveedorEnfocado = true;
    });

    proveedorSearch.addEventListener('blur', () => {
        proveedorEnfocado = false;

        setTimeout(() => {
            if (!proveedorEnfocado) {
                proveedorResultados.classList.add('hidden');
            }
        }, 150);
    });

    document.addEventListener('click', event => {
        if (!proveedorSearch.contains(event.target) &&
            !proveedorResultados.contains(event.target)) {
            proveedorResultados.classList.add('hidden');
        }

        container.querySelectorAll('.material-item').forEach(tarjeta => {
            const input = tarjeta.querySelector('[data-campo="buscar-producto"]');
            const lista = tarjeta.querySelector('[data-campo="resultados-producto"]');

            if (!input.contains(event.target) && !lista.contains(event.target)) {
                lista.classList.add('hidden');
            }
        });
    });

    form.addEventListener('submit', event => {
        const materiales = obtenerMateriales();

        if (!proveedorId.value) {
            event.preventDefault();
            alert('Selecciona un proveedor de la lista de resultados.');
            proveedorSearch.focus();
            return;
        }

        if (!document.getElementById('fecha_entrada').value) {
            event.preventDefault();
            alert('Selecciona la fecha de entrada.');
            return;
        }

        if (materiales.length === 0) {
            event.preventDefault();
            alert('Agrega al menos un material.');
            return;
        }

        for (let i = 0; i < materiales.length; i++) {
            const tarjeta = materiales[i];
            const producto = tarjeta.querySelector('[data-campo="producto-id"]').value;
            const cantidad = Number(tarjeta.querySelector('[data-campo="cantidad"]').value);
            const precio = Number(tarjeta.querySelector('[data-campo="precio"]').value);

            if (!producto) {
                event.preventDefault();
                alert(`Selecciona un producto para el material ${i + 1}.`);
                tarjeta.querySelector('[data-campo="buscar-producto"]').focus();
                return;
            }

            if (!Number.isInteger(cantidad) || cantidad < 1) {
                event.preventDefault();
                alert(`La cantidad del material ${i + 1} debe ser un entero mayor que cero.`);
                tarjeta.querySelector('[data-campo="cantidad"]').focus();
                return;
            }

            if (!Number.isFinite(precio) || precio < 0) {
                event.preventDefault();
                alert(`El precio del material ${i + 1} no es válido.`);
                tarjeta.querySelector('[data-campo="precio"]').focus();
                return;
            }
        }

        if (!confirm(`¿Confirmas registrar la entrada por ${document.getElementById('total-general').textContent}?`)) {
            event.preventDefault();
            return;
        }

        registrarBtn.disabled = true;
        registrarBtn.classList.add('cursor-not-allowed', 'opacity-60');
        registrarBtn.textContent = 'Registrando entrada...';
    });

    // Inicializar con una tarjeta vacía.
    agregarMaterial();
});
