@props([
    'showActiveFilter' => true,
    'extraFilters' => null,
])

<div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-3">
        <div class="row g-2 align-items-center">
            <div class="col-lg-4 col-md-6">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                    <input type="search" class="form-control" placeholder="Search..." wire:model.live.debounce.300ms="search">
                </div>
            </div>

            @if ($showActiveFilter)
                <div class="col-lg-2 col-md-3">
                    <select class="form-select form-select-sm" wire:model.live="activeFilter">
                        <option value="">All statuses</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
            @endif

            @if ($extraFilters)
                {!! $extraFilters !!}
            @endif

            <div class="col-lg-2 col-md-3">
                <select class="form-select form-select-sm" wire:model.live="perPage">
                    @foreach ([10, 25, 50, 100] as $n)
                        <option value="{{ $n }}">{{ $n }} per page</option>
                    @endforeach
                </select>
            </div>

            <div class="col-lg-auto ms-lg-auto">
                <button type="button" class="btn btn-sm btn-outline-secondary" wire:click="resetFilters">
                    <i class="bi bi-arrow-counterclockwise"></i> Reset filters
                </button>
            </div>
        </div>
    </div>
</div>
