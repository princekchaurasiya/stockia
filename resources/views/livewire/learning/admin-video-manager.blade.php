<div class="mt-4">
    @if (session('success_video'))
        <div class="alert alert-success">{{ session('success_video') }}</div>
    @endif

    <div class="card mb-3">
        <div class="card-body">
            <h2 class="h6 mb-3">{{ $video_id ? 'Edit video' : 'Add video' }}</h2>

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
                    <label class="form-label">Label</label>
                    <input type="text" class="form-control @error('label') is-invalid @enderror" wire:model="label">
                    @error('label')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label">YouTube URL</label>
                    <input type="url" class="form-control @error('youtube_url') is-invalid @enderror" wire:model="youtube_url">
                    @error('youtube_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-3">
                    <label class="form-label">Video type (optional)</label>
                    <input type="text" class="form-control @error('video_type') is-invalid @enderror" wire:model="video_type" placeholder="Main, Supplementary, Case Study">
                    @error('video_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-3">
                    <label class="form-label">Sort order</label>
                    <input type="number" class="form-control @error('sort_order') is-invalid @enderror" wire:model="sort_order" min="0">
                    @error('sort_order')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-3 d-flex align-items-center">
                    <div class="form-check mt-3">
                        <input class="form-check-input" type="checkbox" id="video_is_active" wire:model="is_active">
                        <label class="form-check-label" for="video_is_active">Active</label>
                    </div>
                </div>

                <div class="col-12 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Save video</button>
                    <button type="button" class="btn btn-outline-secondary" wire:click="clearVideoForm">Clear</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h2 class="h6 mb-3">Videos</h2>

            @if(!$lecture_id)
                <p class="text-muted small mb-0">Select a lecture above to manage its videos.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-striped align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Label</th>
                                <th>Type</th>
                                <th>Sort</th>
                                <th>Active</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($videos as $video)
                                <tr>
                                    <td>{{ $video->label }}</td>
                                    <td>{{ $video->video_type ?? '-' }}</td>
                                    <td>{{ $video->sort_order }}</td>
                                    <td>
                                        <span class="badge {{ $video->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">
                                            {{ $video->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <button type="button" class="btn btn-sm btn-outline-primary" wire:click="edit({{ $video->id }})">
                                            Edit
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary" wire:click="toggleActive({{ $video->id }})">
                                            Toggle
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-danger" wire:click="delete({{ $video->id }})" onclick="return confirm('Delete this video?')">
                                            Delete
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-muted text-center py-3">No videos for this lecture yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>

