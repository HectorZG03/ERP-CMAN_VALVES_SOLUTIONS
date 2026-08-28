@extends('layouts.app')

@section('content')
<div class="flex min-h-[70vh] items-center justify-center px-4 py-8 sm:px-6">
    <div class="w-full max-w-4xl overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-xl dark:border-gray-700 dark:bg-gray-800">

        <div class="grid md:grid-cols-5">

            {{-- Panel informativo --}}
            <div class="relative overflow-hidden bg-yellow-400 px-8 py-10 text-gray-900 md:col-span-2 md:flex md:flex-col md:justify-between">
                <div class="absolute -right-16 -top-16 h-48 w-48 rounded-full bg-yellow-300 opacity-60"></div>
                <div class="absolute -bottom-20 -left-20 h-56 w-56 rounded-full bg-yellow-500 opacity-40"></div>

                <div class="relative">
                    <div class="mb-8 inline-flex rounded-xl bg-white p-3 shadow-sm">
                        <img
                            src="{{ asset('img/logo/logo.png') }}"
                            alt="Logo CMAN"
                            class="h-14 w-auto"
                        >
                    </div>

                    <p class="mb-2 text-sm font-semibold uppercase tracking-[0.2em] text-gray-700">
                        Sistema empresarial
                    </p>

                    <h1 class="text-3xl font-bold leading-tight">
                        ERP CMAN
                    </h1>

                    <p class="mt-4 max-w-sm text-sm leading-6 text-gray-700">
                        Administración y seguimiento de las operaciones internas de la empresa.
                    </p>
                </div>

                <div class="relative mt-10 border-t border-yellow-500 pt-5">
                    <p class="text-xs text-gray-700">
                        Acceso exclusivo para personal autorizado.
                    </p>
                </div>
            </div>

            {{-- Formulario --}}
            <div class="px-6 py-10 sm:px-10 md:col-span-3">
                <div class="mx-auto max-w-md">
                    <div class="mb-8">
                        <span class="mb-4 block h-1 w-12 rounded-full bg-yellow-400"></span>

                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                            Iniciar sesión
                        </h2>

                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                            Ingresa tus credenciales para acceder al sistema.
                        </p>
                    </div>

                    <form method="POST" action="{{ route('login') }}" class="space-y-5">
                        @csrf

                        {{-- Correo --}}
                        <div>
                            <label for="email" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Correo electrónico
                            </label>

                            <div class="relative">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <svg class="h-5 w-5 text-gray-400"
                                         fill="none"
                                         stroke="currentColor"
                                         viewBox="0 0 24 24">
                                        <path stroke-linecap="round"
                                              stroke-linejoin="round"
                                              stroke-width="2"
                                              d="M16 12H8m8 0a4 4 0 10-8 0m8 0v1a3 3 0 006 0v-1a10 10 0 10-4 8">
                                        </path>
                                    </svg>
                                </div>

                                <input
                                    id="email"
                                    name="email"
                                    type="email"
                                    value="{{ old('email') }}"
                                    required
                                    autofocus
                                    autocomplete="email"
                                    placeholder="correo@empresa.com"
                                    class="block w-full rounded-lg border bg-gray-50 py-3 pl-10 pr-3 text-gray-900 placeholder-gray-400 outline-none transition
                                           focus:border-yellow-500 focus:bg-white focus:ring-2 focus:ring-yellow-200
                                           dark:bg-gray-700 dark:text-white dark:placeholder-gray-500 dark:focus:bg-gray-700
                                           @error('email') border-red-400 @else border-gray-300 dark:border-gray-600 @enderror"
                                >
                            </div>

                            @error('email')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Contraseña --}}
                        <div>
                            <label for="password" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Contraseña
                            </label>

                            <div class="relative">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <svg class="h-5 w-5 text-gray-400"
                                         fill="none"
                                         stroke="currentColor"
                                         viewBox="0 0 24 24">
                                        <path stroke-linecap="round"
                                              stroke-linejoin="round"
                                              stroke-width="2"
                                              d="M12 11c1.657 0 3 1.343 3 3v2H9v-2c0-1.657 1.343-3 3-3zm0 0V8a4 4 0 10-8 0v3">
                                        </path>
                                    </svg>
                                </div>

                                <input
                                    id="password"
                                    name="password"
                                    type="password"
                                    required
                                    autocomplete="current-password"
                                    placeholder="••••••••"
                                    class="block w-full rounded-lg border bg-gray-50 py-3 pl-10 pr-3 text-gray-900 placeholder-gray-400 outline-none transition
                                           focus:border-yellow-500 focus:bg-white focus:ring-2 focus:ring-yellow-200
                                           dark:bg-gray-700 dark:text-white dark:placeholder-gray-500 dark:focus:bg-gray-700
                                           @error('password') border-red-400 @else border-gray-300 dark:border-gray-600 @enderror"
                                >
                            </div>

                            @error('password')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Recordarme --}}
                        <label for="remember" class="flex cursor-pointer items-center">
                            <input
                                id="remember"
                                name="remember"
                                type="checkbox"
                                {{ old('remember') ? 'checked' : '' }}
                                class="h-4 w-4 rounded border-gray-300 text-yellow-500 focus:ring-yellow-400"
                            >

                            <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">
                                Mantener mi sesión iniciada
                            </span>
                        </label>

                        {{-- Errores generales --}}
                        @if ($errors->any() && !$errors->has('email') && !$errors->has('password'))
                            <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700 dark:border-red-800 dark:bg-red-900/20 dark:text-red-300">
                                <p class="font-medium">No fue posible iniciar sesión.</p>

                                @foreach ($errors->all() as $error)
                                    <p class="mt-1">{{ $error }}</p>
                                @endforeach
                            </div>
                        @endif

                        <button
                            type="submit"
                            class="flex w-full items-center justify-center rounded-lg bg-gray-900 px-4 py-3 text-sm font-semibold text-white shadow-sm transition
                                   hover:bg-yellow-500 hover:text-gray-900
                                   focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:ring-offset-2
                                   dark:bg-yellow-400 dark:text-gray-900 dark:hover:bg-yellow-300"
                        >
                            Iniciar sesión
                        </button>
                    </form>

                    <div class="mt-8 border-t border-gray-200 pt-5 text-center dark:border-gray-700">
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            © {{ date('Y') }} {{ config('app.name', 'ERP CMAN') }}.
                            Todos los derechos reservados.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection