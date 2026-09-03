import { crearEspera } from './utilidades.js';

export class BuscadorSolicitudes {
    constructor({
        raiz,
        campoBusqueda,
        campoId,
        resultados,
        estado,
        urlBusqueda,
        alSeleccionar,
        alDescartar,
    }) {
        this.raiz = raiz;
        this.campoBusqueda = campoBusqueda;
        this.campoId = campoId;
        this.resultados = resultados;
        this.estado = estado;
        this.urlBusqueda = urlBusqueda;
        this.alSeleccionar = alSeleccionar;
        this.alDescartar = alDescartar;
        this.controlador = null;
        this.indiceActivo = -1;
        this.botones = [];

        this.buscarConEspera = crearEspera(
            () => this.buscar(),
            300
        );
    }

    iniciar() {
        this.campoBusqueda.addEventListener('input', () => {
            if (this.campoId.value) {
                this.campoId.value = '';
                this.alDescartar();
            }

            this.buscarConEspera();
        });

        this.campoBusqueda.addEventListener('keydown', (event) => {
            this.manejarTeclado(event);
        });

        this.campoBusqueda.addEventListener('focus', () => {
            if (
                !this.campoId.value &&
                this.terminoEsValido(this.obtenerTermino())
            ) {
                this.buscarConEspera();
            }
        });

        document.addEventListener('click', (event) => {
            if (!this.raiz.contains(event.target)) {
                this.ocultarResultados();
            }
        });

        if (this.campoId.value) {
            this.campoBusqueda.value =
                `Solicitud #${this.campoId.value}`;
        }
    }

    async buscar() {
        const termino = this.obtenerTermino();

        if (!this.terminoEsValido(termino)) {
            this.cancelar();
            this.ocultarResultados();

            this.actualizarEstado(
                termino
                    ? 'Escribe al menos dos caracteres.'
                    : 'Escribe un número de solicitud o al menos dos caracteres.'
            );

            return;
        }

        this.cancelar();

        const controlador = new AbortController();
        const terminoConsultado = termino;

        this.controlador = controlador;
        this.actualizarEstado('Buscando solicitudes...');

        try {
            const url = new URL(
                this.urlBusqueda,
                window.location.origin
            );

            url.searchParams.set('q', terminoConsultado);

            const respuesta = await fetch(url, {
                method: 'GET',
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                signal: controlador.signal,
            });

            if (!respuesta.ok) {
                throw new Error(
                    `No fue posible buscar solicitudes. HTTP ${respuesta.status}`
                );
            }

            const solicitudes = await respuesta.json();

            if (this.obtenerTermino() !== terminoConsultado) {
                return;
            }

            this.mostrarResultados(solicitudes);
        } catch (error) {
            if (error.name === 'AbortError') {
                return;
            }

            console.error('Error al buscar solicitudes:', error);

            this.ocultarResultados();
            this.actualizarEstado(
                'No fue posible realizar la búsqueda.',
                true
            );
        } finally {
            if (this.controlador === controlador) {
                this.controlador = null;
            }
        }
    }

    mostrarResultados(solicitudes) {
        this.resultados.innerHTML = '';
        this.botones = [];
        this.indiceActivo = -1;

        if (
            !Array.isArray(solicitudes) ||
            solicitudes.length === 0
        ) {
            const mensaje = document.createElement('p');

            mensaje.className =
                'px-4 py-3 text-sm text-gray-600 dark:text-gray-300';

            mensaje.textContent =
                'No se encontraron solicitudes aprobadas con materiales pendientes.';

            this.resultados.appendChild(mensaje);
            this.mostrarContenedor();
            this.actualizarEstado('Sin coincidencias disponibles.');

            return;
        }

        solicitudes.forEach((solicitud, index) => {
            const boton = this.crearResultado(solicitud, index);

            this.resultados.appendChild(boton);
            this.botones.push(boton);
        });

        this.mostrarContenedor();

        this.actualizarEstado(
            `${solicitudes.length} coincidencia(s) encontrada(s).`
        );
    }

    crearResultado(solicitud, index) {
        const boton = document.createElement('button');
        const encabezado = document.createElement('span');
        const informacion = document.createElement('span');
        const pendientes = document.createElement('span');

        boton.type = 'button';
        boton.id = `solicitud-resultado-${index}`;
        boton.role = 'option';

        boton.className =
            'block w-full border-b border-gray-100 dark:border-gray-600 ' +
            'px-4 py-3 text-left hover:bg-blue-50 dark:hover:bg-gray-600 ' +
            'focus:bg-blue-50 dark:focus:bg-gray-600 focus:outline-none ' +
            'last:border-b-0';

        encabezado.className =
            'block font-semibold text-gray-900 dark:text-white';

            const folio = solicitud.folio ||
            String(solicitud.id).padStart(4, '0');

        encabezado.textContent =
            `Solicitud #${folio} — ${solicitud.solicitante}`;

        informacion.className =
            'mt-1 block text-xs text-gray-600 dark:text-gray-300';

        informacion.textContent = [
            solicitud.destino,
            solicitud.fecha_solicitud,
            `Empleado: ${solicitud.numero_empleado}`,
            `Operador: ${solicitud.operador}`,
        ].join(' | ');

        pendientes.className =
            'mt-1 block text-xs font-medium ' +
            'text-blue-700 dark:text-blue-300';

        pendientes.textContent =
            `${solicitud.productos_pendientes} producto(s) | ` +
            `${solicitud.unidades_pendientes} unidad(es) pendiente(s)`;

        boton.append(encabezado, informacion, pendientes);

        boton.addEventListener('click', () => {
            this.seleccionar(solicitud);
        });

        return boton;
    }

    seleccionar(solicitud) {
        this.campoId.value = String(solicitud.id);

        this.establecerEtiqueta(
            solicitud.id,
            solicitud.solicitante,
            solicitud.destino
        );

        this.ocultarResultados();

        this.actualizarEstado(
            `Solicitud #${solicitud.id} seleccionada.`
        );

        this.alSeleccionar(solicitud.id);
    }

    establecerEtiqueta(id, solicitante, destino) {
        this.campoBusqueda.value =
            `Solicitud #${id} — ` +
            `${solicitante || 'Usuario no disponible'} — ` +
            `${destino || 'Sin destino'}`;
    }

    limpiar() {
        this.cancelar();
        this.campoId.value = '';
        this.campoBusqueda.value = '';
        this.ocultarResultados();

        this.actualizarEstado(
            'Escribe un número de solicitud o al menos dos caracteres.'
        );

        this.alDescartar();
    }

    manejarTeclado(event) {
        if (
            this.resultados.classList.contains('hidden') ||
            this.botones.length === 0
        ) {
            if (event.key === 'Escape') {
                this.ocultarResultados();
            }

            return;
        }

        if (event.key === 'ArrowDown') {
            event.preventDefault();
            this.activarResultado(this.indiceActivo + 1);
        } else if (event.key === 'ArrowUp') {
            event.preventDefault();
            this.activarResultado(this.indiceActivo - 1);
        } else if (
            event.key === 'Enter' &&
            this.indiceActivo >= 0
        ) {
            event.preventDefault();
            this.botones[this.indiceActivo].click();
        } else if (event.key === 'Escape') {
            this.ocultarResultados();
        }
    }

    activarResultado(indice) {
        this.indiceActivo =
            (indice + this.botones.length) %
            this.botones.length;

        this.botones.forEach((boton, posicion) => {
            const activo = posicion === this.indiceActivo;

            boton.setAttribute(
                'aria-selected',
                String(activo)
            );

            boton.classList.toggle('bg-blue-50', activo);
            boton.classList.toggle('dark:bg-gray-600', activo);
        });

        const botonActivo = this.botones[this.indiceActivo];

        this.campoBusqueda.setAttribute(
            'aria-activedescendant',
            botonActivo.id
        );

        botonActivo.scrollIntoView({
            block: 'nearest',
        });
    }

    obtenerTermino() {
        return this.campoBusqueda.value.trim();
    }

    terminoEsValido(termino) {
        return /^\d+$/.test(termino) || termino.length >= 2;
    }

    mostrarContenedor() {
        this.resultados.classList.remove('hidden');

        this.campoBusqueda.setAttribute(
            'aria-expanded',
            'true'
        );
    }

    ocultarResultados() {
        this.resultados.classList.add('hidden');

        this.campoBusqueda.setAttribute(
            'aria-expanded',
            'false'
        );

        this.campoBusqueda.removeAttribute(
            'aria-activedescendant'
        );

        this.indiceActivo = -1;
    }

    actualizarEstado(mensaje, esError = false) {
        this.estado.textContent = mensaje;

        this.estado.classList.toggle(
            'text-red-600',
            esError
        );

        this.estado.classList.toggle(
            'dark:text-red-400',
            esError
        );
    }

    cancelar() {
        if (this.controlador) {
            this.controlador.abort();
            this.controlador = null;
        }
    }
}