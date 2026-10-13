@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-7xl space-y-6">

    <header class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <a href="{{ route('entradas.index') }}"
               class="text-sm font-medium text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                ← Volver a entradas
            </a>

            <p class="mt-4 text-sm font-semibold uppercase tracking-wide text-blue-600 dark:text-blue-400">
                Inventario / Entradas
            </p>

            <h1 class="mt-1 text-2xl font-bold text-gray-900 dark:text-white sm:text-3xl">
                Entrada {{ $entrada->numero_factura ?? '#' . str_pad($entrada->id, 6, '0', STR_PAD_LEFT) }}
            </h1>

            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                Registrada el
                {{ $entrada->created_at ? $entrada->created_at->format('d/m/Y H:i') : 'N/A' }}
            </p>
        </div>

        <div class="flex flex-wrap gap-2">
            <a href="{{ route('entradas.create') }}"
               class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">
                Nueva entrada
            </a>

            <a href="{{ route('entradas.pdf', $entrada) }}"
               class="inline-flex items-center rounded-lg bg-blue-700 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-800">
                Descargar PDF
            </a>
        </div>
    </header>

    @if(session('success'))
        <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800 dark:border-green-800 dark:bg-green-900/20 dark:text-green-300">
            {{ session('success') }}
        </div>
    @endif

    <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <p class="text-sm text-gray-500 dark:text-gray-400">Productos registrados</p>
            <p class="mt-2 text-2xl font-bold text-gray-900 dark:text-white">
                {{ $entrada->detalles->count() }}
            </p>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <p class="text-sm text-gray-500 dark:text-gray-400">Unidades recibidas</p>
            <p class="mt-2 text-2xl font-bold text-gray-900 dark:text-white">
                {{ number_format($entrada->detalles->sum('cantidad')) }}
            </p>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <p class="text-sm text-gray-500 dark:text-gray-400">Descuento global</p>
            <p class="mt-2 text-2xl font-bold text-amber-600 dark:text-amber-400">
                − ${{ number_format((float) ($entrada->descuento_global ?? 0), 2) }}
            </p>
        </div>

        <div class="rounded-xl border border-green-200 bg-green-50 p-5 shadow-sm dark:border-green-900 dark:bg-green-900/20">
            <p class="text-sm text-green-700 dark:text-green-300">Total de entrada</p>
            <p class="mt-2 text-2xl font-bold text-green-800 dark:text-green-200">
                ${{ number_format($entrada->total_con_iva, 2) }}
            </p>
        </div>
    </section>

    <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <div class="flex flex-col gap-2 border-b border-gray-200 px-5 py-4 dark:border-gray-700 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="font-semibold text-gray-900 dark:text-white">Materiales recibidos</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Los subtotales por producto conservan el precio de cada línea.
                </p>
            </div>

            <span class="w-fit rounded-full bg-blue-100 px-3 py-1 text-sm font-medium text-blue-800 dark:bg-blue-900/40 dark:text-blue-300">
                {{ $entrada->detalles->count() }} producto(s)
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700/70">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-300">Producto</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-300">Cantidad</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-300">Precio unitario</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-300">Subtotal</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800">
                    @forelse($entrada->detalles as $detalle)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/40">
                            <td class="px-5 py-4">
                                <p class="font-medium text-gray-900 dark:text-white">
                                    {{ $detalle->inventario->nombre_producto ?? 'Producto no disponible' }}
                                </p>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                    {{ $detalle->inventario->categoria ?? 'Sin categoría' }}
                                </p>
                            </td>

                            <td class="whitespace-nowrap px-5 py-4 text-right text-sm text-gray-700 dark:text-gray-300">
                                {{ number_format($detalle->cantidad) }}
                                {{ $detalle->inventario->medida ?? '' }}
                            </td>

                            <td class="whitespace-nowrap px-5 py-4 text-right text-sm text-gray-700 dark:text-gray-300">
                                ${{ number_format($detalle->precio_unitario, 2) }}
                            </td>

                            <td class="whitespace-nowrap px-5 py-4 text-right text-sm font-semibold text-gray-900 dark:text-white">
                                ${{ number_format($detalle->precio_total, 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-5 py-10 text-center text-sm text-gray-500 dark:text-gray-400">
                                No hay materiales registrados para esta entrada.
                            </td>
                        </tr>
                    @endforelse
                </tbody>

                @if($entrada->detalles->isNotEmpty())
                    <tfoot class="bg-gray-50 dark:bg-gray-700/70">
                        <tr>
                            <td colspan="3" class="px-5 py-3 text-right text-sm font-semibold text-gray-700 dark:text-gray-200">
                                Subtotal de materiales
                            </td>
                            <td class="whitespace-nowrap px-5 py-3 text-right text-sm font-bold text-gray-900 dark:text-white">
                                ${{ number_format($entrada->precio_total, 2) }}
                            </td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </section>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800 lg:col-span-2">
            <h2 class="font-semibold text-gray-900 dark:text-white">Datos de la entrada</h2>

            <dl class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <dt class="text-sm text-gray-500 dark:text-gray-400">Proveedor</dt>
                    <dd class="mt-1 font-medium text-gray-900 dark:text-white">
                        {{ $entrada->proveedor->proveedor ?? 'Proveedor no disponible' }}
                    </dd>
                </div>

                <div>
                    <dt class="text-sm text-gray-500 dark:text-gray-400">Fecha de entrada</dt>
                    <dd class="mt-1 font-medium text-gray-900 dark:text-white">
                        {{ $entrada->fecha_entrada ? $entrada->fecha_entrada->format('d/m/Y') : 'N/A' }}
                    </dd>
                </div>

                @if($entrada->proveedor && $entrada->proveedor->contacto)
                    <div>
                        <dt class="text-sm text-gray-500 dark:text-gray-400">Contacto</dt>
                        <dd class="mt-1 font-medium text-gray-900 dark:text-white">
                            {{ $entrada->proveedor->contacto }}
                        </dd>
                    </div>
                @endif

                @if($entrada->proveedor && $entrada->proveedor->telefono)
                    <div>
                        <dt class="text-sm text-gray-500 dark:text-gray-400">Teléfono</dt>
                        <dd class="mt-1 font-medium text-gray-900 dark:text-white">
                            {{ $entrada->proveedor->telefono }}
                        </dd>
                    </div>
                @endif

                @if($entrada->observaciones)
                    <div class="sm:col-span-2">
                        <dt class="text-sm text-gray-500 dark:text-gray-400">Observaciones</dt>
                        <dd class="mt-1 whitespace-pre-line text-gray-800 dark:text-gray-200">
                            {{ $entrada->observaciones }}
                        </dd>
                    </div>
                @endif
            </dl>
        </section>

        <section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <h2 class="font-semibold text-gray-900 dark:text-white">Resumen financiero</h2>

            <dl class="mt-4 space-y-3">
                <div class="flex justify-between gap-4 text-sm">
                    <dt class="text-gray-600 dark:text-gray-400">Subtotal</dt>
                    <dd class="font-medium text-gray-900 dark:text-white">
                        ${{ number_format($entrada->precio_total, 2) }}
                    </dd>
                </div>

                <div class="flex justify-between gap-4 text-sm">
                    <dt class="text-gray-600 dark:text-gray-400">Descuento global</dt>
                    <dd class="font-medium text-amber-700 dark:text-amber-400">
                        − ${{ number_format((float) ($entrada->descuento_global ?? 0), 2) }}
                    </dd>
                </div>

                <div class="flex justify-between gap-4 border-t border-gray-200 pt-3 text-sm dark:border-gray-700">
                    <dt class="text-gray-600 dark:text-gray-400">Base con descuento</dt>
                    <dd class="font-semibold text-gray-900 dark:text-white">
                        ${{ number_format($entrada->subtotal_con_descuento, 2) }}
                    </dd>
                </div>

                <div class="flex justify-between gap-4 text-sm">
                    <dt class="text-gray-600 dark:text-gray-400">IVA (16%)</dt>
                    <dd class="font-medium text-gray-900 dark:text-white">
                        ${{ number_format($entrada->iva, 2) }}
                    </dd>
                </div>

                <div class="flex justify-between gap-4 rounded-lg bg-green-700 px-4 py-3 text-white dark:bg-green-800">
                    <dt class="font-semibold">Total</dt>
                    <dd class="text-lg font-bold">
                        ${{ number_format($entrada->total_con_iva, 2) }}
                    </dd>
                </div>
            </dl>
        </section>
    </div>

    <section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="font-semibold text-gray-900 dark:text-white">Registro del sistema</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Capturó:
                    <span class="font-medium text-gray-700 dark:text-gray-200">
                        {{ $entrada->user->name ?? 'Usuario no disponible' }}
                    </span>
                </p>
            </div>

            <div class="text-sm text-gray-500 dark:text-gray-400">
                @if($entrada->created_at)
                    Registrado {{ $entrada->created_at->diffForHumans() }}
                @endif
            </div>
        </div>
    </section>
</div>
@endsection