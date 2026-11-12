@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-7xl space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                Gestión de usuarios
            </h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Consulta las cuentas ERP y su vínculo con el expediente de personal.
            </p>
        </div>

        <x-button :href="route('users.create')" variant="primary" icon="add">
            Crear usuario
        </x-button>
    </div>

    @if (session('success'))
        <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 dark:border-emerald-800 dark:bg-emerald-950/30 dark:text-emerald-300">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800 dark:border-red-800 dark:bg-red-950/30 dark:text-red-300">
            <p class="font-semibold">No se pudo completar la acción.</p>
            <ul class="mt-1 list-inside list-disc">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900">
        <div class="border-b border-gray-200 px-5 py-4 dark:border-gray-700">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h2 class="font-semibold text-gray-900 dark:text-white">
                        Cuentas registradas
                    </h2>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        La vinculación conecta la cuenta con su expediente laboral.
                    </p>
                </div>

                <span class="rounded-full bg-slate-100 px-3 py-1 text-sm font-medium text-slate-700 dark:bg-slate-800 dark:text-slate-200">
                    {{ $users->total() }} usuarios
                </span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-[1050px] w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-300">
                            Usuario
                        </th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-300">
                            Correo
                        </th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-300">
                            Rol
                        </th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-300">
                            Expediente de personal
                        </th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-300">
                            Registro
                        </th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-300">
                            Acciones
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-900">
                    @forelse ($users as $user)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/70">
                            <td class="px-5 py-4">
                                <div class="font-semibold text-gray-900 dark:text-white">
                                    {{ $user->name }}
                                </div>
                                <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    Empleado ERP: {{ $user->num_empleado ?: 'Sin número' }}
                                </div>
                            </td>

                            <td class="px-5 py-4 text-sm text-gray-700 dark:text-gray-300">
                                {{ $user->email }}
                            </td>

                            <td class="px-5 py-4">
                                <span @class([
                                    'inline-flex rounded-full px-2.5 py-1 text-xs font-semibold',
                                    'bg-purple-100 text-purple-800 dark:bg-purple-900/40 dark:text-purple-300' => $user->role === 'direccion',
                                    'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300' => $user->role === 'ti',
                                    'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300' => str_contains($user->role, 'almacen'),
                                    'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300' => !in_array($user->role, ['direccion', 'ti']) && !str_contains($user->role, 'almacen'),
                                ])>
                                    {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                                </span>
                            </td>

                            <td class="px-5 py-4">
                                @if ($user->personal)
                                    <div class="font-medium text-gray-900 dark:text-white">
                                        {{ $user->personal->nombre_mostrar }}
                                    </div>

                                    <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                        Empleado:
                                        {{ $user->personal->datosLaborales?->employee_id ?? 'Sin número laboral' }}
                                    </div>

                                    <span class="mt-2 inline-flex rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300">
                                        Vinculado
                                    </span>
                                @else
                                    <span class="inline-flex rounded-full bg-amber-100 px-2.5 py-1 text-xs font-semibold text-amber-800 dark:bg-amber-900/40 dark:text-amber-300">
                                        Sin vincular
                                    </span>
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                        Se puede relacionar desde Gestionar.
                                    </p>
                                @endif
                            </td>

                            <td class="whitespace-nowrap px-5 py-4 text-sm text-gray-600 dark:text-gray-300">
                                {{ $user->created_at?->format('d/m/Y') ?? '—' }}
                            </td>

                            <td class="px-5 py-4">
                                <div class="flex flex-wrap justify-end gap-2">
                                    <x-button
                                        :href="route('users.show', $user)"
                                        variant="secondary"
                                        size="sm"
                                        icon="edit"
                                    >
                                        Gestionar
                                    </x-button>

                                    @if ($user->id !== auth()->id())
                                        <form
                                            method="POST"
                                            action="{{ route('users.destroy', $user) }}"
                                            data-confirm="¿Estás seguro de eliminar este usuario? Esta acción no se puede deshacer."
                                            data-confirm-title="Eliminar usuario"
                                            data-confirm-text="Eliminar"
                                            data-confirm-variant="danger"
                                        >
                                            @csrf
                                            @method('DELETE')

                                            <x-button
                                                type="submit"
                                                variant="danger"
                                                size="sm"
                                                icon="delete"
                                            >
                                                Eliminar
                                            </x-button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-10 text-center text-sm text-gray-500 dark:text-gray-400">
                                No hay usuarios registrados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-gray-200 px-5 py-4 dark:border-gray-700">
            {{ $users->links() }}
        </div>
    </section>
</div>
@endsection