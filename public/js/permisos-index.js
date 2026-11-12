document.addEventListener('DOMContentLoaded', () => {
    const modalCrear = document.getElementById('modal-crear-permiso');
    const modalInfo = document.getElementById('modal-info-permiso');
    const botonAbrirCrear = document.getElementById('abrir-modal-crear-permiso');
    const buscadorPersonal = modalCrear?.querySelector('[data-personal-search]');
    const resultadosPersonal = modalCrear?.querySelector('[data-personal-results]');
    const personalId = modalCrear?.querySelector('[data-personal-id]');
    const personaSeleccionada = modalCrear?.querySelector('[data-personal-selected]');
    const personaSeleccionadaNombre = modalCrear?.querySelector('[data-personal-selected-name]');
    const personaSeleccionadaMeta = modalCrear?.querySelector('[data-personal-selected-meta]');
    const ayudaBuscador = modalCrear?.querySelector('[data-personal-search-hint]');
    const formularioCrear = modalCrear?.querySelector('form');

    let modalActivo = null;
    let elementoAnterior = null;
    let temporizadorCierre = null;
    let temporizadorBusqueda = null;
    let solicitudBusqueda = null;

    const mostrarModal = (modal, elementoOrigen = null) => {
        if (!modal) return;

        window.clearTimeout(temporizadorCierre);

        if (modalActivo && modalActivo !== modal) {
            cerrarModal(modalActivo, false);
        }

        elementoAnterior = elementoOrigen || document.activeElement;
        modalActivo = modal;

        modal.classList.remove('hidden');
        modal.classList.add('flex');
        modal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('overflow-hidden');

        window.requestAnimationFrame(() => {
            modal.classList.remove('opacity-0');

            const panel = modal.querySelector('[data-modal-panel]');
            if (panel) {
                panel.classList.remove('translate-y-2', 'opacity-0');
                panel.classList.add('translate-y-0', 'opacity-100');
                panel.focus();
            }
        });
    };

    const cerrarModal = (modal, restaurarFoco = true) => {
        if (!modal || modal.classList.contains('hidden')) return;

        modal.classList.add('opacity-0');

        const panel = modal.querySelector('[data-modal-panel]');
        if (panel) {
            panel.classList.add('translate-y-2', 'opacity-0');
            panel.classList.remove('translate-y-0', 'opacity-100');
        }

        modal.setAttribute('aria-hidden', 'true');

        temporizadorCierre = window.setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');

            if (modalActivo === modal) {
                modalActivo = null;
                document.body.classList.remove('overflow-hidden');
            }

            if (
                restaurarFoco &&
                elementoAnterior instanceof HTMLElement &&
                document.contains(elementoAnterior)
            ) {
                elementoAnterior.focus();
            }
        }, 210);
    };

    const mostrarError = (mensaje) => {
        const indicadorCarga = modalInfo?.querySelector('[data-permiso-loading]');
        const mensajeError = modalInfo?.querySelector('[data-permiso-error]');
        const contenido = modalInfo?.querySelector('[data-permiso-content]');

        indicadorCarga?.classList.add('hidden');
        contenido?.classList.add('hidden');

        if (mensajeError) {
            mensajeError.textContent = mensaje;
            mensajeError.classList.remove('hidden');
        }
    };

    const formatearFecha = (fecha) => {
        if (!fecha) return '—';

        const partes = String(fecha).split('-');
        if (partes.length !== 3) return fecha;

        return `${partes[2]}/${partes[1]}/${partes[0]}`;
    };

    const nombreUsuario = (usuario) => {
        if (!usuario) return '—';

        return usuario.nombre || usuario.name || usuario.correo || '—';
    };

    const mostrarResultadosPersonal = (resultados) => {
        if (!resultadosPersonal) return;

        resultadosPersonal.replaceChildren();
        resultados.forEach((persona) => {
            const opcion = document.createElement('button');
            opcion.type = 'button';
            opcion.role = 'option';
            opcion.className = 'block w-full border-b border-gray-100 px-4 py-3 text-left last:border-b-0 hover:bg-amber-50 focus:bg-amber-50 focus:outline-none dark:border-gray-700 dark:hover:bg-gray-700 dark:focus:bg-gray-700';

            const nombre = document.createElement('span');
            nombre.className = 'block text-sm font-semibold text-gray-900 dark:text-white';
            nombre.textContent = persona.nombre || 'Personal sin nombre';
            opcion.append(nombre);

            const meta = document.createElement('span');
            meta.className = 'mt-0.5 block text-xs text-gray-500 dark:text-gray-400';
            meta.textContent = [persona.numero_empleado, persona.puesto]
                .filter(Boolean)
                .join(' · ') || `ID ${persona.id}`;
            opcion.append(meta);

            opcion.addEventListener('click', () => seleccionarPersonal(persona));
            resultadosPersonal.append(opcion);
        });

        resultadosPersonal.classList.toggle('hidden', resultados.length === 0);
    };

    const seleccionarPersonal = (persona) => {
        if (!persona || !personalId || !buscadorPersonal) return;

        personalId.value = persona.id;
        buscadorPersonal.value = persona.nombre || '';
        if (personaSeleccionadaNombre) {
            personaSeleccionadaNombre.textContent = persona.nombre || 'Personal sin nombre';
        }
        if (personaSeleccionadaMeta) {
            personaSeleccionadaMeta.textContent = [persona.numero_empleado, persona.puesto]
                .filter(Boolean)
                .join(' · ');
        }

        personaSeleccionada?.classList.remove('hidden');
        resultadosPersonal?.classList.add('hidden');
        if (ayudaBuscador) ayudaBuscador.classList.add('hidden');
    };

    const buscarPersonal = async (termino) => {
        if (!modalCrear || !resultadosPersonal || !ayudaBuscador) return;

        solicitudBusqueda?.abort();
        solicitudBusqueda = new AbortController();

        const url = new URL(modalCrear.dataset.personalSearchUrl, window.location.origin);
        url.searchParams.set('q', termino);

        try {
            const respuesta = await fetch(url, {
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin',
                signal: solicitudBusqueda.signal,
            });

            if (!respuesta.ok) throw new Error('No fue posible buscar personal.');

            const resultado = await respuesta.json();
            const personas = Array.isArray(resultado.resultados) ? resultado.resultados : [];
            mostrarResultadosPersonal(personas);
            ayudaBuscador.textContent = personas.length
                ? 'Selecciona a la persona que recibirá el permiso.'
                : 'No se encontraron coincidencias.';
            ayudaBuscador.classList.remove('hidden');
            return personas;
        } catch (error) {
            if (error.name === 'AbortError') return;
            mostrarResultadosPersonal([]);
            ayudaBuscador.textContent = error.message || 'Ocurrió un error al buscar personal.';
            ayudaBuscador.classList.remove('hidden');
            return [];
        }
    };

    const programarBusquedaPersonal = (termino) => {
        window.clearTimeout(temporizadorBusqueda);
        solicitudBusqueda?.abort();

        if (personalId) personalId.value = '';
        personaSeleccionada?.classList.add('hidden');

        if (termino.trim().length < 2) {
            mostrarResultadosPersonal([]);
            if (ayudaBuscador) {
                ayudaBuscador.textContent = 'Escribe al menos 2 caracteres para buscar en el personal.';
                ayudaBuscador.classList.remove('hidden');
            }
            return;
        }

        if (ayudaBuscador) {
            ayudaBuscador.textContent = 'Buscando…';
            ayudaBuscador.classList.remove('hidden');
        }
        temporizadorBusqueda = window.setTimeout(() => buscarPersonal(termino.trim()), 300);
    };

    buscadorPersonal?.addEventListener('input', () => {
        programarBusquedaPersonal(buscadorPersonal.value);
    });

    modalCrear?.querySelector('[data-personal-clear]')?.addEventListener('click', () => {
        if (personalId) personalId.value = '';
        if (buscadorPersonal) {
            buscadorPersonal.value = '';
            buscadorPersonal.focus();
        }
        personaSeleccionada?.classList.add('hidden');
        if (ayudaBuscador) {
            ayudaBuscador.textContent = 'Escribe al menos 2 caracteres para buscar en el personal.';
            ayudaBuscador.classList.remove('hidden');
        }
    });

    document.addEventListener('click', (evento) => {
        if (!modalCrear?.contains(evento.target)) return;
        if (evento.target === buscadorPersonal || resultadosPersonal?.contains(evento.target)) return;
        resultadosPersonal?.classList.add('hidden');
    });

    formularioCrear?.addEventListener('submit', (evento) => {
        if (personalId && !personalId.value) {
            evento.preventDefault();
            buscadorPersonal?.focus();
            ayudaBuscador?.classList.remove('hidden');
            if (ayudaBuscador) {
                ayudaBuscador.textContent = 'Selecciona una persona de los resultados antes de continuar.';
            }
        }
    });

    const limpiarFormulariosDeRevision = (conservarDatos = false) => {
        const forms = [
            modalInfo?.querySelector('[data-form-revision]'),
            modalInfo?.querySelector('[data-form-resolver]'),
        ].filter(Boolean);

        if (conservarDatos) return;

        forms.forEach((form) => {
            const permisoId = form.querySelector('input[name="permiso_id"]');
            const comentario = form.querySelector(
                'textarea[name="comentario_revision"], textarea[name="comentario_resolucion"]'
            );

            if (permisoId) permisoId.value = '';
            if (comentario) comentario.value = '';
        });

        const decision = modalInfo?.querySelector('[data-form-resolver] [name="estatus"]');
        const goce = modalInfo?.querySelector('[data-form-resolver] [name="goce_sueldo"]');

        if (decision) decision.value = '';
        if (goce) goce.value = '';
    };

    const actualizarCampoGoce = (form) => {
        const decision = form.querySelector('[name="estatus"]');
        const goce = form.querySelector('[name="goce_sueldo"]');
        const campoGoce = form.querySelector('[data-goce-field]');

        if (!decision || !goce || !campoGoce) return;

        const debeMostrar = decision.value === 'aprobado';

        campoGoce.classList.toggle('hidden', !debeMostrar);
        goce.required = debeMostrar;

        if (!debeMostrar) {
            goce.value = '';
        }
    };

    const etapaTexto = (permiso) => {
        if (permiso.etapa === 'pendiente_revision_rh') {
            return 'Pendiente de revisión por Recursos Humanos';
        }

        if (permiso.etapa === 'pendiente_revision_finanzas') {
            return 'Pendiente de revisión por Finanzas';
        }

        if (permiso.etapa === 'pendiente_aprobacion_direccion') {
            return 'Pendiente de autorización por Dirección';
        }

        if (permiso.estatus === 'aprobado') {
            return 'Aprobado por Dirección';
        }

        if (permiso.estatus === 'rechazado') {
            return 'Rechazado por Dirección';
        }

        return permiso.estatus || '—';
    };

    const cargarDetalle = async (boton, conservarDatos = false) => {
        if (!modalInfo || !boton) return;

        const url = boton.dataset.url;
        const indicadorCarga = modalInfo.querySelector('[data-permiso-loading]');
        const mensajeError = modalInfo.querySelector('[data-permiso-error]');
        const contenido = modalInfo.querySelector('[data-permiso-content]');
        const formRevision = modalInfo.querySelector('[data-form-revision]');
        const formResolver = modalInfo.querySelector('[data-form-resolver]');

        if (!url || !indicadorCarga || !mensajeError || !contenido) return;

        limpiarFormulariosDeRevision(conservarDatos);

        indicadorCarga.classList.remove('hidden');
        mensajeError.classList.add('hidden');
        contenido.classList.add('hidden');
        formRevision?.classList.add('hidden');
        formResolver?.classList.add('hidden');

        mostrarModal(modalInfo, boton);

        try {
            const respuesta = await fetch(url, {
                method: 'GET',
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin',
            });

            const resultado = await respuesta.json();

            if (!respuesta.ok || !resultado.permiso) {
                throw new Error(
                    resultado.message || 'No fue posible cargar la información del permiso.'
                );
            }

            const permiso = resultado.permiso;
            const estaPendiente = permiso.estatus === 'pendiente';
            const yaFueRevisado = Boolean(permiso.revisado_at);

            modalInfo.querySelector('[data-detalle-personal]').textContent =
                permiso.personal?.nombre || 'Personal no disponible';

            modalInfo.querySelector('[data-detalle-etapa]').textContent =
                etapaTexto(permiso);

            modalInfo.querySelectorAll('[data-detalle-rol-revisor]').forEach((elemento) => {
                elemento.textContent = permiso.rol_revisor || 'Recursos Humanos';
            });

            const rolFormularioRevision = modalInfo.querySelector('[data-form-revision-role]');
            if (rolFormularioRevision) {
                rolFormularioRevision.textContent = permiso.rol_revisor || 'Recursos Humanos';
            }

            modalInfo.querySelector('[data-detalle-fecha]').textContent =
                formatearFecha(permiso.fecha_permiso);

            modalInfo.querySelector('[data-detalle-horas]').textContent =
                permiso.horas_solicitadas
                    ? `${Number(permiso.horas_solicitadas).toFixed(2)} horas`
                    : '—';

            modalInfo.querySelector('[data-detalle-motivo]').textContent =
                permiso.motivo || '—';

            modalInfo.querySelector('[data-detalle-registrado-por]').textContent =
                nombreUsuario(permiso.registrado_por);

            modalInfo.querySelector('[data-detalle-revisado-por]').textContent =
                nombreUsuario(permiso.revisado_por);

            modalInfo.querySelector('[data-detalle-revisado-at]').textContent =
                permiso.revisado_at
                    ? `Revisado el ${permiso.revisado_at}`
                    : 'Pendiente de revisión';

            modalInfo.querySelector('[data-detalle-comentario-revision]').textContent =
                permiso.comentario_revision || 'Sin comentario';

            modalInfo
                .querySelector('[data-detalle-comentario-revision-contenedor]')
                ?.classList.toggle('hidden', !permiso.comentario_revision);

            let textoGoce = 'Pendiente de resolución';
            if (permiso.estatus === 'aprobado') {
                const tieneGoce =
                    permiso.goce_sueldo === true ||
                    permiso.goce_sueldo === 1 ||
                    permiso.goce_sueldo === '1';

                textoGoce = tieneGoce
                    ? 'Con goce de sueldo'
                    : 'Sin goce de sueldo';
            } else if (permiso.estatus === 'rechazado') {
                textoGoce = 'No aplica';
            }

            modalInfo.querySelector('[data-detalle-goce]').textContent = textoGoce;

            modalInfo.querySelector('[data-detalle-resuelto-por]').textContent =
                nombreUsuario(permiso.resuelto_por);

            modalInfo.querySelector('[data-detalle-comentario-resolucion]').textContent =
                permiso.comentario_resolucion || 'Sin comentario';

            modalInfo
                .querySelector('[data-detalle-comentario-resolucion-contenedor]')
                ?.classList.toggle('hidden', !permiso.comentario_resolucion);

            if (estaPendiente && !yaFueRevisado && formRevision) {
                formRevision.action = boton.dataset.reviewUrl || '';

                const permisoId = formRevision.querySelector(
                    '[data-revision-permiso-id]'
                );
                if (permisoId) permisoId.value = permiso.id;

                if (boton.dataset.canReview === 'true') {
                    formRevision.classList.remove('hidden');
                }
            }

            if (estaPendiente && yaFueRevisado && formResolver) {
                formResolver.action = boton.dataset.resolverUrl || '';

                const permisoId = formResolver.querySelector(
                    '[data-resolver-permiso-id]'
                );
                if (permisoId) permisoId.value = permiso.id;

                if (modalInfo.dataset.canResolve === 'true') {
                    formResolver.classList.remove('hidden');
                    actualizarCampoGoce(formResolver);
                }
            }

            indicadorCarga.classList.add('hidden');
            contenido.classList.remove('hidden');
        } catch (error) {
            mostrarError(
                error.message || 'Ocurrió un error al consultar el permiso.'
            );
        }
    };

    botonAbrirCrear?.addEventListener('click', () => {
        mostrarModal(modalCrear, botonAbrirCrear);
    });

    document.addEventListener('click', (evento) => {
        const boton = evento.target.closest('[data-permiso-open]');
        if (boton) cargarDetalle(boton);
    });

    document.querySelectorAll('[data-modal]').forEach((modal) => {
        modal.querySelectorAll('[data-modal-close]').forEach((boton) => {
            boton.addEventListener('click', () => cerrarModal(modal));
        });

        modal.addEventListener('click', (evento) => {
            if (evento.target === modal) {
                cerrarModal(modal);
            }
        });
    });

    modalInfo
        ?.querySelector('[data-form-resolver] [name="estatus"]')
        ?.addEventListener('change', (evento) => {
            const form = evento.target.closest('[data-form-resolver]');
            if (form) actualizarCampoGoce(form);
        });

    document.addEventListener('keydown', (evento) => {
        if (evento.key === 'Escape' && modalActivo) {
            cerrarModal(modalActivo);
        }
    });

    if (modalCrear?.dataset.openOnError === 'true') {
        mostrarModal(modalCrear);
    }

    if (modalInfo?.dataset.openOnError === 'true') {
        const permisoId = modalInfo.dataset.errorPermisoId;

        if (permisoId) {
            const boton = [...document.querySelectorAll('[data-permiso-open]')].find(
                (elemento) => {
                    const coincidencia = elemento.dataset.url?.match(/\/permisos\/(\d+)$/);
                    return coincidencia && coincidencia[1] === permisoId;
                }
            );

            if (boton) {
                cargarDetalle(boton, true);
            }
        }
    }

    if (modalCrear?.dataset.openOnError === 'true' && modalCrear.dataset.oldPersonalId) {
        buscarPersonal(modalCrear.dataset.oldPersonalId).then((personas) => {
            // La búsqueda numérica devuelve el expediente exacto para restaurarlo
            // cuando el formulario regresa por errores de validación.
            const personaAnterior = personas?.find(
                (persona) => String(persona.id) === modalCrear.dataset.oldPersonalId
            );
            if (personaAnterior) seleccionarPersonal(personaAnterior);
        });
    }
});
