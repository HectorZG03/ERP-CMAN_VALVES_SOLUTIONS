{{-- =========================
    ORDEN DE COMPRA - CREATE
========================= --}}

@extends('layouts.app')

@section('content')

<div class="space-y-6">

    {{-- HEADER --}}
    <div class="flex justify-between items-center">

        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                Nueva Orden de Compra
            </h1>

            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Registra artículos y genera una orden de compra
            </p>
        </div>

        <a href="{{ route('orden-compra.index') }}"
           class="bg-gray-500 hover:bg-gray-600 dark:bg-gray-600 dark:hover:bg-gray-700
                  text-white font-bold py-2 px-4 rounded transition-colors duration-200">

            Volver

        </a>

    </div>

    {{-- FORMULARIO --}}
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 transition-colors duration-200">

        <form method="POST"
              action="{{ route('orden-compra.store') }}"
              id="formOrdenCompra">

            @csrf

            {{-- =========================
                PROVEEDOR
            ========================= --}}
            <div class="mb-8">

                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4 border-b pb-2">
                    Información del Proveedor
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <div>

                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Nombre del Proveedor
                            <span class="text-red-500">*</span>
                        </label>

                        <input type="text"
                               name="nombre_proveedor"
                               required
                               value="{{ old('nombre_proveedor') }}"
                               placeholder="Nombre o razón social"
                               class="mt-1 block w-full bg-white dark:bg-gray-700
                                      border border-gray-300 dark:border-gray-600
                                      rounded-md shadow-sm
                                      focus:ring-blue-500 dark:focus:ring-blue-400
                                      focus:border-blue-500 dark:focus:border-blue-400
                                      text-gray-900 dark:text-white
                                      transition-colors duration-200">

                    </div>

                    <div>

                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Teléfono
                        </label>

                        <input type="text"
                               name="telefono_proveedor"
                               value="{{ old('telefono_proveedor') }}"
                               placeholder="993..."
                               class="mt-1 block w-full bg-white dark:bg-gray-700
                                      border border-gray-300 dark:border-gray-600
                                      rounded-md shadow-sm
                                      focus:ring-blue-500 dark:focus:ring-blue-400
                                      focus:border-blue-500 dark:focus:border-blue-400
                                      text-gray-900 dark:text-white
                                      transition-colors duration-200">

                    </div>

                    <div>

                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Correo
                        </label>

                        <input type="email"
                               name="email_proveedor"
                               value="{{ old('email_proveedor') }}"
                               placeholder="correo@empresa.com"
                               class="mt-1 block w-full bg-white dark:bg-gray-700
                                      border border-gray-300 dark:border-gray-600
                                      rounded-md shadow-sm
                                      focus:ring-blue-500 dark:focus:ring-blue-400
                                      focus:border-blue-500 dark:focus:border-blue-400
                                      text-gray-900 dark:text-white
                                      transition-colors duration-200">

                    </div>

                    <div>

                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Dirección
                        </label>

                        <input type="text"
                               name="direccion_proveedor"
                               value="{{ old('direccion_proveedor') }}"
                               placeholder="Dirección del proveedor"
                               class="mt-1 block w-full bg-white dark:bg-gray-700
                                      border border-gray-300 dark:border-gray-600
                                      rounded-md shadow-sm
                                      focus:ring-blue-500 dark:focus:ring-blue-400
                                      focus:border-blue-500 dark:focus:border-blue-400
                                      text-gray-900 dark:text-white
                                      transition-colors duration-200">

                    </div>

                </div>

            </div>

            {{-- =========================
                ARTICULOS
            ========================= --}}
            <div class="mb-8">

                <div class="flex justify-between items-center mb-4">

                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
                        Artículos
                    </h2>

                    <div class="flex items-center gap-3">
                        <span class="text-sm text-gray-500 dark:text-gray-400" id="contador-articulos">
                            0 artículo(s) agregado(s)
                        </span>
                        <button type="button"
                                id="btnAgregarArticulo"
                                class="bg-green-500 hover:bg-green-600
                                       dark:bg-green-600 dark:hover:bg-green-700
                                       text-white font-bold py-2 px-4 rounded
                                       transition-colors duration-200">

                            + Agregar Artículo

                        </button>
                    </div>

                </div>

                {{-- CONTENEDOR DE ARTÍCULOS DINÁMICOS --}}
                <div id="articulos-container" class="space-y-4">

                </div>

                <div id="mensaje-vacio"
                     class="text-center py-8 text-gray-500 dark:text-gray-400 hidden">
                    <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500 mb-4"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                    <p class="font-medium">No hay artículos agregados</p>
                    <p class="text-sm">Haz clic en "+ Agregar Artículo" para añadir artículos</p>
                </div>

            </div>

            {{-- =========================
                EXTRAS Y COMENTARIOS
            ========================= --}}
            <div class="mb-8">

                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4 border-b pb-2">
                    Extras y Comentarios
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">

                    <div>

                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Envío ($)
                        </label>

                        <input type="number"
                               name="envio"
                               id="envio"
                               value="0"
                               min="0"
                               step="0.01"
                               class="mt-1 block w-full bg-white dark:bg-gray-700
                                      border border-gray-300 dark:border-gray-600
                                      rounded-md shadow-sm
                                      focus:ring-blue-500 dark:focus:ring-blue-400
                                      focus:border-blue-500 dark:focus:border-blue-400
                                      text-gray-900 dark:text-white
                                      transition-colors duration-200">

                    </div>

                    <div>

                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Otros ($)
                        </label>

                        <input type="number"
                               name="otros"
                               id="otros"
                               value="0"
                               min="0"
                               step="0.01"
                               class="mt-1 block w-full bg-white dark:bg-gray-700
                                      border border-gray-300 dark:border-gray-600
                                      rounded-md shadow-sm
                                      focus:ring-blue-500 dark:focus:ring-blue-400
                                      focus:border-blue-500 dark:focus:border-blue-400
                                      text-gray-900 dark:text-white
                                      transition-colors duration-200">

                    </div>

                </div>

                <div>

                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Comentarios
                    </label>

                    <textarea name="comentarios"
                              rows="4"
                              class="w-full bg-white dark:bg-gray-700
                                     border border-gray-300 dark:border-gray-600
                                     rounded-md shadow-sm
                                     focus:ring-blue-500 dark:focus:ring-blue-400
                                     focus:border-blue-500 dark:focus:border-blue-400
                                     text-gray-900 dark:text-white
                                     transition-colors duration-200"
                              placeholder="Observaciones, condiciones de pago, etc.">{{ old('comentarios') }}</textarea>

                </div>

            </div>

            {{-- =========================
                RESUMEN
            ========================= --}}
            <div id="resumen-orden"
                 class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg mb-6 transition-colors duration-200">

                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                    Resumen de Totales
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-5 gap-4 text-center">

                    <div>
                        <div class="text-2xl font-bold text-blue-600 dark:text-blue-400"
                             id="subtotal">

                            $0.00

                        </div>

                        <div class="text-sm text-gray-600 dark:text-gray-400">
                            Subtotal
                        </div>
                    </div>

                    <div>
                        <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-400"
                             id="iva">

                            $0.00

                        </div>

                        <div class="text-sm text-gray-600 dark:text-gray-400">
                            IVA (16%)
                        </div>
                    </div>

                    <div>
                        <div class="text-2xl font-bold text-purple-600 dark:text-purple-400"
                             id="resEnvio">

                            $0.00

                        </div>

                        <div class="text-sm text-gray-600 dark:text-gray-400">
                            Envío
                        </div>
                    </div>

                    <div>
                        <div class="text-2xl font-bold text-orange-600 dark:text-orange-400"
                             id="resOtros">

                            $0.00

                        </div>

                        <div class="text-sm text-gray-600 dark:text-gray-400">
                            Otros
                        </div>
                    </div>

                    <div>
                        <div class="text-3xl font-bold text-green-600 dark:text-green-400"
                             id="total-general">

                            $0.00

                        </div>

                        <div class="text-sm text-gray-600 dark:text-gray-400">
                            Total General
                        </div>
                    </div>

                </div>

            </div>

            {{-- BOTONES --}}
            <div class="flex justify-end space-x-3">

                <button type="button"
                        onclick="limpiarFormulario()"
                        class="bg-gray-500 hover:bg-gray-600
                               dark:bg-gray-600 dark:hover:bg-gray-700
                               text-white font-bold py-2 px-4 rounded
                               transition-colors duration-200">

                    Limpiar

                </button>

                <button type="submit"
                        id="btn-guardar"
                        class="bg-blue-500 hover:bg-blue-600
                               dark:bg-blue-600 dark:hover:bg-blue-700
                               text-white font-bold py-2 px-4 rounded
                               disabled:opacity-50 transition-colors duration-200">

                    Guardar Orden

                </button>

            </div>

        </form>

    </div>

</div>

{{-- TEMPLATE DE ARTÍCULO (para mejor organización) --}}
<template id="template-articulo">
    <div class="articulo-item bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600
                rounded-xl p-5 shadow-sm relative transition-colors duration-200">

        <div class="flex items-center justify-between mb-4">
            <h4 class="font-semibold text-gray-900 dark:text-white">
                Artículo #<span class="articulo-numero"></span>
            </h4>
            <button type="button" onclick="eliminarArticulo(this)"
                    class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">

            <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Código <span class="text-red-500">*</span>
                </label>
                <input type="text" name="articulos[INDEX][codigo]" required placeholder="Código"
                       class="campo-codigo w-full text-sm bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-md focus:ring-blue-500 focus:border-blue-500 text-gray-900 dark:text-white transition-colors duration-200">
            </div>

            <div class="md:col-span-2 relative">
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Descripción <span class="text-red-500">*</span>
                </label>
                <input type="text" name="articulos[INDEX][descripcion]" required
                       placeholder="Descripción o busca en inventario..." autocomplete="off"
                       class="campo-descripcion w-full text-sm bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-md focus:ring-blue-500 focus:border-blue-500 text-gray-900 dark:text-white transition-colors duration-200">
                <div class="autocomplete-list absolute z-50 w-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-md shadow-lg mt-1 max-h-48 overflow-y-auto hidden"></div>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Cantidad <span class="text-red-500">*</span>
                </label>
                <input type="number" name="articulos[INDEX][cantidad]" required
                       min="0.01" step="0.01" value="1" oninput="calcularTotales()"
                       class="campo-cantidad w-full text-sm bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-md focus:ring-blue-500 focus:border-blue-500 text-gray-900 dark:text-white transition-colors duration-200">
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Unidad <span class="text-red-500">*</span>
                </label>
                <input type="text" name="articulos[INDEX][unidad]" required placeholder="PZA, KG..."
                       class="campo-unidad w-full text-sm bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-md focus:ring-blue-500 focus:border-blue-500 text-gray-900 dark:text-white transition-colors duration-200">
            </div>

        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">

            <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Precio Unitario ($) <span class="text-red-500">*</span>
                </label>
                <input type="number" name="articulos[INDEX][precio_unitario]" required
                       min="0" step="0.01" value="0" oninput="calcularTotales()"
                       class="campo-precio w-full text-sm bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-md focus:ring-blue-500 focus:border-blue-500 text-gray-900 dark:text-white transition-colors duration-200">
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Total ($)</label>
                <input type="text" readonly
                       class="campo-total w-full text-sm font-bold bg-gray-100 dark:bg-gray-800 dark:text-green-400 border border-gray-300 dark:border-gray-600 rounded-md">
            </div>

        </div>
    </div>
</template>

<script>
let articuloIdx    = 0;
let totalArticulos = 0;
const buscarProductosUrl = "{{ route('OrdenCompra.buscar-productos') }}";

// Función para limpiar el formulario
function limpiarFormulario() {
    if (confirm('¿Estás seguro de que quieres limpiar todos los datos?')) {
        // Limpiar inputs del proveedor
        document.querySelectorAll('input[name="nombre_proveedor"], input[name="telefono_proveedor"], input[name="email_proveedor"], input[name="direccion_proveedor"]').forEach(input => {
            input.value = '';
        });
        
        // Limpiar comentarios
        document.querySelector('textarea[name="comentarios"]').value = '';
        
        // Resetear envío y otros
        document.getElementById('envio').value = '0';
        document.getElementById('otros').value = '0';
        
        // Limpiar todos los artículos
        const container = document.getElementById('articulos-container');
        container.innerHTML = '';
        
        // Resetear índices
        articuloIdx = 0;
        totalArticulos = 0;
        
        // Agregar un artículo nuevo
        agregarArticulo();
        
        // Recalcular totales
        calcularTotales();
    }
}

// Función para escapar HTML y prevenir XSS
function escapeHtml(str) {
    if (!str) return '';
    return str
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
}

// Agregar artículo
function agregarArticulo(datos = null) {
    const template  = document.getElementById('template-articulo');
    const html      = template.innerHTML.replaceAll('INDEX', articuloIdx++);
    const container = document.getElementById('articulos-container');
    const wrapper   = document.createElement('div');
    wrapper.innerHTML = html;
    const fila = wrapper.firstElementChild;
    container.appendChild(fila);
    totalArticulos++;
    fila.querySelector('.articulo-numero').textContent = totalArticulos;

    if (datos) {
        fila.querySelector('.campo-codigo').value         = datos.codigo          ?? '';
        fila.querySelector('.campo-descripcion').value    = datos.descripcion     ?? '';
        fila.querySelector('.campo-cantidad').value       = datos.cantidad        ?? 1;
        fila.querySelector('.campo-unidad').value         = datos.unidad          ?? '';
        fila.querySelector('.campo-precio').value         = datos.precio_unitario ?? 0;
    }

    activarAutocomplete(fila.querySelector('.campo-descripcion'));
    actualizarContador();
    calcularTotales();
    
    // Ocultar mensaje vacío si existe
    const mensajeVacio = document.getElementById('mensaje-vacio');
    if (mensajeVacio) mensajeVacio.classList.add('hidden');
}

// Eliminar artículo
function eliminarArticulo(btn) {
    if (totalArticulos === 1) { 
        alert('Debe haber al menos un artículo.'); 
        return; 
    }
    btn.closest('.articulo-item').remove();
    totalArticulos--;
    renumerarArticulos();
    actualizarContador();
    calcularTotales();
    
    const mensajeVacio = document.getElementById('mensaje-vacio');
    if (totalArticulos === 0 && mensajeVacio) mensajeVacio.classList.remove('hidden');
}

function renumerarArticulos() {
    document.querySelectorAll('.articulo-numero').forEach((el, i) => el.textContent = i + 1);
}

// Calcular totales
function calcularTotales() {
    let subtotal = 0;
    document.querySelectorAll('.articulo-item').forEach(fila => {
        const cant   = parseFloat(fila.querySelector('.campo-cantidad').value) || 0;
        const precio = parseFloat(fila.querySelector('.campo-precio').value)   || 0;
        const total  = cant * precio;
        const totalInput = fila.querySelector('.campo-total');
        if (totalInput) totalInput.value = '$' + total.toFixed(2);
        subtotal += total;
    });
    
    const envio = parseFloat(document.getElementById('envio').value) || 0;
    const otros = parseFloat(document.getElementById('otros').value) || 0;
    const iva   = subtotal * 0.16;
    const total = subtotal + iva + envio + otros;
    
    document.getElementById('subtotal').innerHTML      = '$' + subtotal.toFixed(2);
    document.getElementById('iva').innerHTML           = '$' + iva.toFixed(2);
    document.getElementById('resEnvio').innerHTML      = '$' + envio.toFixed(2);
    document.getElementById('resOtros').innerHTML      = '$' + otros.toFixed(2);
    document.getElementById('total-general').innerHTML = '$' + total.toFixed(2);
}

function actualizarContador() {
    const contador = document.getElementById('contador-articulos');
    if (contador) {
        contador.textContent = `${totalArticulos} artículo${totalArticulos !== 1 ? 's' : ''} agregado${totalArticulos !== 1 ? 's' : ''}`;
    }
    const btnGuardar = document.getElementById('btn-guardar');
    if (btnGuardar) btnGuardar.disabled = totalArticulos === 0;
}

// Activar autocompletado
function activarAutocomplete(input) {
    let timer = null;
    const lista = input.parentElement.querySelector('.autocomplete-list');

    input.addEventListener('input', function () {
        clearTimeout(timer);
        const q = this.value.trim();
        if (q.length < 2) { 
            if (lista) {
                lista.classList.add('hidden'); 
                lista.innerHTML = ''; 
            }
            return; 
        }

        timer = setTimeout(() => {
            fetch(`${buscarProductosUrl}?q=${encodeURIComponent(q)}`)
                .then(r => r.json())
                .then(productos => {
                    if (!lista) return;
                    lista.innerHTML = '';
                    if (!productos.length) { 
                        lista.classList.add('hidden'); 
                        return; 
                    }
                    productos.forEach(p => {
                        const btn = document.createElement('button');
                        btn.type = 'button';
                        btn.className = 'w-full text-left px-3 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-700 last:border-0 transition-colors duration-150';
                        btn.innerHTML = `
                            <span class="font-semibold block">${escapeHtml(p.nombre_producto)}</span>
                            <span class="text-xs text-gray-500 dark:text-gray-400">
                                Cód: ${escapeHtml(p.economico)} &nbsp;·&nbsp; 
                                Unidad: ${escapeHtml(p.medida)} &nbsp;·&nbsp; 
                                Exist: ${p.existencia}
                            </span>`;
                        btn.addEventListener('click', () => {
                            const fila = input.closest('.articulo-item');
                            input.value = p.nombre_producto;
                            if (fila) {
                                const codigoInput = fila.querySelector('.campo-codigo');
                                const unidadInput = fila.querySelector('.campo-unidad');
                                if (codigoInput) codigoInput.value = p.economico;
                                if (unidadInput) unidadInput.value = p.medida;
                            }
                            if (lista) {
                                lista.classList.add('hidden');
                                lista.innerHTML = '';
                            }
                        });
                        lista.appendChild(btn);
                    });
                    lista.classList.remove('hidden');
                })
                .catch(() => {
                    if (lista) lista.classList.add('hidden');
                });
        }, 300);
    });

    document.addEventListener('click', (e) => {
        if (lista && !input.parentElement.contains(e.target)) {
            lista.classList.add('hidden');
        }
    });
}

// Inicializar
document.addEventListener('DOMContentLoaded', () => {
    // Agregar primer artículo
    agregarArticulo();

    // Evento del botón agregar artículo
    const btnAgregar = document.getElementById('btnAgregarArticulo');
    if (btnAgregar) {
        btnAgregar.addEventListener('click', () => agregarArticulo());
    }

    // Eventos para envío y otros
    const envioInput = document.getElementById('envio');
    const otrosInput = document.getElementById('otros');
    
    if (envioInput) envioInput.addEventListener('input', calcularTotales);
    if (otrosInput) otrosInput.addEventListener('input', calcularTotales);
});

// Validar antes de enviar
document.getElementById('formOrdenCompra').addEventListener('submit', function(e) {
    const articulos = document.querySelectorAll('.articulo-item');
    
    if (articulos.length === 0) {
        e.preventDefault();
        alert('Debe agregar al menos un artículo');
        return;
    }
    
    let hasError = false;
    articulos.forEach((articulo) => {
        const descripcion = articulo.querySelector('.campo-descripcion')?.value;
        const cantidad = articulo.querySelector('.campo-cantidad')?.value;
        const precio = articulo.querySelector('.campo-precio')?.value;
        
        if (!descripcion || !cantidad || !precio || parseFloat(precio) <= 0) {
            hasError = true;
        }
    });
    
    if (hasError) {
        e.preventDefault();
        alert('Complete todos los campos de los artículos (descripción, cantidad y precio unitario)');
    }
});
</script>

@endsection