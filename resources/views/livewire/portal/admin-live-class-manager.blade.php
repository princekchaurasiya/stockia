<div>
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card mb-3">
        <div class="card-body">
            <h2 class="h6 mb-3">{{ $liveClassId ? 'Edit live class' : 'Schedule live class' }}</h2>
            <form wire:submit.prevent="save" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Title</label>
                    <input type="text" class="form-control @error('title') is-invalid @enderror" wire:model="title">
                    @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Batch (optional)</label>
                    <select class="form-select" wire:model="batch_id">
                        <option value="">— None —</option>
                        @foreach($batches as $batch)
                            <option value="{{ $batch->id }}">{{ $batch->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Meeting URL</label>
                    <input type="url" class="form-control @error('meeting_url') is-invalid @enderror" wire:model="meeting_url" placeholder="https://zoom.us/...">
                    @error('meeting_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Scheduled at</label>
                    <input type="datetime-local" class="form-control @error('scheduled_at') is-invalid @enderror" wire:model="scheduled_at">
                    @error('scheduled_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-2">
                    <label class="form-label">Duration (min)</label>
                    <input type="number" class="form-control" wire:model="duration_minutes" min="15">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select class="form-select" wire:model="status">
                        <option value="scheduled">Scheduled</option>
                        <option value="live">Live</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="lc_active" wire:model="is_active">
                        <label class="form-check-label" for="lc_active">Active</label>
                    </div>
                </div>
                <div class="col-12">
                    <label class="form-label">Description</label>
                    <textarea class="form-control" rows="2" wire:model="description"></textarea>
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
            <h2 class="h6 mb-3">Live classes</h2>
            <div class="table-responsive">
                <table class="table table-striped align-middle mb-0">
                    <thead>
                    <tr>
                        <th>Title</th>
                        <th>Batch</th>
                        <th>When</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($liveClasses as $lc)
                        <tr>
                            <td>{{ $lc->title }}</td>
                            <td>{{ $lc->batch?->name ?? '—' }}</td>
                            <td>{{ $lc->scheduled_at->format('d-M-Y H:i') }}</td>
                            <td><span class="badge text-bg-light">{{ $lc->status }}</span></td>
                            <td class="text-end">
                                <a href="{{ $lc->meeting_url }}" target="_blank" class="btn btn-sm btn-outline-success">Link</a>
                                <button type="button" class="btn btn-sm btn-outline-primary" wire:click="edit({{ $lc->id }})">Edit</button>
                                <button type="button" class="btn btn-sm btn-outline-danger" wire:click="delete({{ $lc->id }})" wire:confirm="Delete this live class?">Delete</button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-muted text-center py-3">No live classes scheduled.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            {{ $liveClasses->links() }}
        </div>
    </div>
</div>
