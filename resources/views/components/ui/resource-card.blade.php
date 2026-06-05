@props([
    'title',
    'count' => 0,
    'manageRoute' => null,
    'manageLabel' => 'Manage',
    'empty' => 'Nothing linked yet.',
])

<div {{ $attributes->merge(['class' => 'card border-0 shadow-sm h-100']) }}>
    <div class="card-header bg-white border-bottom d-flex flex-wrap justify-content-between align-items-center gap-2 py-3">
        <span class="fw-semibold">{{ $title }} <span class="text-muted fw-normal">({{ $count }})</span></span>
        @if ($manageRoute)
            <a href="{{ $manageRoute }}" class="btn btn-sm btn-outline-primary">{{ $manageLabel }}</a>
        @endif
    </div>
    <div class="card-body">
        @if ($count > 0)
            {{ $slot }}
        @else
            <p class="small text-muted mb-0">{{ $empty }}</p>
        @endif
    </div>
</div>
