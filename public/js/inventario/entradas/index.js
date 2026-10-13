/**
 * Módulo: Entradas de material
 * Archivo: public/js/inventario/entradas/index.js
 *
 * Funcionalidades:
 * - Búsqueda asíncrona con debounce, sin recargar la página.
 * - Filtro por periodo.
 * - Botón para limpiar filtros.
 * - Compatible con la paginación de Laravel.
 */

document.addEventListener('DOMContentLoaded', () => {
    const form = document.querySelector('[data-entradas-filter-form]');

    // Evitar errores si el formulario no existe en la vista.
    if (!form) {
        return;
    }

    const inputBuscar = form.querySelector('input[name="buscar"]');
    const selectPeriodo = form.querySelector('select[name="periodo"]');
    const botonLimpiar = form.querySelector('[data-entradas-clear]');
    const resultados = document.querySelector('[data-live-results]');

    if (!resultados) return;

    let temporizador = null;
    let controlador = null;

    const crearUrl = () => {
        const url = new URL(form.action, window.location.origin);
        const datos = new FormData(form);

        url.search = '';
        for (const [clave, valor] of datos.entries()) {
            const texto = String(valor).trim();
            if (texto !== '') url.searchParams.set(clave, texto);
        }

        return url;
    };

    const actualizarEstadoLimpiar = (url) => {
        if (!botonLimpiar) return;

        const hayFiltros = Boolean(url.searchParams.get('buscar'))
            || (url.searchParams.get('periodo') || 'todos') !== 'todos';

        botonLimpiar.classList.toggle('hidden', !hayFiltros);
    };

    const cargarResultados = async (url, agregarHistorial = false) => {
        if (controlador) controlador.abort();

        controlador = new AbortController();
        form.setAttribute('aria-busy', 'true');
        resultados.setAttribute('aria-busy', 'true');

        try {
            const respuesta = await fetch(url, {
                headers: {
                    Accept: 'text/html',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                signal: controlador.signal,
            });

            if (!respuesta.ok) throw new Error(`HTTP ${respuesta.status}`);

            const html = await respuesta.text();
            const documento = new DOMParser().parseFromString(html, 'text/html');
            const nuevosResultados = documento.querySelector('[data-live-results]');

            if (!nuevosResultados) throw new Error('No se encontró el bloque de resultados.');

            resultados.innerHTML = nuevosResultados.innerHTML;

            if (agregarHistorial) {
                window.history.pushState({}, '', url);
            } else {
                window.history.replaceState({}, '', url);
            }

            actualizarEstadoLimpiar(url);
        } catch (error) {
            if (error.name !== 'AbortError') {
                console.error('No se pudieron actualizar las entradas:', error);
            }
        } finally {
            form.removeAttribute('aria-busy');
            resultados.removeAttribute('aria-busy');
        }
    };

    const buscar = () => cargarResultados(crearUrl());

    if (inputBuscar) {
        inputBuscar.addEventListener('input', () => {
            window.clearTimeout(temporizador);
            temporizador = window.setTimeout(buscar, 350);
        });
    }

    if (selectPeriodo) {
        selectPeriodo.addEventListener('change', buscar);
    }

    form.addEventListener('submit', (event) => {
        event.preventDefault();
        window.clearTimeout(temporizador);
        buscar();
    });

    if (botonLimpiar) {
        botonLimpiar.addEventListener('click', () => {
            window.clearTimeout(temporizador);
            if (inputBuscar) inputBuscar.value = '';
            if (selectPeriodo) selectPeriodo.value = 'todos';
            buscar();
        });
    }

    resultados.addEventListener('click', (event) => {
        const enlace = event.target.closest('[data-live-pagination] a[href]');
        if (!enlace) return;

        event.preventDefault();
        cargarResultados(new URL(enlace.href), true);
    });

    window.addEventListener('popstate', () => {
        const url = new URL(window.location.href);
        if (inputBuscar) inputBuscar.value = url.searchParams.get('buscar') || '';
        if (selectPeriodo) selectPeriodo.value = url.searchParams.get('periodo') || 'todos';
        actualizarEstadoLimpiar(url);
        cargarResultados(url);
    });
});
