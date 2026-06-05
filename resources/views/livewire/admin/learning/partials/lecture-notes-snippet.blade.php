@props(['lecture', 'max' => 80, 'block' => false])

@if ($lecture && filled($lecture->notes))
    @if ($block)
        <div {{ $attributes->merge(['class' => 'small text-muted mt-2']) }}>
            <span class="fw-semibold"><i class="bi bi-journal-text me-1"></i>Lecture notes:</span>
            {{ \Illuminate\Support\Str::limit($lecture->notes, $max) }}
        </div>
    @else
        <div {{ $attributes->merge(['class' => 'small text-muted text-truncate']) }} style="max-width: 280px;" title="{{ $lecture->notes }}">
            <i class="bi bi-journal-text me-1"></i>{{ \Illuminate\Support\Str::limit($lecture->notes, $max) }}
        </div>
    @endif
@elseif ($block)
    <div {{ $attributes->merge(['class' => 'small text-muted mt-2']) }}>No lecture notes yet.</div>
@else
    <span class="text-muted">—</span>
@endif
