@extends('layouts.app')

@section('content')
<div class="space-y-6">

    {{-- HEADER --}}
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                Editar Orden de Compra
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Folio: <span class="font-semibold text-blue-600 dark:text-blue-400">{{ $ordenCompra->folio }}</span>
            </p>
        </div>
        <a href="{{ route('orden-compra.show', $ordenCompra->id) }}"
           class="bg-gray-500 hover:bg-gray-600 dark:bg-gray-600 dark:hover:bg-gray-700
                  text-white font-bold py-2 px-4 rounded transition-colors duration-200">
            Volver
        </a>
    </div>

    {{-- ERRORES --}}
    @if($errors->any())
        <div class="bg-red-50 dark:bg-red-900/50 border border-red-200 dark:border-red-800
                    text-red-800 dark:text-red-200 px-4 py-3 rounded-md">
            <ul class="list-disc list-inside space-y-1 text-sm">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- FORMULARIO --}}
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 transition-colors duration-200">

        <form method="POST"
              action="{{ route('orden-compra.update', $ordenCompra->id) }}"
              id="formOrdenCompra">
            @csrf
            @method('PUT')

            {{-- SECCIÓN 1: PROVEEDOR --}}
            <div class="mb-8">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4 border-b pb-2">
                    Información del Proveedor
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Nombre del Proveedor <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="nombre_proveedor" required
                               value="{{ old('nombre_proveedor', $ordenCompra->nombre_proveedor) }}"
                               placeholder="Nombre o razón social"
                               class="mt-1 block w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 text-gray-900 dark:text-white transition-colors duration-200">
                    </div>

                    {{-- razon social y rfc --}}

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Razón social</label>
                        <input type="text" name="razon_social_proveedor"
                               value="{{ old('razon_social_proveedor', $ordenCompra->razon_social_proveedor) }}"
                               placeholder="Nombre de la empresa"
                               class="mt-1 block w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 text-gray-900 dark:text-white transition-colors duration-200">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">RFC</label>
                        <input type="text" name="rfc_proveedor"
                               value="{{ old('rfc_proveedor', $ordenCompra->rfc_proveedor) }}"
                               placeholder="RFC del proveedor"
                               class="mt-1 block w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 text-gray-900 dark:text-white transition-colors duration-200">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Teléfono</label>
                        <input type="text" name="telefono_proveedor"
                               value="{{ old('telefono_proveedor', $ordenCompra->telefono_proveedor) }}"
                               placeholder="993..."
                               class="mt-1 block w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 text-gray-900 dark:text-white transition-colors duration-200">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Correo</label>
                        <input type="email" name="email_proveedor"
                               value="{{ old('email_proveedor', $ordenCompra->email_proveedor) }}"
                               placeholder="correo@empresa.com"
                               class="mt-1 block w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 text-gray-900 dark:text-white transition-colors duration-200">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Dirección</label>
                        <input type="text" name="direccion_proveedor"
                               value="{{ old('direccion_proveedor', $ordenCompra->direccion_proveedor) }}"
                               placeholder="Dirección del proveedor"
                               class="mt-1 block w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 text-gray-900 dark:text-white transition-colors duration-200">
                    </div>

                </div>
            </div>

            {{-- SECCIÓN 2: ARTÍCULOS --}}
            <div class="mb-8">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Artículos</h2>
                    <div class="flex items-center gap-3">
                        <span class="text-sm text-gray-500 dark:text-gray-400" id="contador-articulos">
                            {{ $ordenCompra->detalles->count() }} artículo(s) agregado(s)
                        </span>
                        <button type="button" id="btnAgregarArticulo"
                                class="bg-green-500 hover:bg-green-600 dark:bg-green-600 dark:hover:bg-green-700
                                       text-white font-bold py-2 px-4 rounded transition-colors duration-200">
                            + Agregar Artículo
                        </button>
                    </div>
                </div>

                <div id="articulos-container" class="space-y-4"></div>

                <div id="mensaje-vacio"
                     class="text-center py-8 text-gray-500 dark:text-gray-400 {{ $ordenCompra->detalles->count() > 0 ? 'hidden' : '' }}">
                    <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500 mb-4"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                    <p class="font-medium">No hay artículos agregados</p>
                    <p class="text-sm">Haz clic en "+ Agregar Artículo" para añadir artículos</p>
                </div>
            </div>

            {{-- SECCIÓN 3: EXTRAS Y COMENTARIOS --}}
            <div class="mb-8">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4 border-b pb-2">
                    Extras y Comentarios
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Envío ($)</label>
                        <input type="number" name="envio" id="envio"
                               value="{{ old('envio', $ordenCompra->envio) }}" min="0" step="0.01"
                               class="mt-1 block w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 text-gray-900 dark:text-white transition-colors duration-200">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Otros ($)</label>
                        <input type="number" name="otros" id="otros"
                               value="{{ old('otros', $ordenCompra->otros) }}" min="0" step="0.01"
                               class="mt-1 block w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 text-gray-900 dark:text-white transition-colors duration-200">
                    </div>

                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Comentarios</label>
                    <textarea name="comentarios" rows="4"
                              class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 text-gray-900 dark:text-white transition-colors duration-200"
                              placeholder="Observaciones, condiciones de pago, etc.">{{ old('comentarios', $ordenCompra->comentarios) }}</textarea>
                </div>
            </div>

            {{-- RESUMEN DE TOTALES --}}
            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg mb-6 transition-colors duration-200">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Resumen de Totales</h3>
                <div class="grid grid-cols-1 md:grid-cols-5 gap-4 text-center">
                    <div>
                        <div class="text-2xl font-bold text-blue-600 dark:text-blue-400" id="resSubtotal">
                            ${{ number_format($ordenCompra->subtotal, 2) }}
                        </div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Subtotal</div>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-400" id="resIva">
                            ${{ number_format($ordenCompra->iva, 2) }}
                        </div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">IVA (16%)</div>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-purple-600 dark:text-purple-400" id="resEnvio">
                            ${{ number_format($ordenCompra->envio, 2) }}
                        </div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Envío</div>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-orange-600 dark:text-orange-400" id="resOtros">
                            ${{ number_format($ordenCompra->otros, 2) }}
                        </div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Otros</div>
                    </div>
                    <div>
                        <div class="text-3xl font-bold text-green-600 dark:text-green-400" id="resTotal">
                            ${{ number_format($ordenCompra->total_general, 2) }}
                        </div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Total General</div>
                    </div>
                </div>
            </div>

            {{-- BOTONES --}}
            <div class="flex justify-end space-x-3">
                <a href="{{ route('orden-compra.show', $ordenCompra->id) }}"
                   class="bg-gray-500 hover:bg-gray-600 dark:bg-gray-600 dark:hover:bg-gray-700
                          text-white font-bold py-2 px-4 rounded transition-colors duration-200">
                    Cancelar
                </a>
                <button type="submit" id="btn-guardar"
                        class="bg-blue-500 hover:bg-blue-600 dark:bg-blue-600 dark:hover:bg-blue-700
                               text-white font-bold py-2 px-6 rounded disabled:opacity-50 transition-colors duration-200">
                    Guardar Cambios
                </button>
            </div>

        </form>
    </div>
</div>

{{-- TEMPLATE DE ARTÍCULO --}}
<template id="template-articulo">
    <div class="articulo-item bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600
                rounded-xl p-5 shadow-sm transition-colors duration-200">

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

// Artículos existentes precargados desde la BD
// Artículos existentes precargados desde la BD
// Artículos existentes precargados desde la BD
const articulosExistentes = {!! $articulosJson !!};

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
    document.getElementById('mensaje-vacio').classList.add('hidden');
}

function eliminarArticulo(btn) {
    if (totalArticulos === 1) { alert('Debe haber al menos un artículo.'); return; }
    btn.closest('.articulo-item').remove();
    totalArticulos--;
    renumerarArticulos();
    actualizarContador();
    calcularTotales();
    if (totalArticulos === 0) document.getElementById('mensaje-vacio').classList.remove('hidden');
}

function renumerarArticulos() {
    document.querySelectorAll('.articulo-numero').forEach((el, i) => el.textContent = i + 1);
}

function calcularTotales() {
    let subtotal = 0;
    document.querySelectorAll('.articulo-item').forEach(fila => {
        const cant   = parseFloat(fila.querySelector('.campo-cantidad').value) || 0;
        const precio = parseFloat(fila.querySelector('.campo-precio').value)   || 0;
        const total  = cant * precio;
        fila.querySelector('.campo-total').value = '$' + total.toFixed(2);
        subtotal += total;
    });
    const envio = parseFloat(document.getElementById('envio').value) || 0;
    const otros = parseFloat(document.getElementById('otros').value) || 0;
    const iva   = subtotal * 0.16;
    const total = subtotal + iva + envio + otros;
    document.getElementById('resSubtotal').textContent = '$' + subtotal.toFixed(2);
    document.getElementById('resIva').textContent      = '$' + iva.toFixed(2);
    document.getElementById('resEnvio').textContent    = '$' + envio.toFixed(2);
    document.getElementById('resOtros').textContent    = '$' + otros.toFixed(2);
    document.getElementById('resTotal').textContent    = '$' + total.toFixed(2);
}

function actualizarContador() {
    document.getElementById('contador-articulos').textContent =
        `${totalArticulos} artículo${totalArticulos !== 1 ? 's' : ''} agregado${totalArticulos !== 1 ? 's' : ''}`;
    document.getElementById('btn-guardar').disabled = totalArticulos === 0;
}

function activarAutocomplete(input) {
    let timer = null;
    const lista = input.parentElement.querySelector('.autocomplete-list');

    input.addEventListener('input', function () {
        clearTimeout(timer);
        const q = this.value.trim();
        if (q.length < 2) { 
            lista.classList.add('hidden'); 
            lista.innerHTML = ''; 
            return; 
        }

        timer = setTimeout(() => {
            fetch(`${buscarProductosUrl}?q=${encodeURIComponent(q)}`)
                .then(r => r.json())
                .then(productos => {
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
                                Exist: ${p.existencia} &nbsp;·&nbsp;
                                Precio sug: $${p.precio_unitario.toFixed(2)}
                            </span>`;
                        btn.addEventListener('click', () => {
                            const fila = input.closest('.articulo-item');
                            input.value = p.nombre_producto;
                            fila.querySelector('.campo-codigo').value = p.economico;
                            fila.querySelector('.campo-unidad').value  = p.medida;
                            // Opcional: sugerir precio
                            const precioInput = fila.querySelector('.campo-precio');
                            if (precioInput && p.precio_unitario > 0) {
                                precioInput.value = p.precio_unitario;
                                calcularTotales();
                            }
                            lista.classList.add('hidden');
                            lista.innerHTML = '';
                        });
                        lista.appendChild(btn);
                    });
                    lista.classList.remove('hidden');
                })
                .catch(() => lista.classList.add('hidden'));
        }, 300);
    });

    document.addEventListener('click', (e) => {
        if (!input.parentElement.contains(e.target)) lista.classList.add('hidden');
    });
}

// Función auxiliar para escapar HTML
function escapeHtml(str) {
    if (!str) return '';
    return str
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
}

document.addEventListener('DOMContentLoaded', () => {
    // Cargar artículos existentes de la BD
    if (articulosExistentes.length > 0) {
        articulosExistentes.forEach(art => agregarArticulo(art));
    } else {
        agregarArticulo();
    }

    document.getElementById('btnAgregarArticulo').addEventListener('click', () => agregarArticulo());
    document.getElementById('envio').addEventListener('input', calcularTotales);
    document.getElementById('otros').addEventListener('input', calcularTotales);
    calcularTotales();
});

document.getElementById('formOrdenCompra').addEventListener('submit', function (e) {
    if (!document.querySelectorAll('.articulo-item').length) {
        e.preventDefault(); alert('Debe haber al menos un artículo.'); return;
    }
    let error = false;
    document.querySelectorAll('.articulo-item').forEach(fila => {
        const desc   = fila.querySelector('.campo-descripcion').value.trim();
        const cant   = parseFloat(fila.querySelector('.campo-cantidad').value) || 0;
        const precio = parseFloat(fila.querySelector('.campo-precio').value);
        if (!desc || cant <= 0 || isNaN(precio) || precio < 0) error = true;
    });
    if (error) { e.preventDefault(); alert('Completa todos los campos de los artículos.'); }
});
</script>

@endsection