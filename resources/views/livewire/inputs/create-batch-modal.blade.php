<div>
    @if($show)
        <div class="modal fade show d-block" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add batch</h5>
                        <button type="button" class="btn-close" wire:click="$set('show', false)"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <x-ui.form-label :help="\App\Support\FieldHelp::batchName()">Name</x-ui.form-label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" wire:model="name" placeholder="e.g. Feb batch">
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <x-ui.form-check-field id="batch_is_active" wire:model="is_active" :help="\App\Support\FieldHelp::active('batch')">
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
