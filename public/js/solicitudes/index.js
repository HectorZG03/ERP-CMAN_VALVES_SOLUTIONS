document.addEventListener('DOMContentLoaded', () => {
    const buscador = document.getElementById('search');

    if (!buscador) {
        return;
    }

    const filas = Array.from(
        document.querySelectorAll('.searchable-row')
    );

    const filaSinCoincidencias = document.getElementById(
        'sin-coincidencias'
    );

    const contadorVisibles = document.getElementById(
        'contador-visibles'
    );

    const botonLimpiar = document.getElementById(
        'limpiar-busqueda'
    );

    function normalizarTexto(texto) {
        return String(texto || '')
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .toLowerCase()
            .trim();
    }

    function actualizarContador(cantidad) {
        if (!contadorVisibles) {
            return;
        }

        contadorVisibles.textContent =
            `${cantidad} visible${cantidad === 1 ? '' : 's'}`;
    }

    function filtrarSolicitudes() {
        const termino = normalizarTexto(buscador.value);
        let visibles = 0;

        filas.forEach((fila) => {
            const contenido = normalizarTexto(
                fila.dataset.search || fila.textContent
            );

            const coincide = termino === ''
                || contenido.includes(termino);

            fila.classList.toggle('hidden', !coincide);

            if (coincide) {
                visibles += 1;
            }
        });

        filaSinCoincidencias?.classList.toggle(
            'hidden',
            visibles > 0 || filas.length === 0
        );

        botonLimpiar?.classList.toggle(
            'hidden',
            termino === ''
        );

        botonLimpiar?.classList.toggle(
            'flex',
            termino !== ''
        );

        actualizarContador(visibles);
    }

    buscador.addEventListener(
        'input',
        filtrarSolicitudes
    );

    botonLimpiar?.addEventListener('click', () => {
        buscador.value = '';
        filtrarSolicitudes();
        buscador.focus();
    });

    actualizarContador(filas.length);
});