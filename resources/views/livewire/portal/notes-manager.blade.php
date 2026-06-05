<div>
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            {{ $errors->first() }}
        </div>
    @endif

    @if($view === 'form')
        <div class="card mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h2 class="h6 mb-0">{{ $noteId ? 'Edit note' : 'New note' }}</h2>
                    <button type="button" class="btn btn-sm btn-outline-secondary" wire:click="backToList">Back</button>
                </div>
                <form wire:submit.prevent="save" class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label">Title</label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror" wire:model="title">
                        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Link to lecture (optional)</label>
                        <select class="form-select" wire:model="lecture_id">
                            <option value="">— None —</option>
                            @foreach($lectures as $lecture)
                                <option value="{{ $lecture->id }}">
                                    {{ $lecture->batch->name ?? '—' }} · {{ $lecture->module->name ?? '—' }} · {{ $lecture->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Notes</label>
                        <textarea class="form-control @error('body') is-invalid @enderror" rows="10" wire:model="body" placeholder="Write your trading notes here…"></textarea>
                        @error('body')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        @if($phpUploadLimitLow)
                            <div class="alert alert-warning py-2 small">
                                This server only allows uploads up to <strong>{{ $maxImageSizeLabel }}</strong> per file.
                                Use smaller images, or restart with <code>composer dev</code> for a higher limit.
                            </div>
                        @endif
                        <label class="form-label">Example files (optional)</label>
                        <input type="file"
                               class="form-control @error('newImages') is-invalid @enderror @error('newImages.*') is-invalid @enderror"
                               wire:model="newImages"
                               accept="image/jpeg,image/png,image/webp,image/gif,application/pdf,.pdf"
                               multiple>
                        @error('newImages')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        @error('newImages.*')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        <p class="small text-muted mb-0 mt-1">
                            Upload chart screenshots, examples, or PDFs. PNG, JPG, WebP, GIF, or PDF up to {{ $maxImageSizeLabel }} each.
                            Max {{ \App\Livewire\Portal\NotesManager::MAX_ATTACHMENTS_PER_NOTE }} files per note.
                        </p>
                        <div wire:loading wire:target="newImages" class="small text-muted mt-2">Uploading…</div>
                    </div>

                    @if($editingNote && $editingNote->images->isNotEmpty())
                        <div class="col-12">
                            <p class="small fw-semibold mb-2">Saved files</p>
                            <div class="note-images-grid note-images-grid--editable">
                                @foreach($editingNote->images as $image)
                                    <div class="note-image-edit-item" wire:key="existing-image-{{ $image->id }}">
                                        @include('livewire.portal.partials.note-attachment-thumb', [
                                            'url' => $image->url(),
                                            'name' => $image->original_name ?: ($image->isPdf() ? 'Example PDF' : 'Example image'),
                                            'isPdf' => $image->isPdf(),
                                        ])
                                        <button type="button"
                                                class="btn btn-sm btn-outline-danger note-image-remove"
                                                wire:click="removeExistingImage({{ $image->id }})"
                                                wire:confirm="Remove this file?">
                                            Remove
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if(count($newImages) > 0)
                        <div class="col-12">
                            <p class="small fw-semibold mb-2">New uploads</p>
                            <div class="note-images-grid note-images-grid--editable">
                                @foreach($newImages as $index => $image)
                                    @if($image)
                                        @php $isPdf = strtolower($image->getClientOriginalExtension()) === 'pdf'; @endphp
                                        <div class="note-image-edit-item" wire:key="new-image-{{ $index }}">
                                            @if($isPdf)
                                                <div class="note-image-thumb">
                                                    <span class="note-file-thumb">
                                                        <i class="bi bi-file-earmark-pdf" aria-hidden="true"></i>
                                                        <span class="note-file-thumb-label">{{ $image->getClientOriginalName() }}</span>
                                                    </span>
                                                </div>
                                            @else
                                                <div class="note-image-thumb">
                                                    <img src="{{ $image->temporaryUrl() }}" alt="New upload preview" loading="lazy">
                                                </div>
                                            @endif
                                            <button type="button"
                                                    class="btn btn-sm btn-outline-secondary note-image-remove"
                                                    wire:click="removeNewImage({{ $index }})">
                                                Remove
                                            </button>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif
                    @if($this->canShareNotes())
                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="share_note" wire:model="is_shared">
                                <label class="form-check-label" for="share_note">
                                    Share with all students
                                </label>
                            </div>
                            <p class="small text-muted mb-0">Shared notes appear in every student's notebook.</p>
                        </div>
                    @endif
                    <div class="col-12 d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Save note</button>
                        <button type="button" class="btn btn-outline-secondary" wire:click="backToList">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    @elseif($view === 'read' && $viewingNote)
        <div class="card mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <h2 class="h5 mb-1">{{ $viewingNote->title }}</h2>
                        <p class="small text-muted mb-0">
                            {{ $viewingNote->user->name }}
                            · {{ $viewingNote->updated_at->format('d M Y, H:i') }}
                            @if($viewingNote->lecture)
                                · @include('livewire.portal.partials.lecture-context', ['lecture' => $viewingNote->lecture, 'inline' => true])
                            @endif
                        </p>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-secondary" wire:click="backToList">Back</button>
                </div>
                <div class="border rounded p-3 bg-light">
                    {!! nl2br(e($viewingNote->body)) !!}
                    @include('livewire.portal.partials.note-images', ['note' => $viewingNote])
                </div>
                @if($viewingNote->user_id === auth()->id())
                    <div class="mt-3 d-flex gap-2">
                        <button type="button" class="btn btn-sm btn-outline-primary" wire:click="edit({{ $viewingNote->id }})">Edit</button>
                        <button type="button" class="btn btn-sm btn-outline-danger" wire:click="delete({{ $viewingNote->id }})" wire:confirm="Delete this note?">Delete</button>
                    </div>
                @endif
            </div>
        </div>
    @else
        <div class="d-flex justify-content-between align-items-center mb-3">
            <p class="text-muted small mb-0">Private notes are only visible to you. Admins can share notes with all students.</p>
            <button type="button" class="btn btn-primary btn-sm" wire:click="createNew">
                <i class="bi bi-plus-lg me-1"></i> New note
            </button>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <h2 class="h6 mb-3">My notes</h2>
                <div class="list-group list-group-flush">
                    @forelse($myNotes as $note)
                        <div class="list-group-item px-0 d-flex justify-content-between align-items-center">
                            <div>
                                <button type="button" class="btn btn-link p-0 text-start text-decoration-none fw-medium" wire:click="viewNote({{ $note->id }})">
                                    {{ $note->title }}
                                </button>
                                <div class="small text-muted">
                                    Updated {{ $note->updated_at->format('d M Y') }}
                                    @if($note->is_shared)
                                        · <span class="badge text-bg-primary">Shared</span>
                                    @endif
                                    @if($note->lecture)
                                        · @include('livewire.portal.partials.lecture-context', ['lecture' => $note->lecture, 'inline' => true])
                                    @endif
                                    @if($note->images->isNotEmpty())
                                        · <span class="badge text-bg-light border"><i class="bi bi-paperclip me-1"></i>{{ $note->images->count() }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="d-flex gap-1">
                                <button type="button" class="btn btn-sm btn-outline-primary" wire:click="edit({{ $note->id }})">Edit</button>
                                <button type="button" class="btn btn-sm btn-outline-danger" wire:click="delete({{ $note->id }})" wire:confirm="Delete this note?">Delete</button>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted small mb-0">No notes yet. Click "New note" to start writing.</p>
                    @endforelse
                </div>
                @if($myNotes->hasPages())
                    {{ $myNotes->links() }}
                @endif
            </div>
        </div>

        @if($sharedNotes->isNotEmpty() || ! $this->canShareNotes())
            <div class="card">
                <div class="card-body">
                    <h2 class="h6 mb-3">Shared notes from admin</h2>
                    <div class="list-group list-group-flush">
                        @forelse($sharedNotes as $note)
                            <div class="list-group-item px-0">
                                <button type="button" class="btn btn-link p-0 text-start text-decoration-none fw-medium" wire:click="viewNote({{ $note->id }})">
                                    {{ $note->title }}
                                </button>
                                <div class="small text-muted">
                                    {{ $note->user->name }} · {{ $note->updated_at->format('d M Y') }}
                                    @if($note->lecture)
                                        · @include('livewire.portal.partials.lecture-context', ['lecture' => $note->lecture, 'inline' => true])
                                    @endif
                                    @if($note->images->isNotEmpty())
                                        · <span class="badge text-bg-light border"><i class="bi bi-paperclip me-1"></i>{{ $note->images->count() }}</span>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <p class="text-muted small mb-0">No shared notes yet.</p>
                        @endforelse
                    </div>
                    @if($sharedNotes->hasPages())
                        {{ $sharedNotes->links() }}
                    @endif
                </div>
            </div>
        @endif
    @endif
</div>
