
@extends('layouts.app')

@section('content')
<div class="space-y-6">

    <!-- Encabezado -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-gray-900 dark:text-white">
                Proveedores
            </h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Administra los proveedores y consulta su información y categoría.
            </p>
        </div>

        <a href="{{ route('proveedores.create') }}"
           class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-500/30 dark:bg-blue-500 dark:hover:bg-blue-600">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 4v16m8-8H4"/>
            </svg>
            Nuevo proveedor
        </a>
    </div>

    <!-- Estadísticas -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">

        <!-- Total de proveedores -->
        <div class="rounded-2xl border border-blue-200 bg-blue-50 p-5 transition dark:border-blue-900/60 dark:bg-blue-900/20">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-blue-700 dark:text-blue-300">
                        Total de proveedores
                    </p>
                    <p class="mt-2 text-3xl font-bold text-blue-900 dark:text-blue-100">
                        {{ $proveedores->total() }}
                    </p>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-100 text-blue-700 dark:bg-blue-900/50 dark:text-blue-300">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                              d="M16 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2m6-10a4 4 0 100-8 4 4 0 000 8zm10 1v6m3-3h-6"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Registros de la página -->
        <div class="rounded-2xl border border-green-200 bg-green-50 p-5 transition dark:border-green-900/60 dark:bg-green-900/20">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-green-700 dark:text-green-300">
                        En esta página
                    </p>
                    <p class="mt-2 text-3xl font-bold text-green-900 dark:text-green-100">
                        {{ $proveedores->count() }}
                    </p>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-green-100 text-green-700 dark:bg-green-900/50 dark:text-green-300">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                              d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Paginación -->
        <div class="rounded-2xl border border-purple-200 bg-purple-50 p-5 transition dark:border-purple-900/60 dark:bg-purple-900/20">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-purple-700 dark:text-purple-300">
                        Página actual
                    </p>
                    <p class="mt-2 text-3xl font-bold text-purple-900 dark:text-purple-100">
                        {{ $proveedores->currentPage() }}
                        <span class="text-lg font-medium text-purple-500 dark:text-purple-300">
                            / {{ $proveedores->lastPage() }}
                        </span>
                    </p>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-purple-100 text-purple-700 dark:bg-purple-900/50 dark:text-purple-300">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                              d="M12 6v12m-6-6h12"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Listado -->
    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">

        <!-- Barra de búsqueda -->
        <div class="border-b border-gray-200 p-5 dark:border-gray-700 sm:p-6">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                        Directorio de proveedores
                    </h2>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Busca por nombre, económico, categoría o dirección.
                    </p>
                </div>

                <div class="relative w-full sm:max-w-sm">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input
                        type="text"
                        id="search"
                        placeholder="Buscar proveedores..."
                        class="block w-full rounded-xl border border-gray-300 bg-white py-3 pl-11 pr-4 text-sm text-gray-900 shadow-sm outline-none transition placeholder:text-gray-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder:text-gray-400 dark:focus:border-blue-400"
                    >
                </div>
            </div>
        </div>

        <!-- Tabla -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">

                <thead class="bg-gray-50 dark:bg-gray-700/70">
                    <tr>
                        <th scope="col" class="whitespace-nowrap px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-300">
                            Económico
                        </th>

                        <th scope="col" class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-300">
                            Proveedor
                        </th>

                        <th scope="col" class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-300">
                            Categoría
                        </th>

                        <th scope="col" class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-300">
                            Dirección
                        </th>

                        <th scope="col" class="whitespace-nowrap px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-300">
                            Fecha de registro
                        </th>

                        <th scope="col" class="whitespace-nowrap px-5 py-4 text-center text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-300">
                            Acciones
                        </th>
                    </tr>
                </thead>

                <tbody id="proveedores-table-body" class="divide-y divide-gray-100 bg-white dark:divide-gray-700 dark:bg-gray-800">

                    @forelse($proveedores as $proveedor)
                        <tr class="provider-row transition-colors duration-150 hover:bg-gray-50 dark:hover:bg-gray-700/50">

                            <!-- Económico -->
                            <td class="whitespace-nowrap px-5 py-4">
                                <span class="inline-flex items-center rounded-lg border border-gray-200 bg-gray-50 px-3 py-1.5 text-sm font-semibold text-gray-700 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200">
                                    {{ $proveedor->economico ?: 'Sin asignar' }}
                                </span>
                            </td>

                            <!-- Nombre -->
                            <td class="px-5 py-4">
                                <div class="flex min-w-[190px] items-center gap-3">
                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-100 text-sm font-bold uppercase text-blue-700 dark:bg-blue-900/50 dark:text-blue-300">
                                        {{ \Illuminate\Support\Str::substr($proveedor->proveedor ?? 'P', 0, 2) }}
                                    </div>

                                    <div class="min-w-0">
                                        <p class="truncate text-sm font-semibold text-gray-900 dark:text-white"
                                           title="{{ $proveedor->proveedor }}">
                                            {{ $proveedor->proveedor }}
                                        </p>
                                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                            Registro #{{ $proveedor->id }}
                                        </p>
                                    </div>
                                </div>
                            </td>

                            <!-- Categoría -->
                            <td class="px-5 py-4">
                                @if($proveedor->categoria)
                                    <span class="inline-flex max-w-[200px] items-center rounded-full border border-blue-200 bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-700 dark:border-blue-800 dark:bg-blue-900/30 dark:text-blue-300">
                                        <span class="mr-2 h-1.5 w-1.5 shrink-0 rounded-full bg-blue-500"></span>
                                        <span class="truncate" title="{{ $proveedor->categoria }}">
                                            {{ $proveedor->categoria }}
                                        </span>
                                    </span>
                                @else
                                    <span class="text-sm italic text-gray-400 dark:text-gray-500">
                                        Sin categoría
                                    </span>
                                @endif
                            </td>

                            <!-- Dirección -->
                            <td class="px-5 py-4">
                                <p class="max-w-xs truncate text-sm text-gray-600 dark:text-gray-300"
                                   title="{{ $proveedor->direccion }}">
                                    {{ $proveedor->direccion ?: 'Sin dirección registrada' }}
                                </p>
                            </td>

                            <!-- Fecha -->
                            <td class="whitespace-nowrap px-5 py-4">
                                <p class="text-sm font-medium text-gray-800 dark:text-gray-200">
                                    {{ $proveedor->created_at?->format('d/m/Y') ?? 'Sin fecha' }}
                                </p>
                                @if($proveedor->created_at)
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                        {{ $proveedor->created_at->diffForHumans() }}
                                    </p>
                                @endif
                            </td>

                            <!-- Acciones -->
                            <td class="whitespace-nowrap px-5 py-4">
                                <div class="flex items-center justify-center gap-2">

                                    <!-- Ver -->
                                    <a href="{{ route('proveedores.show', $proveedor) }}"
                                       title="Ver proveedor"
                                       aria-label="Ver proveedor"
                                       class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-blue-200 bg-blue-50 text-blue-700 transition hover:bg-blue-100 dark:border-blue-800 dark:bg-blue-900/30 dark:text-blue-300 dark:hover:bg-blue-900/60">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                                  d="M2.25 12s3.5-7 9.75-7 9.75 7 9.75 7-3.5 7-9.75 7-9.75-7-9.75-7z"/>
                                            <circle cx="12" cy="12" r="3" stroke-width="1.8"/>
                                        </svg>
                                    </a>

                                    @if(auth()->user()->canManageInventory())

                                        <!-- Editar -->
                                        <a href="{{ route('proveedores.edit', $proveedor) }}"
                                           title="Editar proveedor"
                                           aria-label="Editar proveedor"
                                           class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-amber-200 bg-amber-50 text-amber-700 transition hover:bg-amber-100 dark:border-amber-800 dark:bg-amber-900/30 dark:text-amber-300 dark:hover:bg-amber-900/60">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                                      d="M16.862 4.487l2.651 2.651M4 20l4.5-1 11-11a1.875 1.875 0 00-2.65-2.65l-11 11L4 20z"/>
                                            </svg>
                                        </a>

                                        <!-- Eliminar -->
                                        <form method="POST"
                                              action="{{ route('proveedores.destroy', $proveedor) }}"
                                              onsubmit="return confirm('¿Estás seguro de eliminar este proveedor?\n\nEsta acción no se puede deshacer.');">
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit"
                                                    title="Eliminar proveedor"
                                                    aria-label="Eliminar proveedor"
                                                    class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-red-200 bg-red-50 text-red-700 transition hover:bg-red-100 dark:border-red-800 dark:bg-red-900/30 dark:text-red-300 dark:hover:bg-red-900/60">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                                          d="M4 7h16m-10 4v6m4-6v6M5 7l1 14h12l1-14M9 7V4h6v3"/>
                                                </svg>
                                            </button>
                                        </form>

                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center">
                                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-gray-100 text-gray-400 dark:bg-gray-700 dark:text-gray-500">
                                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6"
                                              d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                    </svg>
                                </div>
                                <h3 class="mt-4 text-base font-semibold text-gray-900 dark:text-white">
                                    No hay proveedores registrados
                                </h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                    Registra un proveedor para comenzar a construir tu directorio.
                                </p>
                                <a href="{{ route('proveedores.create') }}"
                                   class="mt-5 inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M12 4v16m8-8H4"/>
                                    </svg>
                                    Registrar proveedor
                                </a>
                            </td>
                        </tr>
                    @endforelse

                    <!-- Sin coincidencias en la búsqueda -->
                    <tr id="no-search-results" style="display: none;">
                        <td colspan="6" class="px-6 py-12 text-center">
                            <svg class="mx-auto h-10 w-10 text-gray-400 dark:text-gray-500"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <circle cx="11" cy="11" r="7" stroke-width="1.8"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                      d="M16 16l5 5"/>
                            </svg>
                            <p class="mt-3 font-medium text-gray-800 dark:text-gray-200">
                                No se encontraron coincidencias
                            </p>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                Intenta con otro nombre, categoría o número económico.
                            </p>
                        </td>
                    </tr>

                </tbody>
            </table>
        </div>

        <!-- Pie y paginación -->
        <div class="flex flex-col gap-3 border-t border-gray-200 bg-gray-50 px-5 py-4 dark:border-gray-700 dark:bg-gray-800/80 sm:flex-row sm:items-center sm:justify-between">
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Mostrando
                <span class="font-semibold text-gray-800 dark:text-gray-200">
                    {{ $proveedores->firstItem() ?? 0 }}–{{ $proveedores->lastItem() ?? 0 }}
                </span>
                de
                <span class="font-semibold text-gray-800 dark:text-gray-200">
                    {{ $proveedores->total() }}
                </span>
                proveedores
            </p>

            <div>
                {{ $proveedores->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Búsqueda local -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('search');
    const tableBody = document.getElementById('proveedores-table-body');
    const noResults = document.getElementById('no-search-results');

    if (!searchInput || !tableBody || !noResults) {
        return;
    }

    const rows = Array.from(tableBody.querySelectorAll('tr.provider-row'));

    searchInput.addEventListener('input', function () {
        const searchTerm = this.value
            .trim()
            .toLocaleLowerCase('es');

        let visibleRows = 0;

        rows.forEach(function (row) {
            const rowText = row.textContent.toLocaleLowerCase('es');
            const matches = rowText.includes(searchTerm);

            row.style.display = matches ? '' : 'none';

            if (matches) {
                visibleRows++;
            }
        });

        noResults.style.display =
            rows.length > 0 && visibleRows === 0 ? '' : 'none';
    });
});
</script>
@endsection
