@extends('layouts.app')

@section('content')



<div class="flex items-center justify-center py-4 px-4 sm:px-6 lg:px-8 min-h-[60vh]">
    <div class="max-w-md w-full space-y-8">
                <div class="text-center">
            <div class="flex justify-center mb-6">
                <img src="{{ asset('img/logo/logo.png') }}" 
                     alt="Logo" 
                     class="h-16 w-auto">
            </div>
            <h2 class="text-3xl font-bold text-gray-900">
                Iniciar Sesión
            </h2>
            <p class="mt-2 text-sm text-gray-600">
                Accede a tu panel de control
            </p>
        </div>

        <!-- Formulario -->
        <div class="bg-white py-8 px-6 shadow-sm rounded-lg border">
            <form class="space-y-6" method="POST" action="{{ route('login') }}">
                @csrf
                
                <!-- Campo Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        Correo electrónico
                    </label>
                    <input id="email" 
                           name="email" 
                           type="email" 
                           required 
                           autofocus
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 
                                  focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 
                                  transition-colors duration-200 @error('email') border-red-300 @enderror"
                           placeholder="tu@email.com" 
                           value="{{ old('email') }}">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Campo Contraseña -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        Contraseña
                    </label>
                    <input id="password" 
                           name="password" 
                           type="password" 
                           required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400
                                  focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 
                                  transition-colors duration-200 @error('password') border-red-300 @enderror"
                           placeholder="••••••••">
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Recordarme (opcional) -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input id="remember" 
                               name="remember" 
                               type="checkbox" 
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                               {{ old('remember') ? 'checked' : '' }}>
                        <label for="remember" class="ml-2 block text-sm text-gray-700">
                            Recordarme
                        </label>
                    </div>
                </div>

                <!-- Botón de Login -->
                <div>
                    <button type="submit" 
                            class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-md 
                                   shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 
                                   focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 
                                   transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                        Iniciar Sesión
                    </button>
                </div>
            </form>
        </div>

        <!-- Errores generales -->
        @if ($errors->any() && !$errors->has('email') && !$errors->has('password'))
        <div class="bg-red-50 border border-red-200 rounded-md p-4">
            <div class="flex">
                <svg class="w-5 h-5 text-red-400 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">
                        Error de autenticación
                    </h3>
                    <div class="mt-1 text-sm text-red-700">
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Footer opcional -->
        <div class="text-center">
            <p class="text-xs text-gray-500">
                © {{ date('Y') }} {{ config('app.name', 'ERP System') }}. 
                Todos los derechos reservados.
            </p>
        </div>
    </div>
</div>

@endsection