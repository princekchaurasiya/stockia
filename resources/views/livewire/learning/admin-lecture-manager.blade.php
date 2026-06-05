<div>
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card mb-3">
        <div class="card-body">
            <h2 class="h6 mb-3">{{ $lectureId ? 'Edit lecture' : 'Create lecture' }}</h2>

            <form wire:submit.prevent="save" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Batch</label>
                    <livewire:inputs.batch-select wire:model.live="batch_id" name="batch_id" />
                    @error('batch_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-3">
                    <label class="form-label">Module</label>
                    <livewire:inputs.module-select wire:model.live="module_id" name="module_id" />
                    @error('module_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Lecture title</label>
                    <input type="text" class="form-control @error('title') is-invalid @enderror" wire:model="title">
                    @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-3">
                    <label class="form-label">Sort order</label>
                    <input type="number" class="form-control @error('sort_order') is-invalid @enderror" wire:model="sort_order" min="0">
                    @error('sort_order')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-3 d-flex align-items-center">
                    <div class="form-check mt-3">
                        <input class="form-check-input" type="checkbox" id="is_active" wire:model="is_active">
                        <label class="form-check-label" for="is_active">Active</label>
                    </div>
                </div>

                <div class="col-12">
                    <label class="form-label">Notes</label>
                    <textarea class="form-control @error('notes') is-invalid @enderror" rows="3" wire:model="notes"></textarea>
                    @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-12 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Save</button>
                    <button type="button" class="btn btn-outline-secondary" wire:click="createNew">Clear</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h2 class="h6 mb-3">Lectures</h2>
            <div class="table-responsive">
                <table class="table table-striped align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Batch</th>
                            <th>Module</th>
                            <th>Title</th>
                            <th>Active</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($lectures as $lecture)
                            <tr>
                                <td>{{ $lecture->batch->name ?? '-' }}</td>
                                <td>{{ $lecture->module->name ?? '-' }}</td>
                                <td>{{ $lecture->title }}</td>
                                <td>
                                    <span class="badge {{ $lecture->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">
                                        {{ $lecture->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <button type="button" class="btn btn-sm btn-outline-primary" wire:click="edit({{ $lecture->id }})">
                                        Edit
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" wire:click="toggleActive({{ $lecture->id }})">
                                        Toggle
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-danger"
                                            wire:click="deleteLecture({{ $lecture->id }})"
                                            onclick="return confirm('Delete this lecture and its videos?');">
                                        Delete
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-muted text-center py-3">No lectures yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-2">
                {{ $lectures->links() }}
            </div>
        </div>
    </div>

    <livewire:inputs.create-batch-modal />
    <livewire:inputs.create-module-modal />
</div>

