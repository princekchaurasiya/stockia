<div>
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card mb-3">
        <div class="card-body">
            <h2 class="h6 mb-3">{{ $assetId ? 'Edit chart' : 'Upload chart' }}</h2>
            <form wire:submit.prevent="save" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Title</label>
                    <input type="text" class="form-control @error('title') is-invalid @enderror" wire:model="title">
                    @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label">Category</label>
                    <input type="text" class="form-control" wire:model="category" placeholder="Nifty, Bank Nifty…">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Report date</label>
                    <input type="date" class="form-control" wire:model="report_date">
                </div>
                <div class="col-md-1">
                    <label class="form-label">Order</label>
                    <input type="number" class="form-control" wire:model="sort_order" min="0">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="chart_active" wire:model="is_active">
                        <label class="form-check-label" for="chart_active">Active</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">File (PNG, JPG, WebP, PDF)</label>
                    <input type="file" class="form-control @error('file') is-invalid @enderror" wire:model="file">
                    @error('file')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12 d-flex gap-2">
                    <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">Save</button>
                    <button type="button" class="btn btn-outline-secondary" wire:click="createNew">Clear</button>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-3">
        @forelse($assets as $asset)
            <div class="col-md-4">
                <div class="card h-100">
                    @if(in_array($asset->file_type, ['png', 'jpg', 'jpeg', 'webp']))
                        <img src="{{ asset('storage/'.$asset->file_path) }}" class="card-img-top" alt="{{ $asset->title }}" style="max-height:180px;object-fit:cover;">
                    @endif
                    <div class="card-body">
                        <h3 class="h6">{{ $asset->title }}</h3>
                        <p class="small text-muted mb-2">{{ $asset->category }} · {{ $asset->report_date?->format('d M Y') ?? '—' }}</p>
                        <div class="d-flex gap-2">
                            <a href="{{ asset('storage/'.$asset->file_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">View</a>
                            <button type="button" class="btn btn-sm btn-outline-secondary" wire:click="edit({{ $asset->id }})">Edit</button>
                            <button type="button" class="btn btn-sm btn-outline-danger" wire:click="delete({{ $asset->id }})" wire:confirm="Delete this chart?">Delete</button>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12"><p class="text-muted">No charts uploaded yet.</p></div>
        @endforelse
    </div>

    <div class="mt-3">{{ $assets->links() }}</div>
</div>
