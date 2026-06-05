<div class="d-flex flex-wrap align-items-center gap-3 mb-3">
    <div class="d-flex align-items-center gap-2">
        <label for="per-page" class="form-label mb-0 text-muted small">Show</label>
        <select id="per-page" wire:model.live="perPage" class="form-select form-select-sm w-auto">
            @foreach($perPageOptions as $size)
                <option value="{{ $size }}">{{ $size }}</option>
            @endforeach
        </select>
    </div>
    <div class="flex-grow-1">
        <input type="text"
               wire:model.live.debounce.300ms="search"
               class="form-control form-control-sm"
               placeholder="Search…"
               autocomplete="off">
    </div>
</div>
