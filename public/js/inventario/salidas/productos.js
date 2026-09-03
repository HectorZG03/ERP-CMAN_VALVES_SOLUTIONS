import {
    convertirNumero,
    moneda,
} from './utilidades.js';

export class GestorProductos {
    constructor({
        contenedor,
        totalProductos,
        subtotalTotal,
        ivaTotal,
        totalGeneral,
        alCambiar,
    }) {
        this.contenedor = contenedor;
        this.totalProductos = totalProductos;
        this.subtotalTotal = subtotalTotal;
        this.ivaTotal = ivaTotal;
        this.totalGeneral = totalGeneral;
        this.alCambiar = alCambiar;
        this.productoIndex = 0;
        this.totalCalculado = 0;
    }

    cargar(productos = []) {
        this.limpiar();

        productos.forEach((producto) => {
            this.agregar(producto);
        });

        this.renumerar();
        this.calcularTotales();
    }

    limpiar() {
        this.contenedor.innerHTML = '';
        this.productoIndex = 0;
        this.calcularTotales();
    }

    agregar(producto) {
        const existencia = Math.max(
            0,
            Math.trunc(
                convertirNumero(producto.existencia)
            )
        );

        const cantidadPendiente = Math.max(
            0,
            Math.trunc(
                convertirNumero(
                    producto.cantidad_pendiente
                )
            )
        );

        const cantidadMaxima = Math.min(
            existencia,
            cantidadPendiente
        );

        if (cantidadMaxima < 1) {
            this.agregarAdvertenciaSinStock(
                producto,
                cantidadPendiente
            );

            return;
        }

        const index = this.productoIndex++;

        const precio = Math.max(
            0,
            convertirNumero(producto.precio_unitario)
        );

        const productoDiv =
            document.createElement('div');

        productoDiv.className =
            'producto-item bg-gray-50 dark:bg-gray-700 ' +
            'p-4 rounded-lg border border-gray-200 ' +
            'dark:border-gray-600 transition-colors duration-200';

        productoDiv.dataset.precio = String(precio);

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

                            <input
                                type="hidden"
                                class="inventario-id"
                                name="productos[${index}][inventario_id]"
                                value="${Number(producto.inventario_id)}">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Cantidad a entregar *
                            </label>

                            <input
                                type="number"
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
                    <button
                        type="button"
                        class="eliminar-producto remove-producto bg-red-500 hover:bg-red-600 text-white p-2 rounded transition-colors duration-200"
                        title="No entregar este material ahora">

                        <svg
                            class="w-5 h-5"
                            fill="currentColor"
                            viewBox="0 0 20 20">

                            <path
                                fill-rule="evenodd"
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
        ).textContent =
            producto.nombre_producto;

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
            `Existencia: ${existencia} | ` +
            `Máximo: ${cantidadMaxima}`;

        productoDiv.querySelector(
            '.precio-unitario'
        ).textContent = moneda(precio);

        productoDiv
            .querySelector('.cantidad-input')
            .addEventListener('input', () => {
                this.actualizarProducto(productoDiv);
            });

        productoDiv
            .querySelector('.eliminar-producto')
            .addEventListener('click', () => {
                productoDiv.remove();
                this.renumerar();
                this.calcularTotales();
            });

        this.contenedor.appendChild(productoDiv);
        this.actualizarProducto(productoDiv);
    }

    agregarAdvertenciaSinStock(
        producto,
        cantidadPendiente
    ) {
        const advertencia =
            document.createElement('div');

        advertencia.className =
            'advertencia-stock bg-yellow-50 ' +
            'dark:bg-yellow-900/20 border ' +
            'border-yellow-200 dark:border-yellow-800 ' +
            'rounded-lg p-4 text-sm text-yellow-800 ' +
            'dark:text-yellow-200';

        advertencia.textContent =
            `${producto.nombre_producto}: quedan ` +
            `${cantidadPendiente} unidad(es) pendientes, ` +
            'pero actualmente no hay existencia disponible.';

        this.contenedor.appendChild(advertencia);
    }

    actualizarProducto(productoDiv) {
        const cantidadInput =
            productoDiv.querySelector('.cantidad-input');

        const cantidad = Math.max(
            0,
            Math.trunc(
                convertirNumero(cantidadInput.value)
            )
        );

        const precio = convertirNumero(
            productoDiv.dataset.precio
        );

        const subtotal = cantidad * precio;
        const iva = subtotal * 0.16;
        const total = subtotal + iva;

        const stockDisponible =
            productoDiv.querySelector(
                '.stock-disponible'
            );

        const cantidadValida =
            cantidad >= 1 &&
            cantidadInput.checkValidity();

        stockDisponible.classList.toggle(
            'ok',
            cantidadValida
        );

        stockDisponible.classList.toggle(
            'error',
            !cantidadValida
        );

        productoDiv.querySelector(
            '.subtotal-producto'
        ).textContent = moneda(subtotal);

        productoDiv.querySelector(
            '.iva-producto'
        ).textContent = moneda(iva);

        productoDiv.querySelector(
            '.total-producto'
        ).textContent = moneda(total);

        this.calcularTotales();
    }

    renumerar() {
        const productos = this.obtenerProductos();

        productos.forEach((productoDiv, index) => {
            productoDiv.querySelector(
                '.numero-producto'
            ).textContent = index + 1;

            productoDiv.querySelector(
                '.inventario-id'
            ).name =
                `productos[${index}][inventario_id]`;

            productoDiv.querySelector(
                '.cantidad-input'
            ).name =
                `productos[${index}][cantidad]`;
        });

        this.productoIndex = productos.length;
    }

    calcularTotales() {
        const productos = this.obtenerProductos();

        let subtotalGeneral = 0;
        let ivaGeneral = 0;
        let totalGeneral = 0;
        let formularioValido = productos.length > 0;

        productos.forEach((productoDiv) => {
            const cantidadInput =
                productoDiv.querySelector(
                    '.cantidad-input'
                );

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

        this.totalCalculado = totalGeneral;

        this.totalProductos.textContent =
            productos.length;

        this.subtotalTotal.textContent =
            moneda(subtotalGeneral);

        this.ivaTotal.textContent =
            moneda(ivaGeneral);

        this.totalGeneral.textContent =
            moneda(totalGeneral);

        this.alCambiar({
            valido: formularioValido,
            cantidadProductos: productos.length,
            total: totalGeneral,
        });
    }

    validarYReportar() {
        const productos = this.obtenerProductos();

        if (productos.length === 0) {
            return false;
        }

        for (const productoDiv of productos) {
            const cantidadInput =
                productoDiv.querySelector(
                    '.cantidad-input'
                );

            if (!cantidadInput.checkValidity()) {
                cantidadInput.reportValidity();
                cantidadInput.focus();

                return false;
            }
        }

        return true;
    }

    obtenerTotal() {
        return this.totalCalculado;
    }

    obtenerProductos() {
        return Array.from(
            this.contenedor.querySelectorAll(
                '.producto-item'
            )
        );
    }
}