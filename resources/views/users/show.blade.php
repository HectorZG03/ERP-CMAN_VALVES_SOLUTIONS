@extends('layouts.app')

@section('content')
@php
    $roles = [
        'ti' => 'TI',
        'aux_ti' => 'Auxiliar TI',
        'direccion' => 'Dirección',
        'almacen' => 'Almacén',
        'aux_almacen' => 'Auxiliar de Almacén',
        'calidad' => 'Calidad',
        'aux_calidad' => 'Auxiliar de Calidad',
        'contabilidad' => 'Contabilidad',
        'aux_contabilidad' => 'Auxiliar de Contabilidad',
        'estimaciones' => 'Estimaciones',
        'aux_estimaciones' => 'Auxiliar de Estimaciones',
        'finanzas' => 'Finanzas',
        'aux_finanzas' => 'Auxiliar de Finanzas',
        'logistica' => 'Logística',
        'aux_logistica' => 'Auxiliar de Logística',
        'rh' => 'Recursos Humanos',
        'aux_rh' => 'Auxiliar de Recursos Humanos',
        'operaciones' => 'Operaciones',
        'aux_operaciones' => 'Auxiliar de Operaciones',
        'hse' => 'HSE',
        'aux_hse' => 'Auxiliar de HSE',
    ];

    $inputClass = 'mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 shadow-sm focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-200 dark:border-gray-600 dark:bg-gray-800 dark:text-white dark:focus:border-amber-400';
@endphp

<div class="mx-auto max-w-7xl space-y-6">
    <header class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="text-sm font-semibold uppercase tracking-wide text-amber-600 dark:text-amber-400">
                Gestión de usuarios
            </p>
            <h1 class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">
                {{ $user->name }}
            </h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Consulta y actualiza la cuenta y su expediente relacionado.
            </p>
        </div>

        <x-button
            :href="route('users.index')"
            variant="secondary"
            icon="back"
        >
            Regresar al listado
        </x-button>
    </header>

    @if (session('success'))
        <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 dark:border-emerald-800 dark:bg-emerald-950/30 dark:text-emerald-300">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800 dark:border-red-800 dark:bg-red-950/30 dark:text-red-300">
            <p class="font-semibold">Revisa la información enviada:</p>
            <ul class="mt-1 list-inside list-disc">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <section class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900">
        <div class="flex flex-col gap-4 bg-slate-900 px-6 py-5 text-white sm:flex-row sm:items-center">
            <img
                src="{{ $user->profile_photo_url }}"
                alt="Foto de {{ $user->name }}"
                class="h-16 w-16 shrink-0 rounded-full border-2 border-white/70 object-cover"
            >

            <div class="min-w-0 flex-1">
                <h2 class="truncate text-xl font-bold">{{ $user->name }}</h2>
                <p class="mt-1 truncate text-sm text-slate-300">{{ $user->email }}</p>

                <div class="mt-3 flex flex-wrap gap-2">
                    <span class="rounded-full bg-blue-500/20 px-3 py-1 text-xs font-semibold text-blue-100">
                        {{ $roles[$user->role] ?? ucfirst(str_replace('_', ' ', $user->role)) }}
                    </span>
                    <span class="rounded-full bg-white/10 px-3 py-1 text-xs font-semibold text-slate-100">
                        {{ $user->personal ? 'No. laboral: ' . ($user->numero_empleado_laboral ?? 'Sin número registrado') : 'Sin expediente vinculado' }}
                    </span>
                </div>
            </div>

            <div class="text-sm text-slate-300 sm:text-right">
                <p>Registrado</p>
                <p class="mt-1 font-medium text-white">
                    {{ $user->created_at?->format('d/m/Y') ?? '—' }}
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 items-start gap-6 p-5 lg:grid-cols-12 lg:p-7">
            {{-- Datos generales de la cuenta --}}
            <section class="rounded-xl border border-gray-200 p-5 sm:p-6 lg:col-span-7 dark:border-gray-700">
                <div class="mb-6">
                    <p class="text-xs font-semibold uppercase tracking-wide text-blue-600 dark:text-blue-400">
                        Cuenta ERP
                    </p>
                    <h2 class="mt-1 text-lg font-bold text-gray-900 dark:text-white">
                        Datos generales
                    </h2>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        Guarda aquí los cambios de nombre, correo, rol y firma de la cuenta.
                    </p>
                </div>

                <form
                    method="POST"
                    action="{{ route('users.update', $user) }}"
                    enctype="multipart/form-data"
                    class="space-y-6"
                >
                    @csrf
                    @method('PATCH')

                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Nombre de la cuenta
                            </label>
                            <input
                                id="name"
                                name="name"
                                type="text"
                                value="{{ old('name', $user->name) }}"
                                required
                                maxlength="255"
                                class="{{ $inputClass }}"
                            >
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Correo electrónico
                            </label>
                            <input
                                id="email"
                                name="email"
                                type="email"
                                value="{{ old('email', $user->email) }}"
                                required
                                maxlength="255"
                                class="{{ $inputClass }}"
                            >
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <p class="block text-sm font-medium text-gray-700 dark:text-gray-300">Número laboral del expediente</p>
                            <p class="mt-1 rounded-lg bg-gray-100 px-3 py-2.5 text-sm text-gray-900 dark:bg-gray-800 dark:text-white">
                                {{ $user->personal ? ($user->numero_empleado_laboral ?? 'Sin número registrado') : 'Sin expediente vinculado' }}
                            </p>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                RH administra este dato desde el expediente de personal.
                            </p>
                        </div>

                        <div>
                            <label for="role" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Rol del ERP
                            </label>
                            <select id="role" name="role" required class="{{ $inputClass }}">
                                @foreach ($roles as $value => $label)
                                    <option
                                        value="{{ $value }}"
                                        @selected(old('role', $user->role) === $value)
                                    >
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('role')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <details class="rounded-lg border border-gray-200 dark:border-gray-700">
                        <summary class="cursor-pointer px-4 py-3 text-sm font-semibold text-gray-800 dark:text-gray-200">
                            Cambiar contraseña
                            <span class="ml-1 font-normal text-gray-500">(opcional)</span>
                        </summary>

                        <div class="grid grid-cols-1 gap-4 border-t border-gray-200 p-4 sm:grid-cols-2 dark:border-gray-700">
                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Nueva contraseña
                                </label>
                                <input
                                    id="password"
                                    name="password"
                                    type="password"
                                    minlength="6"
                                    autocomplete="new-password"
                                    class="{{ $inputClass }}"
                                >
                            </div>

                            <div>
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Confirmar contraseña
                                </label>
                                <input
                                    id="password_confirmation"
                                    name="password_confirmation"
                                    type="password"
                                    minlength="6"
                                    autocomplete="new-password"
                                    class="{{ $inputClass }}"
                                >
                            </div>

                            <p class="text-xs text-gray-500 sm:col-span-2 dark:text-gray-400">
                                Déjalas en blanco para conservar la contraseña actual.
                            </p>

                            @error('password')
                                <p class="text-sm text-red-600 sm:col-span-2">{{ $message }}</p>
                            @enderror
                        </div>
                    </details>

                    <details class="rounded-lg border border-gray-200 dark:border-gray-700">
                        <summary class="cursor-pointer px-4 py-3 text-sm font-semibold text-gray-800 dark:text-gray-200">
                            Actualizar firma
                            <span class="ml-1 font-normal text-gray-500">(opcional)</span>
                        </summary>

                        <div class="border-t border-gray-200 p-4 dark:border-gray-700">
                            <div>
                                <div class="mb-3 flex h-12 items-center gap-3">
                                    <div class="flex h-12 w-20 items-center justify-center rounded border border-dashed border-gray-300 bg-gray-50 p-1 dark:border-gray-600 dark:bg-gray-800">
                                        @if ($user->signature)
                                            <img
                                                src="{{ asset('storage/' . $user->signature) }}"
                                                alt="Firma actual"
                                                class="max-h-full max-w-full object-contain"
                                            >
                                        @else
                                            <span class="text-xs text-gray-500">Sin firma</span>
                                        @endif
                                    </div>
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Firma digital
                                    </span>
                                </div>

                                <input
                                    id="signature"
                                    name="signature"
                                    type="file"
                                    accept="image/jpeg,image/png,image/jpg,image/gif"
                                    class="block w-full text-sm text-gray-600 file:mr-3 file:rounded-md file:border-0 file:bg-slate-100 file:px-3 file:py-2 file:font-semibold dark:text-gray-300 dark:file:bg-gray-800"
                                >
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    JPEG, PNG, JPG o GIF; máximo 2 MB.
                                </p>
                                @error('signature')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </details>

                    <div class="flex justify-end border-t border-gray-200 pt-5 dark:border-gray-700">
                        <x-button type="submit" variant="accent" icon="save">
                            Guardar datos generales
                        </x-button>
                    </div>
                </form>
            </section>

            {{-- Vinculación separada: solo TI puede cambiarla --}}
            <section class="rounded-xl border border-amber-200 bg-amber-50/40 p-5 sm:p-6 lg:col-span-5 dark:border-amber-900/60 dark:bg-amber-950/10">
                <div class="mb-5">
                    <p class="text-xs font-semibold uppercase tracking-wide text-amber-700 dark:text-amber-400">
                        Relación laboral
                    </p>
                    <h2 class="mt-1 text-lg font-bold text-gray-900 dark:text-white">
                        Expediente de Personal
                    </h2>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        Vincula la cuenta con el expediente correcto. La relación no se asigna automáticamente por nombre o número.
                    </p>
                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                        El número laboral y la fotografía se toman de este expediente. RH administra ambos datos desde Personal.
                    </p>
                </div>

                @if ($user->personal)
                    <div class="mb-5 rounded-lg border border-emerald-200 bg-emerald-50 p-4 dark:border-emerald-800 dark:bg-emerald-950/30">
                        <p class="text-xs font-semibold uppercase tracking-wide text-emerald-700 dark:text-emerald-400">
                            Expediente actualmente vinculado
                        </p>
                        <p class="mt-2 font-semibold text-gray-900 dark:text-white">
                            {{ $user->personal->nombre_mostrar }}
                        </p>

                        <dl class="mt-3 space-y-2 text-sm">
                            <div class="flex justify-between gap-3">
                                <dt class="text-gray-600 dark:text-gray-400">Número laboral</dt>
                                <dd class="text-right font-medium text-gray-900 dark:text-white">
                                    {{ $user->numero_empleado_laboral ?? 'Sin número registrado' }}
                                </dd>
                            </div>
                            <div class="flex justify-between gap-3">
                                <dt class="text-gray-600 dark:text-gray-400">Estado laboral</dt>
                                <dd class="text-right font-medium text-gray-900 dark:text-white">
                                    {{ $user->personal->datosLaborales?->estadoLaboral?->nombre ?? 'Sin estado' }}
                                </dd>
                            </div>
                        </dl>
                    </div>
                @else
                    <div class="mb-5 rounded-lg border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800 dark:border-amber-800 dark:bg-amber-950/30 dark:text-amber-300">
                        Esta cuenta aún no está vinculada con un expediente de personal.
                    </div>
                @endif

                @if (auth()->user()->role === 'ti')
                    <div class="space-y-4">
                        <form
                            method="POST"
                            action="{{ route('users.personal.update', $user) }}"
                            class="space-y-4"
                        >
                            @csrf
                            @method('PATCH')

                            <div>
                                <label for="personal-search" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Buscar por nombre o número laboral
                                </label>
                                <input
                                    id="personal-search"
                                    type="search"
                                    placeholder="Escribe para filtrar expedientes..."
                                    class="{{ $inputClass }}"
                                >
                            </div>

                            <div>
                                <label for="personal_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Expediente
                                </label>

                                <select
                                    id="personal_id"
                                    name="personal_id"
                                    required
                                    class="{{ $inputClass }}"
                                >
                                    <option value="" disabled @selected(!$user->personal_id)>
                                        Selecciona un expediente
                                    </option>

                                    @foreach ($personalDisponible as $persona)
                                        @php
                                            $employeeId = $persona->datosLaborales?->employee_id ?? 'Sin número laboral';
                                            $employmentStatus = $persona->datosLaborales?->estadoLaboral?->nombre ?? 'Estado no especificado';
                                            $optionLabel = $persona->nombre_mostrar . ' — ' . $employeeId . ' — ' . $employmentStatus;
                                        @endphp

                                        <option
                                            value="{{ $persona->id }}"
                                            data-search="{{ \Illuminate\Support\Str::lower($optionLabel) }}"
                                            @selected((string) old('personal_id', $user->personal_id) === (string) $persona->id)
                                        >
                                            {{ $optionLabel }}
                                        </option>
                                    @endforeach
                                </select>

                                @error('personal_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="flex justify-end border-t border-amber-200 pt-4 dark:border-amber-900/60">
                                <x-button type="submit" variant="accent" icon="save">
                                    Guardar vinculación
                                </x-button>
                            </div>
                        </form>

                        @if ($user->personal_id)
                            <form
                                method="POST"
                                action="{{ route('users.personal.update', $user) }}"
                                class="flex justify-end"
                            >
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="personal_id" value="">

                                <x-button type="submit" variant="danger" icon="close">
                                    Quitar vínculo
                                </x-button>
                            </form>
                        @endif
                    </div>
                @else
                    <p class="rounded-lg bg-white p-3 text-sm text-gray-600 dark:bg-gray-800 dark:text-gray-300">
                        Solo TI puede asignar o cambiar este vínculo.
                    </p>
                @endif
            </section>
        </div>
    </section>

    <details class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900">
        <summary class="cursor-pointer px-5 py-4 font-semibold text-gray-900 dark:text-white">
            Consultar accesos y actividad de la cuenta
        </summary>

        <div class="grid grid-cols-1 gap-6 border-t border-gray-200 p-5 md:grid-cols-2 dark:border-gray-700">
            <div>
                <h2 class="mb-3 font-semibold text-gray-900 dark:text-white">
                    Accesos asociados al rol
                </h2>

                <ul class="space-y-2 text-sm">
                    @if ($user->canManageUsers())
                        <li class="rounded-lg bg-emerald-50 px-3 py-2 text-emerald-800 dark:bg-emerald-950/30 dark:text-emerald-300">
                            Gestionar usuarios
                        </li>
                    @endif

                    @if ($user->canManageInventory())
                        <li class="rounded-lg bg-emerald-50 px-3 py-2 text-emerald-800 dark:bg-emerald-950/30 dark:text-emerald-300">
                            Gestionar inventario
                        </li>
                    @endif

                    @if ($user->canApproveRequests())
                        <li class="rounded-lg bg-emerald-50 px-3 py-2 text-emerald-800 dark:bg-emerald-950/30 dark:text-emerald-300">
                            Aprobar solicitudes
                        </li>
                    @endif

                    <li class="rounded-lg bg-slate-50 px-3 py-2 text-slate-700 dark:bg-gray-800 dark:text-gray-300">
                        Crear solicitudes y requisiciones
                    </li>
                </ul>
            </div>

            <div>
                <h2 class="mb-3 font-semibold text-gray-900 dark:text-white">
                    Actividad registrada
                </h2>

                <div class="grid grid-cols-2 gap-3">
                    @if ($user->canManageInventory())
                        <div class="rounded-lg bg-blue-50 p-3 text-center dark:bg-blue-950/30">
                            <p class="text-xl font-bold text-blue-700 dark:text-blue-300">
                                {{ \App\Models\Entrada::where('user_id', $user->id)->count() }}
                            </p>
                            <p class="mt-1 text-xs text-gray-600 dark:text-gray-400">Entradas</p>
                        </div>

                        <div class="rounded-lg bg-red-50 p-3 text-center dark:bg-red-950/30">
                            <p class="text-xl font-bold text-red-700 dark:text-red-300">
                                {{ \App\Models\Salida::where('user_id', $user->id)->count() }}
                            </p>
                            <p class="mt-1 text-xs text-gray-600 dark:text-gray-400">Salidas</p>
                        </div>
                    @endif

                    <div class="rounded-lg bg-emerald-50 p-3 text-center dark:bg-emerald-950/30">
                        <p class="text-xl font-bold text-emerald-700 dark:text-emerald-300">
                            {{ \App\Models\SolicitudMaterial::where('user_id', $user->id)->count() }}
                        </p>
                        <p class="mt-1 text-xs text-gray-600 dark:text-gray-400">Solicitudes</p>
                    </div>

                    <div class="rounded-lg bg-purple-50 p-3 text-center dark:bg-purple-950/30">
                        <p class="text-xl font-bold text-purple-700 dark:text-purple-300">
                            {{ \App\Models\Requisicion::where('user_id', $user->id)->count() }}
                        </p>
                        <p class="mt-1 text-xs text-gray-600 dark:text-gray-400">Requisiciones</p>
                    </div>
                </div>
            </div>
        </div>
    </details>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('personal-search');
    const personalSelect = document.getElementById('personal_id');

    if (!searchInput || !personalSelect) {
        return;
    }

    searchInput.addEventListener('input', () => {
        const query = searchInput.value.trim().toLowerCase();
        const selectedValue = personalSelect.value;

        Array.from(personalSelect.options).forEach((option) => {
            if (!option.value) {
                option.hidden = false;
                return;
            }

            const text = option.dataset.search || option.textContent.toLowerCase();
            option.hidden = option.value !== selectedValue && !text.includes(query);
        });
    });
});
</script>
@endsection
