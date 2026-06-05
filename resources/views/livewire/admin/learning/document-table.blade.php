<div>
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="d-flex justify-content-end mb-3">
        <button type="button" class="btn btn-primary" wire:click="openCreateModal">
            <i class="bi bi-plus-lg"></i> Add document
        </button>
    </div>

    @include('admin.learning.partials.filters-panel', [
        'extraFilters' => view('livewire.admin.learning.partials.document-extra-filters', compact('lectures', 'fileTypes', 'batches', 'modules')),
    ])

    @if ($selectedLecture)
        @include('livewire.admin.learning.partials.selected-lecture-card', [
            'lecture' => $selectedLecture,
            'allLecturesRoute' => route('admin.learning.lectures.index'),
            'manageRoute' => route('admin.learning.documents.index', ['lecture' => $selectedLecture->id]),
            'manageLabel' => 'Add document',
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
                <a href="{{ route('admin.learning.documents.index') }}" class="btn btn-sm btn-outline-secondary">Clear filters</a>
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
                            @php $pageIds = $documents->pluck('id')->all(); @endphp
                            <input type="checkbox" class="form-check-input"
                                   @checked($pageIds !== [] && count(array_intersect($pageIds, $selectedIds)) === count($pageIds))
                                   wire:click="toggleSelectAllOnPage({{ json_encode($pageIds) }})">
                        </th>
                        <th>Title</th>
                        <th>Lecture</th>
                        <th>Type</th>
                        <th>Sort</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($documents as $document)
                        <tr wire:key="document-{{ $document->id }}">
                            <td><input type="checkbox" class="form-check-input" value="{{ $document->id }}" wire:model.live="selectedIds"></td>
                            <td class="fw-medium">{{ $document->title }}</td>
                            <td>
                                @if ($document->lecture)
                                    <div class="fw-medium">
                                        <a href="{{ route('admin.learning.lectures.index', ['batch' => $document->lecture->batch_id, 'module' => $document->lecture->module_id]) }}" class="text-decoration-none">
                                            {{ $document->lecture->title }}
                                        </a>
                                    </div>
                                    @include('livewire.admin.learning.partials.lecture-context', ['lecture' => $document->lecture, 'linkBatch' => true, 'linkModule' => true])
                                    @include('livewire.admin.learning.partials.lecture-notes-snippet', ['lecture' => $document->lecture, 'max' => 60])
                                @else
                                    —
                                @endif
                            </td>
                            <td>{{ strtoupper($document->file_type) }}</td>
                            <td>{{ $document->sort_order }}</td>
                            <td>
                                <span class="badge {{ $document->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">
                                    {{ $document->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    @if ($document->file_path)
                                        <a href="{{ asset('storage/'.$document->file_path) }}" target="_blank" class="btn btn-outline-secondary">View</a>
                                    @endif
                                    <button type="button" class="btn btn-outline-primary" wire:click="openEditModal({{ $document->id }})">Edit</button>
                                    <x-ui.activate-toggle-button :id="$document->id" :active="$document->is_active" />
                                    <button type="button" class="btn btn-outline-danger" wire:click="delete({{ $document->id }})" wire:confirm="Delete this document?">Delete</button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-5">
                                <p class="mb-2">No documents found.</p>
                                <button type="button" class="btn btn-sm btn-primary" wire:click="openCreateModal">Add document</button>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($documents->hasPages())
            <div class="card-footer bg-white border-0">{{ $documents->links() }}</div>
        @endif
    </div>
</div>
