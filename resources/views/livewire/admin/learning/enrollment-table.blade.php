<div>
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="d-flex justify-content-end mb-3">
        <button type="button" class="btn btn-primary" wire:click="openCreateModal">
            <i class="bi bi-plus-lg"></i> Enroll student
        </button>
    </div>

    @include('admin.learning.partials.filters-panel', [
        'extraFilters' => view('livewire.admin.learning.partials.enrollment-extra-filters', compact('batches')),
    ])

    @if ($selectedBatch)
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div>
                    <div class="small text-muted text-uppercase fw-semibold">Filtered batch</div>
                    <div class="fw-medium">{{ $selectedBatch->name }}</div>
                    <div class="small text-muted">
                        Batch status:
                        <span class="badge {{ $selectedBatch->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">
                            {{ $selectedBatch->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                </div>
                <a href="{{ route('admin.learning.enrollments.index') }}" class="btn btn-sm btn-outline-secondary">All students</a>
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
                            @php $pageIds = $enrollments->pluck('id')->all(); @endphp
                            <input type="checkbox" class="form-check-input"
                                   @checked($pageIds !== [] && count(array_intersect($pageIds, $selectedIds)) === count($pageIds))
                                   wire:click="toggleSelectAllOnPage({{ json_encode($pageIds) }})">
                        </th>
                        <th>
                            <button type="button" class="btn btn-link btn-sm text-decoration-none text-muted text-uppercase p-0 fw-semibold" wire:click="sortBy('user_id')">Student</button>
                        </th>
                        <th>Batch</th>
                        <th>
                            <button type="button" class="btn btn-link btn-sm text-decoration-none text-muted text-uppercase p-0 fw-semibold" wire:click="sortBy('enrolled_at')">Enrolled</button>
                        </th>
                        <th>Enrollment</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($enrollments as $enrollment)
                        <tr wire:key="enrollment-{{ $enrollment->id }}">
                            <td><input type="checkbox" class="form-check-input" value="{{ $enrollment->id }}" wire:model.live="selectedIds"></td>
                            <td>
                                <div class="fw-medium">{{ $enrollment->user->name ?? '—' }}</div>
                                <div class="small text-muted">{{ $enrollment->user->email ?? '—' }}</div>
                            </td>
                            <td>
                                @if ($enrollment->batch)
                                    <a href="{{ route('admin.learning.enrollments.index', ['batch' => $enrollment->batch_id]) }}" class="text-decoration-none">
                                        {{ $enrollment->batch->name }}
                                    </a>
                                    <div class="small text-muted">
                                        Batch:
                                        <span class="badge {{ $enrollment->batch->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">
                                            {{ $enrollment->batch->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </div>
                                @else
                                    —
                                @endif
                            </td>
                            <td class="small text-muted">{{ $enrollment->enrolled_at?->format('M j, Y') ?? '—' }}</td>
                            <td>
                                <span class="badge {{ $enrollment->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">
                                    {{ $enrollment->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-outline-primary" wire:click="openEditModal({{ $enrollment->id }})">Edit</button>
                                    <x-ui.activate-toggle-button :id="$enrollment->id" :active="$enrollment->is_active" />
                                    <button type="button" class="btn btn-outline-danger" wire:click="delete({{ $enrollment->id }})" wire:confirm="Remove this enrollment?">Remove</button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-5">
                                <p class="mb-2">No students enrolled yet.</p>
                                <button type="button" class="btn btn-sm btn-primary" wire:click="openCreateModal">Enroll student</button>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($enrollments->hasPages())
            <div class="card-footer bg-white border-0">{{ $enrollments->links() }}</div>
        @endif
    </div>
</div>
