<div>
    @if ($show)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,.45);">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ $batchId ? 'Edit batch' : 'Create batch' }}</h5>
                        <button type="button" class="btn-close" wire:click="close"></button>
                    </div>
                    <form wire:submit.prevent="save">
                        <div class="modal-body">
                            <div class="mb-3">
                                <x-ui.form-label :help="\App\Support\FieldHelp::batchName()">Name</x-ui.form-label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" wire:model="name" placeholder="e.g. Feb batch" autofocus>
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <x-ui.form-check-field id="batch_modal_active" wire:model="is_active" :help="\App\Support\FieldHelp::active('batch')">
                                Active
                            </x-ui.form-check-field>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" wire:click="close">Cancel</button>
                            <button type="submit" class="btn btn-primary">{{ $batchId ? 'Save changes' : 'Create batch' }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
