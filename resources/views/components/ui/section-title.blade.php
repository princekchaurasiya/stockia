@props([
    'tag' => 'h2',
])

<{{ $tag }} {{ $attributes->merge(['class' => 'stockia-section-title']) }}>
    {{ $slot }}
</{{ $tag }}>
