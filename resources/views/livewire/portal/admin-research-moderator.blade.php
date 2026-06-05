<div>
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="btn-group mb-3" role="group">
        @foreach(['pending', 'approved', 'rejected', 'all'] as $status)
            <button type="button"
                    class="btn btn-sm {{ $statusFilter === $status ? 'btn-primary' : 'btn-outline-primary' }}"
                    wire:click="setStatusFilter('{{ $status }}')">
                {{ ucfirst($status) }}
            </button>
        @endforeach
    </div>

    @if($rejectingId)
        <div class="card mb-3 border-warning">
            <div class="card-body">
                <h3 class="h6">Reject upload</h3>
                <textarea class="form-control mb-2" rows="2" wire:model="rejectionReason" placeholder="Optional reason"></textarea>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-warning btn-sm" wire:click="confirmReject">Confirm reject</button>
                    <button type="button" class="btn btn-outline-secondary btn-sm" wire:click="cancelReject">Cancel</button>
                </div>
            </div>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped align-middle mb-0">
                    <thead>
                    <tr>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Uploader</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($uploads as $upload)
                        <tr>
                            <td>
                                {{ $upload->title }}
                                <div class="small text-muted">{{ $upload->original_name }}</div>
                            </td>
                            <td>{{ str_replace('_', ' ', $upload->category) }}</td>
                            <td>{{ $upload->user?->name ?? '—' }}</td>
                            <td>{{ $upload->report_date?->format('d-M-Y') ?? $upload->created_at->format('d-M-Y') }}</td>
                            <td><span class="badge text-bg-light">{{ $upload->status }}</span></td>
                            <td class="text-end">
                                <a href="{{ asset('storage/'.$upload->file_path) }}" target="_blank" class="btn btn-sm btn-outline-secondary">View</a>
                                @if($upload->status === 'pending')
                                    <button type="button" class="btn btn-sm btn-success" wire:click="approve({{ $upload->id }})">Approve</button>
                                    <button type="button" class="btn btn-sm btn-warning" wire:click="showReject({{ $upload->id }})">Reject</button>
                                @endif
                                <button type="button" class="btn btn-sm btn-outline-danger" wire:click="delete({{ $upload->id }})" wire:confirm="Delete this upload?">Delete</button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-muted text-center py-3">No uploads in this queue.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            {{ $uploads->links() }}
        </div>
    </div>
</div>
