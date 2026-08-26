document.addEventListener('DOMContentLoaded', () => {
    const root = document.getElementById('ajuste-inventario');

    if (!root) {
        return;
    }

    const existenciaActual = Number(root.dataset.existencia);
    const valorActual = Number(root.dataset.valorTotal);
    const costoPromedioActual = existenciaActual > 0 ? valorActual / existenciaActual : 0;
    const form = document.getElementById('form-ajuste-inventario');
    const radios = form.querySelectorAll('input[name="operacion"]');
    const camposStock = document.getElementById('campos-ajuste-stock');
    const camposRevaluacion = document.getElementById('campos-revaluacion');
    const nuevaExistencia = document.getElementById('nueva_existencia');
    const contenedorCosto = document.getElementById('contenedor-costo-ajuste');
    const costoAjuste = document.getElementById('costo_unitario_ajuste');
    const nuevoCosto = document.getElementById('nuevo_costo_unitario');
    const textoDiferencia = document.getElementById('texto-diferencia');
    const previewDiferencia = document.getElementById('preview-diferencia');
    const previewPromedio = document.getElementById('preview-promedio');
    const previewTotal = document.getElementById('preview-total');
    const money = new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' });

    const getOperacion = () => form.querySelector('input[name="operacion"]:checked')?.value ?? 'stock';

    function actualizarVista() {
        const operacion = getOperacion();
        const esStock = operacion === 'stock';

        camposStock.classList.toggle('hidden', !esStock);
        camposRevaluacion.classList.toggle('hidden', esStock);
        nuevaExistencia.required = esStock;
        nuevoCosto.required = !esStock;

        let existenciaFinal = existenciaActual;
        let valorFinal = valorActual;
        let diferencia = 0;

        if (esStock) {
            existenciaFinal = Number(nuevaExistencia.value);

            if (!Number.isFinite(existenciaFinal) || existenciaFinal < 0) {
                existenciaFinal = existenciaActual;
            }

            diferencia = existenciaFinal - existenciaActual;
            const esIncremento = diferencia > 0;
            contenedorCosto.classList.toggle('hidden', !esIncremento);
            costoAjuste.required = esIncremento;

            if (esIncremento) {
                const costoEntrada = Number(costoAjuste.value) || 0;
                valorFinal = valorActual + (diferencia * costoEntrada);
                textoDiferencia.textContent = `Se agregarán ${diferencia} unidades al inventario.`;
            } else if (diferencia < 0) {
                valorFinal = existenciaFinal === 0 ? 0 : costoPromedioActual * existenciaFinal;
                textoDiferencia.textContent = `Se descontarán ${Math.abs(diferencia)} unidades al costo promedio actual.`;
            } else {
                textoDiferencia.textContent = 'La nueva existencia debe ser diferente de la actual.';
            }
        } else {
            const costoRevaluado = Number(nuevoCosto.value) || 0;
            valorFinal = existenciaActual * costoRevaluado;
        }

        const promedioFinal = existenciaFinal > 0 ? valorFinal / existenciaFinal : 0;
        previewDiferencia.textContent = diferencia > 0 ? `+${diferencia}` : String(diferencia);
        previewPromedio.textContent = money.format(promedioFinal);
        previewTotal.textContent = money.format(valorFinal);
    }

    radios.forEach((radio) => radio.addEventListener('change', actualizarVista));
    [nuevaExistencia, costoAjuste, nuevoCosto].forEach((input) => input.addEventListener('input', actualizarVista));

    form.addEventListener('submit', (event) => {
        const operacion = getOperacion();
        const mensaje = operacion === 'stock'
            ? '¿Confirmas el ajuste de existencias? Esta operación quedará registrada en el historial.'
            : '¿Confirmas la revaluación del producto? Esta operación quedará registrada en el historial.';

        if (!window.confirm(mensaje)) {
            event.preventDefault();
        }
    });

    actualizarVista();
});
