@props([
    'label',
    'description',
    'href',
    'icon' => 'bi-arrow-right',
])

<a href="{{ $href }}" {{ $attributes->merge(['class' => 'settings-map-link text-decoration-none']) }}>
    <div class="card border-0 shadow-sm settings-map-card h-100">
        <div class="card-body d-flex gap-3">
            <span class="settings-map-icon flex-shrink-0">
                <i class="bi {{ $icon }}" aria-hidden="true"></i>
            </span>
            <div class="min-w-0">
                <div class="fw-semibold text-body">{{ $label }}</div>
                <p class="small text-muted mb-0">{{ $description }}</p>
            </div>
            <i class="bi bi-chevron-right text-muted ms-auto align-self-center flex-shrink-0" aria-hidden="true"></i>
        </div>
    </div>
</a>
