@props([
    'id',
    'active',
])

<button type="button"
        {{ $attributes->merge(['class' => 'btn btn-sm btn-outline-'.($active ? 'secondary' : 'success')]) }}
        wire:click="toggleActive({{ $id }})">
    {{ $active ? 'Deactivate' : 'Activate' }}
</button>
