@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Préstamos de Material</h1>
        <a href="{{ route('prestamos.create') }}" 
           class="bg-blue-500 hover:bg-blue-600 dark:bg-blue-600 dark:hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition-colors duration-200">
            Nuevo Préstamo
        </a>
    </div>

    <!-- Estadísticas de préstamos -->
    @if(auth()->user()->canApproveRequests() || auth()->user()->canManageInventory())
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-yellow-600 dark:text-yellow-400">Pendientes</p>
                    <p class="text-2xl font-bold text-yellow-900 dark:text-yellow-100">{{ $estadisticas['pendientes'] }}</p>
                </div>
                <svg class="w-8 h-8 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>

        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-blue-600 dark:text-blue-400">Prestados</p>
                    <p class="text-2xl font-bold text-blue-900 dark:text-blue-100">{{ $estadisticas['prestados'] }}</p>
                </div>
                <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
        </div>

        <div class="bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-800 rounded-lg p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-orange-600 dark:text-orange-400">Próximos a Vencer</p>
                    <p class="text-2xl font-bold text-orange-900 dark:text-orange-100">{{ $estadisticas['proximos'] }}</p>
                </div>
                <svg class="w-8 h-8 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
        </div>

        <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-red-600 dark:text-red-400">Vencidos</p>
                    <p class="text-2xl font-bold text-red-900 dark:text-red-100">{{ $estadisticas['vencidos'] }}</p>
                </div>
                <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
    </div>
    @endif

    <!-- Filtros -->
    @if(auth()->user()->canApproveRequests() || auth()->user()->canManageInventory())
    <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-4 transition-colors duration-200">
        <div class="flex flex-wrap gap-4 items-center justify-between">
            <div class="flex flex-wrap gap-4 items-center">
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Filtrar por estado:</span>
                
                <div class="flex gap-2">
                    <button type="button" data-status="all" 
                            class="status-filter active px-3 py-1 text-xs font-semibold rounded-full transition-all duration-200 bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300">
                        Todos
                    </button>

                    <button type="button" data-status="pendiente" 
                            class="status-filter px-3 py-1 text-xs font-semibold rounded-full transition-all duration-200 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                        Pendientes
                    </button>

                    <button type="button" data-status="prestado" 
                            class="status-filter px-3 py-1 text-xs font-semibold rounded-full transition-all duration-200 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                        Prestados
                    </button>

                    <button type="button" data-status="devuelto" 
                            class="status-filter px-3 py-1 text-xs font-semibold rounded-full transition-all duration-200 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                        Devueltos
                    </button>

                    <button type="button" data-status="vencido" 
                            class="status-filter px-3 py-1 text-xs font-semibold rounded-full transition-all duration-200 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                        Vencidos
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-md transition-colors duration-200">
        <div class="px-4 py-5 sm:p-6">
            <div class="mb-6">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input type="text" id="search" placeholder="Buscar préstamos..." 
                           class="block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md">
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                ID / Fecha
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Solicitante
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Destino
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Productos
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Fecha Préstamo
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Fecha Devolución
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Estado
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Acciones
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($prestamos as $prestamo)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200" 
                            data-status="{{ $prestamo->estatus }}"
                            data-vencido="{{ $prestamo->esta_vencido ? 'true' : 'false' }}">
                            
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                    #{{ str_pad($prestamo->id, 5, '0', STR_PAD_LEFT) }}
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $prestamo->created_at->format('d/m/Y') }}
                                </div>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-8 w-8">
                                        <div class="h-8 w-8 rounded-full bg-blue-500 dark:bg-blue-600 flex items-center justify-center">
                                            <span class="text-sm font-medium text-white">
                                                {{ substr($prestamo->user->name, 0, 1) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $prestamo->user->name }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 dark:text-white">{{ $prestamo->destino }}</div>
                            </td>

                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900 dark:text-white">
                                    {{ $prestamo->total_productos }} productos
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $prestamo->total_unidades_prestadas }} unidades
                                </div>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 dark:text-white">
                                    {{ $prestamo->fecha_prestamo ? $prestamo->fecha_prestamo->format('d/m/Y') : 'Por definir' }}
                                </div>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm {{ $prestamo->esta_vencido ? 'text-red-600 dark:text-red-400 font-bold' : 'text-gray-900 dark:text-white' }}">
                                    {{ $prestamo->fecha_devolucion_esperada->format('d/m/Y') }}
                                </div>
                                @if($prestamo->esta_vencido)
                                    <div class="text-xs text-red-600 dark:text-red-400">
                                        Vencido hace {{ $prestamo->dias_vencido }} días
                                    </div>
                                @endif
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($prestamo->estatus === 'pendiente')
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300">
                                        Pendiente
                                    </span>
                                @elseif($prestamo->estatus === 'prestado')
                                    @if($prestamo->esta_vencido)
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300">
                                            Vencido
                                        </span>
                                    @else
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300">
                                            Prestado
                                        </span>
                                    @endif
                                @elseif($prestamo->estatus === 'devuelto')
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300">
                                        Devuelto
                                    </span>
                                @else
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300">
                                        Denegado
                                    </span>
                                @endif
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('prestamos.show', $prestamo) }}" 
                                       class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                                        Ver
                                    </a>

                                    @if(auth()->user()->canApproveRequests() && $prestamo->estatus === 'pendiente')
                                        <form method="POST" action="{{ route('prestamos.updateEstatus', $prestamo) }}" class="inline">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="estatus" value="aprobado">
                                            <button type="submit" 
                                                    class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300"
                                                    onclick="return confirm('¿Aprobar este préstamo?')">
                                                Aprobar
                                            </button>
                                            {{-- // boton para denegar --}}
                                            <form method="POST" action="{{ route('prestamos.updateEstatus', $prestamo) }}" class="inline">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="estatus" value="denegado">
                                                <button type="submit" 
                                                        class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                                                        onclick="return confirm('¿Denegar este préstamo?')">
                                                    Denegar
                                                </button>
                                            </form>
                                        </div>
                                        </form>
                                    @endif

                                    @if(auth()->user()->canManageInventory() && $prestamo->estatus === 'prestado')
                                        <a href="{{ route('prestamos.devolucion', $prestamo) }}" 
                                           class="text-purple-600 hover:text-purple-900 dark:text-purple-400 dark:hover:text-purple-300">
                                            Devolución
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                No hay préstamos registrados
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                {{ $prestamos->links() }}
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('search');
    const statusFilters = document.querySelectorAll('.status-filter');
    const tableRows = document.querySelectorAll('tbody tr[data-status]');
    
    let currentStatusFilter = 'all';
    let currentSearchTerm = '';

    function applyFilters() {
        tableRows.forEach(row => {
            const status = row.getAttribute('data-status');
            const vencido = row.getAttribute('data-vencido') === 'true';
            const text = row.textContent.toLowerCase();
            
            let statusMatch = false;
            if (currentStatusFilter === 'all') {
                statusMatch = true;
            } else if (currentStatusFilter === 'vencido') {
                statusMatch = status === 'prestado' && vencido;
            } else {
                statusMatch = status === currentStatusFilter;
            }
            
            const searchMatch = currentSearchTerm === '' || text.includes(currentSearchTerm);
            
            if (statusMatch && searchMatch) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    if (searchInput) {
        searchInput.addEventListener('keyup', function () {
            currentSearchTerm = this.value.toLowerCase();
            applyFilters();
        });
    }

    statusFilters.forEach(filter => {
        filter.addEventListener('click', function () {
            statusFilters.forEach(f => {
                f.classList.remove('active', 'bg-blue-100', 'dark:bg-blue-900/30', 'text-blue-800', 'dark:text-blue-300');
                f.classList.add('bg-gray-100', 'dark:bg-gray-700', 'text-gray-700', 'dark:text-gray-300');
            });

            this.classList.add('active', 'bg-blue-100', 'dark:bg-blue-900/30', 'text-blue-800', 'dark:text-blue-300');
            this.classList.remove('bg-gray-100', 'dark:bg-gray-700', 'text-gray-700', 'dark:text-gray-300');

            currentStatusFilter = this.getAttribute('data-status');
            applyFilters();
        });
    });
});
</script>
@endsection