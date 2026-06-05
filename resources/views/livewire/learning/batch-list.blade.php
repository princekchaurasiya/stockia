<div>
    <div class="learning-hub-section">
        <div class="learning-hub-section-head">
            <h2 class="learning-hub-section-title">Your batch</h2>
            <p class="learning-hub-section-meta mb-0">Choose the cohort you are enrolled in.</p>
        </div>

        @if($batches->isEmpty())
            <div class="learning-hub-empty">
                <i class="bi bi-collection" aria-hidden="true"></i>
                <p class="mb-0">No batches available yet.</p>
            </div>
        @else
            <div class="learning-batch-pills">
                @foreach($batches as $batch)
                    <button type="button"
                            wire:click="selectBatch({{ $batch->id }})"
                            wire:key="batch-{{ $batch->id }}"
                            class="learning-batch-pill {{ $selectedBatchId === $batch->id ? 'is-active' : '' }}">
                        <i class="bi bi-people-fill" aria-hidden="true"></i>
                        <span>{{ $batch->name }}</span>
                    </button>
                @endforeach
            </div>
        @endif
    </div>
</div>
