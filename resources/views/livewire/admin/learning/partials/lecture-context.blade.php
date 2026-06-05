@props(['lecture', 'linkBatch' => false, 'linkModule' => false])

@if ($lecture)
    <div class="small text-muted">
        @if ($linkBatch && $lecture->batch)
            <a href="{{ route('admin.learning.lectures.index', ['batch' => $lecture->batch_id]) }}" class="text-muted text-decoration-none">{{ $lecture->batch->name }}</a>
        @else
            {{ $lecture->batch->name ?? '—' }}
        @endif
        ·
        @if ($linkModule && $lecture->module)
            <a href="{{ route('admin.learning.lectures.index', ['module' => $lecture->module_id]) }}" class="text-muted text-decoration-none">{{ $lecture->module->name }}</a>
        @else
            {{ $lecture->module->name ?? '—' }}
        @endif
    </div>
@endif
