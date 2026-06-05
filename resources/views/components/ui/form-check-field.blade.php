@props([
    'id',
    'help' => null,
])

<div {{ $attributes->class(['form-check mb-2']) }}>
    <input {{ $attributes->merge(['class' => 'form-check-input', 'type' => 'checkbox', 'id' => $id]) }}>
    <label class="form-check-label" for="{{ $id }}">
        {{ $slot }}
        @if ($help)
            <x-ui.column-help :content="$help" />
        @endif
    </label>
</div>
