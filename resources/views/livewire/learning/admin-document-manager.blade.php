<div class="mt-4">
    @if (session('success_document'))
        <div class="alert alert-success">{{ session('success_document') }}</div>
    @endif
    @if (session('error_document'))
        <div class="alert alert-danger">{{ session('error_document') }}</div>
    @endif

    <div class="card mb-3">
        <div class="card-body">
            <h2 class="h6 mb-3">{{ $document_id ? 'Edit document' : 'Add document' }}</h2>

            <form wire:submit.prevent="save" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Lecture</label>
                    <select class="form-select @error('lecture_id') is-invalid @enderror" wire:model.live="lecture_id">
                        <option value="">Select lecture</option>
                        @foreach($lectures as $lecture)
                            <option value="{{ $lecture->id }}">{{ $lecture->title }}</option>
                        @endforeach
                    </select>
                    @error('lecture_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label">Title</label>
                    <input type="text" class="form-control @error('title') is-invalid @enderror" wire:model="title">
                    @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label">File (PDF, PPT, PPTX)</label>
                    <input type="file" class="form-control @error('file') is-invalid @enderror" wire:model="file" accept=".pdf,.ppt,.pptx,application/pdf,application/vnd.ms-powerpoint,application/vnd.openxmlformats-officedocument.presentationml.presentation">
                    @error('file')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-3">
                    <label class="form-label">Sort order</label>
                    <input type="number" class="form-control @error('sort_order') is-invalid @enderror" wire:model="sort_order" min="0">
                    @error('sort_order')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-3 d-flex align-items-center">
                    <div class="form-check mt-3">
                        <input class="form-check-input" type="checkbox" id="document_is_active" wire:model="is_active">
                        <label class="form-check-label" for="document_is_active">Active</label>
                    </div>
                </div>

                <div class="col-12 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Save document</button>
                    <button type="button" class="btn btn-outline-secondary" wire:click="clearForm">Clear</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h2 class="h6 mb-3">Documents</h2>

            @if(!$lecture_id)
                <p class="text-muted small mb-0">Select a lecture above to manage its documents.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-striped align-middle mb-0">
                        <thead>
                        <tr>
                            <th>Title</th>
                            <th>Type</th>
                            <th>Sort</th>
                            <th>Active</th>
                            <th class="text-end">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($documents as $document)
                            <tr>
                                <td>{{ $document->title }}</td>
                                <td>{{ strtoupper($document->file_type) }}</td>
                                <td>{{ $document->sort_order }}</td>
                                <td>
                                    <span class="badge {{ $document->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">
                                        {{ $document->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ asset('storage/'.$document->file_path) }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                                        View
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-primary" wire:click="edit({{ $document->id }})">
                                        Edit
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" wire:click="toggleActive({{ $document->id }})">
                                        Toggle
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-danger" wire:click="delete({{ $document->id }})" onclick="return confirm('Delete this document?')">
                                        Delete
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-muted text-center py-3">No documents for this lecture yet.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>

