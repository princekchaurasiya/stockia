@props([
    'lectureNotesCount' => 0,
    'linkedNotesCount' => 0,
    'filterRoute' => null,
])

@if ($lectureNotesCount > 0 || $linkedNotesCount > 0)
    <div class="d-flex flex-column gap-1">
        @if ($lectureNotesCount > 0)
            <a href="{{ $filterRoute }}" class="badge text-bg-light border text-decoration-none align-self-start">
                {{ $lectureNotesCount }} lecture note{{ $lectureNotesCount === 1 ? '' : 's' }}
            </a>
        @endif
        @if ($linkedNotesCount > 0)
            <a href="{{ $filterRoute }}" class="badge text-bg-light border text-decoration-none align-self-start">
                {{ $linkedNotesCount }} linked note{{ $linkedNotesCount === 1 ? '' : 's' }}
            </a>
        @endif
    </div>
@else
    <span class="text-muted">—</span>
@endif
