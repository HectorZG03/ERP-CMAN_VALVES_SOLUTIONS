document.addEventListener('DOMContentLoaded', () => {
    const formulario = document.getElementById('solicitudForm');

    if (!formulario) {
        return;
    }

    const buscarProductosUrl = formulario.dataset.buscarProductosUrl;
    const productoBaseUrl = formulario.dataset.productoBaseUrl;

    const productosAnterioresElemento = document.getElementById(
        'productos-anteriores'
    );

    let productosAnteriores = [];

    try {
        productosAnteriores = JSON.parse(
            productosAnterioresElemento?.textContent || '[]'
        );
    } catch (error) {
        console.error(
            'No fue posible leer los productos anteriores.',
            error
        );
    }

    const contenedorFormulario = document.getElementById(
        'contenedor-formulario'
    );

    const buscador = document.getElementById('buscador-producto');
    const resultados = document.getElementById('resultados-busqueda');
    const productosContenedor = document.getElementById(
        'productos-agregados'
    );

    const plantilla = document.getElementById('template-producto');
    const contador = document.getElementById('contador-productos');
    const mensajeInicial = document.getElementById('mensaje-inicial');
    const resumen = document.getElementById('resumen-solicitud');
    const botonEnviar = document.getElementById('btn-enviar');
    const botonLimpiar = document.getElementById('btn-limpiar');
    const descripcionTipo = document.getElementById('descripcion-tipo');
    const etiquetaBuscador = document.getElementById(
        'etiqueta-buscador'
    );

    const ayudaBuscador = document.getElementById('ayuda-buscador');
    const panelBuscador = document.getElementById('panel-buscador');
    const panelProceso = document.getElementById('panel-proceso');
    const tituloProceso = document.getElementById('titulo-proceso');
    const textoProceso = document.getElementById('texto-proceso');

    const productos = new Map();

    let siguienteIndice = 0;
    let temporizadorBusqueda = null;
    let tipoAnterior = obtenerTipoSolicitud();

    function obtenerTipoSolicitud() {
        const seleccionado = document.querySelector(
            'input[name="tipo_solicitud"]:checked'
        );

        if (seleccionado) {
            return seleccionado.value;
        }

        const oculto = document.querySelector(
            'input[name="tipo_solicitud"][type="hidden"]'
        );

        return oculto?.value || 'estandar';
    }

    function esEpp() {
        return obtenerTipoSolicitud() === 'epp';
    }

    function construirUrl(base, parametros = {}) {
        const url = new URL(base, window.location.origin);

        Object.entries(parametros).forEach(([clave, valor]) => {
            if (
                valor !== null
                && valor !== undefined
                && valor !== ''
            ) {
                url.searchParams.set(clave, valor);
            }
        });

        return url.toString();
    }

    async function obtenerJson(url) {
        const respuesta = await fetch(url, {
            headers: {
                Accept: 'application/json',
            },
        });

        if (!respuesta.ok) {
            throw new Error(
                'La solicitud no pudo completarse.'
            );
        }

        return respuesta.json();
    }

    function limpiarBuscador() {
        buscador.value = '';
        resultados.innerHTML = '';
        resultados.classList.add('hidden');
    }

    function aplicarAparienciaTipo() {
        const solicitudEpp = esEpp();

        const indicadorTipo = document.getElementById('indicador-tipo');
        const filtroProductos = document.getElementById('filtro-productos');

        if (indicadorTipo) {
            indicadorTipo.textContent = solicitudEpp
                ? 'EPP'
                : 'Estándar';

            indicadorTipo.className = solicitudEpp
                ? 'rounded-full bg-orange-200 px-3 py-1 text-xs font-bold uppercase tracking-wide text-orange-800 dark:bg-orange-800 dark:text-orange-100'
                : 'rounded-full bg-blue-100 px-3 py-1 text-xs font-bold uppercase tracking-wide text-blue-700 dark:bg-blue-900/40 dark:text-blue-300';
        }

        if (filtroProductos) {
            filtroProductos.textContent = solicitudEpp
                ? 'Categoría SEGURIDAD'
                : 'Todas las categorías';
        }

        contenedorFormulario.classList.toggle(
            'bg-white',
            !solicitudEpp
        );

        contenedorFormulario.classList.toggle(
            'dark:bg-gray-800',
            !solicitudEpp
        );

        contenedorFormulario.classList.toggle(
            'bg-orange-50',
            solicitudEpp
        );

        contenedorFormulario.classList.toggle(
            'border-orange-300',
            solicitudEpp
        );

        contenedorFormulario.classList.toggle(
            'ring-2',
            solicitudEpp
        );

        contenedorFormulario.classList.toggle(
            'ring-orange-200',
            solicitudEpp
        );

        contenedorFormulario.classList.toggle(
            'dark:bg-orange-950/30',
            solicitudEpp
        );

        contenedorFormulario.classList.toggle(
            'dark:border-orange-700',
            solicitudEpp
        );

        panelBuscador.classList.toggle(
            'bg-gray-50',
            !solicitudEpp
        );

        panelBuscador.classList.toggle(
            'dark:bg-gray-700',
            !solicitudEpp
        );

        panelBuscador.classList.toggle(
            'bg-orange-100',
            solicitudEpp
        );

        panelBuscador.classList.toggle(
            'dark:bg-orange-900/30',
            solicitudEpp
        );

        panelProceso.classList.toggle(
            'bg-blue-50',
            !solicitudEpp
        );

        panelProceso.classList.toggle(
            'dark:bg-blue-900/30',
            !solicitudEpp
        );

        panelProceso.classList.toggle(
            'bg-orange-100',
            solicitudEpp
        );

        panelProceso.classList.toggle(
            'dark:bg-orange-900/30',
            solicitudEpp
        );

        tituloProceso.className = solicitudEpp
            ? 'mb-2 text-lg font-medium text-orange-900 dark:text-orange-200'
            : 'mb-2 text-lg font-medium text-blue-900 dark:text-blue-200';

        textoProceso.className = solicitudEpp
            ? 'space-y-1 text-sm text-orange-700 dark:text-orange-300'
            : 'space-y-1 text-sm text-blue-700 dark:text-blue-300';

        if (solicitudEpp) {
            descripcionTipo.textContent =
                'Solicitud de equipos de protección personal para almacén.';

            etiquetaBuscador.textContent =
                'Buscar equipo de seguridad';

            buscador.placeholder =
                'Nombre, número económico, talla o medida...';

            ayudaBuscador.textContent =
                'Solo se mostrarán productos de la categoría SEGURIDAD.';
        } else {
            descripcionTipo.textContent =
                'Registra los materiales que necesitas solicitar al almacén.';

            etiquetaBuscador.textContent =
                'Buscar producto';

            buscador.placeholder =
                'Nombre, número económico, categoría o medida...';

            ayudaBuscador.textContent =
                'Escribe al menos dos caracteres para buscar.';
        }

        limpiarBuscador();
    }

    function actualizarResumen() {
        let totalUnidades = 0;
        let valorEstimado = 0;

        productosContenedor
            .querySelectorAll('.producto-item')
            .forEach((elemento) => {
                const cantidad = Number(
                    elemento.querySelector('.cantidad-input').value
                ) || 0;

                const precio = Number(
                    elemento.dataset.precio
                ) || 0;

                totalUnidades += cantidad;
                valorEstimado += cantidad * precio;
            });

        document.getElementById('total-productos').textContent =
            productos.size;

        document.getElementById('total-unidades').textContent =
            totalUnidades;

        document.getElementById('valor-estimado').textContent =
            new Intl.NumberFormat('es-MX', {
                style: 'currency',
                currency: 'MXN',
            }).format(valorEstimado);
    }

    function actualizarInterfaz() {
        const cantidad = productos.size;

        contador.textContent =
            `${cantidad} producto${cantidad === 1 ? '' : 's'} `
            + `agregado${cantidad === 1 ? '' : 's'}`;

        mensajeInicial.classList.toggle(
            'hidden',
            cantidad > 0
        );

        resumen.classList.toggle(
            'hidden',
            cantidad === 0
        );

        botonEnviar.disabled = cantidad === 0;

        actualizarResumen();
    }

    function vaciarProductos() {
        productos.clear();
        siguienteIndice = 0;
        productosContenedor.innerHTML = '';

        actualizarInterfaz();
    }

    function eliminarProducto(inventarioId) {
        productos.delete(inventarioId);

        const elemento = productosContenedor.querySelector(
            `[data-inventario-id="${inventarioId}"]`
        );

        elemento?.remove();

        actualizarInterfaz();
    }

    function agregarProducto(
        producto,
        cantidadInicial = 1
    ) {
        const inventarioId = Number(producto.id);

        if (productos.has(inventarioId)) {
            return;
        }

        const fragmento = plantilla.content.cloneNode(true);
        const elemento = fragmento.querySelector(
            '.producto-item'
        );

        const cantidadInput = fragmento.querySelector(
            '.cantidad-input'
        );

        const inventarioInput = fragmento.querySelector(
            '.inventario-id'
        );

        const botonEliminar = fragmento.querySelector(
            '.btn-eliminar'
        );

        const precio = Number(
            producto.precio_promedio
        ) || 0;

        const cantidad = Math.max(
            1,
            Number(cantidadInicial) || 1
        );

        elemento.dataset.inventarioId = inventarioId;
        elemento.dataset.precio = precio;

        fragmento.querySelector(
            '.producto-nombre'
        ).textContent = producto.nombre_producto;

        fragmento.querySelector(
            '.producto-economico'
        ).textContent = producto.economico
            ? `Económico: ${producto.economico}`
            : 'Sin número económico';

        fragmento.querySelector(
            '.producto-categoria'
        ).textContent = producto.categoria || 'N/A';

        fragmento.querySelector(
            '.producto-medida'
        ).textContent = producto.medida || 'N/A';

        fragmento.querySelector(
            '.producto-stock'
        ).textContent = producto.existencia;

        fragmento.querySelector(
            '.producto-precio'
        ).textContent = new Intl.NumberFormat('es-MX', {
            style: 'currency',
            currency: 'MXN',
        }).format(precio);

        inventarioInput.name =
            `productos[${siguienteIndice}][inventario_id]`;

        inventarioInput.value = inventarioId;

        cantidadInput.name =
            `productos[${siguienteIndice}]`
            + '[cantidad_solicitada]';

        cantidadInput.value = cantidad;
        cantidadInput.max = producto.existencia;

        cantidadInput.addEventListener(
            'input',
            actualizarResumen
        );

        botonEliminar.addEventListener('click', () => {
            eliminarProducto(inventarioId);
        });

        productos.set(inventarioId, producto);
        siguienteIndice += 1;

        productosContenedor.appendChild(fragmento);

        actualizarInterfaz();
        limpiarBuscador();
    }

    async function cargarProducto(
        inventarioId,
        cantidadInicial = 1
    ) {
        const url = construirUrl(
            `${productoBaseUrl}/${inventarioId}`,
            {
                tipo_solicitud: obtenerTipoSolicitud(),
            }
        );

        const producto = await obtenerJson(url);

        agregarProducto(
            producto,
            cantidadInicial
        );
    }

    function mostrarResultados(listaProductos) {
        resultados.innerHTML = '';

        if (
            !Array.isArray(listaProductos)
            || listaProductos.length === 0
        ) {
            const mensaje = document.createElement('div');

            mensaje.className =
                'p-3 text-center text-gray-500 '
                + 'dark:text-gray-400';

            mensaje.textContent =
                'No se encontraron productos.';

            resultados.appendChild(mensaje);
            resultados.classList.remove('hidden');

            return;
        }

        listaProductos.forEach((producto) => {
            const inventarioId = Number(producto.id);
            const yaAgregado = productos.has(inventarioId);
            const opcion = document.createElement('button');

            opcion.type = 'button';
            opcion.disabled = yaAgregado;

            opcion.className =
                'block w-full border-b border-gray-100 '
                + 'p-3 text-left transition-colors '
                + 'dark:border-gray-600 '
                + (
                    yaAgregado
                        ? 'cursor-not-allowed opacity-50'
                        : 'hover:bg-gray-50 dark:hover:bg-gray-600'
                );

            const contenido = document.createElement('div');

            contenido.className =
                'flex items-center justify-between gap-4';

            const informacion = document.createElement('div');

            informacion.className = 'min-w-0';

            const nombre = document.createElement('div');

            nombre.className =
                'font-medium text-gray-900 dark:text-white';

            nombre.textContent = producto.nombre_producto;

            const detalle = document.createElement('div');

            detalle.className =
                'text-sm text-gray-600 dark:text-gray-400';

            detalle.textContent = [
                producto.economico || 'Sin económico',
                producto.categoria || 'Sin categoría',
                producto.medida || 'Sin medida',
            ].join(' · ');

            informacion.appendChild(nombre);
            informacion.appendChild(detalle);

            const existencia = document.createElement('div');

            existencia.className =
                producto.existencia > 10
                    ? 'shrink-0 text-sm font-medium '
                        + 'text-green-600 dark:text-green-400'
                    : 'shrink-0 text-sm font-medium '
                        + 'text-yellow-600 dark:text-yellow-400';

            existencia.textContent = yaAgregado
                ? 'Ya agregado'
                : `Stock: ${producto.existencia}`;

            contenido.appendChild(informacion);
            contenido.appendChild(existencia);
            opcion.appendChild(contenido);

            if (!yaAgregado) {
                opcion.addEventListener('click', async () => {
                    try {
                        await cargarProducto(inventarioId);
                    } catch (error) {
                        console.error(error);

                        alert(
                            'No fue posible obtener '
                            + 'el producto seleccionado.'
                        );
                    }
                });
            }

            resultados.appendChild(opcion);
        });

        resultados.classList.remove('hidden');
    }

    async function buscarProductos(termino) {
        if (termino.length < 2) {
            resultados.classList.add('hidden');
            return;
        }

        try {
            const url = construirUrl(
                buscarProductosUrl,
                {
                    q: termino,
                    tipo_solicitud: obtenerTipoSolicitud(),
                }
            );

            const listaProductos = await obtenerJson(url);

            mostrarResultados(listaProductos);
        } catch (error) {
            console.error(error);
            resultados.classList.add('hidden');
        }
    }

    document
        .querySelectorAll(
            'input[name="tipo_solicitud"][type="radio"]'
        )
        .forEach((radio) => {
            radio.addEventListener('change', (evento) => {
                const nuevoTipo = evento.target.value;

                if (
                    productos.size > 0
                    && !window.confirm(
                        'Al cambiar el tipo de solicitud '
                        + 'se eliminarán los productos agregados. '
                        + '¿Deseas continuar?'
                    )
                ) {
                    const radioAnterior = document.querySelector(
                        `input[name="tipo_solicitud"]`
                        + `[value="${tipoAnterior}"]`
                    );

                    if (radioAnterior) {
                        radioAnterior.checked = true;
                    }

                    return;
                }

                if (productos.size > 0) {
                    vaciarProductos();
                }

                tipoAnterior = nuevoTipo;

                aplicarAparienciaTipo();
            });
        });

    buscador.addEventListener('input', () => {
        clearTimeout(temporizadorBusqueda);

        const termino = buscador.value.trim();

        temporizadorBusqueda = window.setTimeout(() => {
            buscarProductos(termino);
        }, 300);
    });

    document.addEventListener('click', (evento) => {
        if (
            !evento.target.closest('#buscador-producto')
            && !evento.target.closest('#resultados-busqueda')
        ) {
            resultados.classList.add('hidden');
        }
    });

    botonLimpiar.addEventListener('click', () => {
        if (
            productos.size > 0
            && !window.confirm(
                '¿Estás seguro de que deseas '
                + 'limpiar toda la solicitud?'
            )
        ) {
            return;
        }

        formulario.reset();
        vaciarProductos();

        tipoAnterior = obtenerTipoSolicitud();

        aplicarAparienciaTipo();
    });

    formulario.addEventListener('submit', (evento) => {
        if (productos.size === 0) {
            evento.preventDefault();

            alert(
                'Debe agregar al menos un producto '
                + 'a la solicitud.'
            );
        }
    });

    async function restaurarProductosAnteriores() {
        for (const productoAnterior of productosAnteriores) {
            try {
                await cargarProducto(
                    productoAnterior.inventario_id,
                    productoAnterior.cantidad_solicitada
                );
            } catch (error) {
                console.error(
                    'No fue posible restaurar '
                    + 'un producto anterior.',
                    error
                );
            }
        }
    }

    aplicarAparienciaTipo();
    actualizarInterfaz();
    restaurarProductosAnteriores();
});