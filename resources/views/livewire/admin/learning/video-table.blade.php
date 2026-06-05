<div>
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="d-flex justify-content-end mb-3">
        <button type="button" class="btn btn-primary" wire:click="openCreateModal">
            <i class="bi bi-plus-lg"></i> Add video
        </button>
    </div>

    @include('admin.learning.partials.filters-panel', [
        'extraFilters' => view('livewire.admin.learning.partials.video-extra-filters', compact('lectures', 'videoTypes', 'batches', 'modules')),
    ])

    @if ($selectedLecture)
        @include('livewire.admin.learning.partials.selected-lecture-card', [
            'lecture' => $selectedLecture,
            'allLecturesRoute' => route('admin.learning.lectures.index'),
            'manageRoute' => route('admin.learning.videos.index', ['lecture' => $selectedLecture->id]),
            'manageLabel' => 'Add video',
        ])
    @elseif ($selectedBatch || $selectedModule)
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div>
                    <div class="small text-muted text-uppercase fw-semibold">Filtered by</div>
                    <div class="fw-medium">
                        @if ($selectedBatch && $selectedModule)
                            {{ $selectedBatch->name }} · {{ $selectedModule->name }}
                        @elseif ($selectedBatch)
                            Batch: {{ $selectedBatch->name }}
                        @else
                            Module: {{ $selectedModule->name }}
                        @endif
                    </div>
                </div>
                <a href="{{ route('admin.learning.videos.index') }}" class="btn btn-sm btn-outline-secondary">Clear filters</a>
            </div>
        </div>
    @endif

    @include('admin.learning.partials.bulk-actions')

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 40px;">
                            @php $pageIds = $videos->pluck('id')->all(); @endphp
                            <input type="checkbox" class="form-check-input"
                                   @checked($pageIds !== [] && count(array_intersect($pageIds, $selectedIds)) === count($pageIds))
                                   wire:click="toggleSelectAllOnPage({{ json_encode($pageIds) }})">
                        </th>
                        <th>Video</th>
                        <th>Lecture</th>
                        <th>Type</th>
                        <th>Sort</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($videos as $video)
                        <tr wire:key="video-{{ $video->id }}">
                            <td><input type="checkbox" class="form-check-input" value="{{ $video->id }}" wire:model.live="selectedIds"></td>
                            <td>
                                @include('livewire.admin.learning.partials.linked-video-item', [
                                    'video' => $video,
                                    'modalIdPrefix' => 'youtube-preview-video',
                                ])
                            </td>
                            <td>
                                @if ($video->lecture)
                                    <div class="fw-medium">
                                        <a href="{{ route('admin.learning.lectures.index', ['batch' => $video->lecture->batch_id, 'module' => $video->lecture->module_id]) }}" class="text-decoration-none">
                                            {{ $video->lecture->title }}
                                        </a>
                                    </div>
                                    @include('livewire.admin.learning.partials.lecture-context', ['lecture' => $video->lecture, 'linkBatch' => true, 'linkModule' => true])
                                    @include('livewire.admin.learning.partials.lecture-notes-snippet', ['lecture' => $video->lecture, 'max' => 60])
                                @else
                                    —
                                @endif
                            </td>
                            <td>{{ $video->video_type ?: '—' }}</td>
                            <td>{{ $video->sort_order }}</td>
                            <td>
                                <span class="badge {{ $video->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">
                                    {{ $video->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-outline-primary" wire:click="openEditModal({{ $video->id }})">Edit</button>
                                    <x-ui.activate-toggle-button :id="$video->id" :active="$video->is_active" />
                                    <button type="button" class="btn btn-outline-danger" wire:click="delete({{ $video->id }})" wire:confirm="Delete this video?">Delete</button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-5">
                                <p class="mb-2">No videos found.</p>
                                <button type="button" class="btn btn-sm btn-primary" wire:click="openCreateModal">Add video</button>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($videos->hasPages())
            <div class="card-footer bg-white border-0">{{ $videos->links() }}</div>
        @endif
    </div>
</div>
