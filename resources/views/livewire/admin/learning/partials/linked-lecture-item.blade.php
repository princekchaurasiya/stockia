@props(['lecture', 'showBatch' => false])

@php
    $lectureAdminUrl = route('admin.learning.lectures.index', array_filter([
        'batch' => $lecture->batch_id ?? null,
        'module' => $lecture->module_id ?? null,
        'lecture' => $lecture->id,
    ]));
    $moduleUrl = $lecture->module_id
        ? route('admin.learning.lectures.index', array_filter([
            'batch' => $lecture->batch_id ?? null,
            'module' => $lecture->module_id,
        ]))
        : null;
@endphp

<div {{ $attributes->merge(['class' => 'linked-lecture-item']) }}>
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-2">
        <div class="min-w-0">
            <a href="{{ $lectureAdminUrl }}" class="fw-medium text-decoration-none">{{ $lecture->title }}</a>
            @if ($moduleUrl)
                <div class="small">
                    <span class="text-muted">Module:</span>
                    <a href="{{ $moduleUrl }}" class="text-muted text-decoration-none">{{ $lecture->module->name }}</a>
                </div>
            @else
                <div class="small text-muted">{{ $lecture->module->name ?? '—' }}</div>
            @endif
        </div>
        <div class="d-flex flex-wrap gap-1">
            <a href="{{ route('admin.learning.videos.index', ['lecture' => $lecture->id, 'batch' => $lecture->batch_id]) }}" class="badge text-bg-light border text-decoration-none">
                {{ $lecture->videos_count ?? 0 }} video{{ ($lecture->videos_count ?? 0) === 1 ? '' : 's' }}
            </a>
            <a href="{{ route('admin.learning.documents.index', ['lecture' => $lecture->id, 'batch' => $lecture->batch_id]) }}" class="badge text-bg-light border text-decoration-none">
                {{ $lecture->documents_count ?? 0 }} doc{{ ($lecture->documents_count ?? 0) === 1 ? '' : 's' }}
            </a>
            <span class="badge {{ $lecture->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">
                {{ $lecture->is_active ? 'Active' : 'Inactive' }}
            </span>
        </div>
    </div>
    @include('livewire.admin.learning.partials.lecture-notes-cell', ['lecture' => $lecture])
</div>
