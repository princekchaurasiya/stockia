<div>
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="d-flex justify-content-end mb-3">
        <button type="button" class="btn btn-primary" wire:click="openCreateModal">
            <i class="bi bi-plus-lg"></i> Create module
        </button>
    </div>

    @include('admin.learning.partials.filters-panel', [
        'extraFilters' => view('livewire.admin.learning.partials.module-extra-filters', compact('batches')),
    ])

    @include('admin.learning.partials.bulk-actions')

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 40px;">
                            @php $pageIds = $modules->pluck('id')->all(); @endphp
                            <input type="checkbox" class="form-check-input"
                                   @checked($pageIds !== [] && count(array_intersect($pageIds, $selectedIds)) === count($pageIds))
                                   wire:click="toggleSelectAllOnPage({{ json_encode($pageIds) }})">
                        </th>
                        <th><button type="button" class="btn btn-link btn-sm text-decoration-none text-muted text-uppercase p-0 fw-semibold" wire:click="sortBy('name')">Name</button></th>
                        <th>Batches</th>
                        <th>Lectures</th>
                        <th>Notes</th>
                        <th>Videos</th>
                        <th>
                            <span class="text-muted text-uppercase fw-semibold small">Trading style</span>
                            <x-ui.module-trading-style-help />
                        </th>
                        <th>
                            <button type="button" class="btn btn-link btn-sm text-decoration-none text-muted text-uppercase p-0 fw-semibold" wire:click="sortBy('sort_order')">
                                Sort
                            </button>
                            <x-ui.module-sort-order-help />
                        </th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($modules as $module)
                        <tr wire:key="module-{{ $module->id }}">
                            <td><input type="checkbox" class="form-check-input" value="{{ $module->id }}" wire:model.live="selectedIds"></td>
                            <td>
                                <div class="fw-medium">{{ $module->name }}</div>
                                @if ($module->description)
                                    <div class="small text-muted text-truncate" style="max-width: 280px;">{{ $module->description }}</div>
                                @endif
                            </td>
                            <td>
                                @if ($module->batches->isNotEmpty())
                                    @foreach ($module->batches as $batch)
                                        <a href="{{ route('admin.learning.lectures.index', ['batch' => $batch->id, 'module' => $module->id]) }}" class="badge text-bg-light border text-decoration-none me-1 mb-1">{{ $batch->name }}</a>
                                    @endforeach
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.learning.lectures.index', ['module' => $module->id]) }}" class="badge text-bg-light border text-decoration-none">
                                    {{ $module->lectures_count }} lecture{{ $module->lectures_count === 1 ? '' : 's' }}
                                </a>
                            </td>
                            <td>
                                @include('livewire.admin.learning.partials.notes-summary-cell', [
                                    'lectureNotesCount' => $module->lectures_with_notes_count,
                                    'linkedNotesCount' => $module->user_notes_count,
                                    'filterRoute' => route('admin.learning.lectures.index', ['module' => $module->id]),
                                ])
                            </td>
                            <td>
                                <a href="{{ route('admin.learning.videos.index', ['module' => $module->id]) }}" class="badge text-bg-light border text-decoration-none">
                                    {{ $module->videos_count }} video{{ $module->videos_count === 1 ? '' : 's' }}
                                </a>
                            </td>
                            <td>{{ $module->timeframe ?: '—' }}</td>
                            <td>{{ $module->sort_order }}</td>
                            <td>
                                <span class="badge {{ $module->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">
                                    {{ $module->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-outline-primary" wire:click="openEditModal({{ $module->id }})">Edit</button>
                                    <x-ui.activate-toggle-button :id="$module->id" :active="$module->is_active" />
                                    <button type="button" class="btn btn-outline-danger" wire:click="delete({{ $module->id }})" wire:confirm="Delete this module?">Delete</button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center text-muted py-5">
                                <p class="mb-2">No modules found.</p>
                                <button type="button" class="btn btn-sm btn-primary" wire:click="openCreateModal">Create module</button>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($modules->hasPages())
            <div class="card-footer bg-white border-0">{{ $modules->links() }}</div>
        @endif
    </div>
</div>
