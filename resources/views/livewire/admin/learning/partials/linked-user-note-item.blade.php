@props(['note'])

@php
    $lectureAdminUrl = $note->lecture
        ? route('admin.learning.lectures.index', array_filter([
            'batch' => $note->lecture->batch_id ?? null,
            'module' => $note->lecture->module_id ?? null,
            'lecture' => $note->lecture_id,
        ]))
        : null;
@endphp

<div {{ $attributes->merge(['class' => 'linked-user-note-item']) }}>
    <div class="d-flex justify-content-between align-items-start gap-2">
        <div class="min-w-0">
            <div class="small text-muted mb-1">Student note</div>
            @if ($lectureAdminUrl)
                <a href="{{ $lectureAdminUrl }}" class="fw-medium text-decoration-none text-truncate d-block">{{ $note->title }}</a>
            @else
                <div class="fw-medium text-truncate">{{ $note->title }}</div>
            @endif
            <div class="small text-muted">
                {{ $note->user->name ?? 'User' }}
                · {{ $note->updated_at?->format('d M Y') }}
                @if ($note->is_shared)
                    · <span class="badge text-bg-primary">Shared</span>
                @endif
            </div>
            <div class="small text-muted text-truncate mt-1" title="{{ $note->body }}">
                {{ \Illuminate\Support\Str::limit($note->body, 120) }}
            </div>
        </div>
    </div>
</div>
