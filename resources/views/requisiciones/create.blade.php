@extends('layouts.app')

@section('content')
@php
    $departamento = old(
        'departamento',
        auth()->user()->departamento ?? auth()->user()->role
    );

    // Clases reutilizables (misma paleta que la vista original).
    $etiqueta = 'block text-sm font-medium text-gray-700 dark:text-gray-300';
    $campoBase = 'mt-1 block w-full rounded-md border bg-white px-3 text-sm text-gray-900 shadow-sm transition-colors duration-200 focus:ring-blue-500 dark:bg-gray-700 dark:text-white dark:focus:ring-blue-400';
    $campoOk = 'border-gray-300 focus:border-blue-500 dark:border-gray-600 dark:focus:border-blue-400';
    $campoError = 'border-red-400 focus:border-red-500 dark:border-red-500 dark:focus:border-red-400';
    $textoError = 'mt-1 text-sm text-red-600 dark:text-red-400';
    $tituloSeccion = 'mb-4 border-b border-gray-200 pb-2 text-xl font-semibold text-gray-900 dark:border-gray-700 dark:text-white';
    $cuadricula = 'grid grid-cols-1 gap-x-5 gap-y-5 sm:grid-cols-6 lg:grid-cols-12';

    // Clases del campo según tenga error o no. Los inputs y selects llevan altura fija (h-10).
    $clase = fn (string $campo) => "$campoBase h-10 " . ($errors->has($campo) ? $campoError : $campoOk);

    // Campos de texto de "Información del Proyecto" con su ancho por breakpoint.
    // Filas en escritorio: Proyecto/SIT/Partida (4+4+4) y Área/Activo/Plataforma/Embarcación (3+3+3+3).
    $camposProyecto = [
        'proyecto'    => ['Proyecto', 'Nombre del proyecto o N/A', 'sm:col-span-6 lg:col-span-4'],
        'sit'         => ['SIT (Sistema de Identificación de Trabajo)', 'Código SIT o N/A', 'sm:col-span-3 lg:col-span-4'],
        'partida'     => ['Partida', 'Número de partida o N/A', 'sm:col-span-3 lg:col-span-4'],
        'area'        => ['Área', 'Área específica o N/A', 'sm:col-span-3 lg:col-span-3'],
        'activo'      => ['Activo', 'Número de activo o N/A', 'sm:col-span-3 lg:col-span-3'],
        'plataforma'  => ['Plataforma', 'Ej: Plataforma A, Oficinas o N/A', 'sm:col-span-3 lg:col-span-3'],
    ];
@endphp

<div class="mx-auto w-full max-w-6xl space-y-6">
    {{-- Encabezado --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white sm:text-3xl">
            Nueva Requisición de Material
        </h1>

        <a href="{{ route('requisiciones.index') }}"
           class="self-start rounded bg-gray-500 px-4 py-2 font-bold text-white transition-colors duration-200 hover:bg-gray-600 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2 dark:bg-gray-600 dark:hover:bg-gray-700 dark:focus-visible:ring-offset-gray-900 sm:self-auto">
            Volver
        </a>
    </div>

    {{-- Errores de validación --}}
    @if($errors->any())
        <div role="alert" class="rounded-lg border border-red-200 bg-red-50 px-5 py-4 dark:border-red-800 dark:bg-red-900/30">
            <h2 class="font-semibold text-red-800 dark:text-red-200">
                No fue posible registrar la requisición
            </h2>

            <ul class="mt-1 list-inside list-disc text-sm text-red-700 dark:text-red-300">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="rounded-lg bg-white p-4 shadow transition-colors duration-200 dark:bg-gray-800 sm:p-6">
        <form method="POST" action="{{ route('requisiciones.store') }}" id="requisicionForm">
            @csrf

            {{-- SECCIÓN 1: INFORMACIÓN DEL SOLICITANTE --}}
            <section class="mb-8" aria-labelledby="titulo-solicitante">
                <h2 id="titulo-solicitante" class="{{ $tituloSeccion }}">
                    Información del Solicitante
                </h2>

                <div class="{{ $cuadricula }}">
                    <div class="sm:col-span-6 lg:col-span-8">
                        <label for="nombre_solicitante" class="{{ $etiqueta }}">
                            Nombre del Solicitante
                            <span class="text-red-500" aria-hidden="true">*</span>
                            <span class="sr-only">(obligatorio)</span>
                        </label>

                        <input type="text"
                               name="nombre_solicitante"
                               id="nombre_solicitante"
                               required
                               autocomplete="name"
                               class="{{ $clase('nombre_solicitante') }}"
                               value="{{ old('nombre_solicitante', auth()->user()->name) }}">

                        @error('nombre_solicitante')
                            <p class="{{ $textoError }}">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="sm:col-span-6 lg:col-span-4">
                        <label for="departamento" class="{{ $etiqueta }}">
                            Departamento
                            <span class="text-red-500" aria-hidden="true">*</span>
                            <span class="sr-only">(obligatorio, solo lectura)</span>
                        </label>

                        <input type="text"
                               id="departamento"
                               class="mt-1 block h-10 w-full truncate rounded-md border border-gray-300 bg-gray-100 px-3 text-sm font-semibold uppercase tracking-wider text-gray-900 shadow-sm transition-colors duration-200 dark:border-gray-600 dark:bg-gray-600 dark:text-white"
                               value="{{ $departamento }}"
                               readonly>

                        <input type="hidden" name="departamento" value="{{ $departamento }}">

                        @error('departamento')
                            <p class="{{ $textoError }}">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </section>

            {{-- SECCIÓN 2: INFORMACIÓN DEL PROYECTO --}}
            <section class="mb-8" aria-labelledby="titulo-proyecto">
                <h2 id="titulo-proyecto" class="{{ $tituloSeccion }}">
                    Información del Proyecto
                </h2>

                <div class="{{ $cuadricula }}">
                    {{-- Fila 1: campos obligatorios (contrato ancho + tipo) --}}
                    <div class="sm:col-span-6 lg:col-span-8">
                        <label for="contrato_id" class="{{ $etiqueta }}">
                            Contrato / Empresa
                            <span class="text-red-500" aria-hidden="true">*</span>
                            <span class="sr-only">(obligatorio)</span>
                        </label>

                        <select name="contrato_id"
                                id="contrato_id"
                                required
                                class="{{ $clase('contrato_id') }}">
                            <option value="">Selecciona un contrato</option>

                            @foreach ($contratos as $c)
                                <option value="{{ $c->id }}"
                                        @selected((string) old('contrato_id') === (string) $c->id)>
                                    {{ $c->empresa_nombre }} — {{ $c->contrato }} — {{ $c->convenio }}
                                </option>
                            @endforeach
                        </select>

                        @error('contrato_id')
                            <p class="{{ $textoError }}">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="sm:col-span-6 lg:col-span-4">
                        <label for="tipo_requerimiento" class="{{ $etiqueta }}">
                            Tipo de Requerimiento
                            <span class="text-red-500" aria-hidden="true">*</span>
                            <span class="sr-only">(obligatorio)</span>
                        </label>

                        <select name="tipo_requerimiento"
                                id="tipo_requerimiento"
                                required
                                class="{{ $clase('tipo_requerimiento') }}">
                            <option value="">Seleccionar tipo</option>
                            <option value="interno" @selected(old('tipo_requerimiento') === 'interno')>Interno</option>
                            <option value="externo" @selected(old('tipo_requerimiento') === 'externo')>Externo</option>
                        </select>

                        @error('tipo_requerimiento')
                            <p class="{{ $textoError }}">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Filas 2 y 3: campos opcionales --}}
                                        {{-- Filas 2 y 3: campos opcionales --}}
                    @foreach($camposProyecto as $nombre => [$texto, $placeholder, $ancho])
                        <div class="{{ $ancho }}">
                            <label for="{{ $nombre }}" class="{{ $etiqueta }}">
                                {{ $texto }}
                            </label>

                            <input type="text"
                                   name="{{ $nombre }}"
                                   id="{{ $nombre }}"
                                   class="{{ $clase($nombre) }}"
                                   value="{{ old($nombre) }}"
                                   placeholder="{{ $placeholder }}">

                            @error($nombre)
                                <p class="{{ $textoError }}">{{ $message }}</p>
                            @enderror
                        </div>
                    @endforeach

                    {{-- Destino (select desde catálogo) --}}
                    <div class="sm:col-span-6 lg:col-span-3">
                        <label for="destino_id" class="{{ $etiqueta }}">
                            Destino
                        </label>

                        <select name="destino_id"
                                id="destino_id"
                                class="{{ $clase('destino_id') }}">
                            <option value="">— Sin destino / N/A —</option>

                            @foreach($destinos as $destino)
                                <option value="{{ $destino->id }}"
                                        @selected((string) old('destino_id') === (string) $destino->id)>
                                    {{ $destino->nombre }}
                                </option>
                            @endforeach
                        </select>

                        @error('destino_id')
                            <p class="{{ $textoError }}">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </section>

            {{-- SECCIÓN 3: MATERIALES SOLICITADOS --}}
            <section class="mb-8" aria-labelledby="titulo-materiales">
                <div class="mb-4 flex flex-wrap items-center justify-between gap-2">
                    <h2 id="titulo-materiales" class="text-xl font-semibold text-gray-900 dark:text-white">
                        Materiales Solicitados
                    </h2>

                    <span class="text-sm text-gray-500 dark:text-gray-400"
                          id="contador-materiales"
                          aria-live="polite">
                        0 materiales agregados
                    </span>
                </div>

                <button type="button"
                        id="btn-agregar"
                        class="mb-4 inline-flex items-center gap-2 rounded bg-green-500 px-4 py-2 font-bold text-white transition-colors duration-200 hover:bg-green-600 focus:outline-none focus-visible:ring-2 focus-visible:ring-green-500 focus-visible:ring-offset-2 dark:bg-green-600 dark:hover:bg-green-700 dark:focus-visible:ring-offset-gray-800">
                    <span aria-hidden="true">+</span>
                    Agregar Material
                </button>

                {{-- Materiales agregados dinámicamente --}}
                <div id="materiales-agregados" class="space-y-3"></div>

                {{-- Mensaje cuando no hay materiales --}}
                <div id="mensaje-inicial" class="py-8 text-center text-gray-500 dark:text-gray-400">
                    <svg class="mx-auto mb-4 h-12 w-12 text-gray-400 dark:text-gray-500"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>

                    <p class="font-medium">No hay materiales agregados</p>
                    <p class="text-sm">Haz clic en "Agregar Material" para añadir materiales a tu requisición</p>
                </div>

                {{-- Sugerencias de unidad (el campo sigue siendo de texto libre) --}}
                <datalist id="unidades-sugeridas">
                    <option value="Piezas"></option>
                    <option value="Kg"></option>
                    <option value="Litros"></option>
                    <option value="Metros"></option>
                    <option value="Cajas"></option>
                    <option value="Juegos"></option>
                    <option value="Rollos"></option>
                    <option value="Pares"></option>
                </datalist>
            </section>

            {{-- Comentario --}}
            <div class="mb-6">
                <label for="comentario" class="{{ $etiqueta }}">
                    Comentario/Justificación
                    <span class="text-red-500" aria-hidden="true">*</span>
                    <span class="sr-only">(obligatorio)</span>
                </label>

                <textarea name="comentario"
                          id="comentario"
                          rows="4"
                          required
                          class="{{ $campoBase }} py-2 {{ $errors->has('comentario') ? $campoError : $campoOk }}"
                          placeholder="Explica detalladamente la necesidad del material, uso específico, urgencia, etc.">{{ old('comentario') }}</textarea>

                @error('comentario')
                    <p class="{{ $textoError }}">{{ $message }}</p>
                @enderror
            </div>

            {{-- Resumen --}}
            <div id="resumen-requisicion"
                 class="mb-6 hidden rounded-lg bg-gray-50 p-4 transition-colors duration-200 dark:bg-gray-700"
                 aria-live="polite">
                <h3 class="mb-3 text-lg font-medium text-gray-900 dark:text-white">
                    Resumen de la Requisición
                </h3>

                <dl class="grid grid-cols-2 gap-4 text-sm">
                    <div class="text-center">
                        <dd class="text-2xl font-bold text-purple-600 dark:text-purple-400" id="total-materiales">0</dd>
                        <dt class="text-gray-600 dark:text-gray-400">Materiales Diferentes</dt>
                    </div>

                    <div class="text-center">
                        <dd class="text-2xl font-bold text-green-600 dark:text-green-400" id="total-unidades">0</dd>
                        <dt class="text-gray-600 dark:text-gray-400">Total Unidades</dt>
                    </div>
                </dl>
            </div>

            {{-- Información --}}
            <div class="mb-6 rounded-lg bg-purple-50 p-4 transition-colors duration-200 dark:bg-purple-900/30">
                <h3 class="mb-2 text-lg font-medium text-purple-900 dark:text-purple-200">
                    Información sobre Requisiciones
                </h3>

                <div class="space-y-1 text-sm text-purple-700 dark:text-purple-300">
                    <p><strong>Interno:</strong> Material que se requiere de otros departamentos o almacén interno</p>
                    <p><strong>Externo:</strong> Material que debe ser comprado a proveedores externos</p>
                    <p><strong>Proceso:</strong> La requisición será revisada por Dirección y luego enviada a Almacén</p>
                </div>
            </div>

            {{-- Acciones --}}
            <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                <button type="button"
                        id="btn-limpiar"
                        class="rounded bg-gray-500 px-4 py-2 font-bold text-white transition-colors duration-200 hover:bg-gray-600 focus:outline-none focus-visible:ring-2 focus-visible:ring-gray-500 focus-visible:ring-offset-2 dark:bg-gray-600 dark:hover:bg-gray-700 dark:focus-visible:ring-offset-gray-800">
                    Limpiar Todo
                </button>

                <button type="submit"
                        id="btn-enviar"
                        class="rounded bg-purple-500 px-4 py-2 font-bold text-white transition-colors duration-200 hover:bg-purple-600 focus:outline-none focus-visible:ring-2 focus-visible:ring-purple-500 focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 dark:bg-purple-600 dark:hover:bg-purple-700 dark:focus-visible:ring-offset-gray-800"
                        disabled>
                    Enviar Requisición
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Plantilla de material (clonada por el script) --}}
<template id="template-material">
    <div class="material-item rounded-lg border border-gray-200 bg-white p-4 transition-all duration-200 hover:shadow-md dark:border-gray-600 dark:bg-gray-700">
        <div class="mb-3 flex items-start justify-between">
            <h4 class="font-semibold text-gray-900 dark:text-white">
                Material #<span class="material-numero"></span>
            </h4>

            <button type="button"
                    class="btn-eliminar rounded text-red-600 transition-colors duration-200 hover:text-red-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-500 dark:text-red-400 dark:hover:text-red-300"
                    aria-label="Eliminar material"
                    title="Eliminar material">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </button>
        </div>

        {{-- Descripción amplia (6) + cantidad compacta (2) + unidad (4) en escritorio --}}
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-6 lg:grid-cols-12">
            <div class="sm:col-span-6 lg:col-span-6">
                <label data-label="descripcion" class="mb-1 block text-xs font-medium text-gray-700 dark:text-gray-300">
                    Material/Descripción <span class="text-red-500" aria-hidden="true">*</span>
                </label>
                <input type="text"
                       data-campo="descripcion"
                       name="materiales[INDEX][material]"
                       class="material-descripcion h-10 w-full rounded-md border border-gray-300 bg-white px-3 text-sm text-gray-900 transition-colors duration-200 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-500 dark:bg-gray-600 dark:text-white dark:focus:border-blue-400 dark:focus:ring-blue-400"
                       placeholder="Descripción del material"
                       required>
            </div>

            <div class="sm:col-span-3 lg:col-span-2">
                <label data-label="cantidad" class="mb-1 block text-xs font-medium text-gray-700 dark:text-gray-300">
                    Cantidad <span class="text-red-500" aria-hidden="true">*</span>
                </label>
                <input type="number"
                       data-campo="cantidad"
                       name="materiales[INDEX][cantidad]"
                       inputmode="numeric"
                       class="cantidad-input h-10 w-full rounded-md border border-gray-300 bg-white px-3 text-center text-sm font-semibold text-gray-900 transition-colors duration-200 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-500 dark:bg-gray-600 dark:text-white dark:focus:border-blue-400 dark:focus:ring-blue-400"
                       min="1"
                       step="1"
                       value="1"
                       required>
            </div>

            <div class="sm:col-span-3 lg:col-span-4">
                <label data-label="unidad" class="mb-1 block text-xs font-medium text-gray-700 dark:text-gray-300">
                    Unidad <span class="text-red-500" aria-hidden="true">*</span>
                </label>
                <input type="text"
                       data-campo="unidad"
                       name="materiales[INDEX][unidad]"
                       list="unidades-sugeridas"
                       autocomplete="off"
                       class="unidad-input h-10 w-full rounded-md border border-gray-300 bg-white px-3 text-sm text-gray-900 transition-colors duration-200 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-500 dark:bg-gray-600 dark:text-white dark:focus:border-blue-400 dark:focus:ring-blue-400"
                       placeholder="Ej: Piezas, Kg, Litros"
                       required>
            </div>
        </div>
    </div>
</template>

{{-- Materiales previos (se restauran si la validación falla) --}}
<script id="materiales-anteriores" type="application/json">
    @json(array_values(old('materiales', [])))
</script>

<script>
(() => {
    const formulario   = document.getElementById('requisicionForm');
    const contenedor   = document.getElementById('materiales-agregados');
    const plantilla    = document.getElementById('template-material');
    const mensaje      = document.getElementById('mensaje-inicial');
    const contador     = document.getElementById('contador-materiales');
    const resumen      = document.getElementById('resumen-requisicion');
    const btnEnviar    = document.getElementById('btn-enviar');
    const btnAgregar   = document.getElementById('btn-agregar');
    const btnLimpiar   = document.getElementById('btn-limpiar');

    // El índice solo crece: evita que dos filas compartan el mismo name.
    let indiceSiguiente = 0;

    const items = () => contenedor.querySelectorAll('.material-item');

    function agregarMaterial(datos = null) {
        const clon = plantilla.content.cloneNode(true);
        const indice = indiceSiguiente++;

        clon.querySelectorAll('[name*="INDEX"]').forEach(campo => {
            campo.name = campo.name.replace('INDEX', indice);
        });

        // Vincula cada etiqueta con su campo (accesibilidad).
        clon.querySelectorAll('[data-campo]').forEach(campo => {
            const id = `material-${indice}-${campo.dataset.campo}`;
            campo.id = id;

            const etiqueta = clon.querySelector(`[data-label="${campo.dataset.campo}"]`);
            if (etiqueta) etiqueta.htmlFor = id;
        });

        if (datos) {
            clon.querySelector('.material-descripcion').value = datos.material ?? '';
            clon.querySelector('.cantidad-input').value = datos.cantidad ?? 1;
            clon.querySelector('.unidad-input').value = datos.unidad ?? '';
        }

        contenedor.appendChild(clon);
        actualizarVista();

        if (!datos) {
            contenedor.lastElementChild.querySelector('.material-descripcion').focus();
        }
    }

    function eliminarMaterial(item) {
        item.remove();
        actualizarVista();
        btnAgregar.focus();
    }

    function limpiarRequisicion() {
        if (items().length > 0 && !confirm('¿Estás seguro de que deseas limpiar toda la requisición?')) {
            return;
        }

        contenedor.innerHTML = '';
        indiceSiguiente = 0;
        document.getElementById('comentario').value = '';
        actualizarVista();
    }

    // Actualiza contador, numeración, resumen y estado del botón de envío.
    function actualizarVista() {
        const lista = items();
        const total = lista.length;
        let totalUnidades = 0;

        lista.forEach((item, posicion) => {
            item.querySelector('.material-numero').textContent = posicion + 1;
            totalUnidades += parseInt(item.querySelector('.cantidad-input').value, 10) || 0;
        });

        contador.textContent = `${total} material${total !== 1 ? 'es' : ''} agregado${total !== 1 ? 's' : ''}`;
        document.getElementById('total-materiales').textContent = total;
        document.getElementById('total-unidades').textContent = totalUnidades;

        mensaje.classList.toggle('hidden', total > 0);
        resumen.classList.toggle('hidden', total === 0);
        btnEnviar.disabled = total === 0;
    }

    btnAgregar.addEventListener('click', () => agregarMaterial());
    btnLimpiar.addEventListener('click', limpiarRequisicion);

    // Delegación de eventos: funciona con filas creadas dinámicamente.
    contenedor.addEventListener('click', evento => {
        const boton = evento.target.closest('.btn-eliminar');
        if (boton) eliminarMaterial(boton.closest('.material-item'));
    });

    contenedor.addEventListener('input', evento => {
        if (evento.target.classList.contains('cantidad-input')) actualizarVista();
    });

    // Evita envíos duplicados.
    formulario.addEventListener('submit', evento => {
        if (items().length === 0) {
            evento.preventDefault();
            return;
        }

        btnEnviar.disabled = true;
        btnEnviar.textContent = 'Enviando...';
    });

    // Restaura los materiales si la validación del servidor falló.
    try {
        const anteriores = JSON.parse(
            document.getElementById('materiales-anteriores').textContent
        );
        anteriores.forEach(material => agregarMaterial(material));
    } catch (error) {
        console.error('No se pudieron restaurar los materiales previos:', error);
    }

    actualizarVista();
})();
</script>
@endsection