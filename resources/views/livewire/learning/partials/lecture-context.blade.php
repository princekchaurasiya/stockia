@props(['lecture'])

@if ($lecture)
    <p class="small text-muted mb-0">
        {{ $lecture->batch->name ?? '—' }} · {{ $lecture->module->name ?? '—' }}
    </p>
@endif
