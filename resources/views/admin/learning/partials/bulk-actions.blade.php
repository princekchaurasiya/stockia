@if (count($selectedIds) > 0)
    <div class="alert alert-light border d-flex flex-wrap align-items-center justify-content-between gap-2 py-2 mb-3">
        <span class="small mb-0"><strong>{{ count($selectedIds) }}</strong> selected</span>
        <div class="d-flex flex-wrap gap-2">
            <button type="button" class="btn btn-sm btn-outline-success" wire:click="bulkActivate">
                Activate selected
            </button>
            <button type="button" class="btn btn-sm btn-outline-secondary" wire:click="bulkDeactivate">
                Deactivate selected
            </button>
            <button type="button" class="btn btn-sm btn-outline-danger" wire:click="bulkDelete" wire:confirm="Delete selected records?">
                Delete selected
            </button>
        </div>
    </div>
@endif
