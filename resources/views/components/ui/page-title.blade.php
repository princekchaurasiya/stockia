@props([
    'tag' => 'h1',
])

<{{ $tag }} {{ $attributes->merge(['class' => 'stockia-page-title']) }}>
    {{ $slot }}
</{{ $tag }}>
