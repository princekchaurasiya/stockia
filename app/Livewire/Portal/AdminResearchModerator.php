<?php

namespace App\Livewire\Portal;

use App\Models\ResearchUpload;
use App\Services\ResearchUploadService;
use Livewire\Component;
use Livewire\WithPagination;

class AdminResearchModerator extends Component
{
    use WithPagination;

    public string $statusFilter = 'pending';
    public ?string $rejectionReason = null;
    public ?int $rejectingId = null;

    public function setStatusFilter(string $status): void
    {
        $this->statusFilter = $status;
        $this->resetPage();
    }

    public function approve(int $id): void
    {
        $upload = ResearchUpload::findOrFail($id);
        app(ResearchUploadService::class)->approve($upload, auth()->user());
        session()->flash('success', 'Research upload approved.');
    }

    public function showReject(int $id): void
    {
        $this->rejectingId = $id;
        $this->rejectionReason = null;
    }

    public function confirmReject(): void
    {
        if (! $this->rejectingId) {
            return;
        }

        $upload = ResearchUpload::findOrFail($this->rejectingId);
        app(ResearchUploadService::class)->reject($upload, auth()->user(), $this->rejectionReason);
        $this->rejectingId = null;
        $this->rejectionReason = null;
        session()->flash('success', 'Research upload rejected.');
    }

    public function cancelReject(): void
    {
        $this->rejectingId = null;
        $this->rejectionReason = null;
    }

    public function delete(int $id): void
    {
        $upload = ResearchUpload::findOrFail($id);
        app(ResearchUploadService::class)->delete($upload);
        session()->flash('success', 'Research upload deleted.');
    }

    public function render()
    {
        $query = ResearchUpload::with('user')->orderByDesc('created_at');

        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        return view('livewire.portal.admin-research-moderator', [
            'uploads' => $query->paginate(15),
        ]);
    }
}
