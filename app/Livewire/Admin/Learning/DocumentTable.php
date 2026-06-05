<?php

namespace App\Livewire\Admin\Learning;

use App\Livewire\Admin\Learning\Concerns\HasResourceTable;
use App\Models\Batch;
use App\Models\Lecture;
use App\Models\LectureDocument;
use App\Models\Module;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class DocumentTable extends Component
{
    use HasResourceTable;
    use WithPagination;

    public $lectureFilter = null;

    public $batchFilter = null;

    public $moduleFilter = null;

    public ?string $fileTypeFilter = null;

    public function mount(): void
    {
        $this->sortField = 'sort_order';

        $lectureId = request()->integer('lecture');
        if ($lectureId > 0) {
            $this->lectureFilter = $lectureId;
        }

        $batchId = request()->integer('batch');
        if ($batchId > 0) {
            $this->batchFilter = $batchId;
        }

        $moduleId = request()->integer('module');
        if ($moduleId > 0) {
            $this->moduleFilter = $moduleId;
        }
    }

    public function updatingLectureFilter(): void
    {
        $this->resetPage();
    }

    public function updatingFileTypeFilter(): void
    {
        $this->resetPage();
    }

    public function updatingBatchFilter(): void
    {
        $this->resetPage();
    }

    public function updatingModuleFilter(): void
    {
        $this->resetPage();
    }

    public function updatedBatchFilter($value): void
    {
        $this->batchFilter = ($value === '' || $value === null) ? null : (int) $value;
    }

    public function updatedModuleFilter($value): void
    {
        $this->moduleFilter = ($value === '' || $value === null) ? null : (int) $value;
    }

    public function updatedLectureFilter($value): void
    {
        $this->lectureFilter = ($value === '' || $value === null) ? null : (int) $value;
    }

    #[On('documentTableRefresh')]
    public function refreshTable(): void
    {
        $this->resetPage();
    }

    public function openCreateModal(): void
    {
        $this->dispatch('openDocumentFormModal', lectureId: $this->lectureFilter ? (int) $this->lectureFilter : null);
    }

    public function openEditModal(int $id): void
    {
        $this->dispatch('openDocumentFormModal', id: $id);
    }

    public function delete(int $id): void
    {
        DB::beginTransaction();

        try {
            $document = LectureDocument::lockForUpdate()->findOrFail($id);
            $path = $document->file_path;
            $document->delete();
            DB::commit();

            if ($path && Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }

            $this->selectedIds = array_values(array_diff($this->selectedIds, [$id]));
            $this->resetPage();
            session()->flash('success', 'Document deleted.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Failed to delete lecture document', ['document_id' => $id, 'error' => $e->getMessage()]);
            session()->flash('error', 'Unable to delete document.');
        }
    }

    public function toggleActive(int $id): void
    {
        $document = LectureDocument::findOrFail($id);
        $document->is_active = ! $document->is_active;
        $document->save();
    }

    public function bulkDelete(): void
    {
        if ($this->selectedIds === []) {
            return;
        }

        $documents = LectureDocument::whereIn('id', $this->selectedIds)->get();

        foreach ($documents as $document) {
            $this->delete($document->id);
        }

        $this->clearSelection();
    }

    protected function bulkSetActive(bool $active): void
    {
        if ($this->selectedIds === []) {
            return;
        }

        LectureDocument::whereIn('id', $this->selectedIds)->update(['is_active' => $active]);
        $this->clearSelection();
        session()->flash('success', $active ? 'Selected documents activated.' : 'Selected documents deactivated.');
    }

    public function resetFilters(): void
    {
        parent::resetFilters();
        $this->lectureFilter = null;
        $this->batchFilter = null;
        $this->moduleFilter = null;
        $this->fileTypeFilter = null;
    }

    public function render()
    {
        $query = LectureDocument::query()->with(['lecture.batch', 'lecture.module'])
            ->when($this->search !== '', function ($q) {
                $q->where(function ($inner) {
                    $inner->where('title', 'like', '%'.$this->search.'%')
                        ->orWhereHas('lecture', function ($l) {
                            $l->where('title', 'like', '%'.$this->search.'%')
                                ->orWhere('notes', 'like', '%'.$this->search.'%');
                        });
                });
            })
            ->when($this->lectureFilter, fn ($q) => $q->where('lecture_id', (int) $this->lectureFilter))
            ->when($this->batchFilter, fn ($q) => $q->whereHas('lecture', fn ($l) => $l->where('batch_id', (int) $this->batchFilter)))
            ->when($this->moduleFilter, fn ($q) => $q->whereHas('lecture', fn ($l) => $l->where('module_id', (int) $this->moduleFilter)))
            ->when($this->fileTypeFilter !== null && $this->fileTypeFilter !== '', fn ($q) => $q->where('file_type', $this->fileTypeFilter));

        $query = $this->applyActiveFilter($query);
        $documents = $query->orderBy($this->sortField, $this->sortDirection)->paginate($this->perPage);

        $lectures = Lecture::with(['batch', 'module'])->orderBy('title')->get();
        $selectedLecture = $this->lectureFilter ? Lecture::with(['batch', 'module'])->find((int) $this->lectureFilter) : null;
        $selectedBatch = $this->batchFilter ? Batch::find((int) $this->batchFilter) : null;
        $selectedModule = $this->moduleFilter ? Module::find((int) $this->moduleFilter) : null;
        $batches = Batch::orderBy('name')->get();
        $modules = Module::orderBy('sort_order')->get();

        $fileTypes = LectureDocument::query()
            ->whereNotNull('file_type')
            ->where('file_type', '!=', '')
            ->distinct()
            ->orderBy('file_type')
            ->pluck('file_type');

        return view('livewire.admin.learning.document-table', compact('documents', 'lectures', 'selectedLecture', 'selectedBatch', 'selectedModule', 'batches', 'modules', 'fileTypes'));
    }
}
