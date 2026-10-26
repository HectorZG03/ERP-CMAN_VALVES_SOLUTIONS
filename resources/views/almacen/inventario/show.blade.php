@extends('layouts.app')

@section('content')

@php
    $stock = max(0, (int) $inventario->existencia);
    $nombre = $inventario->nombre_producto ?: 'Producto sin nombre';
    $precioPromedio = $inventario->getPrecioPromedio();
    $valorTotal = $inventario->precio_total ?? 0;

    if ($stock > 10) {
        $estado = 'Disponible';
        $estadoTexto = 'text-green-600 dark:text-green-400';
        $estadoFondo = 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300';
        $estadoPunto = 'bg-green-500';
    } elseif ($stock > 0) {
        $estado = 'Stock bajo';
        $estadoTexto = 'text-yellow-600 dark:text-yellow-400';
        $estadoFondo = 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300';
        $estadoPunto = 'bg-yellow-500';
    } else {
        $estado = 'Agotado';
        $estadoTexto = 'text-red-600 dark:text-red-400';
        $estadoFondo = 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300';
        $estadoPunto = 'bg-red-500';
    }
@endphp

<div class="mx-auto max-w-6xl space-y-5">

    {{-- Encabezado --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

        <div class="flex min-w-0 items-center gap-3">

            <a href="{{ route('inventario.index') }}"
               title="Volver al inventario"
               class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg
                      border border-gray-200 bg-white text-gray-500
                      hover:bg-gray-50 hover:text-blue-600
                      dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400
                      dark:hover:bg-gray-700">

                <svg class="h-4 w-4"
                     fill="none"
                     stroke="currentColor"
                     viewBox="0 0 24 24">
                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>

            </a>

            <div class="min-w-0">

                <div class="flex flex-wrap items-center gap-2">

                    <h1 class="truncate text-xl font-bold text-gray-900 dark:text-white">
                        {{ $nombre }}
                    </h1>

                    <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $estadoFondo }}">
                        {{ $estado }}
                    </span>

                </div>

                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                    ID #{{ str_pad($inventario->id, 4, '0', STR_PAD_LEFT) }}
                    <span class="mx-1">•</span>
                    Económico: {{ $inventario->economico ?: 'Sin asignar' }}
                </p>

            </div>

        </div>


        @if(auth()->user()->canManageInventory())

            <div class="flex items-center gap-2">

                <a href="{{ route('inventario.ajustes.index', ['inventario_id' => $inventario->id]) }}"
                   class="rounded-lg border border-gray-200 bg-white px-3 py-2 text-xs
                          font-medium text-gray-700 hover:bg-gray-50
                          dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300
                          dark:hover:bg-gray-700">
                    Historial
                </a>

                <a href="{{ route('inventario.edit', $inventario) }}"
                   class="rounded-lg bg-blue-600 px-3.5 py-2 text-xs font-semibold
                          text-white hover:bg-blue-700">
                    Editar
                </a>

            </div>

        @endif

    </div>


    {{-- Resumen --}}
    <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">

        {{-- Existencia --}}
        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm
                    dark:border-gray-700 dark:bg-gray-800">

            <p class="text-xs font-medium text-gray-500 dark:text-gray-400">
                Existencia
            </p>

            <div class="mt-1 flex items-end justify-between gap-3">

                <p class="text-2xl font-bold {{ $estadoTexto }}">
                    {{ number_format($stock) }}
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">
                        {{ $inventario->medida }}
                    </span>
                </p>

                <span class="flex items-center gap-1.5 text-xs {{ $estadoTexto }}">
                    <span class="h-1.5 w-1.5 rounded-full {{ $estadoPunto }}"></span>
                    {{ $estado }}
                </span>

            </div>

        </div>


        {{-- Costo promedio --}}
        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm
                    dark:border-gray-700 dark:bg-gray-800">

            <p class="text-xs font-medium text-gray-500 dark:text-gray-400">
                Precio unitario promedio
            </p>

            <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">
                ${{ number_format($precioPromedio, 2) }}
            </p>

            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                Por {{ $inventario->medida }}
            </p>

        </div>


        {{-- Valor total --}}
        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm
                    dark:border-gray-700 dark:bg-gray-800">

            <p class="text-xs font-medium text-gray-500 dark:text-gray-400">
                Valor del inventario
            </p>

            <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">
                ${{ number_format($valorTotal, 2) }}
            </p>

            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                Existencia actual
            </p>

        </div>

    </div>


    {{-- Información del producto --}}
    <div class="rounded-xl border border-gray-200 bg-white shadow-sm
                dark:border-gray-700 dark:bg-gray-800">

        <div class="border-b border-gray-200 px-5 py-4 dark:border-gray-700">

            <h2 class="text-sm font-semibold text-gray-900 dark:text-white">
                Información del producto
            </h2>

            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                Datos registrados en el catálogo.
            </p>

        </div>


        <div class="grid grid-cols-2 gap-x-6 gap-y-5 p-5 sm:grid-cols-4">

            <div>
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    Categoría
                </p>

                <p class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">
                    {{ $inventario->categoria ?: 'Sin categoría' }}
                </p>
            </div>


            <div>
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    Económico
                </p>

                <p class="mt-1 break-all text-sm font-semibold text-gray-900 dark:text-white">
                    {{ $inventario->economico ?: 'Sin asignar' }}
                </p>
            </div>


            <div>
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    Unidad de medida
                </p>

                <p class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">
                    {{ $inventario->medida ?: 'N/A' }}
                </p>
            </div>


            <div>
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    Ubicación
                </p>

                <p class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">
                    {{ $inventario->ubicacion ?: 'N/A' }}
                </p>
            </div>

        </div>

    </div>


    {{-- Información del registro --}}
    <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">

        <div class="rounded-lg border border-gray-200 bg-white px-4 py-3
                    dark:border-gray-700 dark:bg-gray-800">

            <p class="text-xs text-gray-500 dark:text-gray-400">
                ID del producto
            </p>

            <p class="mt-1 font-mono text-sm font-semibold text-gray-900 dark:text-white">
                #{{ str_pad($inventario->id, 6, '0', STR_PAD_LEFT) }}
            </p>

        </div>


        <div class="rounded-lg border border-gray-200 bg-white px-4 py-3
                    dark:border-gray-700 dark:bg-gray-800">

            <p class="text-xs text-gray-500 dark:text-gray-400">
                Creado
            </p>

            <p class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">
                {{ $inventario->created_at?->format('d/m/Y H:i') ?? 'N/A' }}
            </p>

        </div>


        <div class="rounded-lg border border-gray-200 bg-white px-4 py-3
                    dark:border-gray-700 dark:bg-gray-800">

            <p class="text-xs text-gray-500 dark:text-gray-400">
                Última actualización
            </p>

            <p class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">
                {{ $inventario->updated_at?->format('d/m/Y H:i') ?? 'N/A' }}
            </p>

        </div>

    </div>


    {{-- Últimos movimientos --}}
    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm
                dark:border-gray-700 dark:bg-gray-800">

        <div class="flex items-center justify-between border-b border-gray-200 px-5 py-4
                    dark:border-gray-700">

            <div>
                <h2 class="text-sm font-semibold text-gray-900 dark:text-white">
                    Últimos movimientos
                </h2>

                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                    Entradas y salidas recientes.
                </p>
            </div>

            <span class="rounded-full bg-gray-100 px-2.5 py-1 text-xs font-medium
                         text-gray-600 dark:bg-gray-700 dark:text-gray-300">
                {{ $movimientos->count() }}
            </span>

        </div>


        @forelse($movimientos as $movimiento)

            @php
                $entrada = ($movimiento['tipo'] ?? '') === 'entrada';

                $tipo = $entrada ? 'Entrada' : 'Salida';

                $tipoTexto = $entrada
                    ? 'text-green-600 dark:text-green-400'
                    : 'text-red-600 dark:text-red-400';

                $tipoFondo = $entrada
                    ? 'bg-green-100 dark:bg-green-900/30'
                    : 'bg-red-100 dark:bg-red-900/30';

                $cantidad = (float) ($movimiento['cantidad'] ?? 0);

                $persona = $entrada
                    ? ($movimiento['proveedor'] ?? 'N/A')
                    : ($movimiento['cliente'] ?? 'N/A');

                $personaTipo = $entrada ? 'Proveedor' : 'Cliente';
            @endphp


            <div class="border-b border-gray-100 px-5 py-4 last:border-0
                        dark:border-gray-700">

                <div class="flex items-start gap-3">

                    {{-- Tipo --}}
                    <div class="flex h-8 w-8 shrink-0 items-center justify-center
                                rounded-lg {{ $tipoFondo }} {{ $tipoTexto }}">

                        @if($entrada)

                            <svg class="h-4 w-4"
                                 fill="none"
                                 stroke="currentColor"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      stroke-width="2"
                                      d="M7 16l-4-4m0 0l4-4m-4 4h18"/>
                            </svg>

                        @else

                            <svg class="h-4 w-4"
                                 fill="none"
                                 stroke="currentColor"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      stroke-width="2"
                                      d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                            </svg>

                        @endif

                    </div>


                    {{-- Información --}}
                    <div class="min-w-0 flex-1">

                        <div class="flex flex-wrap items-center gap-x-2 gap-y-1">

                            <span class="text-sm font-semibold {{ $tipoTexto }}">
                                {{ $tipo }}
                            </span>

                            <span class="text-xs text-gray-400">
                                #{{ $movimiento['numero_factura'] ?? 'N/A' }}
                            </span>

                        </div>


                        <div class="mt-1 flex flex-wrap gap-x-3 gap-y-1 text-xs
                                    text-gray-500 dark:text-gray-400">

                            <span>
                                {{ $personaTipo }}: {{ $persona ?: 'N/A' }}
                            </span>

                            @if(!empty($movimiento['usuario']))
                                <span>
                                    Usuario: {{ $movimiento['usuario'] }}
                                </span>
                            @endif

                            @if(!empty($movimiento['fecha']))
                                <span>
                                    {{ \Carbon\Carbon::parse($movimiento['fecha'])->format('d/m/Y H:i') }}
                                </span>
                            @endif

                        </div>


                        @if(
                            !empty($movimiento['observaciones']) &&
                            $movimiento['observaciones'] !== 'Sin observaciones'
                        )

                            <p class="mt-2 rounded-md bg-gray-50 px-3 py-2 text-xs
                                      text-gray-500 dark:bg-gray-700/50 dark:text-gray-400">

                                <span class="font-medium text-gray-600 dark:text-gray-300">
                                    Observación:
                                </span>

                                {{ $movimiento['observaciones'] }}

                            </p>

                        @endif

                    </div>


                    {{-- Cantidad --}}
                    <div class="shrink-0 text-right">

                        <p class="text-sm font-bold {{ $tipoTexto }}">
                            {{ $entrada ? '+' : '-' }}{{ number_format($cantidad, 2) }}
                        </p>

                        <p class="text-[11px] text-gray-400">
                            {{ $inventario->medida }}
                        </p>

                    </div>

                </div>

            </div>

        @empty

            <div class="px-5 py-12 text-center">

                <div class="mx-auto flex h-10 w-10 items-center justify-center
                            rounded-full bg-gray-100 text-gray-400
                            dark:bg-gray-700 dark:text-gray-500">

                    <svg class="h-5 w-5"
                         fill="none"
                         viewBox="0 0 24 24"
                         stroke="currentColor">

                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="1.7"
                              d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a3 3 0 006 0M9 5a3 3 0 013-3 3 3 0 013 3"/>

                    </svg>

                </div>

                <p class="mt-3 text-sm font-semibold text-gray-900 dark:text-white">
                    Sin movimientos registrados
                </p>

                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                    Este producto todavía no tiene entradas ni salidas.
                </p>

            </div>

        @endforelse

    </div>


    {{-- Nota --}}
    <div class="rounded-lg border border-blue-100 bg-blue-50 px-4 py-3
                dark:border-blue-900/40 dark:bg-blue-900/10">

        <p class="text-xs text-blue-700 dark:text-blue-400">
            La existencia se actualiza mediante entradas, salidas y ajustes de inventario.
        </p>

    </div>

</div>

@endsection