@props(['lecture', 'max' => 60])

@php
    $lectureAdminUrl = route('admin.learning.lectures.index', array_filter([
        'batch' => $lecture->batch_id ?? null,
        'module' => $lecture->module_id ?? null,
        'lecture' => $lecture->id,
    ]));
@endphp

@if ($lecture)
    @if (filled($lecture->notes))
        <div class="small mb-1" style="max-width: 420px;">
            <span class="text-muted">Lecture notes:</span>
            <a href="{{ $lectureAdminUrl }}" class="text-decoration-none ms-1" title="{{ $lecture->notes }}">
                {{ \Illuminate\Support\Str::limit($lecture->notes, $max) }}
            </a>
        </div>
    @endif

    @if (($lecture->user_notes_count ?? 0) > 0)
        <div class="small">
            @foreach ($lecture->userNotes->take(2) as $note)
                <div class="mb-1" style="max-width: 420px;">
                    <span class="text-muted">Student note:</span>
                    <a href="{{ $lectureAdminUrl }}" class="text-decoration-none fw-medium ms-1" title="{{ $note->body }}">
                        <i class="bi bi-sticky me-1"></i>{{ $note->title }}
                    </a>
                    @if ($note->is_shared)
                        <span class="badge text-bg-primary ms-1">Shared</span>
                    @endif
                </div>
            @endforeach
            @if ($lecture->user_notes_count > 2)
                <a href="{{ $lectureAdminUrl }}" class="text-muted text-decoration-none">
                    +{{ $lecture->user_notes_count - 2 }} more student note{{ $lecture->user_notes_count - 2 === 1 ? '' : 's' }}
                </a>
            @endif
        </div>
    @elseif (! filled($lecture->notes))
        <span class="text-muted">—</span>
    @endif
@else
    <span class="text-muted">—</span>
@endif
