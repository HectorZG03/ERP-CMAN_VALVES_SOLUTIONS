const formatoMoneda = new Intl.NumberFormat('es-MX', {
    style: 'currency',
    currency: 'MXN',
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
});

export function moneda(valor) {
    return formatoMoneda.format(convertirNumero(valor));
}

export function convertirNumero(valor) {
    const numero = Number(valor);

    return Number.isFinite(numero) ? numero : 0;
}

export function crearEspera(callback, tiempo = 300) {
    let temporizador = null;

    return (...argumentos) => {
        window.clearTimeout(temporizador);

        temporizador = window.setTimeout(
            () => callback(...argumentos),
            tiempo
        );
    };
}