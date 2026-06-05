<div>
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card mb-4">
        <div class="card-body">
            <h2 class="h6 mb-3">Upload research</h2>
            <form wire:submit.prevent="upload" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Category</label>
                    <select class="form-select" wire:model="category">
                        <option value="fii">FII</option>
                        <option value="dii">DII</option>
                        <option value="open_interest">Open Interest</option>
                        <option value="sector">Sector</option>
                        <option value="stock_research">Stock Research</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Title</label>
                    <input type="text" class="form-control @error('title') is-invalid @enderror" wire:model="title">
                    @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-2">
                    <label class="form-label">Report date</label>
                    <input type="date" class="form-control" wire:model="report_date">
                </div>
                <div class="col-md-3">
                    <label class="form-label">File (PDF, Excel, CSV)</label>
                    <input type="file" class="form-control @error('file') is-invalid @enderror" wire:model="file">
                    @error('file')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <div wire:loading wire:target="file" class="small text-muted mt-1">Uploading…</div>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary" wire:loading.attr="disabled" wire:target="upload,file">Submit for review</button>
                </div>
            </form>
        </div>
    </div>

    @if($myUploads->isNotEmpty())
        <div class="card mb-4">
            <div class="card-body">
                <h2 class="h6 mb-3">My uploads</h2>
                <div class="table-responsive">
                    <table class="table table-sm align-middle mb-0">
                        <thead>
                        <tr>
                            <th>Title</th>
                            <th>Status</th>
                            <th>Submitted</th>
                            <th class="text-end">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($myUploads as $item)
                            <tr>
                                <td>{{ $item->title }}</td>
                                <td><span class="badge text-bg-light">{{ $item->status }}</span></td>
                                <td>{{ $item->created_at->format('d M Y') }}</td>
                                <td class="text-end">
                                    @if(in_array($item->status, ['pending', 'rejected']))
                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                                wire:click="deleteUpload({{ $item->id }})"
                                                wire:confirm="Delete this upload?">Delete</button>
                                    @else
                                        <a href="{{ asset('storage/'.$item->file_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">View</a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="h6 mb-0">Approved research</h2>
        <select class="form-select form-select-sm w-auto" wire:model.live="categoryFilter">
            <option value="">All categories</option>
            <option value="fii">FII</option>
            <option value="dii">DII</option>
            <option value="open_interest">Open Interest</option>
            <option value="sector">Sector</option>
            <option value="stock_research">Stock Research</option>
            <option value="other">Other</option>
        </select>
    </div>

    <div class="row g-3">
        @forelse($approved as $item)
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body d-flex flex-column">
                        <span class="badge text-bg-light align-self-start mb-2">{{ str_replace('_', ' ', $item->category) }}</span>
                        <h3 class="h6">{{ $item->title }}</h3>
                        <p class="small text-muted mb-2">
                            {{ $item->report_date?->format('d M Y') ?? $item->created_at->format('d M Y') }}
                            · {{ $item->user?->name }}
                        </p>
                        <a href="{{ asset('storage/'.$item->file_path) }}" target="_blank" class="btn btn-outline-primary btn-sm mt-auto align-self-start">Download</a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12"><p class="text-muted">No approved research yet.</p></div>
        @endforelse
    </div>

    <div class="mt-3">{{ $approved->links() }}</div>
</div>
