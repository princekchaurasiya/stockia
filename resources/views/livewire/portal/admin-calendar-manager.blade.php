<div>
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card mb-3">
        <div class="card-body">
            <h2 class="h6 mb-3">{{ $eventId ? 'Edit event' : 'Add event' }}</h2>
            <form wire:submit.prevent="save" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Title</label>
                    <input type="text" class="form-control @error('title') is-invalid @enderror" wire:model="title">
                    @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label">Date</label>
                    <input type="date" class="form-control @error('event_date') is-invalid @enderror" wire:model="event_date">
                    @error('event_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label">Type</label>
                    <select class="form-select" wire:model="event_type">
                        <option value="expiry">Expiry</option>
                        <option value="rbi">RBI</option>
                        <option value="results">Results</option>
                        <option value="holiday">Holiday</option>
                        <option value="custom">Custom</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="cal_active" wire:model="is_active">
                        <label class="form-check-label" for="cal_active">Active</label>
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
            <div class="table-responsive">
                <table class="table table-striped align-middle mb-0">
                    <thead>
                    <tr>
                        <th>Date</th>
                        <th>Title</th>
                        <th>Type</th>
                        <th class="text-end">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($events as $event)
                        <tr>
                            <td>{{ $event->event_date->format('d-M-Y') }}</td>
                            <td>{{ $event->title }}</td>
                            <td><span class="badge text-bg-light">{{ $event->event_type }}</span></td>
                            <td class="text-end">
                                <button type="button" class="btn btn-sm btn-outline-primary" wire:click="edit({{ $event->id }})">Edit</button>
                                <button type="button" class="btn btn-sm btn-outline-danger" wire:click="delete({{ $event->id }})" wire:confirm="Delete this event?">Delete</button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-muted text-center py-3">No events yet.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            {{ $events->links() }}
        </div>
    </div>
</div>
