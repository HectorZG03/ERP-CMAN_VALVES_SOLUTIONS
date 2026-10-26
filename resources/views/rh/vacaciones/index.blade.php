@extends('layouts.app')

@section('title', 'Vacaciones')

@section('content')
<div class="mx-auto max-w-5xl">
    <section class="overflow-hidden rounded-2xl border border-amber-200 bg-white shadow-sm dark:border-amber-900/50 dark:bg-gray-900">
        <div class="border-b border-amber-100 bg-amber-50/70 px-6 py-5 dark:border-amber-900/40 dark:bg-amber-950/20">
            <p class="text-sm font-semibold uppercase tracking-wider text-amber-700 dark:text-amber-400">
                Recursos Humanos
            </p>

            <h1 class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">
                Módulo de vacaciones
            </h1>

            <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                Estamos preparando este módulo. Próximamente podrás consultar y administrar las vacaciones del personal.
            </p>
        </div>

        <div class="flex min-h-[420px] flex-col items-center justify-center px-6 py-12 text-center">
            <div
                class="construction-scene"
                role="img"
                aria-label="Ilustración animada de construcción"
            >
                <span class="construction-cloud cloud-one">☁️</span>
                <span class="construction-cloud cloud-two">☁️</span>
                <span class="construction-crane">🏗️</span>
                <span class="construction-barrier">🚧</span>
            </div>

            <span class="mt-5 inline-flex items-center gap-2 rounded-full bg-amber-100 px-4 py-2 text-sm font-semibold text-amber-800 dark:bg-amber-900/40 dark:text-amber-300">
                <i class="fas fa-person-digging"></i>
                En construcción
            </span>

            <h2 class="mt-4 text-xl font-bold text-gray-900 dark:text-white">
                Estamos trabajando en esta sección
            </h2>

            <p class="mt-2 max-w-xl text-gray-600 dark:text-gray-400">
                El módulo de vacaciones estará disponible más adelante. Por ahora, esta página es solo informativa.
            </p>

            <a
                href="{{ route('dashboard') }}"
                class="mt-6 inline-flex items-center gap-2 rounded-lg bg-blue-600 px-5 py-3 font-semibold text-white transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900"
            >
                <i class="fas fa-arrow-left"></i>
                Volver al dashboard
            </a>
        </div>
    </section>
</div>

<style>
    .construction-scene {
        position: relative;
        display: flex;
        width: min(100%, 360px);
        height: 180px;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        border: 1px solid rgb(253 230 138);
        border-radius: 1.5rem;
        background: linear-gradient(180deg, #fffbeb 0%, #fef3c7 100%);
    }

    .dark .construction-scene {
        border-color: rgb(120 53 15 / 0.55);
        background: linear-gradient(180deg, #292014 0%, #33230e 100%);
    }

    .construction-crane {
        z-index: 1;
        font-size: 6rem;
        animation: crane-bob 2.4s ease-in-out infinite;
    }

    .construction-barrier {
        position: absolute;
        right: 24px;
        bottom: 18px;
        font-size: 2.5rem;
        animation: barrier-pulse 1.8s ease-in-out infinite;
    }

    .construction-cloud {
        position: absolute;
        font-size: 1.6rem;
        opacity: 0.8;
        animation: cloud-drift 6s ease-in-out infinite alternate;
    }

    .cloud-one {
        top: 24px;
        left: 32px;
    }

    .cloud-two {
        top: 48px;
        right: 48px;
        animation-delay: 0.8s;
    }

    @keyframes crane-bob {
        0%, 100% { transform: translateY(0) rotate(-2deg); }
        50% { transform: translateY(-8px) rotate(2deg); }
    }

    @keyframes barrier-pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.08); }
    }

    @keyframes cloud-drift {
        from { transform: translateX(-5px); }
        to { transform: translateX(8px); }
    }

    @media (prefers-reduced-motion: reduce) {
        .construction-crane,
        .construction-barrier,
        .construction-cloud {
            animation: none;
        }
    }
</style>
@endsection