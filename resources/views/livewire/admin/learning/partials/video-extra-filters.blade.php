@include('livewire.admin.learning.partials.learning-hierarchy-filters')
<div class="col-lg-2 col-md-3">
    <select class="form-select form-select-sm" wire:model.live="videoTypeFilter">
        <option value="">All types</option>
        @foreach ($videoTypes as $type)
            <option value="{{ $type }}">{{ $type }}</option>
        @endforeach
    </select>
</div>
