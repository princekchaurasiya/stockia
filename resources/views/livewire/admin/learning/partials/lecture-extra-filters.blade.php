<div class="col-lg-2 col-md-3">
    <select class="form-select form-select-sm" wire:model.live="batchFilter">
        <option value="">All batches</option>
        @foreach ($batches as $batch)
            <option value="{{ $batch->id }}">{{ $batch->name }}</option>
        @endforeach
    </select>
</div>
<div class="col-lg-2 col-md-3">
    <select class="form-select form-select-sm" wire:model.live="moduleFilter">
        <option value="">All modules</option>
        @foreach ($modules as $module)
            <option value="{{ $module->id }}">{{ $module->name }}</option>
        @endforeach
    </select>
</div>
