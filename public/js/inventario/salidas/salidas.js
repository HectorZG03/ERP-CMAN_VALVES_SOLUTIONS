import {
    DetalleSolicitud,
} from './detalle-solicitud.js';

import {
    GestorProductos,
} from './productos.js';

import {
    BuscadorSolicitudes,
} from './solicitudes.js';

import {
    moneda,
} from './utilidades.js';

document.addEventListener('DOMContentLoaded', () => {
    'use strict';

    const formulario =
        document.getElementById('salidaForm');

    const raizBuscador =
        document.getElementById(
            'buscador-solicitudes'
        );

    const campoBusqueda =
        document.getElementById(
            'buscar-solicitud'
        );

    const campoSolicitudId =
        document.getElementById(
            'solicitud_material_id'
        );

    const resultadosSolicitud =
        document.getElementById(
            'resultados-solicitudes'
        );

    const estadoBusqueda =
        document.getElementById(
            'estado-busqueda-solicitudes'
        );

    const contenedorProductos =
        document.getElementById(
            'productos-container'
        );

    const botonRecargar =
        document.getElementById(
            'recargar-materiales'
        );

    const botonLimpiar =
        document.getElementById(
            'limpiar-formulario'
        );

    const botonEnviar =
        formulario?.querySelector(
            'button[type="submit"]'
        );

    const elementosRequeridos = [
        formulario,
        raizBuscador,
        campoBusqueda,
        campoSolicitudId,
        resultadosSolicitud,
        estadoBusqueda,
        contenedorProductos,
        botonRecargar,
        botonEnviar,
        document.getElementById(
            'total-productos'
        ),
        document.getElementById(
            'subtotal-total'
        ),
        document.getElementById(
            'iva-total'
        ),
        document.getElementById(
            'total-general'
        ),
    ];

    if (
        elementosRequeridos.some(
            (elemento) => !elemento
        )
    ) {
        console.error(
            'No fue posible iniciar el formulario de salidas: faltan elementos requeridos.'
        );

        return;
    }

    const urlBusqueda =
        formulario.dataset.buscarSolicitudesUrl;

    const urlDetalleTemplate =
        formulario.dataset.solicitudUrlTemplate;

    if (!urlBusqueda || !urlDetalleTemplate) {
        console.error(
            'No fue posible iniciar el formulario de salidas: faltan las rutas de consulta.'
        );

        return;
    }

    const panelSolicitud =
        document.createElement('div');

    panelSolicitud.id = 'detalle-solicitud';

    panelSolicitud.className =
        'hidden mb-4 rounded-lg border p-4';

    contenedorProductos.parentNode.insertBefore(
        panelSolicitud,
        contenedorProductos
    );

    let cargandoSolicitud = false;
    let productosValidos = false;

    function establecerEstadoBoton(
        boton,
        habilitado
    ) {
        if (!boton) {
            return;
        }

        boton.disabled = !habilitado;

        boton.classList.toggle(
            'opacity-50',
            !habilitado
        );

        boton.classList.toggle(
            'cursor-not-allowed',
            !habilitado
        );
    }

    function actualizarEstadoFormulario() {
        establecerEstadoBoton(
            botonEnviar,
            Boolean(campoSolicitudId.value) &&
                productosValidos &&
                !cargandoSolicitud
        );
    }

    const gestorProductos =
        new GestorProductos({
            contenedor: contenedorProductos,

            totalProductos:
                document.getElementById(
                    'total-productos'
                ),

            subtotalTotal:
                document.getElementById(
                    'subtotal-total'
                ),

            ivaTotal:
                document.getElementById(
                    'iva-total'
                ),

            totalGeneral:
                document.getElementById(
                    'total-general'
                ),

            alCambiar: ({ valido }) => {
                productosValidos = valido;
                actualizarEstadoFormulario();
            },
        });

    let buscadorSolicitudes = null;

    const detalleSolicitud =
        new DetalleSolicitud({
            panel: panelSolicitud,

            urlTemplate:
                urlDetalleTemplate,

            obtenerSolicitudId: () =>
                campoSolicitudId.value,

            alIniciarCarga: () => {
                cargandoSolicitud = true;

                gestorProductos.limpiar();

                establecerEstadoBoton(
                    botonRecargar,
                    false
                );

                actualizarEstadoFormulario();
            },

            alCargar: (solicitud) => {
                cargandoSolicitud = false;

                buscadorSolicitudes.establecerEtiqueta(
                    solicitud.id,
                    solicitud.solicitante?.nombre,
                    solicitud.destino
                );

                if (solicitud.completada) {
                    gestorProductos.limpiar();
                } else {
                    gestorProductos.cargar(
                        solicitud.productos
                    );
                }

                establecerEstadoBoton(
                    botonRecargar,
                    true
                );

                actualizarEstadoFormulario();
            },

            alError: () => {
                cargandoSolicitud = false;
                gestorProductos.limpiar();

                establecerEstadoBoton(
                    botonRecargar,
                    Boolean(
                        campoSolicitudId.value
                    )
                );

                actualizarEstadoFormulario();
            },
        });

    buscadorSolicitudes =
        new BuscadorSolicitudes({
            raiz: raizBuscador,
            campoBusqueda,
            campoId: campoSolicitudId,
            resultados: resultadosSolicitud,
            estado: estadoBusqueda,
            urlBusqueda,

            alSeleccionar: (
                solicitudId
            ) => {
                detalleSolicitud.cargar(
                    solicitudId
                );
            },

            alDescartar: () => {
                detalleSolicitud.limpiar();
                cargandoSolicitud = false;
                gestorProductos.limpiar();

                establecerEstadoBoton(
                    botonRecargar,
                    false
                );

                actualizarEstadoFormulario();
            },
        });

    botonRecargar.addEventListener(
        'click',
        () => {
            detalleSolicitud.cargar();
        }
    );

    if (botonLimpiar) {
        botonLimpiar.addEventListener(
            'click',
            () => {
                if (
                    !window.confirm(
                        '¿Está seguro de que desea limpiar el formulario?'
                    )
                ) {
                    return;
                }

                formulario.reset();
                buscadorSolicitudes.limpiar();
            }
        );
    }

    formulario.addEventListener(
        'submit',
        (event) => {
            if (!campoSolicitudId.value) {
                event.preventDefault();

                window.alert(
                    'Debe buscar y seleccionar una solicitud aprobada.'
                );

                campoBusqueda.focus();
                return;
            }

            if (
                !gestorProductos
                    .validarYReportar()
            ) {
                event.preventDefault();

                window.alert(
                    'Debe incluir al menos un material con una cantidad válida.'
                );

                return;
            }

            const confirmacion =
                window.confirm(
                    `¿Confirmar la salida de materiales por ${moneda(
                        gestorProductos.obtenerTotal()
                    )}?`
                );

            if (!confirmacion) {
                event.preventDefault();
                return;
            }

            botonEnviar.disabled = true;

            botonEnviar.textContent =
                'Registrando salida...';
        }
    );

    gestorProductos.limpiar();

    establecerEstadoBoton(
        botonRecargar,
        false
    );

    actualizarEstadoFormulario();
    buscadorSolicitudes.iniciar();

    if (campoSolicitudId.value) {
        detalleSolicitud.cargar();
    }
});