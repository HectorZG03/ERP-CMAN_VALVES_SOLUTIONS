<style>
    .profile-photo-viewer {
        width: min(94vw, 64rem);
        max-width: none;
        max-height: 94vh;
        margin: auto;
        padding: 0;
        overflow: auto;
        border: 0;
        border-radius: 1rem;
        background: #0f172a;
        color: #fff;
        box-shadow: 0 25px 60px rgb(0 0 0 / 40%);
    }

    .profile-photo-viewer::backdrop {
        background: rgb(2 6 23 / 78%);
        backdrop-filter: blur(3px);
    }

    .profile-photo-viewer[open] {
        animation: profile-photo-enter 160ms ease-out;
    }

    .profile-photo-viewer-image {
        width: auto;
        height: auto;
        max-width: 100%;
        max-height: 72vh;
        object-fit: contain;
    }

    @keyframes profile-photo-enter {
        from {
            opacity: 0;
            transform: translateY(8px) scale(.98);
        }

        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .profile-photo-viewer[open] {
            animation: none;
        }
    }
</style>

<dialog
    id="profile-photo-viewer"
    class="profile-photo-viewer"
    aria-labelledby="profile-photo-viewer-title"
    aria-modal="true"
>
    <section>
        <header class="flex items-center justify-between gap-4 border-b border-slate-700 px-4 py-3 sm:px-5">
            <div class="min-w-0">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                    Fotografía
                </p>

                <h2
                    id="profile-photo-viewer-title"
                    class="mt-1 break-words text-base font-semibold text-white"
                >
                    Fotografía del colaborador
                </h2>
            </div>

            <x-button
                id="profile-photo-viewer-close"
                type="button"
                variant="secondary"
                size="sm"
                icon="close"
                autofocus
            >
                Cerrar
            </x-button>
        </header>

        <div
            class="flex min-h-48 items-center justify-center bg-slate-950 p-4 sm:p-6"
            aria-busy="false"
            id="profile-photo-viewer-content"
        >
            <p
                id="profile-photo-viewer-status"
                class="px-4 py-10 text-center text-sm text-slate-300"
                role="status"
                aria-live="polite"
            ></p>

            <img
                id="profile-photo-viewer-image"
                class="profile-photo-viewer-image"
                alt=""
                hidden
            >
        </div>
    </section>
</dialog>