<div>
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="d-flex justify-content-end mb-3">
        <button type="button" class="btn btn-primary" wire:click="openCreateModal">
            <i class="bi bi-plus-lg"></i> Create batch
        </button>
    </div>

    @include('admin.learning.partials.filters-panel')

    @include('admin.learning.partials.bulk-actions')

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 40px;">
                            @php $pageIds = $batches->pluck('id')->all(); @endphp
                            <input type="checkbox" class="form-check-input"
                                   @checked($pageIds !== [] && count(array_intersect($pageIds, $selectedIds)) === count($pageIds))
                                   wire:click="toggleSelectAllOnPage({{ json_encode($pageIds) }})">
                        </th>
                        <th>
                            <button type="button" class="btn btn-link btn-sm text-decoration-none text-muted text-uppercase p-0 fw-semibold" wire:click="sortBy('name')">
                                Name @if($sortField === 'name')<i class="bi bi-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>@endif
                            </button>
                        </th>
                        <th>Lectures</th>
                        <th>Students</th>
                        <th>Notes</th>
                        <th>Videos</th>
                        <th>Status</th>
                        <th>
                            <button type="button" class="btn btn-link btn-sm text-decoration-none text-muted text-uppercase p-0 fw-semibold" wire:click="sortBy('created_at')">
                                Created @if($sortField === 'created_at')<i class="bi bi-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>@endif
                            </button>
                        </th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($batches as $batch)
                        <tr wire:key="batch-{{ $batch->id }}">
                            <td>
                                <input type="checkbox" class="form-check-input" value="{{ $batch->id }}" wire:model.live="selectedIds">
                            </td>
                            <td class="fw-medium">
                                <a href="{{ route('admin.learning.batches.show', $batch) }}" class="text-decoration-none fw-medium">
                                    {{ $batch->name }}
                                </a>
                            </td>
                            <td>
                                <a href="{{ route('admin.learning.lectures.index', ['batch' => $batch->id]) }}" class="badge text-bg-light border text-decoration-none">
                                    {{ $batch->lectures_count }} lecture{{ $batch->lectures_count === 1 ? '' : 's' }}
                                </a>
                            </td>
                            <td>
                                <a href="{{ route('admin.learning.enrollments.index', ['batch' => $batch->id]) }}" class="badge text-bg-light border text-decoration-none">
                                    {{ $batch->enrollments_count }} enrolled
                                </a>
                                @if ($batch->active_enrollments_count !== $batch->enrollments_count)
                                    <div class="small text-muted mt-1">{{ $batch->active_enrollments_count }} active</div>
                                @endif
                            </td>
                            <td>
                                @include('livewire.admin.learning.partials.notes-summary-cell', [
                                    'lectureNotesCount' => $batch->lectures_with_notes_count,
                                    'linkedNotesCount' => $batch->user_notes_count,
                                    'filterRoute' => route('admin.learning.lectures.index', ['batch' => $batch->id]),
                                ])
                            </td>
                            <td>
                                <a href="{{ route('admin.learning.videos.index', ['batch' => $batch->id]) }}" class="badge text-bg-light border text-decoration-none">
                                    {{ $batch->videos_count }} video{{ $batch->videos_count === 1 ? '' : 's' }}
                                </a>
                            </td>
                            <td>
                                <span class="badge {{ $batch->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">
                                    {{ $batch->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="text-muted small">{{ $batch->created_at?->format('M j, Y') }}</td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.learning.batches.show', $batch) }}" class="btn btn-outline-secondary">View</a>
                                    <button type="button" class="btn btn-outline-primary" wire:click="openEditModal({{ $batch->id }})">Edit</button>
                                    <x-ui.activate-toggle-button :id="$batch->id" :active="$batch->is_active" />
                                    <button type="button" class="btn btn-outline-danger" wire:click="delete({{ $batch->id }})" wire:confirm="Delete this batch and its lectures?">Delete</button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-5">
                                <p class="mb-2">No batches found.</p>
                                <button type="button" class="btn btn-sm btn-primary" wire:click="openCreateModal">Create batch</button>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($batches->hasPages())
            <div class="card-footer bg-white border-0">{{ $batches->links() }}</div>
        @endif
    </div>
</div>
