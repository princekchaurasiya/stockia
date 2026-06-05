@props([
    'title' => '',
    'content',
])

<button type="button"
        {{ $attributes->merge(['class' => 'btn btn-link btn-sm p-0 ms-1 text-muted align-baseline column-help-popover']) }}
        data-bs-placement="top"
        data-bs-custom-class="column-help-popover-panel"
        @if ($title !== '') data-bs-title="{{ $title }}" @endif
        data-bs-content="{{ $content }}"
        aria-label="Help"
        aria-expanded="false">
    <i class="bi bi-info-circle" aria-hidden="true"></i>
</button>
