<div class="card mb-3">
    <div class="card-body">
        <h2 class="h6 mb-3">Batches</h2>

        @if (session('success_batch'))
            <div class="alert alert-success">{{ session('success_batch') }}</div>
        @endif

        <form wire:submit.prevent="save" class="row g-2 mb-3">
            <div class="col-md-4">
                <x-ui.form-label :help="\App\Support\FieldHelp::batchName()">Name</x-ui.form-label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" wire:model="name" placeholder="e.g. Feb batch">
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <x-ui.form-check-field id="batch_manager_active" wire:model="is_active" :help="\App\Support\FieldHelp::active('batch')">
                    Active
                </x-ui.form-check-field>
            </div>
            <div class="col-md-5 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-primary">{{ $batchId ? 'Update' : 'Add' }} batch</button>
                <button type="button" class="btn btn-outline-secondary" wire:click="createNew">Clear</button>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-striped align-middle mb-0">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Active</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($batches as $batch)
                        <tr>
                            <td>{{ $batch->name }}</td>
                            <td>
                                <span class="badge {{ $batch->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">
                                    {{ $batch->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="text-end">
                                <button type="button" class="btn btn-sm btn-outline-primary" wire:click="edit({{ $batch->id }})">Edit</button>
                                <button type="button" class="btn btn-sm btn-outline-danger" wire:click="delete({{ $batch->id }})" onclick="return confirm('Delete this batch and its lectures/videos?');">Delete</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-muted text-center py-3">No batches yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-2">
            {{ $batches->links() }}
        </div>
    </div>
</div>
