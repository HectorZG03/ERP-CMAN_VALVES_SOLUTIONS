@extends('layouts.app')

@section('content')
@php
    $solicitud = $valepp->solicitudMaterial;

    $asignacionesIniciales = old('detalles');

    if (!is_array($asignacionesIniciales)) {
        $asignacionesIniciales = $valepp->detalles
            ->map(fn ($detalle) => [
                'solicitud_material_detalle_id' => $detalle->solicitud_material_detalle_id,
                'cantidad' => $detalle->cantidad,
            ])
            ->values()
            ->all();
    }

    $productosIniciales = $valepp->detalles
        ->mapWithKeys(fn ($detalle) => [
            (string) $detalle->solicitud_material_detalle_id => [
                'id' => $detalle->solicitud_material_detalle_id,
                'nombre_producto' => $detalle->inventario?->nombre_producto ?? 'Producto no disponible',
                'economico' => $detalle->inventario?->economico,
                'categoria' => $detalle->inventario?->categoria,
                'medida' => $detalle->inventario?->medida,
            ],
        ])
        ->all();
@endphp

<div class="mx-auto max-w-7xl space-y-6 pb-8">
    <header class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="text-sm font-semibold uppercase tracking-wide text-orange-600 dark:text-orange-400">
                Corrección de vale EPP
            </p>
            <h1 class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">
                Editar {{ $valepp->numero_vale }}
            </h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Agrega, quita o ajusta productos de la solicitud enlazada.
            </p>
        </div>

        <a
            href="{{ route('valepp.show', $valepp) }}"
            class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700"
        >
            Volver al vale
        </a>
    </header>

    @if ($errors->any())
        <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-800 dark:border-red-800 dark:bg-red-900/30 dark:text-red-200">
            <p class="font-semibold">Revisa los datos del vale:</p>
            <ul class="mt-2 list-inside list-disc space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form
        id="valepp-edit-form"
        method="POST"
        action="{{ route('valepp.update', $valepp) }}"
        class="space-y-6"
    >
        @csrf
        @method('PUT')

        <input
            type="hidden"
            name="personal_id"
            value="{{ old('personal_id', $valepp->personal_id) }}"
        >

        <section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                Datos que se conservan
            </h2>

            <dl class="mt-4 grid gap-4 sm:grid-cols-3">
                <div>
                    <dt class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">
                        Vale
                    </dt>
                    <dd class="mt-1 font-semibold text-gray-900 dark:text-white">
                        {{ $valepp->numero_vale }}
                    </dd>
                </div>

                <div>
                    <dt class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">
                        Operador
                    </dt>
                    <dd class="mt-1 font-semibold text-gray-900 dark:text-white">
                        {{ $valepp->personal?->nombre_mostrar ?? 'No disponible' }}
                    </dd>
                </div>

                <div>
                    <dt class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">
                        Solicitud enlazada
                    </dt>
                    <dd class="mt-1 font-semibold text-gray-900 dark:text-white">
                        Solicitud #{{ str_pad((string) $solicitud->id, 4, '0', STR_PAD_LEFT) }}
                        <span class="font-normal text-gray-500 dark:text-gray-400">
                            · {{ $solicitud->destino ?? 'Sin destino' }}
                        </span>
                    </dd>
                </div>
            </dl>
        </section>

        <section
            id="valepp-edit-app"
            class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800"
        >
            <div class="border-b border-gray-200 p-5 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                    Productos asignados
                </h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Solo puedes agregar productos de la solicitud enlazada. Las cantidades liberadas quedarán disponibles para otros vales.
                </p>
            </div>

            <div class="grid gap-4 p-5 sm:grid-cols-[1fr_auto]">
                <div>
                    <label for="producto-disponible" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-200">
                        Agregar producto disponible
                    </label>
                    <select
                        id="producto-disponible"
                        disabled
                        class="block w-full rounded-lg border-gray-300 bg-white text-sm text-gray-900 shadow-sm focus:border-orange-500 focus:ring-orange-500 disabled:opacity-60 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                    >
                        <option value="">Cargando productos de la solicitud…</option>
                    </select>
                </div>

                <div class="flex items-end">
                    <button
                        id="agregar-producto-btn"
                        type="button"
                        disabled
                        class="w-full rounded-lg bg-orange-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-orange-700 disabled:cursor-not-allowed disabled:opacity-50 sm:w-auto"
                    >
                        Agregar producto
                    </button>
                </div>
            </div>

            <p
                id="valepp-edit-status"
                class="hidden px-5 pb-4 text-sm"
                aria-live="polite"
            ></p>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-900/40">
                        <tr>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">
                                Producto
                            </th>
                            <th class="px-5 py-3 text-center text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">
                                Solicitado
                            </th>
                            <th class="px-5 py-3 text-center text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">
                                Máximo para este vale
                            </th>
                            <th class="px-5 py-3 text-center text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">
                                Cantidad en el vale
                            </th>
                            <th class="px-5 py-3 text-right text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">
                                Acción
                            </th>
                        </tr>
                    </thead>

                    <tbody
                        id="valepp-edit-rows"
                        class="divide-y divide-gray-200 dark:divide-gray-700"
                    ></tbody>
                </table>
            </div>

            <div class="border-t border-gray-200 p-5 dark:border-gray-700">
                @error('detalles')
                    <p class="mb-3 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="fecha_solicitud" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-200">
                            Fecha del vale
                        </label>
                        <input
                            id="fecha_solicitud"
                            name="fecha_solicitud"
                            type="date"
                            max="{{ now()->toDateString() }}"
                            value="{{ old('fecha_solicitud', $valepp->fecha_solicitud?->format('Y-m-d')) }}"
                            required
                            class="block w-full rounded-lg border-gray-300 bg-white text-sm text-gray-900 shadow-sm focus:border-orange-500 focus:ring-orange-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                        >
                        @error('fecha_solicitud')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="observaciones" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-200">
                            Observaciones
                        </label>
                        <textarea
                            id="observaciones"
                            name="observaciones"
                            rows="3"
                            maxlength="2000"
                            class="block w-full rounded-lg border-gray-300 bg-white text-sm text-gray-900 shadow-sm focus:border-orange-500 focus:ring-orange-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                        >{{ old('observaciones', $valepp->observaciones) }}</textarea>
                    </div>
                </div>

                <div class="mt-5 flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
                    <a
                        href="{{ route('valepp.show', $valepp) }}"
                        class="rounded-lg border border-gray-300 px-4 py-2.5 text-center text-sm font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700"
                    >
                        Cancelar
                    </a>
                    <button
                        id="guardar-vale-btn"
                        type="submit"
                        class="rounded-lg bg-orange-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-orange-700 disabled:cursor-not-allowed disabled:opacity-50"
                    >
                        Guardar cambios
                    </button>
                </div>
            </div>
        </section>
    </form>
</div>

<script>
    window.valeppEditConfig = {
        detailUrl: @json(route('valepp.solicitudes-epp.show', ['solicitud' => $solicitud->id])),
        valeppId: @json($valepp->id),
        assignments: @json($asignacionesIniciales),
        existingProducts: @json($productosIniciales),
    };
</script>
<script src="{{ asset('js/valepp/edit.js') }}" defer></script>
@endsection