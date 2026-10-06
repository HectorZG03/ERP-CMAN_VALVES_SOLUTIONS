<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>
        @yield('title', 'ERP CMAN')
    </title>


    {{-- Favicon --}}
    <link rel="icon" type="image/png"
          href="{{ asset('favicon.png') }}">


    {{-- Font Awesome --}}
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">


    {{-- Tailwind --}}
    <script src="https://cdn.tailwindcss.com"></script>

    <script>
        tailwind.config = {
            darkMode: 'class'
        };
    </script>


    {{-- Evita parpadeo al cargar el tema --}}
    <script>
        (() => {

            const savedTheme = localStorage.getItem('theme');

            const systemTheme = window.matchMedia(
                '(prefers-color-scheme: dark)'
            ).matches
                ? 'dark'
                : 'light';

            const theme = savedTheme || systemTheme;

            if (theme === 'dark') {
                document.documentElement.classList.add('dark');
            }

        })();
    </script>

</head>


<body class="bg-gray-100 dark:bg-gray-950
             text-gray-900 dark:text-gray-100">


    @auth

        {{-- Sidebar --}}
        @include('layouts.sidebar')


        {{-- Contenido principal --}}
        <div class="ml-64 min-h-screen">


            {{-- Header --}}
            @include('layouts.header')


            {{-- Main --}}
            <main class="p-6">


                {{-- Mensaje de éxito --}}
                @if(session('success'))

                    <div class="mb-6 rounded-lg
                                border border-green-200
                                bg-green-50
                                px-4 py-3
                                text-green-800
                                dark:border-green-800
                                dark:bg-green-900/20
                                dark:text-green-300">

                        <div class="flex items-center gap-3">

                            <i class="fas fa-circle-check"></i>

                            <span>
                                {{ session('success') }}
                            </span>

                        </div>

                    </div>

                @endif


                {{-- Mensaje de error --}}
                @if(session('error'))

                    <div class="mb-6 rounded-lg
                                border border-red-200
                                bg-red-50
                                px-4 py-3
                                text-red-800
                                dark:border-red-800
                                dark:bg-red-900/20
                                dark:text-red-300">

                        <div class="flex items-center gap-3">

                            <i class="fas fa-circle-exclamation"></i>

                            <span>
                                {{ session('error') }}
                            </span>

                        </div>

                    </div>

                @endif


                {{-- Errores de validación --}}
                @if($errors->any())

                    <div class="mb-6 rounded-lg
                                border border-red-200
                                bg-red-50
                                px-4 py-3
                                text-red-800
                                dark:border-red-800
                                dark:bg-red-900/20
                                dark:text-red-300">

                        <div class="flex items-start gap-3">

                            <i class="fas fa-triangle-exclamation mt-1"></i>

                            <div>

                                <p class="font-semibold mb-1">
                                    Hay errores en el formulario:
                                </p>

                                <ul class="list-disc ml-5 space-y-1">

                                    @foreach($errors->all() as $error)

                                        <li>
                                            {{ $error }}
                                        </li>

                                    @endforeach

                                </ul>

                            </div>

                        </div>

                    </div>

                @endif


                {{-- Contenido de cada vista --}}
                @yield('content')


            </main>

        </div>

    @else

        {{-- Vistas públicas / login --}}
        @yield('content')

    @endauth


    {{-- Aviso de nueva actualización --}}
    @include('layouts.update-notification')


    {{-- JavaScript global --}}
    <script src="{{ asset('js/app.js') }}"></script>


    {{-- JavaScript específico de cada vista --}}
    @stack('scripts')


</body>

</html>