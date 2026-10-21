@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-4 xl:flex-row xl:items-center">
        <a
            href="{{ route('requisiciones.index') }}"
            class="inline-flex items-center self-start rounded-md bg-gray-100 px-4 py-2 text-sm font-medium text-gray-700 transition-colors duration-200 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600"
        >
            <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Volver a Requisiciones
        </a>

        <h1 class="min-w-0 break-words text-2xl font-bold text-gray-900 dark:text-white sm:text-3xl">
            Requisición #{{ $requisicion->folio ?? str_pad($requisicion->id, 4, '0', STR_PAD_LEFT) }}
        </h1>

        <div class="flex flex-wrap items-center gap-3 xl:ml-auto">
            @if($requisicion->estatus_finanzas === 'pendiente')
                <span class="inline-flex items-center rounded-full bg-blue-100 px-3 py-2 text-sm font-medium text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">
                    <svg class="mr-2 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                    </svg>
                    Pendiente Finanzas
                </span>
            @elseif($requisicion->estatus_finanzas === 'aprobado')
                <span class="inline-flex items-center rounded-full bg-green-100 px-3 py-2 text-sm font-medium text-green-800 dark:bg-green-900/30 dark:text-green-300">
                    <svg class="mr-2 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                    Aprobado Finanzas
                </span>
            @else
                <span class="inline-flex items-center rounded-full bg-red-100 px-3 py-2 text-sm font-medium text-red-800 dark:bg-red-900/30 dark:text-red-300">
                    <svg class="mr-2 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                    Denegado Finanzas
                </span>
            @endif

            @if($requisicion->estatus_finanzas === 'aprobado')
                @if($requisicion->estatus === 'pendiente')
                    <span class="inline-flex items-center rounded-full bg-yellow-100 px-3 py-2 text-sm font-medium text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300">
                        <svg class="mr-2 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                        </svg>
                        Pendiente Dirección
                    </span>
                @elseif($requisicion->estatus === 'aprobado')
                    <span class="inline-flex items-center rounded-full bg-green-100 px-3 py-2 text-sm font-medium text-green-800 dark:bg-green-900/30 dark:text-green-300">
                        <svg class="mr-2 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        Aprobado Dirección
                    </span>
                @else
                    <span class="inline-flex items-center rounded-full bg-red-100 px-3 py-2 text-sm font-medium text-red-800 dark:bg-red-900/30 dark:text-red-300">
                        <svg class="mr-2 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                        Denegado Dirección
                    </span>
                @endif
            @endif

            <a
                href="{{ route('requisiciones.exportExcel', $requisicion) }}"
                class="inline-flex items-center rounded-md bg-green-600 px-4 py-2 text-sm font-medium text-white transition-colors duration-200 hover:bg-green-700 dark:bg-green-700 dark:hover:bg-green-800"
            >
                <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Exportar Excel
            </a>
        </div>
    </div>

    @if($requisicion->detalles && $requisicion->detalles->count() > 0)
        <div class="rounded-lg border border-purple-200 bg-gradient-to-r from-purple-50 to-indigo-50 p-6 transition-colors duration-200 dark:border-purple-800 dark:from-purple-900/20 dark:to-indigo-900/20">
            <h3 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">
                Resumen de la Requisición
            </h3>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div class="text-center">
                    <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">
                        {{ $requisicion->total_materiales }}
                    </div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">
                        Materiales Diferentes
                    </div>
                </div>

                <div class="text-center">
                    <div class="text-2xl font-bold text-green-600 dark:text-green-400">
                        {{ $requisicion->total_unidades }}
                    </div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">
                        Total Unidades
                    </div>
                </div>

                <div class="text-center">
                    <div class="text-2xl font-bold text-gray-600 dark:text-gray-400">
                        {{ $requisicion->created_at?->format('d/m/Y') ?? 'N/A' }}
                    </div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">
                        Fecha Requisición
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="grid min-w-0 grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="min-w-0 space-y-6 lg:col-span-2">
            <div class="rounded-lg bg-white shadow transition-colors duration-200 dark:bg-gray-800">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="mb-4 text-lg font-medium leading-6 text-gray-900 dark:text-white">
                        Materiales Solicitados
                    </h3>

                    @if($requisicion->detalles && $requisicion->detalles->count() > 0)
                        <div class="max-h-[65vh] space-y-4 overflow-y-auto overscroll-contain pr-2">
                            @foreach($requisicion->detalles as $index => $detalle)
                                <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 transition-colors duration-200 hover:border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:hover:border-gray-500">
                                    <div class="flex items-start gap-4">
                                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-purple-500 dark:bg-purple-600">
                                            <span class="text-sm font-medium text-white">
                                                {{ $index + 1 }}
                                            </span>
                                        </div>

                                        <div class="min-w-0 flex-1">
                                            <h4 class="break-words text-lg font-semibold text-gray-900 dark:text-white">
                                                {{ $detalle->material }}
                                            </h4>

                                            <div class="mt-3 grid grid-cols-2 gap-4 md:grid-cols-3">
                                                <div>
                                                    <dt class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                                        Cantidad
                                                    </dt>
                                                    <dd class="text-lg font-bold text-purple-600 dark:text-purple-400">
                                                        {{ $detalle->cantidad }}
                                                    </dd>
                                                </div>

                                                <div>
                                                    <dt class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                                        Unidad
                                                    </dt>
                                                    <dd class="break-words text-sm font-medium text-gray-900 dark:text-white">
                                                        {{ $detalle->unidad }}
                                                    </dd>
                                                </div>

                                                <div>
                                                    <dt class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                                        Tipo
                                                    </dt>
                                                    <dd class="text-sm font-medium text-gray-900 dark:text-white">
                                                        <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium {{ $requisicion->tipo_requerimiento === 'interno' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300' : 'bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-300' }}">
                                                            {{ ucfirst($requisicion->tipo_requerimiento) }}
                                                        </span>
                                                    </dd>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="py-8 text-center text-gray-500 dark:text-gray-400">
                            <svg class="mx-auto mb-4 h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                            <p class="font-medium">No hay materiales en esta requisición</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="rounded-lg bg-white shadow transition-colors duration-200 dark:bg-gray-800">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="mb-4 text-lg font-medium leading-6 text-gray-900 dark:text-white">
                        Información del Proyecto
                    </h3>

                    <dl class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Proyecto</dt>
                            <dd class="break-words text-sm font-medium text-gray-900 dark:text-white">{{ $requisicion->proyecto }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">SIT</dt>
                            <dd class="break-words text-sm font-medium text-gray-900 dark:text-white">{{ $requisicion->sit }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Partida</dt>
                            <dd class="break-words text-sm font-medium text-gray-900 dark:text-white">{{ $requisicion->partida }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Área</dt>
                            <dd class="break-words text-sm font-medium text-gray-900 dark:text-white">{{ $requisicion->area }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Activo</dt>
                            <dd class="break-words text-sm font-medium text-gray-900 dark:text-white">{{ $requisicion->activo }}</dd>
                        </div>

                        @if($requisicion->contrato)
                            <div class="md:col-span-2">
                                <dt class="mb-2 text-sm font-medium text-gray-500 dark:text-gray-400">
                                    Contrato
                                </dt>
                                <dd class="rounded-lg bg-purple-50 p-3 text-sm text-gray-900 dark:bg-purple-900/20 dark:text-white">
                                    <div class="break-words font-semibold">
                                        {{ $requisicion->contrato->empresa_nombre }}
                                    </div>
                                    <div class="mt-1 break-words text-gray-600 dark:text-gray-400">
                                        {{ $requisicion->contrato->contrato }} — {{ $requisicion->contrato->convenio }}
                                    </div>
                                </dd>
                            </div>
                        @endif
                    </dl>
                </div>
            </div>

            <div class="rounded-lg bg-white shadow transition-colors duration-200 dark:bg-gray-800">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="mb-4 text-lg font-medium leading-6 text-gray-900 dark:text-white">
                        Información de Ubicación
                    </h3>

                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div class="rounded-lg bg-blue-50 p-4 transition-colors duration-200 dark:bg-blue-900/30">
                            <div class="flex items-center">
                                <svg class="mr-3 h-8 w-8 shrink-0 text-blue-500 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                                <div class="min-w-0">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Plataforma</dt>
                                    <dd class="break-words text-lg font-semibold text-gray-900 dark:text-white">
                                        {{ $requisicion->plataforma }}
                                    </dd>
                                </div>
                            </div>
                        </div>

                        <div class="rounded-lg bg-green-50 p-4 transition-colors duration-200 dark:bg-green-900/30">
                            <div class="flex items-center">
                                <svg class="mr-3 h-8 w-8 shrink-0 text-green-500 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                </svg>
                                <div class="min-w-0">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Destino</dt>
                                    <dd class="break-words text-lg font-semibold text-gray-900 dark:text-white">
                                        {{ $requisicion->destino ?? 'N/A' }}
                                    </dd>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="rounded-lg bg-white shadow transition-colors duration-200 dark:bg-gray-800">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="mb-4 text-lg font-medium leading-6 text-gray-900 dark:text-white">
                        Comentarios y Justificación
                    </h3>
                    <div class="rounded-lg bg-gray-50 p-4 transition-colors duration-200 dark:bg-gray-700">
                        <p class="whitespace-pre-line break-words text-gray-700 dark:text-gray-300">
                            {{ $requisicion->comentario }}
                        </p>
                    </div>
                </div>
            </div>

            @if(auth()->user()->canApproveFinanzas() && $requisicion->estatus_finanzas === 'pendiente')
                <div class="rounded-lg bg-white shadow transition-colors duration-200 dark:bg-gray-800">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="mb-4 text-lg font-medium leading-6 text-gray-900 dark:text-white">
                            Aprobación de Finanzas
                        </h3>

                        <div class="flex flex-col gap-4 sm:flex-row">
                            <form
                                method="POST"
                                action="{{ route('requisiciones.updateEstatusFinanzas', $requisicion) }}"
                                class="min-w-0 flex-1"
                            >
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="estatus_finanzas" value="aprobado">

                                <button
                                    type="submit"
                                    class="inline-flex w-full items-center justify-center rounded-md border border-transparent bg-green-600 px-4 py-3 text-base font-medium text-white transition-colors duration-200 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:bg-green-700 dark:hover:bg-green-800"
                                    onclick="return confirm('¿Aprobar esta requisición desde finanzas?')"
                                >
                                    Aprobar (Finanzas)
                                </button>
                            </form>

                            <form
                                method="POST"
                                action="{{ route('requisiciones.updateEstatusFinanzas', $requisicion) }}"
                                class="min-w-0 flex-1"
                            >
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="estatus_finanzas" value="denegado">

                                <button
                                    type="submit"
                                    class="inline-flex w-full items-center justify-center rounded-md border border-transparent bg-red-600 px-4 py-3 text-base font-medium text-white transition-colors duration-200 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:bg-red-700 dark:hover:bg-red-800"
                                    onclick="return confirm('¿Denegar esta requisición desde finanzas?')"
                                >
                                    Denegar (Finanzas)
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endif

            @if(auth()->user()->canApproveRequests() && $requisicion->estatus_finanzas === 'aprobado' && $requisicion->estatus === 'pendiente')
                <div class="rounded-lg bg-white shadow transition-colors duration-200 dark:bg-gray-800">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="mb-4 text-lg font-medium leading-6 text-gray-900 dark:text-white">
                            Aprobación de Dirección
                        </h3>

                        <div class="mb-4 rounded-lg bg-blue-50 p-3 dark:bg-blue-900/30">
                            <p class="text-sm text-blue-800 dark:text-blue-300">
                                ✓ Esta requisición ya fue aprobada por finanzas y está lista para su revisión final.
                            </p>
                        </div>

                        <div class="flex flex-col gap-4 sm:flex-row">
                            <form
                                method="POST"
                                action="{{ route('requisiciones.updateEstatus', $requisicion) }}"
                                class="min-w-0 flex-1"
                            >
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="estatus" value="aprobado">

                                <button
                                    type="submit"
                                    class="inline-flex w-full items-center justify-center rounded-md border border-transparent bg-green-600 px-4 py-3 text-base font-medium text-white transition-colors duration-200 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:bg-green-700 dark:hover:bg-green-800"
                                    onclick="return confirm('¿Aprobar esta requisición?')"
                                >
                                    Aprobar Requisición
                                </button>
                            </form>

                            <form
                                method="POST"
                                action="{{ route('requisiciones.updateEstatus', $requisicion) }}"
                                class="min-w-0 flex-1"
                            >
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="estatus" value="denegado">

                                <button
                                    type="submit"
                                    class="inline-flex w-full items-center justify-center rounded-md border border-transparent bg-red-600 px-4 py-3 text-base font-medium text-white transition-colors duration-200 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:bg-red-700 dark:hover:bg-red-800"
                                    onclick="return confirm('¿Denegar esta requisición?')"
                                >
                                    Denegar Requisición
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div class="min-w-0 space-y-6">
            <div class="rounded-lg bg-white shadow transition-colors duration-200 dark:bg-gray-800">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="mb-4 text-lg font-medium leading-6 text-gray-900 dark:text-white">
                        Información del Solicitante
                    </h3>

                    <div class="text-center">
                        <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-purple-500 dark:bg-purple-600">
                            <span class="text-2xl font-medium text-white">
                                {{ substr($requisicion->nombre_solicitante, 0, 1) }}
                            </span>
                        </div>

                        <h4 class="mt-3 break-words text-lg font-medium text-gray-900 dark:text-white">
                            {{ $requisicion->nombre_solicitante }}
                        </h4>
                        <p class="break-words text-sm text-gray-500 dark:text-gray-400">
                            {{ $requisicion->departamento }}
                        </p>

                        @if($requisicion->user)
                            <p class="mt-1 break-words text-xs text-gray-400 dark:text-gray-500">
                                {{ $requisicion->user->email }}
                            </p>
                            <p class="break-words text-xs text-gray-400 dark:text-gray-500">
                                {{ ucfirst(str_replace('_', ' ', $requisicion->user->role)) }}
                            </p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="rounded-lg bg-white shadow transition-colors duration-200 dark:bg-gray-800">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="mb-4 text-lg font-medium leading-6 text-gray-900 dark:text-white">
                        Historial de Aprobaciones
                    </h3>

                    <dl class="space-y-4">
                        <div class="border-l-4 {{ $requisicion->estatus_finanzas === 'aprobado' ? 'border-green-500' : ($requisicion->estatus_finanzas === 'denegado' ? 'border-red-500' : 'border-blue-500') }} pl-4">
                            <dt class="text-xs font-medium uppercase text-gray-500 dark:text-gray-400">
                                Finanzas
                            </dt>
                            <dd class="mt-1 text-sm font-medium text-gray-900 dark:text-white">
                                @if($requisicion->estatus_finanzas === 'aprobado')
                                    ✓ Aprobado
                                @elseif($requisicion->estatus_finanzas === 'denegado')
                                    ✗ Denegado
                                @else
                                    ⏳ Pendiente
                                @endif
                            </dd>

                            @if($requisicion->aprobadorFinanzas)
                                <dd class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    Por: {{ $requisicion->aprobadorFinanzas->name }}
                                </dd>

                                @if($requisicion->fecha_aprobacion_finanzas)
                                    <dd class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $requisicion->fecha_aprobacion_finanzas->format('d/m/Y H:i') }}
                                    </dd>
                                @endif
                            @endif
                        </div>

                        @if($requisicion->estatus_finanzas === 'aprobado')
                            <div class="border-l-4 {{ $requisicion->estatus === 'aprobado' ? 'border-green-500' : ($requisicion->estatus === 'denegado' ? 'border-red-500' : 'border-yellow-500') }} pl-4">
                                <dt class="text-xs font-medium uppercase text-gray-500 dark:text-gray-400">
                                    Dirección
                                </dt>
                                <dd class="mt-1 text-sm font-medium text-gray-900 dark:text-white">
                                    @if($requisicion->estatus === 'aprobado')
                                        ✓ Aprobado
                                    @elseif($requisicion->estatus === 'denegado')
                                        ✗ Denegado
                                    @else
                                        ⏳ Pendiente
                                    @endif
                                </dd>
                            </div>
                        @endif
                    </dl>
                </div>
            </div>

            <div class="rounded-lg bg-white shadow transition-colors duration-200 dark:bg-gray-800">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="mb-4 text-lg font-medium leading-6 text-gray-900 dark:text-white">
                        Información de Fechas
                    </h3>

                    <dl class="space-y-3">
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                Fecha de Requisición
                            </dt>
                            <dd class="font-medium text-gray-900 dark:text-white">
                                @if($requisicion->created_at)
                                    {{ $requisicion->created_at->format('d/m/Y H:i:s') }}
                                @else
                                    <span class="text-gray-400 dark:text-gray-500">No disponible</span>
                                @endif
                            </dd>

                            @if($requisicion->created_at)
                                <dd class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $requisicion->created_at->diffForHumans() }}
                                </dd>
                            @endif
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                Última Actualización
                            </dt>
                            <dd class="font-medium text-gray-900 dark:text-white">
                                @if($requisicion->updated_at)
                                    {{ $requisicion->updated_at->format('d/m/Y H:i:s') }}
                                @else
                                    <span class="text-gray-400 dark:text-gray-500">No disponible</span>
                                @endif
                            </dd>

                            @if($requisicion->updated_at)
                                <dd class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $requisicion->updated_at->diffForHumans() }}
                                </dd>
                            @endif
                        </div>
                    </dl>
                </div>
            </div>

            <div class="rounded-lg bg-white shadow transition-colors duration-200 dark:bg-gray-800">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="mb-4 text-lg font-medium leading-6 text-gray-900 dark:text-white">
                        Tipo de Requerimiento
                    </h3>

                    <div class="text-center">
                        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full transition-colors duration-200 {{ $requisicion->tipo_requerimiento === 'interno' ? 'bg-blue-100 dark:bg-blue-900/30' : 'bg-orange-100 dark:bg-orange-900/30' }}">
                            @if($requisicion->tipo_requerimiento === 'interno')
                                <svg class="h-8 w-8 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2H4zm0 2h12v8H4V6z" clip-rule="evenodd"/>
                                </svg>
                            @else
                                <svg class="h-8 w-8 text-orange-600 dark:text-orange-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM4.332 8.027a6.012 6.012 0 011.912-2.706C6.512 5.73 6.974 6 7.5 6A1.5 1.5 0 019 7.5V8a2 2 0 004 0v-.5A1.5 1.5 0 0114.5 6c.526 0 .988-.27 1.256-.679a6.012 6.012 0 011.912 2.706A8.012 8.012 0 0110 16a8.012 8.012 0 01-7.668-8.027z" clip-rule="evenodd"/>
                                </svg>
                            @endif
                        </div>

                        <h4 class="mt-3 text-lg font-medium {{ $requisicion->tipo_requerimiento === 'interno' ? 'text-blue-900 dark:text-blue-300' : 'text-orange-900 dark:text-orange-300' }}">
                            {{ ucfirst($requisicion->tipo_requerimiento) }}
                        </h4>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            {{ $requisicion->tipo_requerimiento === 'interno' ? 'Material disponible en almacén' : 'Material requiere compra externa' }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="rounded-lg bg-white shadow transition-colors duration-200 dark:bg-gray-800">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="mb-4 text-lg font-medium leading-6 text-gray-900 dark:text-white">
                        Seguimiento
                    </h3>

                    <div class="rounded-lg bg-gray-50 p-3 transition-colors duration-200 dark:bg-gray-700">
                        <div class="text-center">
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Folio de Requisición
                            </p>
                            <p class="break-all font-mono text-2xl font-bold text-gray-900 dark:text-white">
                                {{ $requisicion->folio ?? '#' . str_pad($requisicion->id, 6, '0', STR_PAD_LEFT) }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection