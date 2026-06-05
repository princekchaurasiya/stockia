<?php

namespace App\Livewire\Portal;

use App\Models\ResearchUpload;
use App\Services\ResearchUploadService;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class StudentResearchHub extends Component
{
    use WithFileUploads;
    use WithPagination;

    public string $category = 'fii';
    public string $title = '';
    public ?string $report_date = null;
    public $file;
    public string $categoryFilter = '';

    protected $rules = [
        'category' => ['required', 'in:fii,dii,open_interest,sector,stock_research,other'],
        'title' => ['required', 'string', 'max:255'],
        'report_date' => ['nullable', 'date'],
        'file' => ['required', 'file', 'mimes:pdf,xlsx,xls,csv', 'max:20480'],
    ];

    public function upload(): void
    {
        $data = $this->validate();

        app(ResearchUploadService::class)->store(
            $this->file,
            auth()->user(),
            [
                'category' => $data['category'],
                'title' => $data['title'],
                'report_date' => $data['report_date'] ?? null,
            ]
        );

        $this->reset(['title', 'report_date', 'file']);
        $this->category = 'fii';
        $this->resetPage();
        session()->flash('success', 'Upload submitted for admin review.');
    }

    public function updatedCategoryFilter(): void
    {
        $this->resetPage();
    }

    public function deleteUpload(int $id): void
    {
        $upload = ResearchUpload::whereKey($id)
            ->where('user_id', auth()->id())
            ->whereIn('status', ['pending', 'rejected'])
            ->firstOrFail();

        app(ResearchUploadService::class)->delete($upload);
        session()->flash('success', 'Upload removed.');
    }

    public function render()
    {
        $approvedQuery = ResearchUpload::approved()->with('user')->orderByDesc('report_date')->orderByDesc('created_at');

        if ($this->categoryFilter !== '') {
            $approvedQuery->where('category', $this->categoryFilter);
        }

        return view('livewire.portal.student-research-hub', [
            'approved' => $approvedQuery->paginate(12),
            'myUploads' => ResearchUpload::where('user_id', auth()->id())
                ->orderByDesc('created_at')
                ->limit(10)
                ->get(),
        ]);
    }
}
