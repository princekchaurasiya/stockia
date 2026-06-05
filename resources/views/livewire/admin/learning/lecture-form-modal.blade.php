<div>
    @if ($show)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,.45);">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ $lectureId ? 'Edit lecture' : 'Create lecture' }}</h5>
                        <button type="button" class="btn-close" wire:click="close"></button>
                    </div>
                    <form wire:submit.prevent="save">
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <x-ui.form-label :help="\App\Support\FieldHelp::lectureBatch()">Batch</x-ui.form-label>
                                    <livewire:inputs.batch-select wire:model.live="batch_id" name="batch_id" />
                                    @error('batch_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <x-ui.form-label :help="\App\Support\FieldHelp::lectureModule()">Module</x-ui.form-label>
                                    <livewire:inputs.module-select wire:model.live="module_id" name="module_id" />
                                    @error('module_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-12">
                                    <x-ui.form-label :help="\App\Support\FieldHelp::lectureTitle()">Title</x-ui.form-label>
                                    <input type="text" class="form-control @error('title') is-invalid @enderror" wire:model="title" placeholder="e.g. Chart analysis combination">
                                    @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-4">
                                    <x-ui.form-label-sort-order context="lectures" />
                                    <input type="number" class="form-control @error('sort_order') is-invalid @enderror" wire:model="sort_order" min="0">
                                    @error('sort_order')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-4 d-flex align-items-end">
                                    <x-ui.form-check-field id="lecture_modal_active" wire:model="is_active" :help="\App\Support\FieldHelp::active('lecture')">
                                        Active
                                    </x-ui.form-check-field>
                                </div>
                                <div class="col-12">
                                    <x-ui.form-label :help="\App\Support\FieldHelp::lectureNotes()">Notes</x-ui.form-label>
                                    <textarea class="form-control @error('notes') is-invalid @enderror" rows="3" wire:model="notes" placeholder="Lecture notes visible to students on the learning page"></textarea>
                                    @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                @if ($lectureId)
                                    <div class="col-12">
                                        <div class="border rounded-3 p-3 bg-light">
                                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                                                <span class="small text-uppercase text-muted fw-semibold">
                                                    Linked videos ({{ $linkedVideos->count() }})
                                                </span>
                                                <a href="{{ route('admin.learning.videos.index', ['lecture' => $lectureId]) }}" class="btn btn-sm btn-outline-primary">Manage videos</a>
                                            </div>

                                            @forelse ($linkedVideos as $video)
                                                @include('livewire.admin.learning.partials.linked-video-item', [
                                                    'video' => $video,
                                                    'modalIdPrefix' => 'lecture-video-preview',
                                                    'class' => 'mb-2 pb-2' . ($loop->last ? '' : ' border-bottom'),
                                                ])
                                            @empty
                                                <p class="small text-muted mb-0">No videos linked yet.</p>
                                            @endforelse
                                        </div>

                                        <div class="border rounded-3 p-3 bg-light mt-3">
                                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                                                <span class="small text-uppercase text-muted fw-semibold">
                                                    Linked documents ({{ $linkedDocuments->count() }})
                                                </span>
                                                <a href="{{ route('admin.learning.documents.index', ['lecture' => $lectureId]) }}" class="btn btn-sm btn-outline-secondary">Manage documents</a>
                                            </div>

                                            @forelse ($linkedDocuments as $document)
                                                @include('livewire.admin.learning.partials.linked-document-item', [
                                                    'document' => $document,
                                                    'class' => 'mb-2 pb-2' . ($loop->last ? '' : ' border-bottom'),
                                                ])
                                            @empty
                                                <p class="small text-muted mb-0">No documents linked yet.</p>
                                            @endforelse
                                        </div>
                                        <div class="border rounded-3 p-3 bg-light mt-3">
                                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                                                <span class="small text-uppercase text-muted fw-semibold">
                                                    Linked notes ({{ $linkedUserNotes->count() }})
                                                </span>
                                                <a href="{{ route('notes.index') }}" class="btn btn-sm btn-outline-secondary">My Notes</a>
                                            </div>

                                            @forelse ($linkedUserNotes as $note)
                                                @include('livewire.admin.learning.partials.linked-user-note-item', [
                                                    'note' => $note,
                                                    'class' => 'mb-2 pb-2' . ($loop->last ? '' : ' border-bottom'),
                                                ])
                                            @empty
                                                <p class="small text-muted mb-0">No notes linked to this lecture yet.</p>
                                            @endforelse
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" wire:click="close">Cancel</button>
                            <button type="submit" class="btn btn-primary">{{ $lectureId ? 'Save changes' : 'Create lecture' }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <livewire:inputs.create-batch-modal />
        <livewire:inputs.create-module-modal />
    @endif
</div>
