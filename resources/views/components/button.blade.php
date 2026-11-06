@props([
    'href' => null,
    'variant' => 'primary',
    'size' => 'md',
    'icon' => 'action',
    'type' => 'button',
    'disabled' => false,
    'iconOnly' => false,
])

@php
    $variants = [
        'primary' => 'bg-slate-900 text-white hover:bg-slate-700 focus-visible:ring-slate-500 dark:bg-slate-700 dark:hover:bg-slate-600',
        'secondary' => 'border border-slate-300 bg-white text-slate-700 hover:bg-slate-50 focus-visible:ring-slate-400 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700',
        'accent' => 'bg-amber-400 text-slate-950 hover:bg-amber-300 focus-visible:ring-amber-500',
        'danger' => 'bg-red-600 text-white hover:bg-red-700 focus-visible:ring-red-500',
        'success' => 'bg-emerald-600 text-white hover:bg-emerald-700 focus-visible:ring-emerald-500',
        'warning' => 'bg-amber-500 text-white hover:bg-amber-600 focus-visible:ring-amber-500',
        'ghost' => 'text-slate-700 hover:bg-slate-100 focus-visible:ring-slate-400 dark:text-slate-200 dark:hover:bg-slate-700',
    ];

    $sizes = [
        'sm' => 'min-h-8 gap-1.5 rounded-md px-2.5 py-1.5 text-xs',
        'md' => 'min-h-10 gap-2 rounded-lg px-4 py-2.5 text-sm',
        'lg' => 'min-h-11 gap-2 rounded-lg px-5 py-3 text-sm',
    ];

    $icons = [
        'add' => 'M12 5v14m-7-7h14',
        'save' => 'M17 21v-8H7v8M7 3v5h10V3M5 21h14a2 2 0 002-2V7l-4-4H5a2 2 0 00-2 2v14a2 2 0 002 2z',
        'edit' => 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-7.5-3.5l6-6a2.12 2.12 0 013 3l-6 6L9 14l1.5-4.5z',
        'delete' => 'M3 6h18m-2 0-.9 14H5.9L5 6m4 0V4h6v2m-5 4v6m4-6v6',
        'view' => 'M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12zm10-3a3 3 0 100 6 3 3 0 000-6z',
        'download' => 'M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4m4-5l5 5 5-5m-5 5V3',
        'back' => 'M19 12H5m7 7-7-7 7-7',
        'search' => 'M21 21l-4.3-4.3m1.3-5.2a6.5 6.5 0 11-13 0 6.5 6.5 0 0113 0z',
        'check' => 'M5 12l4 4L19 6',
        'close' => 'M18 6L6 18M6 6l12 12',
        'filter' => 'M4 6h16M7 12h10m-7 6h4',
        'refresh' => 'M20 7v5h-5M4 17v-5h5m-3.2-3.2A7 7 0 0118 7l2 5M4 12l2 5a7 7 0 0012.2 1.2',
        'action' => 'M5 12h14m-7-7l7 7-7 7',
    ];

    $buttonClasses = 'inline-flex shrink-0 items-center justify-center whitespace-nowrap font-semibold leading-none transition focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 ' .
        ($sizes[$size] ?? $sizes['md']) . ' ' .
        ($variants[$variant] ?? $variants['primary']) .
        ($iconOnly ? ' !h-8 !w-8 !min-h-8 !p-0' : '');
    $iconPath = $icons[$icon] ?? $icons['action'];
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->class($buttonClasses) }}>
        <svg aria-hidden="true" class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <path d="{{ $iconPath }}" />
        </svg>
        @unless($iconOnly)<span data-button-label>{{ $slot }}</span>@endunless
    </a>
@else
    <button type="{{ $type }}" @disabled($disabled) {{ $attributes->class($buttonClasses) }}>
        <svg aria-hidden="true" class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <path d="{{ $iconPath }}" />
        </svg>
        @unless($iconOnly)<span data-button-label>{{ $slot }}</span>@endunless
    </button>
@endif
