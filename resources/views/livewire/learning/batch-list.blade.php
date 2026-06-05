<div>
    <h2 class="h6 mb-3">Batches</h2>
    @if($batches->isEmpty())
        <p class="text-muted small mb-0">No batches yet.</p>
    @else
        <div class="list-group">
            @foreach($batches as $batch)
                <button type="button"
                        wire:click="selectBatch({{ $batch->id }})"
                        class="list-group-item list-group-item-action {{ $selectedBatchId === $batch->id ? 'active' : '' }}">
                    {{ $batch->name }}
                </button>
            @endforeach
        </div>
    @endif
</div>

