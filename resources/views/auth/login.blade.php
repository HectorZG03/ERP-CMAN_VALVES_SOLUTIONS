@extends('layouts.app')

@section('content')
<div class="relative isolate flex min-h-screen items-center justify-center overflow-hidden px-4 py-8 sm:px-6">

    {{-- Fondo difuminado que cubre toda la pantalla --}}
    <div
        aria-hidden="true"
        class="fixed inset-0 -z-20 scale-105 bg-cover bg-center blur-sm"
        style="background-image: url('{{ asset('img/logo/fondo.jpg') }}')"
    ></div>

    {{-- Capa para mejorar el contraste del card --}}
    <div
        aria-hidden="true"
        class="fixed inset-0 -z-10 bg-slate-950/45"
    ></div>

    {{-- Card de inicio de sesión --}}
    <div class="w-full max-w-4xl overflow-hidden rounded-2xl border border-yellow-500/50 bg-yellow-400 shadow-2xl">
        <div class="grid md:grid-cols-5">

            {{-- Panel informativo --}}
            <div class="relative overflow-hidden px-8 py-10 text-gray-900 md:col-span-2 md:flex md:flex-col md:justify-between">
                <div class="absolute -right-16 -top-16 h-48 w-48 rounded-full bg-yellow-300/70"></div>
                <div class="absolute -bottom-20 -left-20 h-56 w-56 rounded-full bg-amber-500/50"></div>

                <div class="relative">
                    <div class="mb-8 inline-flex rounded-xl bg-white p-3 shadow-sm">
                        <img
                            src="{{ asset('img/logo/logo_cman.png') }}"
                            alt="Logo CMAN"
                            class="h-[4.5rem] w-auto"
                        >
                    </div>

                    <p class="mb-2 text-sm font-semibold uppercase tracking-[0.2em] text-gray-700">
                        Sistema empresarial
                    </p>

                    <h1 class="text-3xl font-bold leading-tight">
                        ERP CMAN
                    </h1>

                    <p class="mt-4 max-w-sm text-sm leading-6 text-gray-800">
                        Administración y seguimiento de las operaciones internas de la empresa.
                    </p>
                </div>

                <div class="relative mt-10 border-t border-yellow-600/50 pt-5">
                    <p class="text-xs text-gray-800">
                        Acceso exclusivo para personal autorizado.
                    </p>
                </div>
            </div>

            {{-- Formulario --}}
            <div class="bg-yellow-50/95 px-6 py-10 sm:px-10 md:col-span-3">
                <div class="mx-auto max-w-md">
                    <div class="mb-8">
                        <span class="mb-4 block h-1 w-12 rounded-full bg-yellow-500"></span>

                        <h2 class="text-2xl font-bold text-gray-900">
                            Iniciar sesión
                        </h2>

                        <p class="mt-2 text-sm text-gray-600">
                            Ingresa tus credenciales para acceder al sistema.
                        </p>
                    </div>

                    <form method="POST" action="{{ route('login') }}" class="space-y-5">
                        @csrf

                        {{-- Correo electrónico --}}
                        <div>
                            <label for="email" class="mb-2 block text-sm font-medium text-gray-800">
                                Correo electrónico
                            </label>

                            <div class="relative">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <svg
                                        class="h-5 w-5 text-gray-400"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                        aria-hidden="true"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M16 12H8m8 0a4 4 0 10-8 0m8 0v1a3 3 0 006 0v-1a10 10 0 10-4 8"
                                        ></path>
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
                                    class="block w-full rounded-lg border bg-white py-3 pl-10 pr-3 text-gray-900 placeholder-gray-400 outline-none transition focus:border-yellow-500 focus:ring-2 focus:ring-yellow-200 @error('email') border-red-400 @else border-gray-300 @enderror"
                                >
                            </div>

                            @error('email')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Contraseña --}}
                        <div>
                            <label for="password" class="mb-2 block text-sm font-medium text-gray-800">
                                Contraseña
                            </label>

                            <div class="relative">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <svg
                                        class="h-5 w-5 text-gray-400"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                        aria-hidden="true"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M12 11c1.657 0 3 1.343 3 3v2H9v-2c0-1.657 1.343-3 3-3zm0 0V8a4 4 0 10-8 0v3"
                                        ></path>
                                    </svg>
                                </div>

                                <input
                                    id="password"
                                    name="password"
                                    type="password"
                                    required
                                    autocomplete="current-password"
                                    placeholder="••••••••"
                                    class="block w-full rounded-lg border bg-white py-3 pl-10 pr-3 text-gray-900 placeholder-gray-400 outline-none transition focus:border-yellow-500 focus:ring-2 focus:ring-yellow-200 @error('password') border-red-400 @else border-gray-300 @enderror"
                                >
                            </div>

                            @error('password')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Mantener sesión --}}
                        <label for="remember" class="flex cursor-pointer items-center">
                            <input
                                id="remember"
                                name="remember"
                                type="checkbox"
                                {{ old('remember') ? 'checked' : '' }}
                                class="h-4 w-4 rounded border-gray-300 text-yellow-600 focus:ring-yellow-500"
                            >

                            <span class="ml-2 text-sm text-gray-700">
                                Mantener mi sesión iniciada
                            </span>
                        </label>

                        {{-- Errores generales --}}

                        <button
                            type="submit"
                            class="flex w-full items-center justify-center rounded-lg bg-slate-900 px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-yellow-500 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2"
                        >
                            Iniciar sesión
                        </button>
                    </form>

                    <div class="mt-8 border-t border-yellow-200 pt-5 text-center">
                        <p class="text-xs text-gray-600">
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