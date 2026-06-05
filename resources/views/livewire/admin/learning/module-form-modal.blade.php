<div>
    @if ($show)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,.45);">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ $moduleId ? 'Edit module' : 'Create module' }}</h5>
                        <button type="button" class="btn-close" wire:click="close"></button>
                    </div>
                    <form wire:submit.prevent="save">
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <x-ui.form-label :help="\App\Support\FieldHelp::moduleName()">Name</x-ui.form-label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" wire:model="name" placeholder="e.g. lecture 5, Chart patterns">
                                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <x-ui.form-label-trading-style />
                                    <input type="text" class="form-control @error('timeframe') is-invalid @enderror" wire:model="timeframe" placeholder="e.g. Intraday — 1 min, 5 min">
                                    @error('timeframe')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-4">
                                    <x-ui.form-label-sort-order context="modules" />
                                    <input type="number" class="form-control @error('sort_order') is-invalid @enderror" wire:model="sort_order" min="0">
                                    @error('sort_order')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-4 d-flex align-items-end">
                                    <x-ui.form-check-field id="module_modal_active" wire:model="is_active" :help="\App\Support\FieldHelp::active('module')">
                                        Active
                                    </x-ui.form-check-field>
                                </div>
                                <div class="col-12">
                                    <x-ui.form-label :help="\App\Support\FieldHelp::moduleDescription()">Description</x-ui.form-label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" rows="3" wire:model="description" placeholder="Optional summary of this topic"></textarea>
                                    @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" wire:click="close">Cancel</button>
                            <button type="submit" class="btn btn-primary">{{ $moduleId ? 'Save changes' : 'Create module' }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
