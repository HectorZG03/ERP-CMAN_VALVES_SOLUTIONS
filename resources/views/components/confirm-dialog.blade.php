<div
    id="erp-confirm-dialog"
    class="fixed inset-0 z-[100] hidden items-center justify-center bg-slate-950/50 p-4 opacity-0 transition-opacity duration-150 motion-reduce:duration-0"
    aria-hidden="true"
>
    <section
        id="erp-confirm-panel"
        role="alertdialog"
        aria-modal="true"
        aria-labelledby="erp-confirm-title"
        aria-describedby="erp-confirm-message"
        tabindex="-1"
        class="w-full max-w-md translate-y-2 scale-95 rounded-2xl border border-slate-200 bg-white p-6 opacity-0 shadow-2xl transition duration-150 motion-reduce:duration-0 dark:border-slate-700 dark:bg-slate-900"
    >
        <div class="flex items-start gap-4">
            <div id="erp-confirm-icon" class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300">
                <svg aria-hidden="true" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 9v4m0 4h.01M10.3 3.9 2.7 17a2 2 0 0 0 1.7 3h15.2a2 2 0 0 0 1.7-3L13.7 3.9a2 2 0 0 0-3.4 0Z" />
                </svg>
            </div>

            <div class="min-w-0 flex-1">
                <h2 id="erp-confirm-title" class="text-lg font-bold text-slate-900 dark:text-white">Confirmar acción</h2>
                <p id="erp-confirm-message" class="mt-2 whitespace-pre-line text-sm leading-6 text-slate-600 dark:text-slate-300"></p>
            </div>
        </div>

        <div class="mt-6 flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
            <x-button id="erp-confirm-cancel" type="button" variant="secondary" icon="close">Cancelar</x-button>
            <x-button id="erp-confirm-accept" type="button" variant="warning" icon="check">Confirmar</x-button>
        </div>
    </section>
</div>
