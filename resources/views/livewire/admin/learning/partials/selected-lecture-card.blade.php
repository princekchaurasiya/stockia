@props(['lecture', 'allLecturesRoute' => null, 'manageRoute' => null, 'manageLabel' => 'Manage'])

@if ($lecture)
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body py-3 d-flex flex-wrap justify-content-between align-items-start gap-2">
            <div>
                <div class="small text-muted text-uppercase fw-semibold">Filtered lecture</div>
                <div class="fw-medium">{{ $lecture->title }}</div>
                @include('livewire.admin.learning.partials.lecture-context', [
                    'lecture' => $lecture,
                    'linkBatch' => true,
                    'linkModule' => true,
                ])
                @include('livewire.admin.learning.partials.lecture-notes-snippet', [
                    'lecture' => $lecture,
                    'max' => 200,
                    'block' => true,
                ])
            </div>
            <div class="d-flex flex-wrap gap-2">
                @if ($allLecturesRoute)
                    <a href="{{ $allLecturesRoute }}" class="btn btn-sm btn-outline-secondary">All lectures</a>
                @endif
                @if ($manageRoute)
                    <a href="{{ $manageRoute }}" class="btn btn-sm btn-outline-primary">{{ $manageLabel }}</a>
                @endif
            </div>
        </div>
    </div>
@endif
