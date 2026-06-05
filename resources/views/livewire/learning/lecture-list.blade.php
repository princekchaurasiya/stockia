<div>
    <h2 class="h6 mb-3">Lectures</h2>

    @if(!$batchId)
        <p class="text-muted small mb-0">Select a batch to see lectures.</p>
        @return
    @endif

    @foreach($modules as $module)
        @php
            $lectures = $lecturesByModule[$module->id] ?? collect();
        @endphp
        @if($lectures->isNotEmpty())
            <div class="mb-3">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <strong>{{ $module->name }}</strong>
                    @if ($module->timeframe)
                        <span class="badge text-bg-light">{{ $module->timeframe }}</span>
                    @endif
                </div>
                <ul class="list-group list-group-flush">
                    @foreach($lectures as $lecture)
                        <li class="list-group-item px-0 py-1">
                            <button type="button"
                                    class="btn btn-link p-0"
                                    wire:click="selectLecture({{ $lecture->id }})">
                                {{ $lecture->title }}
                            </button>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
    @endforeach
</div>

