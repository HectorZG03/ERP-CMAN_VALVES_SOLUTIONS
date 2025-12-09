@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header con navegación -->
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <a href="{{ route('entradas.index') }}" 
               class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-md transition-colors duration-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Volver a Entradas
            </a>
            
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                Entrada #{{ $entrada->numero_factura ?? str_pad($entrada->id, 6, '0', STR_PAD_LEFT) }}
            </h1>
        </div>
        
        <!-- Botón Descargar PDF -->
        <a href="{{ route('entradas.pdf', $entrada) }}"
           class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 dark:bg-green-700 dark:hover:bg-green-800 text-white font-medium rounded-md transition-colors duration-200">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Descargar PDF
        </a>
    </div>

    <!-- Contenido principal -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Información principal -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Lista de Productos Recibidos -->
            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg transition-colors duration-200">
                <div class="px-4 py-5 sm:p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">
                            Productos Recibidos
                        </h3>
                        <span class="bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-300 text-sm font-medium px-3 py-1 rounded-full">
                            {{ $entrada->detalles->count() }} producto(s)
                        </span>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Producto
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Cantidad
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Precio Unitario
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Subtotal
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Total
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($entrada->detalles as $detalle)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0">
                                                <div class="h-10 w-10 bg-green-500 dark:bg-green-600 rounded-lg flex items-center justify-center">
                                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                                    </svg>
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                    {{ $detalle->inventario->nombre_producto ?? 'Producto no disponible' }}
                                                </div>
                                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                                    {{ $detalle->inventario->categoria ?? 'Sin categoría' }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-2 text-green-500 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l-4-4m0 0l4-4m-4 4h18"/>
                                            </svg>
                                            {{ $detalle->cantidad }}
                                            @if($detalle->inventario)
                                                {{ $detalle->inventario->medida }}
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                        ${{ number_format($detalle->precio_unitario, 2) }}
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                        ${{ number_format($detalle->precio_total, 2) }}
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm font-bold text-green-600 dark:text-green-400">
                                        ${{ number_format($detalle->total_con_iva, 2) }}
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                        No hay productos registrados para esta entrada
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                            <tfoot class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <td colspan="3" class="px-4 py-4 text-right text-sm font-medium text-gray-900 dark:text-white">
                                        Totales:
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm font-bold text-gray-900 dark:text-white">
                                        ${{ number_format($entrada->precio_total, 2) }}
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm font-bold text-green-600 dark:text-green-400">
                                        ${{ number_format($entrada->total_con_iva, 2) }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Información del Proveedor -->
            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg transition-colors duration-200">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">
                        Información del Proveedor
                    </h3>
                    
                    <div class="bg-blue-50 dark:bg-blue-900/30 rounded-lg p-6 transition-colors duration-200">
                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0">
                                <div class="w-16 h-16 bg-blue-500 dark:bg-blue-600 rounded-lg flex items-center justify-center">
                                    @if($entrada->proveedor)
                                        <span class="text-2xl font-bold text-white">
                                            {{ substr($entrada->proveedor->proveedor, 0, 1) }}
                                        </span>
                                    @else
                                        <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2H4zm0 2h12v8H4V6z" clip-rule="evenodd"/>
                                        </svg>
                                    @endif
                                </div>
                            </div>
                            <div class="flex-1">
                                <h4 class="text-xl font-semibold text-gray-900 dark:text-white">
                                    @if($entrada->proveedor)
                                        {{ $entrada->proveedor->proveedor }}
                                    @else
                                        Proveedor no disponible
                                    @endif
                                </h4>
                                <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">
                                    @if($entrada->proveedor && $entrada->proveedor->categoria)
                                        {{ $entrada->proveedor->categoria }}
                                    @else
                                        Información no disponible
                                    @endif
                                </p>
                                
                                @if($entrada->proveedor)
                                <div class="mt-3">
                                    <div class="grid grid-cols-1 gap-2">
                                        @if($entrada->proveedor->contacto)
                                        <div class="flex items-center text-sm text-gray-600 dark:text-gray-300">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                            </svg>
                                            Contacto: {{ $entrada->proveedor->contacto }}
                                        </div>
                                        @endif
                                        @if($entrada->proveedor->telefono)
                                        <div class="flex items-center text-sm text-gray-600 dark:text-gray-300">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                            </svg>
                                            {{ $entrada->proveedor->telefono }}
                                        </div>
                                        @endif
                                        @if($entrada->proveedor->email)
                                        <div class="flex items-center text-sm text-gray-600 dark:text-gray-300">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                            </svg>
                                            {{ $entrada->proveedor->email }}
                                        </div>
                                        @endif
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Resumen de la Entrada -->
            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg transition-colors duration-200">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">
                        Resumen de la Entrada
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Información de la transacción -->
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6 transition-colors duration-200">
                            <dl class="space-y-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Número de Factura</dt>
                                    <dd class="text-lg font-semibold text-gray-900 dark:text-white">
                                        {{ $entrada->numero_factura ?? 'N/A' }}
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Fecha de Entrada</dt>
                                    <dd class="text-sm text-gray-900 dark:text-white">
                                        @if($entrada->fecha_entrada)
                                            {{ $entrada->fecha_entrada->format('d/m/Y') }}
                                        @else
                                            N/A
                                        @endif
                                    </dd>
                                </div>
                                @if($entrada->observaciones)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Observaciones</dt>
                                    <dd class="text-sm text-gray-700 dark:text-gray-300">
                                        {{ $entrada->observaciones }}
                                    </dd>
                                </div>
                                @endif
                            </dl>
                        </div>

                        <!-- Detalles Financieros -->
                        <div class="bg-green-50 dark:bg-green-900/30 rounded-lg p-6 transition-colors duration-200">
                            <div class="space-y-4">
                                <div class="flex justify-between items-center pb-3 border-b border-green-200 dark:border-green-700">
                                    <span class="text-sm font-medium text-gray-600 dark:text-gray-300">Total Productos:</span>
                                    <span class="text-lg font-semibold text-gray-900 dark:text-white">
                                        {{ $entrada->detalles->sum('cantidad') }} unidades
                                    </span>
                                </div>
                                
                                <div class="flex justify-between items-center pb-3 border-b border-green-200 dark:border-green-700">
                                    <span class="text-sm font-medium text-gray-600 dark:text-gray-300">Subtotal:</span>
                                    <span class="text-lg font-semibold text-gray-900 dark:text-white">
                                        ${{ number_format($entrada->precio_total, 2) }}
                                    </span>
                                </div>
                                
                                <div class="flex justify-between items-center pb-3 border-b border-green-200 dark:border-green-700">
                                    <span class="text-sm font-medium text-gray-600 dark:text-gray-300">IVA (16%):</span>
                                    <span class="text-lg font-semibold text-gray-700 dark:text-gray-200">
                                        ${{ number_format($entrada->iva, 2) }}
                                    </span>
                                </div>
                                
                                <div class="flex justify-between items-center pt-2">
                                    <span class="text-base font-bold text-gray-900 dark:text-white">Total Pagado:</span>
                                    <span class="text-2xl font-bold text-green-600 dark:text-green-400">
                                        ${{ number_format($entrada->total_con_iva, 2) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Información lateral -->
        <div class="space-y-6">
            
            <!-- Información del Usuario -->
            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg transition-colors duration-200">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">
                        Registrado por
                    </h3>
                    
                    <div class="text-center">
                        @if($entrada->user)
                            <div class="mx-auto h-20 w-20 rounded-full bg-green-500 dark:bg-green-600 flex items-center justify-center">
                                <span class="text-2xl font-medium text-white">
                                    {{ substr($entrada->user->name, 0, 1) }}
                                </span>
                            </div>
                            <h4 class="mt-3 text-lg font-medium text-gray-900 dark:text-white">
                                {{ $entrada->user->name }}
                            </h4>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                {{ $entrada->user->email }}
                            </p>
                            @if($entrada->user->role)
                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                                Rol: {{ ucfirst($entrada->user->role) }}
                            </p>
                            @endif
                        @else
                            <div class="mx-auto h-20 w-20 rounded-full bg-gray-400 dark:bg-gray-600 flex items-center justify-center">
                                <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <h4 class="mt-3 text-lg font-medium text-gray-900 dark:text-white">Usuario No Encontrado</h4>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Información del Sistema -->
            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg transition-colors duration-200">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">
                        Información del Sistema
                    </h3>
                    
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Registrado</dt>
                            <dd class="text-sm text-gray-900 dark:text-white font-medium">
                                @if($entrada->created_at)
                                    {{ $entrada->created_at->format('d/m/Y H:i') }}
                                @else
                                    N/A
                                @endif
                            </dd>
                            @if($entrada->created_at)
                                <dd class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $entrada->created_at->diffForHumans() }}
                                </dd>
                            @endif
                        </div>
                        
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Actualizado</dt>
                            <dd class="text-sm text-gray-900 dark:text-white font-medium">
                                @if($entrada->updated_at)
                                    {{ $entrada->updated_at->format('d/m/Y H:i') }}
                                @else
                                    N/A
                                @endif
                            </dd>
                            @if($entrada->updated_at)
                                <dd class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $entrada->updated_at->diffForHumans() }}
                                </dd>
                            @endif
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Impacto en Inventario -->
            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg transition-colors duration-200">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">
                        Impacto en Inventario
                    </h3>
                    
                    <div class="bg-green-50 dark:bg-green-900/30 rounded-lg p-4 transition-colors duration-200">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="w-8 h-8 text-green-500 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l-4-4m0 0l4-4m-4 4h18"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-green-800 dark:text-green-300">
                                    Inventario Incrementado
                                </p>
                                <p class="text-xs text-green-600 dark:text-green-400">
                                    +{{ $entrada->detalles->sum('cantidad') }} unidades agregadas al stock
                                </p>
                            </div>
                        </div>
                    </div>

                    @if($entrada->detalles->count() > 0)
                    <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-600">
                        <div class="text-center">
                            <div class="text-xs text-gray-400 dark:text-gray-500 space-y-1">
                                <p>Productos actualizados en inventario</p>
                                <p class="text-lg font-bold text-green-600 dark:text-green-400">
                                    {{ $entrada->detalles->count() }}
                                </p>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Acciones -->
            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg transition-colors duration-200">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">
                        Acciones
                    </h3>
                    
                    <div class="space-y-3">
                        <a href="{{ route('entradas.index') }}" 
                           class="w-full inline-flex justify-center items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 dark:bg-gray-700 dark:hover:bg-gray-600 text-white font-medium rounded-md transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            Ver Todas las Entradas
                        </a>
                        
                        <a href="{{ route('entradas.pdf', $entrada) }}"
                           class="w-full inline-flex justify-center items-center px-4 py-2 bg-green-600 hover:bg-green-700 dark:bg-green-700 dark:hover:bg-green-800 text-white font-medium rounded-md transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Descargar Factura PDF
                        </a>
                        
                        <a href="{{ route('entradas.create') }}" 
                           class="w-full inline-flex justify-center items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 dark:bg-blue-700 dark:hover:bg-blue-800 text-white font-medium rounded-md transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Nueva Entrada
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection