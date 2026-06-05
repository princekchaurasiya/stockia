<div>
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body d-flex flex-wrap justify-content-between align-items-start gap-3">
            <div>
                <div class="small text-muted text-uppercase fw-semibold mb-1">Batch overview</div>
                <h2 class="h4 mb-2">{{ $batch->name }}</h2>
                <div class="d-flex flex-wrap gap-2 align-items-center">
                    <span class="badge {{ $batch->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">
                        Batch {{ $batch->is_active ? 'Active' : 'Inactive' }}
                    </span>
                    <span class="small text-muted">Created {{ $batch->created_at?->format('M j, Y') }}</span>
                </div>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('admin.learning.batches.index') }}" class="btn btn-sm btn-outline-secondary">All batches</a>
                <button type="button" class="btn btn-sm btn-outline-primary" wire:click="$dispatch('openBatchFormModal', { id: {{ $batch->id }} })">
                    Edit batch
                </button>
            </div>
        </div>
        <div class="card-footer bg-white border-top py-3">
            <div class="row g-2 text-center">
                <div class="col-6 col-md-4 col-xl-2">
                    <div class="fw-semibold">{{ $batch->active_enrollments_count }}/{{ $batch->enrollments_count }}</div>
                    <div class="small text-muted">Students active</div>
                </div>
                <div class="col-6 col-md-4 col-xl-2">
                    <div class="fw-semibold">{{ $batch->lectures_count }}</div>
                    <div class="small text-muted">Lectures</div>
                </div>
                <div class="col-6 col-md-4 col-xl-2">
                    <div class="fw-semibold">{{ $modules->count() }}</div>
                    <div class="small text-muted">Modules</div>
                </div>
                <div class="col-6 col-md-4 col-xl-2">
                    <div class="fw-semibold">{{ $batch->videos_count }}</div>
                    <div class="small text-muted">Videos</div>
                </div>
                <div class="col-6 col-md-4 col-xl-2">
                    <div class="fw-semibold">{{ $batch->documents_count }}</div>
                    <div class="small text-muted">Documents</div>
                </div>
                <div class="col-6 col-md-4 col-xl-2">
                    <div class="fw-semibold">{{ $batch->user_notes_count }}</div>
                    <div class="small text-muted">Linked notes</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-6">
            <x-ui.resource-card
                title="Students"
                :count="$enrollments->count()"
                :manage-route="route('admin.learning.enrollments.index', ['batch' => $batch->id])"
                manage-label="Manage students"
                empty="No students enrolled in this batch yet."
            >
                @foreach ($enrollments as $enrollment)
                    @include('livewire.admin.learning.partials.linked-enrollment-item', [
                        'enrollment' => $enrollment,
                        'batchId' => $batch->id,
                        'class' => 'mb-2 pb-2' . ($loop->last ? '' : ' border-bottom'),
                    ])
                @endforeach
            </x-ui.resource-card>
        </div>

        <div class="col-lg-6">
            <x-ui.resource-card
                title="Modules"
                :count="$modules->count()"
                :manage-route="route('admin.learning.modules.index')"
                manage-label="All modules"
                empty="No modules linked through lectures yet."
            >
                @foreach ($modules as $module)
                    @include('livewire.admin.learning.partials.linked-module-item', [
                        'module' => $module,
                        'batchId' => $batch->id,
                        'class' => 'mb-2 pb-2' . ($loop->last ? '' : ' border-bottom'),
                    ])
                @endforeach
            </x-ui.resource-card>
        </div>

        <div class="col-12">
            <x-ui.resource-card
                title="Lectures"
                :count="$lectures->count()"
                :manage-route="route('admin.learning.lectures.index', ['batch' => $batch->id])"
                manage-label="Manage lectures"
                empty="No lectures in this batch yet."
            >
                @foreach ($lectures as $lecture)
                    @include('livewire.admin.learning.partials.linked-lecture-item', [
                        'lecture' => $lecture,
                        'class' => 'mb-3 pb-3' . ($loop->last ? '' : ' border-bottom'),
                    ])
                @endforeach
            </x-ui.resource-card>
        </div>

        <div class="col-lg-6">
            <x-ui.resource-card
                title="Videos"
                :count="$videos->count()"
                :manage-route="route('admin.learning.videos.index', ['batch' => $batch->id])"
                manage-label="Manage videos"
                empty="No videos linked to this batch yet."
            >
                @foreach ($videos as $video)
                    @include('livewire.admin.learning.partials.linked-video-item', [
                        'video' => $video,
                        'modalIdPrefix' => 'batch-video-preview',
                        'class' => 'mb-2 pb-2' . ($loop->last ? '' : ' border-bottom'),
                    ])
                @endforeach
            </x-ui.resource-card>
        </div>

        <div class="col-lg-6">
            <x-ui.resource-card
                title="Documents"
                :count="$documents->count()"
                :manage-route="route('admin.learning.documents.index', ['batch' => $batch->id])"
                manage-label="Manage documents"
                empty="No documents linked to this batch yet."
            >
                @foreach ($documents as $document)
                    @include('livewire.admin.learning.partials.linked-document-item', [
                        'document' => $document,
                        'class' => 'mb-2 pb-2' . ($loop->last ? '' : ' border-bottom'),
                    ])
                @endforeach
            </x-ui.resource-card>
        </div>

        <div class="col-12">
            <x-ui.resource-card
                title="Linked notes"
                :count="$linkedNotes->count()"
                :manage-route="route('admin.learning.lectures.index', ['batch' => $batch->id])"
                manage-label="View lectures"
                empty="No student notes linked to lectures in this batch yet."
            >
                @foreach ($linkedNotes as $note)
                    <div class="mb-2 pb-2{{ $loop->last ? '' : ' border-bottom' }}">
                        @include('livewire.admin.learning.partials.linked-user-note-item', ['note' => $note])
                        @if ($note->lecture)
                            <div class="small text-muted ms-1 mt-1">
                                Linked to lecture:
                                <a href="{{ route('admin.learning.lectures.index', ['batch' => $batch->id, 'lecture' => $note->lecture_id]) }}" class="text-decoration-none">
                                    {{ $note->lecture->title }}
                                </a>
                                @if ($note->lecture->module)
                                    · {{ $note->lecture->module->name }}
                                @endif
                            </div>
                        @endif
                    </div>
                @endforeach
            </x-ui.resource-card>
        </div>
    </div>
</div>
