document.addEventListener('DOMContentLoaded', function () {
    const modulo = document.getElementById('embarcacionesModulo');

    if (!modulo) {
        return;
    }

    /*
    |--------------------------------------------------------------------------
    | Elementos generales
    |--------------------------------------------------------------------------
    */

    const storeUrl = modulo.dataset.storeUrl;

    const mensajeModulo = document.getElementById(
        'mensajeEmbarcaciones'
    );

    /*
    |--------------------------------------------------------------------------
    | Modal para crear y editar
    |--------------------------------------------------------------------------
    */

    const modalEmbarcacion = document.getElementById(
        'modalEmbarcacion'
    );

    const btnNuevaEmbarcacion = document.getElementById(
        'btnNuevaEmbarcacion'
    );

    const formEmbarcacion = document.getElementById(
        'formEmbarcacion'
    );

    const embarcacionId = document.getElementById(
        'embarcacionId'
    );

    const metodoFormulario = document.getElementById(
        'metodoFormulario'
    );

    const nombreEmbarcacion = document.getElementById(
        'nombreEmbarcacion'
    );

    const tituloModalEmbarcacion = document.getElementById(
        'tituloModalEmbarcacion'
    );

    const descripcionModalEmbarcacion = document.getElementById(
        'descripcionModalEmbarcacion'
    );

    const errorGeneralEmbarcacion = document.getElementById(
        'errorGeneralEmbarcacion'
    );

    const errorNombreEmbarcacion = document.getElementById(
        'errorNombreEmbarcacion'
    );

    const btnGuardarEmbarcacion = document.getElementById(
        'btnGuardarEmbarcacion'
    );

    const textoBtnGuardarEmbarcacion = document.getElementById(
        'textoBtnGuardarEmbarcacion'
    );

    const iconoCargandoEmbarcacion = document.getElementById(
        'iconoCargandoEmbarcacion'
    );

    /*
    |--------------------------------------------------------------------------
    | Modal para eliminar
    |--------------------------------------------------------------------------
    */

    const modalEliminarEmbarcacion = document.getElementById(
        'modalEliminarEmbarcacion'
    );

    const nombreEmbarcacionEliminar = document.getElementById(
        'nombreEmbarcacionEliminar'
    );

    const advertenciaRelaciones = document.getElementById(
        'advertenciaRelaciones'
    );

    const errorEliminarEmbarcacion = document.getElementById(
        'errorEliminarEmbarcacion'
    );

    const btnConfirmarEliminar = document.getElementById(
        'btnConfirmarEliminar'
    );

    const textoBtnEliminar = document.getElementById(
        'textoBtnEliminar'
    );

    const iconoCargandoEliminar = document.getElementById(
        'iconoCargandoEliminar'
    );

    /*
    |--------------------------------------------------------------------------
    | Estado interno
    |--------------------------------------------------------------------------
    */

    let urlFormulario = storeUrl;
    let urlEliminar = null;

    const csrfInput = formEmbarcacion.querySelector(
        'input[name="_token"]'
    );

    const csrfToken = csrfInput
        ? csrfInput.value
        : '';

    /*
    |--------------------------------------------------------------------------
    | Mensajes generales
    |--------------------------------------------------------------------------
    */

    function mostrarMensaje(mensaje, tipo = 'success') {
        if (!mensajeModulo) {
            return;
        }

        mensajeModulo.textContent = mensaje;
        mensajeModulo.classList.remove('hidden');

        if (tipo === 'success') {
            mensajeModulo.className =
                'rounded-md border border-green-200 ' +
                'bg-green-50 px-4 py-3 text-green-700 ' +
                'dark:border-green-800 dark:bg-green-900/30 ' +
                'dark:text-green-300';
        } else {
            mensajeModulo.className =
                'rounded-md border border-red-200 ' +
                'bg-red-50 px-4 py-3 text-red-700 ' +
                'dark:border-red-800 dark:bg-red-900/30 ' +
                'dark:text-red-300';
        }

        mensajeModulo.scrollIntoView({
            behavior: 'smooth',
            block: 'center'
        });
    }

    function guardarMensajeTemporal(mensaje, tipo = 'success') {
        sessionStorage.setItem(
            'mensajeEmbarcaciones',
            JSON.stringify({
                mensaje: mensaje,
                tipo: tipo
            })
        );
    }

    function mostrarMensajeTemporal() {
        const mensajeGuardado = sessionStorage.getItem(
            'mensajeEmbarcaciones'
        );

        if (!mensajeGuardado) {
            return;
        }

        sessionStorage.removeItem('mensajeEmbarcaciones');

        try {
            const datos = JSON.parse(mensajeGuardado);

            mostrarMensaje(
                datos.mensaje,
                datos.tipo
            );
        } catch (error) {
            console.error(
                'No se pudo mostrar el mensaje:',
                error
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Funciones de los modales
    |--------------------------------------------------------------------------
    */

    function abrirModal(modal) {
        if (!modal) {
            return;
        }

        modal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    function cerrarModal(modal) {
        if (!modal) {
            return;
        }

        modal.classList.add('hidden');

        const hayModalAbierto =
            !modalEmbarcacion.classList.contains('hidden') ||
            !modalEliminarEmbarcacion.classList.contains('hidden');

        if (!hayModalAbierto) {
            document.body.classList.remove('overflow-hidden');
        }
    }

    function limpiarErroresFormulario() {
        errorGeneralEmbarcacion.textContent = '';
        errorGeneralEmbarcacion.classList.add('hidden');

        errorNombreEmbarcacion.textContent = '';
        errorNombreEmbarcacion.classList.add('hidden');

        nombreEmbarcacion.classList.remove(
            'border-red-500',
            'focus:border-red-500',
            'focus:ring-red-500/20'
        );

        nombreEmbarcacion.removeAttribute('aria-invalid');
    }

    function limpiarErrorEliminar() {
        errorEliminarEmbarcacion.textContent = '';
        errorEliminarEmbarcacion.classList.add('hidden');
    }

    function normalizarNombre(nombre) {
        return nombre
            .trim()
            .replace(/\s+/g, ' ');
    }

    /*
    |--------------------------------------------------------------------------
    | Abrir modal para crear
    |--------------------------------------------------------------------------
    */

    function abrirModalCrear() {
        formEmbarcacion.reset();
        limpiarErroresFormulario();

        embarcacionId.value = '';
        metodoFormulario.value = 'POST';

        urlFormulario = storeUrl;

        tituloModalEmbarcacion.textContent =
            'Nueva embarcación';

        descripcionModalEmbarcacion.textContent =
            'Ingresa el nombre del nuevo destino o embarcación.';

        textoBtnGuardarEmbarcacion.textContent =
            'Guardar';

        btnGuardarEmbarcacion.dataset.textoNormal =
            'Guardar';

        abrirModal(modalEmbarcacion);

        setTimeout(function () {
            nombreEmbarcacion.focus();
        }, 100);
    }

    /*
    |--------------------------------------------------------------------------
    | Abrir modal para editar
    |--------------------------------------------------------------------------
    */

    function abrirModalEditar(boton) {
        formEmbarcacion.reset();
        limpiarErroresFormulario();

        embarcacionId.value = boton.dataset.id;
        nombreEmbarcacion.value = boton.dataset.nombre;

        metodoFormulario.value = 'PUT';
        urlFormulario = boton.dataset.url;

        tituloModalEmbarcacion.textContent =
            'Editar embarcación';

        descripcionModalEmbarcacion.textContent =
            'Modifica el nombre del destino o embarcación.';

        textoBtnGuardarEmbarcacion.textContent =
            'Guardar cambios';

        btnGuardarEmbarcacion.dataset.textoNormal =
            'Guardar cambios';

        abrirModal(modalEmbarcacion);

        setTimeout(function () {
            nombreEmbarcacion.focus();
            nombreEmbarcacion.select();
        }, 100);
    }

    /*
    |--------------------------------------------------------------------------
    | Abrir modal para eliminar
    |--------------------------------------------------------------------------
    */

    function abrirModalEliminar(boton) {
        limpiarErrorEliminar();

        const nombre = boton.dataset.nombre;
        const solicitudes = Number(
            boton.dataset.solicitudes || 0
        );

        const requisiciones = Number(
            boton.dataset.requisiciones || 0
        );

        urlEliminar = boton.dataset.url;

        nombreEmbarcacionEliminar.textContent =
            `"${nombre}"`;

        const tieneRelaciones =
            solicitudes > 0 || requisiciones > 0;

        if (tieneRelaciones) {
            advertenciaRelaciones.innerHTML =
                'Esta embarcación tiene ' +
                `<strong>${solicitudes}</strong> solicitud(es) y ` +
                `<strong>${requisiciones}</strong> requisición(es). ` +
                'No puede eliminarse mientras tenga registros relacionados.';

            advertenciaRelaciones.classList.remove('hidden');

            btnConfirmarEliminar.disabled = true;
            btnConfirmarEliminar.title =
                'La embarcación tiene registros relacionados';
        } else {
            advertenciaRelaciones.textContent = '';
            advertenciaRelaciones.classList.add('hidden');

            btnConfirmarEliminar.disabled = false;
            btnConfirmarEliminar.removeAttribute('title');
        }

        abrirModal(modalEliminarEmbarcacion);
    }

    /*
    |--------------------------------------------------------------------------
    | Estado de carga
    |--------------------------------------------------------------------------
    */

    function establecerCargaFormulario(cargando) {
        btnGuardarEmbarcacion.disabled = cargando;

        iconoCargandoEmbarcacion.classList.toggle(
            'hidden',
            !cargando
        );

        if (cargando) {
            textoBtnGuardarEmbarcacion.textContent =
                'Procesando...';
        } else {
            textoBtnGuardarEmbarcacion.textContent =
                btnGuardarEmbarcacion.dataset.textoNormal ||
                'Guardar';
        }
    }

    function establecerCargaEliminar(cargando) {
        btnConfirmarEliminar.disabled = cargando;

        iconoCargandoEliminar.classList.toggle(
            'hidden',
            !cargando
        );

        textoBtnEliminar.textContent = cargando
            ? 'Eliminando...'
            : 'Eliminar';
    }

    /*
    |--------------------------------------------------------------------------
    | Errores de validación
    |--------------------------------------------------------------------------
    */

    function mostrarErrorNombre(mensaje) {
        errorNombreEmbarcacion.textContent = mensaje;
        errorNombreEmbarcacion.classList.remove('hidden');

        nombreEmbarcacion.classList.add(
            'border-red-500',
            'focus:border-red-500',
            'focus:ring-red-500/20'
        );

        nombreEmbarcacion.setAttribute(
            'aria-invalid',
            'true'
        );

        nombreEmbarcacion.focus();
    }

    function mostrarErrorGeneral(mensaje) {
        errorGeneralEmbarcacion.textContent = mensaje;
        errorGeneralEmbarcacion.classList.remove('hidden');
    }

    function procesarErroresValidacion(errores) {
        if (!errores) {
            mostrarErrorGeneral(
                'No fue posible validar la información.'
            );

            return;
        }

        if (
            errores.nombre &&
            errores.nombre.length > 0
        ) {
            mostrarErrorNombre(errores.nombre[0]);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Procesar respuestas HTTP
    |--------------------------------------------------------------------------
    */

    async function obtenerRespuesta(response) {
        const contentType =
            response.headers.get('content-type') || '';

        if (contentType.includes('application/json')) {
            return await response.json();
        }

        const texto = await response.text();

        return {
            success: false,
            message: texto || 'Ocurrió un error inesperado.'
        };
    }

    function obtenerMensajeError(response, datos) {
        if (response.status === 419) {
            return 'Tu sesión expiró. Recarga la página e inténtalo nuevamente.';
        }

        if (response.status === 403) {
            return 'No tienes permisos para realizar esta acción.';
        }

        if (response.status === 404) {
            return 'La embarcación seleccionada ya no existe.';
        }

        return datos.message ||
            'No fue posible completar la operación.';
    }

    /*
    |--------------------------------------------------------------------------
    | Guardar o actualizar
    |--------------------------------------------------------------------------
    */

    formEmbarcacion.addEventListener(
        'submit',
        async function (event) {
            event.preventDefault();

            limpiarErroresFormulario();

            const nombre = normalizarNombre(
                nombreEmbarcacion.value
            );

            nombreEmbarcacion.value = nombre;

            if (nombre === '') {
                mostrarErrorNombre(
                    'El nombre de la embarcación es obligatorio.'
                );

                return;
            }

            const metodo = metodoFormulario.value;

            establecerCargaFormulario(true);

            let operacionExitosa = false;

            try {
                const response = await fetch(
                    urlFormulario,
                    {
                        method: metodo,
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({
                            nombre: nombre
                        })
                    }
                );

                const datos = await obtenerRespuesta(response);

                if (response.status === 422) {
                    procesarErroresValidacion(datos.errors);
                    return;
                }

                if (!response.ok) {
                    throw new Error(
                        obtenerMensajeError(response, datos)
                    );
                }

                operacionExitosa = true;

                guardarMensajeTemporal(
                    datos.message ||
                    'La operación se realizó correctamente.',
                    'success'
                );

                window.location.reload();
            } catch (error) {
                mostrarErrorGeneral(
                    error.message ||
                    'Ocurrió un error al guardar la embarcación.'
                );
            } finally {
                if (!operacionExitosa) {
                    establecerCargaFormulario(false);
                }
            }
        }
    );

    /*
    |--------------------------------------------------------------------------
    | Eliminar
    |--------------------------------------------------------------------------
    */

    btnConfirmarEliminar.addEventListener(
        'click',
        async function () {
            if (!urlEliminar) {
                return;
            }

            limpiarErrorEliminar();
            establecerCargaEliminar(true);

            let operacionExitosa = false;

            try {
                const response = await fetch(
                    urlEliminar,
                    {
                        method: 'DELETE',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    }
                );

                const datos = await obtenerRespuesta(response);

                if (!response.ok) {
                    throw new Error(
                        obtenerMensajeError(response, datos)
                    );
                }

                operacionExitosa = true;

                guardarMensajeTemporal(
                    datos.message ||
                    'Embarcación eliminada correctamente.',
                    'success'
                );

                window.location.reload();
            } catch (error) {
                errorEliminarEmbarcacion.textContent =
                    error.message ||
                    'No fue posible eliminar la embarcación.';

                errorEliminarEmbarcacion.classList.remove(
                    'hidden'
                );
            } finally {
                if (!operacionExitosa) {
                    establecerCargaEliminar(false);
                }
            }
        }
    );

    /*
    |--------------------------------------------------------------------------
    | Eventos de botones
    |--------------------------------------------------------------------------
    */

    btnNuevaEmbarcacion.addEventListener(
        'click',
        abrirModalCrear
    );

    document
        .querySelectorAll('.btn-editar-embarcacion')
        .forEach(function (boton) {
            boton.addEventListener('click', function () {
                abrirModalEditar(boton);
            });
        });

    document
        .querySelectorAll('.btn-eliminar-embarcacion')
        .forEach(function (boton) {
            boton.addEventListener('click', function () {
                abrirModalEliminar(boton);
            });
        });

    /*
    |--------------------------------------------------------------------------
    | Cerrar modales
    |--------------------------------------------------------------------------
    */

    document
        .querySelectorAll('[data-modal-close]')
        .forEach(function (boton) {
            boton.addEventListener('click', function () {
                const modalId =
                    boton.dataset.modalClose;

                const modal =
                    document.getElementById(modalId);

                cerrarModal(modal);
            });
        });

    document.addEventListener(
        'keydown',
        function (event) {
            if (event.key !== 'Escape') {
                return;
            }

            if (
                !modalEmbarcacion.classList.contains('hidden')
            ) {
                cerrarModal(modalEmbarcacion);
            }

            if (
                !modalEliminarEmbarcacion.classList.contains(
                    'hidden'
                )
            ) {
                cerrarModal(modalEliminarEmbarcacion);
            }
        }
    );

    /*
    |--------------------------------------------------------------------------
    | Limpiar error al escribir
    |--------------------------------------------------------------------------
    */

    nombreEmbarcacion.addEventListener(
        'input',
        function () {
            errorNombreEmbarcacion.textContent = '';
            errorNombreEmbarcacion.classList.add('hidden');

            nombreEmbarcacion.classList.remove(
                'border-red-500',
                'focus:border-red-500',
                'focus:ring-red-500/20'
            );

            nombreEmbarcacion.removeAttribute(
                'aria-invalid'
            );
        }
    );

    /*
    |--------------------------------------------------------------------------
    | Inicialización
    |--------------------------------------------------------------------------
    */

    mostrarMensajeTemporal();
});