@props([
    'variant' => 'body',
])

@php
    $classes = match ($variant) {
        'muted' => 'stockia-text-muted',
        'small' => 'stockia-text-small',
        'caption' => 'stockia-text-caption',
        default => '',
    };
@endphp

<span {{ $attributes->merge(['class' => trim($classes)]) }}>
    {{ $slot }}
</span>
