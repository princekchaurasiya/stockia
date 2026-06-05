<div>
    @if($show)
        <div class="modal fade show d-block" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add module</h5>
                        <button type="button" class="btn-close" wire:click="$set('show', false)"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <x-ui.form-label :help="\App\Support\FieldHelp::moduleName()">Name</x-ui.form-label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" wire:model="name" placeholder="e.g. lecture 5">
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <x-ui.form-label-trading-style />
                            <input type="text" class="form-control @error('timeframe') is-invalid @enderror" wire:model="timeframe" placeholder="e.g. Intraday — 1 min, 5 min">
                            @error('timeframe')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <x-ui.form-label :help="\App\Support\FieldHelp::moduleDescription()">Description</x-ui.form-label>
                            <textarea class="form-control @error('description') is-invalid @enderror" rows="2" wire:model="description"></textarea>
                            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <x-ui.form-label-sort-order context="modules" />
                            <input type="number" class="form-control @error('sort_order') is-invalid @enderror" wire:model="sort_order" min="0">
                            @error('sort_order')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <x-ui.form-check-field id="module_is_active" wire:model="is_active" :help="\App\Support\FieldHelp::active('module')">
                            Active
                        </x-ui.form-check-field>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="$set('show', false)">Cancel</button>
                        <button type="button" class="btn btn-primary" wire:click="save">Save</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-backdrop fade show"></div>
    @endif
</div>
