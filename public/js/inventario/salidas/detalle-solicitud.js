const clasesPanel = {
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

export class DetalleSolicitud {
    constructor({
        panel,
        urlTemplate,
        obtenerSolicitudId,
        alIniciarCarga,
        alCargar,
        alError,
    }) {
        this.panel = panel;
        this.urlTemplate = urlTemplate;
        this.obtenerSolicitudId = obtenerSolicitudId;
        this.alIniciarCarga = alIniciarCarga;
        this.alCargar = alCargar;
        this.alError = alError;
        this.controlador = null;
    }

    async cargar(
        solicitudId = this.obtenerSolicitudId()
    ) {
        if (!solicitudId) {
            return;
        }

        this.cancelar();

        const controlador = new AbortController();

        this.controlador = controlador;
        this.alIniciarCarga();

        this.mostrarPanel(
            'informacion',
            'Consultando solicitud aprobada...'
        );

        try {
            const respuesta = await fetch(
                this.construirUrl(solicitudId),
                {
                    method: 'GET',
                    headers: {
                        Accept: 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    signal: controlador.signal,
                }
            );

            if (!respuesta.ok) {
                throw new Error(
                    `No fue posible consultar la solicitud. HTTP ${respuesta.status}`
                );
            }

            const solicitud = await respuesta.json();

            if (
                String(this.obtenerSolicitudId()) !==
                String(solicitudId)
            ) {
                return;
            }

            this.mostrarPanel(
                solicitud.completada
                    ? 'advertencia'
                    : 'correcto',
                solicitud.completada
                    ? 'Solicitud completamente entregada'
                    : `Solicitud #${solicitud.id}`,
                this.obtenerDatosPanel(solicitud)
            );

            this.alCargar(solicitud);
        } catch (error) {
            if (error.name === 'AbortError') {
                return;
            }

            console.error(
                'Error al consultar la solicitud:',
                error
            );

            this.mostrarPanel(
                'error',
                'No fue posible cargar la solicitud'
            );

            this.alError();
        } finally {
            if (this.controlador === controlador) {
                this.controlador = null;
            }
        }
    }

    limpiar() {
        this.cancelar();
        this.panel.classList.add('hidden');
        this.panel.innerHTML = '';
    }

    cancelar() {
        if (this.controlador) {
            this.controlador.abort();
            this.controlador = null;
        }
    }

    construirUrl(solicitudId) {
        return this.urlTemplate.replace(
            '__SOLICITUD__',
            encodeURIComponent(solicitudId)
        );
    }

    obtenerDatosPanel(solicitud) {
        return [
            {
                etiqueta: 'Solicitante',
                valor: solicitud.solicitante?.nombre,
            },
            {
                etiqueta: 'Número de empleado',
                valor:
                    solicitud.solicitante?.numero_empleado,
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
        ];
    }

    mostrarPanel(tipo, titulo, datos = []) {
        Object.values(clasesPanel)
            .flat()
            .forEach((clase) => {
                this.panel.classList.remove(clase);
            });

        this.panel.classList.add(...clasesPanel[tipo]);
        this.panel.innerHTML = '';

        const encabezado = document.createElement('h3');

        encabezado.className = 'font-semibold';
        encabezado.textContent = titulo;

        this.panel.appendChild(encabezado);

        if (datos.length === 0) {
            this.panel.classList.remove('hidden');
            return;
        }

        const listado = document.createElement('div');

        listado.className =
            'mt-2 grid grid-cols-1 md:grid-cols-2 ' +
            'gap-2 text-sm';

        datos.forEach((dato) => {
            const elemento = document.createElement('p');
            const etiqueta = document.createElement('strong');

            etiqueta.textContent = `${dato.etiqueta}: `;

            elemento.appendChild(etiqueta);

            elemento.appendChild(
                document.createTextNode(
                    dato.valor || 'N/A'
                )
            );

            listado.appendChild(elemento);
        });

        this.panel.appendChild(listado);
        this.panel.classList.remove('hidden');
    }
}