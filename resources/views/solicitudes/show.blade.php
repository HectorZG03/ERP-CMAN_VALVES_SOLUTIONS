@extends('layouts.app')

@section('content')
@php
    $usuarioActual = auth()->user();
    $esEpp = $solicitud->esEpp();

    $nombreSolicitante = $solicitud->user?->name
        ?? 'Usuario no disponible';

    $correoSolicitante = $solicitud->user?->email
        ?? 'Sin correo';

    $rolSolicitante = $solicitud->user?->role
        ?? 'N/A';

    $personalAsignado = $solicitud->operadorPersonal?->nombre_completo;

    if (
        !$personalAsignado
        && $solicitud->operador
        && $solicitud->operador !== 'N/A'
    ) {
        $personalAsignado = $solicitud->operador;
    }

    $personalAsignado = $personalAsignado
        ?: ($esEpp
            ? 'Se asignará mediante los vales EPP'
            : 'Sin personal asignado');
@endphp

<div class="mx-auto w-full max-w-[1700px] space-y-5">
    <header class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a
                href="{{ route('solicitudes.index') }}"
                class="inline-flex h-10 items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700"
            >
                <svg
                    class="h-4 w-4"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M15 19l-7-7 7-7"
                    />
                </svg>

                Volver
            </a>

            <div>
                <div class="flex items-center gap-3">
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                        Solicitud #{{ str_pad(
                            $solicitud->id,
                            4,
                            '0',
                            STR_PAD_LEFT
                        ) }}
                    </h1>

                    @if($esEpp)
                        <span class="rounded-full bg-orange-200 px-3 py-1 text-xs font-bold uppercase tracking-wide text-orange-800 dark:bg-orange-800 dark:text-orange-100">
                            Solicitud EPP
                        </span>
                    @else
                        <span class="rounded-full bg-blue-100 px-3 py-1 text-xs font-bold uppercase tracking-wide text-blue-700 dark:bg-blue-900/40 dark:text-blue-300">
                            Estándar
                        </span>
                    @endif
                </div>

                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Creada el
                    {{ $solicitud->created_at?->format('d/m/Y \a \l\a\s H:i')
                        ?? 'Sin fecha' }}
                </p>
            </div>
        </div>

        <div class="flex items-center gap-3">
            @if($solicitud->estatus === 'pendiente')
                <span class="inline-flex h-10 items-center gap-2 rounded-full bg-yellow-100 px-4 text-sm font-bold text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300">
                    <span class="h-2 w-2 rounded-full bg-yellow-500"></span>
                    Pendiente de aprobación
                </span>
            @elseif($solicitud->estatus === 'aprobado')
                <span class="inline-flex h-10 items-center gap-2 rounded-full bg-green-100 px-4 text-sm font-bold text-green-800 dark:bg-green-900/30 dark:text-green-300">
                    <span class="h-2 w-2 rounded-full bg-green-500"></span>
                    Aprobada
                </span>
            @else
                <span class="inline-flex h-10 items-center gap-2 rounded-full bg-red-100 px-4 text-sm font-bold text-red-800 dark:bg-red-900/30 dark:text-red-300">
                    <span class="h-2 w-2 rounded-full bg-red-500"></span>
                    Denegada
                </span>
            @endif

            <a
                href="{{ route('solicitudes.pdf', $solicitud) }}"
                target="_blank"
                class="inline-flex h-10 items-center gap-2 rounded-lg bg-gray-700 px-4 text-sm font-bold text-white shadow-sm transition hover:bg-gray-800 dark:bg-gray-600 dark:hover:bg-gray-500"
            >
                <svg
                    class="h-4 w-4"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M7 3h7l5 5v13H7V3zm7 0v5h5M9 13h6M9 17h6"
                    />
                </svg>

                Ver PDF
            </a>
        </div>
    </header>

    @if(session('success'))
        <div class="rounded-lg border border-green-200 bg-green-50 px-5 py-4 text-green-800 dark:border-green-800 dark:bg-green-900/30 dark:text-green-200">
            {{ session('success') }}
        </div>
    @endif

    @if($esEpp)
        <section class="flex items-center justify-between rounded-lg border border-orange-300 bg-orange-50 px-5 py-4 dark:border-orange-700 dark:bg-orange-950/30">
            <div>
                <h2 class="font-bold text-orange-900 dark:text-orange-200">
                    Solicitud general de equipo de protección personal
                </h2>

                <p class="mt-1 text-sm text-orange-700 dark:text-orange-300">
                    Los materiales se distribuirán posteriormente entre los
                    trabajadores mediante vales EPP vinculados a esta solicitud.
                </p>
            </div>

            <span class="rounded-lg bg-orange-200 px-4 py-2 text-sm font-bold text-orange-800 dark:bg-orange-800 dark:text-orange-100">
                Categoría SEGURIDAD
            </span>
        </section>
    @endif

    <section class="grid grid-cols-4 overflow-hidden rounded-xl border
        {{ $esEpp
            ? 'border-orange-200 bg-orange-50 dark:border-orange-800 dark:bg-orange-950/20'
            : 'border-blue-200 bg-blue-50 dark:border-blue-800 dark:bg-blue-950/20' }}"
    >
        <div class="border-r px-5 py-4 text-center
            {{ $esEpp
                ? 'border-orange-200 dark:border-orange-800'
                : 'border-blue-200 dark:border-blue-800' }}"
        >
            <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                {{ $solicitud->total_productos }}
            </p>

            <p class="mt-1 text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                Productos diferentes
            </p>
        </div>

        <div class="border-r px-5 py-4 text-center
            {{ $esEpp
                ? 'border-orange-200 dark:border-orange-800'
                : 'border-blue-200 dark:border-blue-800' }}"
        >
            <p class="text-2xl font-bold text-green-600 dark:text-green-400">
                {{ $solicitud->total_unidades }}
            </p>

            <p class="mt-1 text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                Unidades solicitadas
            </p>
        </div>

        <div class="border-r px-5 py-4 text-center
            {{ $esEpp
                ? 'border-orange-200 dark:border-orange-800'
                : 'border-blue-200 dark:border-blue-800' }}"
        >
            <p class="text-2xl font-bold text-purple-600 dark:text-purple-400">
                ${{ number_format($solicitud->total, 2) }}
            </p>

            <p class="mt-1 text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                Valor estimado
            </p>
        </div>

        <div class="px-5 py-4 text-center">
            <p class="text-2xl font-bold text-gray-700 dark:text-gray-200">
                {{ $solicitud->created_at?->format('d/m/Y') ?? 'N/A' }}
            </p>

            <p class="mt-1 text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                Fecha de solicitud
            </p>
        </div>
    </section>

    <div class="grid grid-cols-12 items-start gap-5">
        <main class="col-span-8 space-y-5">
            <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <div class="flex items-center justify-between border-b border-gray-200 px-5 py-4 dark:border-gray-700">
                    <div>
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">
                            Materiales solicitados
                        </h2>

                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Existencias y precios actuales del inventario.
                        </p>
                    </div>

                    <span class="rounded-full bg-gray-100 px-3 py-1.5 text-xs font-semibold text-gray-600 dark:bg-gray-700 dark:text-gray-300">
                        {{ $solicitud->detalles->count() }}
                        registros
                    </span>
                </div>

                @if($solicitud->detalles->isNotEmpty())
                    <table class="w-full table-fixed">
                        <colgroup>
                            <col class="w-[8%]">
                            <col class="w-[32%]">
                            <col class="w-[12%]">
                            <col class="w-[10%]">
                            <col class="w-[10%]">
                            <col class="w-[10%]">
                            <col class="w-[9%]">
                            <col class="w-[9%]">
                        </colgroup>

                        <thead class="{{ $esEpp
                            ? 'bg-orange-100 dark:bg-orange-900/30'
                            : 'bg-gray-50 dark:bg-gray-900/40' }}"
                        >
                            <tr>
                                <th class="px-3 py-3 text-center text-xs font-bold uppercase tracking-wide text-gray-500 dark:text-gray-300">
                                    #
                                </th>

                                <th class="px-3 py-3 text-left text-xs font-bold uppercase tracking-wide text-gray-500 dark:text-gray-300">
                                    Producto
                                </th>

                                <th class="px-3 py-3 text-left text-xs font-bold uppercase tracking-wide text-gray-500 dark:text-gray-300">
                                    Categoría
                                </th>

                                <th class="px-3 py-3 text-center text-xs font-bold uppercase tracking-wide text-gray-500 dark:text-gray-300">
                                    Unidad
                                </th>

                                <th class="px-3 py-3 text-center text-xs font-bold uppercase tracking-wide text-gray-500 dark:text-gray-300">
                                    Solicitado
                                </th>

                                <th class="px-3 py-3 text-center text-xs font-bold uppercase tracking-wide text-gray-500 dark:text-gray-300">
                                    Existencia
                                </th>

                                <th class="px-3 py-3 text-right text-xs font-bold uppercase tracking-wide text-gray-500 dark:text-gray-300">
                                    Precio
                                </th>

                                <th class="px-3 py-3 text-right text-xs font-bold uppercase tracking-wide text-gray-500 dark:text-gray-300">
                                    Subtotal
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($solicitud->detalles as $indice => $detalle)
                                @php
                                    $inventario = $detalle->inventario;

                                    $stockSuficiente = $inventario
                                        && $inventario->existencia
                                            >= $detalle->cantidad_solicitada;

                                    $subtotal = $detalle->cantidad_solicitada
                                        * ($detalle->precio_unitario ?? 0);
                                @endphp

                                <tr class="transition hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                    <td class="px-3 py-4 text-center align-middle">
                                        <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg
                                            {{ $esEpp
                                                ? 'bg-orange-100 text-orange-700 dark:bg-orange-900/40 dark:text-orange-300'
                                                : 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300' }}
                                            text-sm font-bold"
                                        >
                                            {{ $indice + 1 }}
                                        </span>
                                    </td>

                                    <td class="px-3 py-4 align-middle">
                                        @if($inventario)
                                            <p class="line-clamp-2 text-sm font-semibold leading-5 text-gray-900 dark:text-white">
                                                {{ $inventario->nombre_producto }}
                                            </p>

                                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                                {{ $inventario->economico
                                                    ?: 'Sin número económico' }}
                                            </p>
                                        @else
                                            <p class="font-semibold text-red-600 dark:text-red-400">
                                                Producto no disponible
                                            </p>

                                            <p class="text-xs text-red-500">
                                                El registro fue eliminado.
                                            </p>
                                        @endif
                                    </td>

                                    <td class="px-3 py-4 align-middle">
                                        <span class="text-xs font-semibold text-gray-600 dark:text-gray-300">
                                            {{ $inventario?->categoria ?? 'N/A' }}
                                        </span>
                                    </td>

                                    <td class="px-3 py-4 text-center align-middle text-sm text-gray-700 dark:text-gray-200">
                                        {{ $inventario?->medida ?? 'N/A' }}
                                    </td>

                                    <td class="px-3 py-4 text-center align-middle">
                                        <span class="text-lg font-bold text-blue-600 dark:text-blue-400">
                                            {{ $detalle->cantidad_solicitada }}
                                        </span>
                                    </td>

                                    <td class="px-3 py-4 text-center align-middle">
                                        @if($inventario)
                                            <span class="inline-flex min-w-12 justify-center rounded-full px-2 py-1 text-xs font-bold
                                                {{ $stockSuficiente
                                                    ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300'
                                                    : 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300' }}"
                                            >
                                                {{ $inventario->existencia }}
                                            </span>
                                        @else
                                            <span class="text-xs text-gray-400">
                                                N/A
                                            </span>
                                        @endif
                                    </td>

                                    <td class="px-3 py-4 text-right align-middle text-sm font-semibold text-gray-700 dark:text-gray-200">
                                        ${{ number_format(
                                            $detalle->precio_unitario ?? 0,
                                            2
                                        ) }}
                                    </td>

                                    <td class="px-3 py-4 text-right align-middle text-sm font-bold text-gray-900 dark:text-white">
                                        ${{ number_format($subtotal, 2) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="border-t border-gray-200 bg-gray-50 px-5 py-3 text-xs text-gray-500 dark:border-gray-700 dark:bg-gray-900/30 dark:text-gray-400">
                        La existencia mostrada es informativa y puede cambiar.
                        La aprobación no descuenta inventario.
                    </div>
                @else
                    <div class="px-6 py-16 text-center text-gray-500 dark:text-gray-400">
                        Esta solicitud no contiene productos.
                    </div>
                @endif
            </section>

            @if($solicitud->comentario)
                <section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <h2 class="font-bold text-gray-900 dark:text-white">
                        Comentario de la solicitud
                    </h2>

                    <p class="mt-3 whitespace-pre-line rounded-lg bg-gray-50 p-4 text-sm leading-6 text-gray-700 dark:bg-gray-700 dark:text-gray-200">{{ $solicitud->comentario }}</p>
                </section>
            @endif
        </main>

        <aside class="col-span-4 space-y-5">
            <section class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <div class="border-b border-gray-200 px-5 py-4 dark:border-gray-700">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white">
                        Datos de la solicitud
                    </h2>
                </div>

                <dl class="divide-y divide-gray-100 px-5 dark:divide-gray-700">
                    <div class="grid grid-cols-[145px_1fr] gap-4 py-4">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                            Tipo
                        </dt>

                        <dd class="text-sm font-bold
                            {{ $esEpp
                                ? 'text-orange-700 dark:text-orange-300'
                                : 'text-blue-700 dark:text-blue-300' }}"
                        >
                            {{ $esEpp
                                ? 'Equipo de protección personal'
                                : 'Solicitud estándar' }}
                        </dd>
                    </div>

                    <div class="grid grid-cols-[145px_1fr] gap-4 py-4">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                            Destino
                        </dt>

                        <dd class="text-sm font-semibold text-gray-900 dark:text-white">
                            {{ $solicitud->destino ?: 'No disponible' }}
                        </dd>
                    </div>

                    <div class="grid grid-cols-[145px_1fr] gap-4 py-4">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                            Personal
                        </dt>

                        <dd class="text-sm font-semibold text-gray-900 dark:text-white">
                            {{ $personalAsignado }}
                        </dd>
                    </div>

                    <div class="grid grid-cols-[145px_1fr] gap-4 py-4">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                            Creación
                        </dt>

                        <dd>
                            <p class="text-sm font-semibold text-gray-900 dark:text-white">
                                {{ $solicitud->created_at?->format(
                                    'd/m/Y H:i:s'
                                ) ?? 'N/A' }}
                            </p>

                            @if($solicitud->created_at)
                                <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">
                                    {{ $solicitud->created_at->diffForHumans() }}
                                </p>
                            @endif
                        </dd>
                    </div>

                    <div class="grid grid-cols-[145px_1fr] gap-4 py-4">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                            Actualización
                        </dt>

                        <dd>
                            <p class="text-sm font-semibold text-gray-900 dark:text-white">
                                {{ $solicitud->updated_at?->format(
                                    'd/m/Y H:i:s'
                                ) ?? 'N/A' }}
                            </p>

                            @if($solicitud->updated_at)
                                <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">
                                    {{ $solicitud->updated_at->diffForHumans() }}
                                </p>
                            @endif
                        </dd>
                    </div>
                </dl>
            </section>

            <section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <h2 class="mb-4 text-lg font-bold text-gray-900 dark:text-white">
                    Solicitante
                </h2>

                <div class="flex items-center gap-4">
                    <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-full
                        {{ $esEpp
                            ? 'bg-orange-500'
                            : 'bg-blue-600' }}"
                    >
                        <span class="text-xl font-bold text-white">
                            {{ mb_strtoupper(
                                mb_substr(
                                    $nombreSolicitante,
                                    0,
                                    1
                                )
                            ) }}
                        </span>
                    </div>

                    <div class="min-w-0">
                        <p class="truncate font-bold text-gray-900 dark:text-white">
                            {{ $nombreSolicitante }}
                        </p>

                        <p class="truncate text-sm text-gray-500 dark:text-gray-400">
                            {{ $correoSolicitante }}
                        </p>

                        <p class="mt-1 text-xs font-semibold uppercase tracking-wide text-gray-400">
                            {{ str_replace(
                                '_',
                                ' ',
                                $rolSolicitante
                            ) }}
                        </p>
                    </div>
                </div>
            </section>

            @if(
                $usuarioActual->canApproveRequests()
                && $solicitud->estatus === 'pendiente'
            )
                <section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white">
                        Decisión de Dirección
                    </h2>

                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Aprobar únicamente cambia el estatus. El inventario se
                        descontará cuando Almacén registre la salida.
                    </p>

                    <div class="mt-5 grid grid-cols-2 gap-3">
                        <form
                            method="POST"
                            action="{{ route(
                                'solicitudes.updateEstatus',
                                $solicitud
                            ) }}"
                        >
                            @csrf
                            @method('PUT')

                            <input
                                type="hidden"
                                name="estatus"
                                value="aprobado"
                            >

                            <button
                                type="submit"
                                class="inline-flex h-11 w-full items-center justify-center rounded-lg bg-green-600 px-4 font-bold text-white transition hover:bg-green-700"
                                onclick="return confirm('¿Aprobar esta solicitud? La aprobación no descontará inventario.')"
                            >
                                Aprobar
                            </button>
                        </form>

                        <form
                            method="POST"
                            action="{{ route(
                                'solicitudes.updateEstatus',
                                $solicitud
                            ) }}"
                        >
                            @csrf
                            @method('PUT')

                            <input
                                type="hidden"
                                name="estatus"
                                value="denegado"
                            >

                            <button
                                type="submit"
                                class="inline-flex h-11 w-full items-center justify-center rounded-lg bg-red-600 px-4 font-bold text-white transition hover:bg-red-700"
                                onclick="return confirm('¿Denegar esta solicitud?')"
                            >
                                Denegar
                            </button>
                        </form>
                    </div>
                </section>
            @endif
        </aside>
    </div>
</div>
@endsection