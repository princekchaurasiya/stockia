@if ($lecture)
    <div class="learning-lecture-meta">
        @if ($lecture->batch)
            <span class="learning-meta-badge">
                <i class="bi bi-people" aria-hidden="true"></i>
                {{ $lecture->batch->name }}
            </span>
        @endif
        @if ($lecture->module)
            <span class="learning-meta-badge">
                <i class="bi bi-folder2" aria-hidden="true"></i>
                {{ $lecture->module->name }}
            </span>
        @endif
        @if ($lecture->module?->timeframe)
            <span class="learning-meta-badge learning-meta-badge-accent">
                <i class="bi bi-graph-up" aria-hidden="true"></i>
                {{ $lecture->module->timeframe }}
            </span>
        @endif
    </div>
@endif
