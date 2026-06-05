@props([
    'for' => null,
    'help' => null,
])

<label
    @if ($for) for="{{ $for }}" @endif
    {{ $attributes->merge(['class' => 'form-label']) }}
>
    {{ $slot }}
    @if ($help)
        <x-ui.column-help :content="$help" />
    @endif
</label>
