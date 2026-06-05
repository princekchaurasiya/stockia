@props(['lecture', 'inline' => false])

@if ($lecture)
    @if ($inline)
        <span class="text-muted">{{ $lecture->batch->name ?? '—' }} · {{ $lecture->module->name ?? '—' }} · {{ $lecture->title }}</span>
    @else
        <p class="small text-muted mb-0">{{ $lecture->batch->name ?? '—' }} · {{ $lecture->module->name ?? '—' }} · {{ $lecture->title }}</p>
    @endif
@endif
