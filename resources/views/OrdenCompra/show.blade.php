@extends('layouts.app')

@section('content')

<div class="space-y-6">

    {{-- HEADER --}}
    <div class="flex items-center space-x-4">

        <a href="{{ route('orden-compra.index') }}"
           class="inline-flex items-center px-4 py-2
                  bg-gray-100 hover:bg-gray-200
                  dark:bg-gray-700 dark:hover:bg-gray-600
                  text-gray-700 dark:text-gray-300
                  text-sm font-medium rounded-md
                  transition-colors duration-200">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Volver a Órdenes
        </a>

        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
            Orden de Compra #{{ $ordenCompra->folio ?? str_pad($ordenCompra->id, 6, '0', STR_PAD_LEFT) }}
        </h1>

        {{-- BOTONES DERECHA --}}
        <div class="ml-auto flex items-center space-x-3">

            {{-- Botón Editar --}}
            <a href="{{ route('orden-compra.edit', $ordenCompra->id) }}"
               class="inline-flex items-center px-4 py-2 text-sm font-medium text-white
                      bg-yellow-500 hover:bg-yellow-600 rounded-md transition-colors">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Editar
            </a>

            {{-- Botón PDF --}}
            <a href="{{ route('orden-compra.pdf', $ordenCompra) }}"
               target="_blank"
               class="inline-flex items-center px-4 py-2 text-sm font-medium text-white
                      bg-red-600 hover:bg-red-700 rounded-md transition-colors">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
                Descargar PDF
            </a>

        </div>

    </div>

    {{-- ALERTAS --}}
    @if(session('success'))
        <div class="bg-green-50 dark:bg-green-900/50 border border-green-200 dark:border-green-800
                    text-green-800 dark:text-green-200 px-4 py-3 rounded-md">
            {{ session('success') }}
        </div>
    @endif

    {{-- RESUMEN SUPERIOR --}}
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50
                dark:from-blue-900/20 dark:to-indigo-900/20
                rounded-lg p-6
                border border-blue-200 dark:border-blue-800
                transition-colors duration-200">

        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
            Resumen de la Orden
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

            <div class="text-center">
                <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                    {{ $ordenCompra->detalles->count() }}
                </div>
                <div class="text-sm text-gray-600 dark:text-gray-400">Artículos</div>
            </div>

            <div class="text-center">
                <div class="text-2xl font-bold text-green-600 dark:text-green-400">
                    ${{ number_format($ordenCompra->subtotal, 2) }}
                </div>
                <div class="text-sm text-gray-600 dark:text-gray-400">Subtotal</div>
            </div>

            <div class="text-center">
                <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">
                    ${{ number_format($ordenCompra->iva, 2) }}
                </div>
                <div class="text-sm text-gray-600 dark:text-gray-400">IVA (16%)</div>
            </div>

            <div class="text-center">
                <div class="text-3xl font-bold text-purple-600 dark:text-purple-400">
                    ${{ number_format($ordenCompra->total_general, 2) }}
                </div>
                <div class="text-sm text-gray-600 dark:text-gray-400">Total General</div>
            </div>

        </div>

    </div>

    {{-- CONTENIDO PRINCIPAL --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- COLUMNA IZQUIERDA (artículos + comentarios) --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- ARTÍCULOS --}}
            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg transition-colors duration-200">
                <div class="px-4 py-5 sm:p-6">

                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">
                        Artículos de la Orden
                    </h3>

                    @if($ordenCompra->detalles && $ordenCompra->detalles->count() > 0)

                        <div class="space-y-4">
                            @foreach($ordenCompra->detalles as $index => $detalle)
                                <div class="bg-gray-50 dark:bg-gray-700
                                            rounded-lg p-4
                                            border border-gray-200 dark:border-gray-600
                                            hover:border-gray-300 dark:hover:border-gray-500
                                            transition-colors duration-200">

                                    <div class="flex items-start space-x-4">

                                        {{-- Número --}}
                                        <div class="flex-shrink-0">
                                            <div class="w-12 h-12 bg-blue-500 dark:bg-blue-600
                                                        rounded-lg flex items-center justify-center">
                                                <span class="text-white font-medium text-sm">
                                                    {{ $index + 1 }}
                                                </span>
                                            </div>
                                        </div>

                                        {{-- Datos --}}
                                        <div class="flex-1">

                                            <h4 class="text-lg font-semibold text-gray-900 dark:text-white">
                                                {{ $detalle->descripcion }}
                                            </h4>

                                            <div class="mt-3 grid grid-cols-2 md:grid-cols-5 gap-4">

                                                <div>
                                                    <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                                        Código
                                                    </dt>
                                                    <dd class="text-sm font-medium text-gray-900 dark:text-white">
                                                        {{ $detalle->codigo ?? 'N/A' }}
                                                    </dd>
                                                </div>

                                                <div>
                                                    <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                                        Cantidad
                                                    </dt>
                                                    <dd class="text-lg font-bold text-blue-600 dark:text-blue-400">
                                                        {{ $detalle->cantidad }}
                                                    </dd>
                                                </div>

                                                <div>
                                                    <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                                        Unidad
                                                    </dt>
                                                    <dd class="text-sm font-medium text-gray-900 dark:text-white">
                                                        {{ $detalle->unidad }}
                                                    </dd>
                                                </div>

                                                <div>
                                                    <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                                        Precio Unitario
                                                    </dt>
                                                    <dd class="text-sm font-medium text-gray-900 dark:text-white">
                                                        ${{ number_format($detalle->precio_unitario, 2) }}
                                                    </dd>
                                                </div>

                                                <div>
                                                    <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                                        Total
                                                    </dt>
                                                    <dd class="text-lg font-bold text-green-600 dark:text-green-400">
                                                        ${{ number_format($detalle->total, 2) }}
                                                    </dd>
                                                </div>

                                            </div>

                                        </div>

                                    </div>

                                </div>
                            @endforeach
                        </div>

                    @else

                        <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                            <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500 mb-4"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                            <p class="font-medium">No hay artículos registrados</p>
                        </div>

                    @endif

                </div>
            </div>

            {{-- COMENTARIOS --}}
            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg transition-colors duration-200">
                <div class="px-4 py-5 sm:p-6">

                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">
                        Comentarios y Observaciones
                    </h3>

                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 transition-colors duration-200">
                        <p class="text-gray-700 dark:text-gray-300 whitespace-pre-line">
                            {{ $ordenCompra->comentarios ?? 'Sin comentarios registrados.' }}
                        </p>
                    </div>

                </div>
            </div>

        </div>

        {{-- COLUMNA DERECHA --}}
        <div class="space-y-6">

            {{-- PROVEEDOR --}}
            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg transition-colors duration-200">
                <div class="px-4 py-5 sm:p-6">

                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">
                        Información del Proveedor
                    </h3>

                    <div class="text-center mb-4">
                        <div class="mx-auto h-16 w-16 rounded-full bg-blue-500 dark:bg-blue-600
                                    flex items-center justify-center">
                            <span class="text-2xl font-medium text-white">
                                {{ strtoupper(substr($ordenCompra->nombre_proveedor, 0, 1)) }}
                            </span>
                        </div>
                        <h4 class="mt-3 text-lg font-medium text-gray-900 dark:text-white">
                            {{ $ordenCompra->nombre_proveedor }}
                        </h4>
                    </div>

                    <dl class="space-y-2 text-sm">

                        @if($ordenCompra->razon_social_proveedor)
                        <div class="flex justify-between">
                            <dt class="text-gray-500 dark:text-gray-400">Razón Social</dt>
                            <dd class="font-medium text-gray-900 dark:text-white text-right">
                                {{ $ordenCompra->razon_social_proveedor }}
                            </dd>
                        </div>
                        @endif

                        @if($ordenCompra->rfc_proveedor)
                        <div class="flex justify-between">
                            <dt class="text-gray-500 dark:text-gray-400">RFC</dt>
                            <dd class="font-medium text-gray-900 dark:text-white">
                                {{ $ordenCompra->rfc_proveedor }}
                            </dd>
                        </div>
                        @endif

                        @if($ordenCompra->telefono_proveedor)
                        <div class="flex justify-between">
                            <dt class="text-gray-500 dark:text-gray-400">Teléfono</dt>
                            <dd class="font-medium text-gray-900 dark:text-white">
                                {{ $ordenCompra->telefono_proveedor }}
                            </dd>
                        </div>
                        @endif

                        @if($ordenCompra->email_proveedor)
                        <div class="flex justify-between">
                            <dt class="text-gray-500 dark:text-gray-400">Correo</dt>
                            <dd class="font-medium text-gray-900 dark:text-white break-all">
                                {{ $ordenCompra->email_proveedor }}
                            </dd>
                        </div>
                        @endif

                        @if($ordenCompra->direccion_proveedor)
                        <div class="flex justify-between">
                            <dt class="text-gray-500 dark:text-gray-400">Dirección</dt>
                            <dd class="font-medium text-gray-900 dark:text-white text-right">
                                {{ $ordenCompra->direccion_proveedor }}
                            </dd>
                        </div>
                        @endif

                    </dl>

                </div>
            </div>

            {{-- TOTALES --}}
            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg transition-colors duration-200">
                <div class="px-4 py-5 sm:p-6">

                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">
                        Desglose de Totales
                    </h3>

                    <dl class="space-y-3">

                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500 dark:text-gray-400">Subtotal</dt>
                            <dd class="text-sm font-medium text-gray-900 dark:text-white">
                                ${{ number_format($ordenCompra->subtotal, 2) }}
                            </dd>
                        </div>

                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500 dark:text-gray-400">IVA (16%)</dt>
                            <dd class="text-sm font-medium text-gray-900 dark:text-white">
                                ${{ number_format($ordenCompra->iva, 2) }}
                            </dd>
                        </div>

                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500 dark:text-gray-400">Envío</dt>
                            <dd class="text-sm font-medium text-gray-900 dark:text-white">
                                ${{ number_format($ordenCompra->envio, 2) }}
                            </dd>
                        </div>

                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500 dark:text-gray-400">Otros</dt>
                            <dd class="text-sm font-medium text-gray-900 dark:text-white">
                                ${{ number_format($ordenCompra->otros, 2) }}
                            </dd>
                        </div>

                        <div class="border-t border-gray-200 dark:border-gray-600 pt-3 flex justify-between">
                            <dt class="text-lg font-bold text-gray-900 dark:text-white">Total General</dt>
                            <dd class="text-lg font-bold text-green-600 dark:text-green-400">
                                ${{ number_format($ordenCompra->total_general, 2) }}
                            </dd>
                        </div>

                    </dl>

                </div>
            </div>

            {{-- INFORMACIÓN ADICIONAL --}}
            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg transition-colors duration-200">
                <div class="px-4 py-5 sm:p-6">

                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">
                        Información de Registro
                    </h3>

                    <dl class="space-y-3">

                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Creado por</dt>
                            <dd class="text-sm text-gray-900 dark:text-white font-medium">
                                {{ $ordenCompra->user->name ?? 'N/A' }}
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Fecha de Registro</dt>
                            <dd class="text-sm text-gray-900 dark:text-white font-medium">
                                {{ $ordenCompra->created_at->format('d/m/Y H:i:s') }}
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Última Actualización</dt>
                            <dd class="text-sm text-gray-900 dark:text-white font-medium">
                                {{ $ordenCompra->updated_at->format('d/m/Y H:i:s') }}
                            </dd>
                        </div>

                    </dl>

                </div>
            </div>

            {{-- FOLIO --}}
            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg transition-colors duration-200">
                <div class="px-4 py-5 sm:p-6">

                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">
                        Seguimiento
                    </h3>

                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3 transition-colors duration-200">
                        <div class="text-center">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Folio de Orden</p>
                            <p class="text-2xl font-mono font-bold text-gray-900 dark:text-white">
                                {{ $ordenCompra->folio ?? '#' . str_pad($ordenCompra->id, 6, '0', STR_PAD_LEFT) }}
                            </p>
                        </div>
                    </div>

                </div>
            </div>

        </div>

    </div>

</div>

@endsection