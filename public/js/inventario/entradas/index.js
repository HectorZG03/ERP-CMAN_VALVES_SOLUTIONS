/**
 * Módulo: Entradas de material
 * Archivo: public/js/inventario/entradas/index.js
 *
 * Funcionalidades:
 * - Búsqueda automática con debounce.
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

    let temporizador = null;

    /**
     * Envía el formulario mediante GET.
     * Laravel recibirá los filtros y actualizará los resultados.
     */
    const enviarFormulario = () => {
        form.requestSubmit();
    };

    /**
     * Reinicia la búsqueda cuando el usuario sigue escribiendo.
     * Espera 350 ms antes de enviar el formulario.
     */
    if (inputBuscar) {
        inputBuscar.addEventListener('input', () => {
            window.clearTimeout(temporizador);

            temporizador = window.setTimeout(() => {
                enviarFormulario();
            }, 350);
        });

        // Permite buscar inmediatamente al presionar Enter.
        inputBuscar.addEventListener('keydown', (event) => {
            if (event.key === 'Enter') {
                window.clearTimeout(temporizador);
                enviarFormulario();
            }
        });
    }

    /**
     * Actualiza automáticamente al cambiar el periodo.
     */
    if (selectPeriodo) {
        selectPeriodo.addEventListener('change', () => {
            window.clearTimeout(temporizador);
            enviarFormulario();
        });
    }

    /**
     * Limpia el buscador y restablece todos los periodos.
     */
    if (botonLimpiar) {
        botonLimpiar.addEventListener('click', () => {
            window.clearTimeout(temporizador);

            if (inputBuscar) {
                inputBuscar.value = '';
            }

            if (selectPeriodo) {
                selectPeriodo.value = 'todos';
            }

            enviarFormulario();
        });
    }

    /**
     * Evita que una búsqueda pendiente interfiera
     * cuando el usuario envía el formulario manualmente.
     */
    form.addEventListener('submit', () => {
        window.clearTimeout(temporizador);
    });
});
