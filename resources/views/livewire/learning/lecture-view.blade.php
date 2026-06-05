<div>
    @if(!$lecture)
        <div class="learning-view-empty">
            <div class="learning-view-empty-icon">
                <i class="bi bi-play-btn" aria-hidden="true"></i>
            </div>
            <h3 class="h5 mb-2">Select a lecture</h3>
            <p class="text-muted mb-0">Choose a lecture from the curriculum on the left to watch videos, download resources, and read notes.</p>
        </div>
    @else
        <div class="learning-view-header">
            <div>
                <h2 class="learning-view-title">{{ $lecture->title }}</h2>
                @include('livewire.learning.partials.lecture-context', ['lecture' => $lecture])
            </div>
        </div>

        <div class="row g-4 mt-1">
            <div class="col-lg-6">
                <div class="learning-view-section">
                    <div class="learning-view-section-head">
                        <h3 class="learning-view-section-title">Videos</h3>
                        @if($videos->isNotEmpty())
                            <span class="badge text-bg-light">{{ $videos->count() }}</span>
                        @endif
                    </div>

                    @if($videos->isEmpty())
                        <div class="learning-hub-empty learning-hub-empty-compact">
                            <i class="bi bi-camera-video-off" aria-hidden="true"></i>
                            <p class="mb-0">No videos added yet.</p>
                        </div>
                    @else
                        <div class="learning-video-preview-list">
                            @foreach($videos as $video)
                                @include('livewire.learning.partials.video-item', [
                                    'video' => $video,
                                    'modalIdPrefix' => 'learning-video',
                                ])
                            @endforeach
                        </div>
                    @endif
                </div>

                @if($lecture->documents && $lecture->documents->isNotEmpty())
                    <div class="learning-view-section mt-4">
                        <div class="learning-view-section-head">
                            <h3 class="learning-view-section-title">Resources</h3>
                            <span class="badge text-bg-light">{{ $lecture->documents->count() }}</span>
                        </div>

                        <div class="learning-resource-list">
                            @foreach($lecture->documents as $document)
                                <a href="{{ asset('storage/'.$document->file_path) }}"
                                   target="_blank"
                                   rel="noopener"
                                   class="learning-resource-item">
                                    <span class="learning-resource-icon">
                                        @if($document->file_type === 'pdf')
                                            <i class="bi bi-file-earmark-pdf" aria-hidden="true"></i>
                                        @elseif(in_array($document->file_type, ['ppt', 'pptx']))
                                            <i class="bi bi-file-earmark-slides" aria-hidden="true"></i>
                                        @else
                                            <i class="bi bi-file-earmark-text" aria-hidden="true"></i>
                                        @endif
                                    </span>
                                    <span class="min-w-0 flex-grow-1">
                                        <span class="learning-resource-title d-block text-truncate">{{ $document->title }}</span>
                                        <span class="learning-resource-type">{{ strtoupper($document->file_type) }}</span>
                                    </span>
                                    <i class="bi bi-download" aria-hidden="true"></i>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <div class="col-lg-6">
                @if($lecture->notes)
                    <div class="learning-notes-card">
                        <div class="learning-view-section-head mb-3">
                            <h3 class="learning-view-section-title mb-0">Lecture notes</h3>
                            <span class="badge text-bg-light">Instructor</span>
                        </div>
                        <div class="learning-notes-body">
                            {!! nl2br(e($lecture->notes)) !!}
                        </div>
                    </div>
                @endif

                @if($linkedNotes->isNotEmpty())
                    <div class="learning-notes-card {{ $lecture->notes ? 'mt-4' : '' }}">
                        <div class="learning-view-section-head mb-3">
                            <h3 class="learning-view-section-title mb-0">Linked notes</h3>
                            <span class="badge text-bg-light">{{ $linkedNotes->count() }}</span>
                        </div>

                        <div class="learning-linked-notes-list">
                            @foreach($linkedNotes as $note)
                                <article class="learning-linked-note" wire:key="linked-note-{{ $note->id }}">
                                    <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                                        <h4 class="h6 mb-0">{{ $note->title }}</h4>
                                        @if($note->user_id === auth()->id())
                                            <span class="badge text-bg-light border">Your note</span>
                                        @elseif($note->is_shared)
                                            <span class="badge text-bg-primary">Shared by {{ $note->user->name }}</span>
                                        @endif
                                    </div>
                                    <div class="learning-notes-body">
                                        {!! nl2br(e($note->body)) !!}
                                    </div>
                                    @include('livewire.portal.partials.note-images', ['note' => $note])
                                    <div class="small text-muted mt-2">
                                        Updated {{ $note->updated_at->format('d M Y, H:i') }}
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if(! $lecture->notes && $linkedNotes->isEmpty())
                    <div class="learning-hub-empty">
                        <i class="bi bi-journal-text" aria-hidden="true"></i>
                        <p class="mb-2">No notes for this lecture yet.</p>
                        <a href="{{ route('notes.index') }}" class="btn btn-sm btn-outline-primary">Add a note in My Notes</a>
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>
