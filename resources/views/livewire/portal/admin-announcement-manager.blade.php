<div>
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card mb-3">
        <div class="card-body">
            <h2 class="h6 mb-3">{{ $announcementId ? 'Edit announcement' : 'Create announcement' }}</h2>
            <form wire:submit.prevent="save" class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Title</label>
                    <input type="text" class="form-control @error('title') is-invalid @enderror" wire:model="title">
                    @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label">Type</label>
                    <select class="form-select" wire:model="type">
                        <option value="general">General</option>
                        <option value="class_update">Class update</option>
                        <option value="market_alert">Market alert</option>
                        <option value="material">New material</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Publish at</label>
                    <input type="datetime-local" class="form-control" wire:model="published_at">
                </div>
                <div class="col-12">
                    <label class="form-label">Body</label>
                    <textarea class="form-control @error('body') is-invalid @enderror" rows="4" wire:model="body"></textarea>
                    @error('body')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-3 d-flex align-items-center gap-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="is_pinned" wire:model="is_pinned">
                        <label class="form-check-label" for="is_pinned">Pinned</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="ann_active" wire:model="is_active">
                        <label class="form-check-label" for="ann_active">Active</label>
                    </div>
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
            <h2 class="h6 mb-3">Announcements</h2>
            <div class="table-responsive">
                <table class="table table-striped align-middle mb-0">
                    <thead>
                    <tr>
                        <th>Title</th>
                        <th>Type</th>
                        <th>Published</th>
                        <th>Pinned</th>
                        <th class="text-end">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($announcements as $item)
                        <tr>
                            <td>{{ $item->title }}</td>
                            <td><span class="badge text-bg-light">{{ $item->type }}</span></td>
                            <td>{{ $item->published_at?->format('d-M-Y H:i') ?? '—' }}</td>
                            <td>{{ $item->is_pinned ? 'Yes' : 'No' }}</td>
                            <td>
                                <span class="badge {{ $item->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">
                                    {{ $item->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="text-end">
                                <button type="button" class="btn btn-sm btn-outline-primary" wire:click="edit({{ $item->id }})">Edit</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" wire:click="toggleActive({{ $item->id }})">Toggle</button>
                                <button type="button" class="btn btn-sm btn-outline-danger" wire:click="delete({{ $item->id }})" wire:confirm="Delete this announcement?">Delete</button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-muted text-center py-3">No announcements yet.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            {{ $announcements->links() }}
        </div>
    </div>
</div>
