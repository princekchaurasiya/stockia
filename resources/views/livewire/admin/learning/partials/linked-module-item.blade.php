@props(['module', 'batchId' => null])

@php
    $moduleUrl = route('admin.learning.lectures.index', array_filter([
        'batch' => $batchId,
        'module' => $module->id,
    ]));
@endphp

<div {{ $attributes->merge(['class' => 'linked-module-item d-flex justify-content-between align-items-start gap-3']) }}>
    <div class="min-w-0">
        <a href="{{ $moduleUrl }}" class="fw-medium text-decoration-none">{{ $module->name }}</a>
        @if ($module->description)
            <div class="small text-muted text-truncate">{{ $module->description }}</div>
        @endif
        @if ($module->timeframe)
            <div class="small text-muted mt-1">{{ $module->timeframe }}</div>
        @endif
    </div>
    <div class="text-end flex-shrink-0">
        <a href="{{ $moduleUrl }}" class="badge text-bg-light border text-decoration-none">
            {{ $module->lectures_count ?? 0 }} lecture{{ ($module->lectures_count ?? 0) === 1 ? '' : 's' }}
        </a>
    </div>
</div>
