@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Historial de ajustes</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Registro de correcciones de stock y revaluaciones del inventario.</p>
        </div>
        <a href="{{ route('inventario.index') }}"
           class="inline-flex self-start rounded-md bg-gray-600 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-gray-700">
            Volver al inventario
        </a>
    </div>

    <div class="rounded-lg border border-gray-200 bg-white p-5 shadow dark:border-gray-700 dark:bg-gray-800">
        <form method="GET" action="{{ route('inventario.ajustes.index') }}" class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-6">
            <div class="xl:col-span-2">
                <label for="search" class="mb-1 block text-xs font-medium uppercase tracking-wide text-gray-600 dark:text-gray-400">Buscar</label>
                <input type="text" name="search" id="search" value="{{ $filters['search'] ?? '' }}"
                       placeholder="Producto, económico o motivo"
                       class="block w-full rounded-md border border-gray-300 bg-white text-sm text-gray-900 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
            </div>
            <div>
                <label for="inventario_id" class="mb-1 block text-xs font-medium uppercase tracking-wide text-gray-600 dark:text-gray-400">Producto</label>
                <select name="inventario_id" id="inventario_id" class="block w-full rounded-md border border-gray-300 bg-white text-sm text-gray-900 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    <option value="">Todos</option>
                    @foreach($productos as $producto)
                        <option value="{{ $producto->id }}" {{ (string) ($filters['inventario_id'] ?? '') === (string) $producto->id ? 'selected' : '' }}>
                            {{ $producto->nombre_producto }} ({{ $producto->economico }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="tipo" class="mb-1 block text-xs font-medium uppercase tracking-wide text-gray-600 dark:text-gray-400">Tipo</label>
                <select name="tipo" id="tipo" class="block w-full rounded-md border border-gray-300 bg-white text-sm text-gray-900 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    <option value="">Todos</option>
                    <option value="incremento" {{ ($filters['tipo'] ?? '') === 'incremento' ? 'selected' : '' }}>Incremento</option>
                    <option value="disminucion" {{ ($filters['tipo'] ?? '') === 'disminucion' ? 'selected' : '' }}>Disminución</option>
                    <option value="revaluacion" {{ ($filters['tipo'] ?? '') === 'revaluacion' ? 'selected' : '' }}>Revaluación</option>
                </select>
            </div>
            <div>
                <label for="user_id" class="mb-1 block text-xs font-medium uppercase tracking-wide text-gray-600 dark:text-gray-400">Usuario</label>
                <select name="user_id" id="user_id" class="block w-full rounded-md border border-gray-300 bg-white text-sm text-gray-900 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    <option value="">Todos</option>
                    @foreach($usuarios as $usuario)
                        <option value="{{ $usuario->id }}" {{ (string) ($filters['user_id'] ?? '') === (string) $usuario->id ? 'selected' : '' }}>{{ $usuario->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">Filtrar</button>
                <a href="{{ route('inventario.ajustes.index') }}" class="rounded-md bg-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-300 dark:bg-gray-600 dark:text-gray-200">Limpiar</a>
            </div>
            <div>
                <label for="fecha_desde" class="mb-1 block text-xs font-medium uppercase tracking-wide text-gray-600 dark:text-gray-400">Desde</label>
                <input type="date" name="fecha_desde" id="fecha_desde" value="{{ $filters['fecha_desde'] ?? '' }}"
                       class="block w-full rounded-md border border-gray-300 bg-white text-sm text-gray-900 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
            </div>
            <div>
                <label for="fecha_hasta" class="mb-1 block text-xs font-medium uppercase tracking-wide text-gray-600 dark:text-gray-400">Hasta</label>
                <input type="date" name="fecha_hasta" id="fecha_hasta" value="{{ $filters['fecha_hasta'] ?? '' }}"
                       class="block w-full rounded-md border border-gray-300 bg-white text-sm text-gray-900 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
            </div>
        </form>
    </div>

    <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow dark:border-gray-700 dark:bg-gray-800">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-600 dark:text-gray-300">Fecha / usuario</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-600 dark:text-gray-300">Producto</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-600 dark:text-gray-300">Tipo</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-600 dark:text-gray-300">Existencia</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-600 dark:text-gray-300">Costo promedio</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-600 dark:text-gray-300">Valor total</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-600 dark:text-gray-300">Motivo</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($ajustes as $ajuste)
                        @php
                            $tipoClases = match($ajuste->tipo) {
                                'incremento' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
                                'disminucion' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300',
                                default => 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300',
                            };
                        @endphp
                        <tr class="align-top hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <td class="whitespace-nowrap px-4 py-4 text-sm text-gray-700 dark:text-gray-300">
                                <div class="font-medium">{{ $ajuste->created_at->format('d/m/Y H:i') }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $ajuste->usuario_nombre }}</div>
                            </td>
                            <td class="px-4 py-4 text-sm text-gray-700 dark:text-gray-300">
                                <div class="font-medium text-gray-900 dark:text-white">{{ $ajuste->producto }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $ajuste->economico ?: 'Sin económico' }}</div>
                                @if($ajuste->inventario && auth()->user()->canManageInventory())
                                    <a href="{{ route('inventario.edit', $ajuste->inventario) }}" class="mt-1 inline-block text-xs text-blue-600 hover:underline dark:text-blue-400">Abrir producto</a>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-4 py-4 text-sm">
                                <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $tipoClases }}">{{ ucfirst($ajuste->tipo) }}</span>
                            </td>
                            <td class="whitespace-nowrap px-4 py-4 text-sm text-gray-700 dark:text-gray-300">
                                <div>{{ $ajuste->existencia_anterior }} → {{ $ajuste->existencia_nueva }}</div>
                                <div class="text-xs font-semibold {{ $ajuste->diferencia > 0 ? 'text-green-600' : ($ajuste->diferencia < 0 ? 'text-red-600' : 'text-gray-500') }}">
                                    {{ $ajuste->diferencia > 0 ? '+' : '' }}{{ $ajuste->diferencia }}
                                </div>
                            </td>
                            <td class="whitespace-nowrap px-4 py-4 text-sm text-gray-700 dark:text-gray-300">
                                <div>${{ number_format((float) $ajuste->costo_promedio_anterior, 2) }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">→ ${{ number_format((float) $ajuste->costo_promedio_nuevo, 2) }}</div>
                            </td>
                            <td class="whitespace-nowrap px-4 py-4 text-sm text-gray-700 dark:text-gray-300">
                                <div>${{ number_format((float) $ajuste->valor_total_anterior, 2) }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">→ ${{ number_format((float) $ajuste->valor_total_nuevo, 2) }}</div>
                                <div class="text-xs font-semibold {{ $ajuste->diferencia_valor > 0 ? 'text-green-600' : ($ajuste->diferencia_valor < 0 ? 'text-red-600' : 'text-gray-500') }}">
                                    {{ $ajuste->diferencia_valor > 0 ? '+' : '' }}${{ number_format((float) $ajuste->diferencia_valor, 2) }}
                                </div>
                            </td>
                            <td class="max-w-sm px-4 py-4 text-sm text-gray-700 dark:text-gray-300">{{ $ajuste->motivo }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-sm text-gray-500 dark:text-gray-400">No se encontraron ajustes con los filtros seleccionados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($ajustes->hasPages())
            <div class="border-t border-gray-200 px-4 py-4 dark:border-gray-700">{{ $ajustes->links() }}</div>
        @endif
    </div>
</div>
@endsection
