document.addEventListener('DOMContentLoaded', () => {
    'use strict';

    const formulario = document.getElementById('salidaForm');
    const contenedor = document.getElementById('productos-container');
    const botonAgregar = document.getElementById('agregar-producto');
    const botonLimpiar = document.getElementById('limpiar-formulario');

    if (!formulario || !contenedor) {
        return;
    }

    const buscarProductosUrl = formulario.dataset.buscarProductosUrl;
    let productoIndex = 0;

    const formatoNumero = new Intl.NumberFormat('es-MX', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    });

    function moneda(valor) {
        return `$${formatoNumero.format(Number(valor) || 0)}`;
    }

    function agregarProducto() {
        const index = productoIndex++;
        const productoDiv = document.createElement('div');

        productoDiv.className =
            'producto-item bg-gray-50 dark:bg-gray-700 p-4 rounded-lg border border-gray-200 dark:border-gray-600 transition-colors duration-200';

        productoDiv.id = `producto-${index}`;
        productoDiv.dataset.index = index;
        productoDiv.productoSeleccionado = null;
        productoDiv.resultadosBusqueda = [];
        productoDiv.resultadoActivo = -1;

        productoDiv.innerHTML = `
            <div class="flex items-start space-x-4">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 bg-red-500 dark:bg-red-600 rounded-lg flex items-center justify-center">
                        <span class="numero-producto text-white font-medium"></span>
                    </div>
                </div>

                <div class="flex-1 grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Producto *
                        </label>

                        <div class="producto-buscador relative">
                            <input type="hidden"
                                   name="productos[${index}][inventario_id]"
                                   class="inventario-id">

                            <input type="text"
                                   class="producto-busqueda block w-full bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-gray-900 dark:text-white text-sm p-2"
                                   placeholder="Escriba el nombre, económico o categoría..."
                                   autocomplete="off"
                                   role="combobox"
                                   aria-autocomplete="list"
                                   aria-expanded="false">

                            <div class="producto-resultados hidden absolute z-50 left-0 right-0 mt-1 max-h-64 overflow-y-auto bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-500 rounded-md shadow-lg"
                                 role="listbox">
                            </div>
                        </div>

                        <div class="stock-disponible"></div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Cantidad *
                        </label>

                        <input type="number"
                               name="productos[${index}][cantidad]"
                               required
                               min="1"
                               value="1"
                               class="cantidad-input block w-full bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-gray-900 dark:text-white text-sm p-2">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Precio Unitario
                        </label>

                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500 dark:text-gray-400">
                                $
                            </span>

                            <input type="text"
                                   readonly
                                   class="precio-input block w-full pl-8 bg-gray-100 dark:bg-gray-500 border border-gray-300 dark:border-gray-500 rounded-md shadow-sm text-gray-900 dark:text-white text-sm p-2"
                                   value="0.00">
                        </div>
                    </div>
                </div>

                <div class="flex-shrink-0">
                    <button type="button"
                            class="eliminar-producto mt-6 remove-producto bg-red-500 hover:bg-red-600 dark:bg-red-600 dark:hover:bg-red-700 text-white p-2 rounded transition-colors duration-200"
                            title="Eliminar producto">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                  d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                  clip-rule="evenodd">
                            </path>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="mt-3 grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm bg-white dark:bg-gray-600 p-3 rounded">
                <div>
                    <span class="text-gray-600 dark:text-gray-400">Subtotal:</span>
                    <span class="subtotal-producto font-semibold text-gray-900 dark:text-white ml-2">
                        $0.00
                    </span>
                </div>

                <div>
                    <span class="text-gray-600 dark:text-gray-400">IVA (16%):</span>
                    <span class="iva-producto font-semibold text-gray-900 dark:text-white ml-2">
                        $0.00
                    </span>
                </div>

                <div>
                    <span class="text-gray-600 dark:text-gray-400">Total:</span>
                    <span class="total-producto font-semibold text-red-600 dark:text-red-400 ml-2">
                        $0.00
                    </span>
                </div>
            </div>
        `;

        contenedor.appendChild(productoDiv);
        configurarProducto(productoDiv);
        renumerarProductos();
        calcularTotales();

        productoDiv.querySelector('.producto-busqueda').focus();
    }

    function configurarProducto(productoDiv) {
        const buscador = productoDiv.querySelector('.producto-busqueda');
        const cantidad = productoDiv.querySelector('.cantidad-input');
        const botonEliminar = productoDiv.querySelector('.eliminar-producto');

        buscador.addEventListener('input', () => {
            limpiarSeleccion(productoDiv);

            clearTimeout(productoDiv.temporizadorBusqueda);

            const termino = buscador.value.trim();

            if (termino.length < 2) {
                cerrarResultados(productoDiv);
                return;
            }

            productoDiv.temporizadorBusqueda = setTimeout(() => {
                buscarProductos(productoDiv, termino);
            }, 300);
        });

        buscador.addEventListener('keydown', (event) => {
            controlarTecladoResultados(productoDiv, event);
        });

        cantidad.addEventListener('input', () => {
            actualizarProducto(productoDiv);
        });

        botonEliminar.addEventListener('click', () => {
            eliminarProducto(productoDiv);
        });
    }

    async function buscarProductos(productoDiv, termino) {
        const buscador = productoDiv.querySelector('.producto-busqueda');

        if (productoDiv.controladorBusqueda) {
            productoDiv.controladorBusqueda.abort();
        }

        productoDiv.controladorBusqueda = new AbortController();

        mostrarMensajeResultados(productoDiv, 'Buscando productos...');

        try {
            const url = new URL(buscarProductosUrl, window.location.origin);
            url.searchParams.set('q', termino);

            const respuesta = await fetch(url, {
                method: 'GET',
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                signal: productoDiv.controladorBusqueda.signal,
            });

            if (!respuesta.ok) {
                throw new Error(`Error HTTP ${respuesta.status}`);
            }

            const productos = await respuesta.json();

            if (buscador.value.trim() !== termino) {
                return;
            }

            const productosDisponibles = productos.filter((producto) => {
                return !productoYaSeleccionado(
                    producto.id,
                    productoDiv
                );
            });

            mostrarResultados(productoDiv, productosDisponibles);
        } catch (error) {
            if (error.name === 'AbortError') {
                return;
            }

            console.error('Error al buscar productos:', error);

            mostrarMensajeResultados(
                productoDiv,
                'No fue posible consultar los productos.'
            );
        }
    }

    function mostrarResultados(productoDiv, productos) {
        const resultados = productoDiv.querySelector('.producto-resultados');
        const buscador = productoDiv.querySelector('.producto-busqueda');

        resultados.innerHTML = '';
        productoDiv.resultadosBusqueda = productos;
        productoDiv.resultadoActivo = -1;

        if (productos.length === 0) {
            mostrarMensajeResultados(
                productoDiv,
                'No se encontraron productos disponibles.'
            );

            return;
        }

        productos.forEach((producto, index) => {
            const opcion = document.createElement('button');

            opcion.type = 'button';
            opcion.dataset.resultadoIndex = index;
            opcion.className =
                'resultado-producto w-full text-left px-4 py-3 border-b border-gray-200 dark:border-gray-600 last:border-b-0 hover:bg-blue-50 dark:hover:bg-gray-600 focus:outline-none';

            const nombre = document.createElement('div');
            nombre.className =
                'font-semibold text-sm text-gray-900 dark:text-white';
            nombre.textContent = producto.nombre_producto;

            const detalles = document.createElement('div');
            detalles.className =
                'mt-1 text-xs text-gray-600 dark:text-gray-300';

            const informacion = [];

            if (producto.economico) {
                informacion.push(`Económico: ${producto.economico}`);
            }

            if (producto.categoria) {
                informacion.push(`Categoría: ${producto.categoria}`);
            }

            if (producto.medida) {
                informacion.push(`Medida: ${producto.medida}`);
            }

            informacion.push(`Disponible: ${producto.existencia}`);

            detalles.textContent = informacion.join(' | ');

            opcion.appendChild(nombre);
            opcion.appendChild(detalles);

            opcion.addEventListener('click', () => {
                seleccionarProducto(productoDiv, producto);
            });

            resultados.appendChild(opcion);
        });

        resultados.classList.remove('hidden');
        buscador.setAttribute('aria-expanded', 'true');
    }

    function mostrarMensajeResultados(productoDiv, mensaje) {
        const resultados = productoDiv.querySelector('.producto-resultados');
        const buscador = productoDiv.querySelector('.producto-busqueda');

        resultados.innerHTML = '';

        const elemento = document.createElement('div');
        elemento.className =
            'px-4 py-3 text-sm text-gray-600 dark:text-gray-300';
        elemento.textContent = mensaje;

        resultados.appendChild(elemento);
        resultados.classList.remove('hidden');
        buscador.setAttribute('aria-expanded', 'true');
    }

    function seleccionarProducto(productoDiv, producto) {
        if (productoYaSeleccionado(producto.id, productoDiv)) {
            alert('Este producto ya fue agregado a la salida');
            return;
        }

        const buscador = productoDiv.querySelector('.producto-busqueda');
        const inventarioId = productoDiv.querySelector('.inventario-id');
        const cantidad = productoDiv.querySelector('.cantidad-input');

        productoDiv.productoSeleccionado = producto;

        inventarioId.value = producto.id;

        buscador.value = producto.economico
            ? `${producto.nombre_producto} - ${producto.economico}`
            : producto.nombre_producto;

        cantidad.max = producto.existencia;
        cantidad.setCustomValidity('');

        cerrarResultados(productoDiv);
        actualizarProducto(productoDiv);
    }

    function limpiarSeleccion(productoDiv) {
        productoDiv.productoSeleccionado = null;

        const inventarioId = productoDiv.querySelector('.inventario-id');
        const cantidad = productoDiv.querySelector('.cantidad-input');
        const precio = productoDiv.querySelector('.precio-input');
        const stock = productoDiv.querySelector('.stock-disponible');

        inventarioId.value = '';
        cantidad.removeAttribute('max');
        cantidad.setCustomValidity('');
        precio.value = '0.00';
        stock.textContent = '';
        stock.className = 'stock-disponible';

        actualizarImportesProducto(productoDiv, 0, 0, 0);
        calcularTotales();
    }

    function actualizarProducto(productoDiv) {
        const producto = productoDiv.productoSeleccionado;
        const cantidadInput = productoDiv.querySelector('.cantidad-input');
        const precioInput = productoDiv.querySelector('.precio-input');
        const stockInfo = productoDiv.querySelector('.stock-disponible');

        if (!producto) {
            precioInput.value = '0.00';
            stockInfo.textContent = '';
            actualizarImportesProducto(productoDiv, 0, 0, 0);
            calcularTotales();
            return;
        }

        const cantidad = Number.parseInt(cantidadInput.value, 10) || 0;
        const existencia = Number.parseInt(producto.existencia, 10) || 0;
        const precio = Number.parseFloat(producto.precio_promedio) || 0;

        precioInput.value = formatoNumero.format(precio);

        if (cantidad > existencia) {
            stockInfo.textContent =
                `Stock insuficiente. Disponible: ${existencia}`;

            stockInfo.className = 'stock-disponible error';

            cantidadInput.setCustomValidity(
                `La existencia disponible es ${existencia}`
            );
        } else {
            stockInfo.textContent = `Disponible: ${existencia}`;
            stockInfo.className = 'stock-disponible ok';
            cantidadInput.setCustomValidity('');
        }

        const subtotal = cantidad * precio;
        const iva = subtotal * 0.16;
        const total = subtotal + iva;

        actualizarImportesProducto(productoDiv, subtotal, iva, total);
        calcularTotales();
    }

    function actualizarImportesProducto(
        productoDiv,
        subtotal,
        iva,
        total
    ) {
        productoDiv.querySelector('.subtotal-producto').textContent =
            moneda(subtotal);

        productoDiv.querySelector('.iva-producto').textContent =
            moneda(iva);

        productoDiv.querySelector('.total-producto').textContent =
            moneda(total);
    }

    function calcularTotales() {
        const productos = contenedor.querySelectorAll('.producto-item');

        let subtotalGeneral = 0;
        let ivaGeneral = 0;
        let totalGeneral = 0;
        let contadorProductos = 0;
        let stockValido = true;

        productos.forEach((productoDiv) => {
            const producto = productoDiv.productoSeleccionado;
            const cantidadInput =
                productoDiv.querySelector('.cantidad-input');

            if (!producto) {
                return;
            }

            const cantidad =
                Number.parseInt(cantidadInput.value, 10) || 0;

            const existencia =
                Number.parseInt(producto.existencia, 10) || 0;

            const precio =
                Number.parseFloat(producto.precio_promedio) || 0;

            if (cantidad < 1 || cantidad > existencia) {
                stockValido = false;
            }

            subtotalGeneral += cantidad * precio;
            ivaGeneral += cantidad * precio * 0.16;
            totalGeneral += cantidad * precio * 1.16;
            contadorProductos++;
        });

        document.getElementById('total-productos').textContent =
            contadorProductos;

        document.getElementById('subtotal-total').textContent =
            moneda(subtotalGeneral);

        document.getElementById('iva-total').textContent =
            moneda(ivaGeneral);

        document.getElementById('total-general').textContent =
            moneda(totalGeneral);

        actualizarEstadoBoton(stockValido);
    }

    function actualizarEstadoBoton(stockValido) {
        const botonEnviar = formulario.querySelector(
            'button[type="submit"]'
        );

        botonEnviar.disabled = !stockValido;

        botonEnviar.classList.toggle('opacity-50', !stockValido);
        botonEnviar.classList.toggle('cursor-not-allowed', !stockValido);
    }

    function productoYaSeleccionado(productoId, productoActual) {
        const productos = contenedor.querySelectorAll('.producto-item');

        return Array.from(productos).some((productoDiv) => {
            if (productoDiv === productoActual) {
                return false;
            }

            const inventarioId =
                productoDiv.querySelector('.inventario-id').value;

            return String(inventarioId) === String(productoId);
        });
    }

    function eliminarProducto(productoDiv) {
        const productos = contenedor.querySelectorAll('.producto-item');

        if (productos.length <= 1) {
            alert('Debe haber al menos un producto en la salida');
            return;
        }

        clearTimeout(productoDiv.temporizadorBusqueda);

        if (productoDiv.controladorBusqueda) {
            productoDiv.controladorBusqueda.abort();
        }

        productoDiv.remove();

        renumerarProductos();
        calcularTotales();
    }

    function renumerarProductos() {
        const productos = contenedor.querySelectorAll('.producto-item');

        productos.forEach((productoDiv, index) => {
            const numero = productoDiv.querySelector('.numero-producto');

            numero.textContent = index + 1;
        });
    }

    function controlarTecladoResultados(productoDiv, event) {
        const resultados = productoDiv.querySelectorAll(
            '[data-resultado-index]'
        );

        if (resultados.length === 0) {
            if (event.key === 'Escape') {
                cerrarResultados(productoDiv);
            }

            return;
        }

        if (event.key === 'ArrowDown') {
            event.preventDefault();

            productoDiv.resultadoActivo =
                (productoDiv.resultadoActivo + 1) % resultados.length;

            actualizarResultadoActivo(productoDiv, resultados);
        }

        if (event.key === 'ArrowUp') {
            event.preventDefault();

            productoDiv.resultadoActivo =
                productoDiv.resultadoActivo <= 0
                    ? resultados.length - 1
                    : productoDiv.resultadoActivo - 1;

            actualizarResultadoActivo(productoDiv, resultados);
        }

        if (event.key === 'Enter') {
            if (productoDiv.resultadoActivo >= 0) {
                event.preventDefault();

                const producto =
                    productoDiv.resultadosBusqueda[
                        productoDiv.resultadoActivo
                    ];

                seleccionarProducto(productoDiv, producto);
            }
        }

        if (event.key === 'Escape') {
            cerrarResultados(productoDiv);
        }
    }

    function actualizarResultadoActivo(productoDiv, resultados) {
        resultados.forEach((resultado, index) => {
            const activo = index === productoDiv.resultadoActivo;

            resultado.classList.toggle('bg-blue-100', activo);
            resultado.classList.toggle('dark:bg-gray-600', activo);

            if (activo) {
                resultado.scrollIntoView({
                    block: 'nearest',
                });
            }
        });
    }

    function cerrarResultados(productoDiv) {
        const resultados =
            productoDiv.querySelector('.producto-resultados');

        const buscador =
            productoDiv.querySelector('.producto-busqueda');

        resultados.classList.add('hidden');
        buscador.setAttribute('aria-expanded', 'false');

        productoDiv.resultadosBusqueda = [];
        productoDiv.resultadoActivo = -1;
    }

    function resetForm() {
        if (!confirm('¿Está seguro de que desea limpiar el formulario?')) {
            return;
        }

        formulario.reset();
        contenedor.innerHTML = '';
        productoIndex = 0;

        agregarProducto();
        calcularTotales();
    }

    function validarFormulario(event) {
        const productos =
            contenedor.querySelectorAll('.producto-item');

        if (productos.length === 0) {
            event.preventDefault();
            alert('Debe agregar al menos un producto a la salida');
            return;
        }

        for (const productoDiv of productos) {
            const producto = productoDiv.productoSeleccionado;
            const buscador =
                productoDiv.querySelector('.producto-busqueda');

            const cantidad =
                productoDiv.querySelector('.cantidad-input');

            if (!producto) {
                event.preventDefault();

                alert(
                    'Debe buscar y seleccionar un producto válido en cada renglón'
                );

                buscador.focus();
                return;
            }

            if (!cantidad.checkValidity()) {
                event.preventDefault();
                cantidad.reportValidity();
                cantidad.focus();
                return;
            }
        }

        const totalGeneral =
            document.getElementById('total-general').textContent;

        if (!confirm(`¿Confirmar salida por ${totalGeneral}?`)) {
            event.preventDefault();
        }
    }

    botonAgregar.addEventListener('click', agregarProducto);
    botonLimpiar.addEventListener('click', resetForm);
    formulario.addEventListener('submit', validarFormulario);

    document.addEventListener('click', (event) => {
        if (event.target.closest('.producto-buscador')) {
            return;
        }

        contenedor
            .querySelectorAll('.producto-item')
            .forEach(cerrarResultados);
    });

    agregarProducto();
});