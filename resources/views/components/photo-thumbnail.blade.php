@props([
    'src' => null,
    'name' => '',
    'imageClass' => 'h-10 w-10 rounded-full',
    'loading' => 'lazy',
])

@php
    $defaultAvatar = asset('images/default-avatar.svg');
    $source = trim((string) $src);

    if ($source === '' || strtoupper($source) === 'N/A') {
        $source = $defaultAvatar;
    }

    $canPreview = $source !== $defaultAvatar;
    $description = $name !== '' ? 'Fotografía de ' . $name : 'Fotografía';
@endphp

@if($canPreview)
    <button
        type="button"
        {{ $attributes->class([
            'inline-flex shrink-0 cursor-zoom-in rounded-lg p-0 leading-none',
            'focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2',
        ]) }}
        data-photo-preview
        data-photo-src="{{ $source }}"
        data-photo-name="{{ $name }}"
        aria-label="Ampliar {{ $description }}"
        title="Ver fotografía completa"
    >
        <img
            src="{{ $source }}"
            alt="{{ $description }}"
            class="block object-cover {{ $imageClass }}"
            loading="{{ $loading }}"
        >
    </button>
@else
    <img
        src="{{ $defaultAvatar }}"
        alt="{{ $description }}"
        class="block shrink-0 object-cover {{ $imageClass }}"
        loading="{{ $loading }}"
    >
@endif