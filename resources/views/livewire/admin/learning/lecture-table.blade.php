<div>
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="d-flex justify-content-end mb-3">
        <button type="button" class="btn btn-primary" wire:click="openCreateModal">
            <i class="bi bi-plus-lg"></i> Create lecture
        </button>
    </div>

    @include('admin.learning.partials.filters-panel', [
        'extraFilters' => view('livewire.admin.learning.partials.lecture-extra-filters', compact('batches', 'modules')),
    ])

    @if ($selectedBatch || $selectedModule)
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
                <a href="{{ route('admin.learning.lectures.index') }}" class="btn btn-sm btn-outline-secondary">Clear filters</a>
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
                            @php $pageIds = $lectures->pluck('id')->all(); @endphp
                            <input type="checkbox" class="form-check-input"
                                   @checked($pageIds !== [] && count(array_intersect($pageIds, $selectedIds)) === count($pageIds))
                                   wire:click="toggleSelectAllOnPage({{ json_encode($pageIds) }})">
                        </th>
                        <th><button type="button" class="btn btn-link btn-sm text-decoration-none text-muted text-uppercase p-0 fw-semibold" wire:click="sortBy('title')">Title</button></th>
                        <th>Batch</th>
                        <th>Module</th>
                        <th>Notes</th>
                        <th>Videos</th>
                        <th>Docs</th>
                        <th>Sort</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($lectures as $lecture)
                        <tr wire:key="lecture-{{ $lecture->id }}">
                            <td><input type="checkbox" class="form-check-input" value="{{ $lecture->id }}" wire:model.live="selectedIds"></td>
                            <td class="fw-medium">{{ $lecture->title }}</td>
                            <td>
                                @if ($lecture->batch)
                                    <a href="{{ route('admin.learning.lectures.index', ['batch' => $lecture->batch_id]) }}" class="text-decoration-none">{{ $lecture->batch->name }}</a>
                                @else
                                    —
                                @endif
                            </td>
                            <td>
                                @if ($lecture->module)
                                    <a href="{{ route('admin.learning.lectures.index', ['module' => $lecture->module_id]) }}" class="text-decoration-none">{{ $lecture->module->name }}</a>
                                @else
                                    —
                                @endif
                            </td>
                            <td>
                                @include('livewire.admin.learning.partials.lecture-notes-cell', ['lecture' => $lecture])
                            </td>
                            <td>
                                <a href="{{ route('admin.learning.videos.index', ['lecture' => $lecture->id]) }}" class="badge text-bg-light border text-decoration-none">
                                    {{ $lecture->videos_count }} video{{ $lecture->videos_count === 1 ? '' : 's' }}
                                </a>
                            </td>
                            <td>
                                <a href="{{ route('admin.learning.documents.index', ['lecture' => $lecture->id]) }}" class="badge text-bg-light border text-decoration-none">
                                    {{ $lecture->documents_count }} doc{{ $lecture->documents_count === 1 ? '' : 's' }}
                                </a>
                            </td>
                            <td>{{ $lecture->sort_order }}</td>
                            <td>
                                <span class="badge {{ $lecture->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">
                                    {{ $lecture->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-outline-primary" wire:click="openEditModal({{ $lecture->id }})">Edit</button>
                                    <x-ui.activate-toggle-button :id="$lecture->id" :active="$lecture->is_active" />
                                    <button type="button" class="btn btn-outline-danger" wire:click="delete({{ $lecture->id }})" wire:confirm="Delete this lecture and its videos?">Delete</button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center text-muted py-5">
                                <p class="mb-2">No lectures found.</p>
                                <button type="button" class="btn btn-sm btn-primary" wire:click="openCreateModal">Create lecture</button>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($lectures->hasPages())
            <div class="card-footer bg-white border-0">{{ $lectures->links() }}</div>
        @endif
    </div>
</div>
