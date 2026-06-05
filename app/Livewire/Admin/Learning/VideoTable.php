<?php

namespace App\Livewire\Admin\Learning;

use App\Livewire\Admin\Learning\Concerns\HasResourceTable;
use App\Models\Batch;
use App\Models\Lecture;
use App\Models\LectureVideo;
use App\Models\Module;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class VideoTable extends Component
{
    use HasResourceTable;
    use WithPagination;

    public $lectureFilter = null;

    public $batchFilter = null;

    public $moduleFilter = null;

    public ?string $videoTypeFilter = null;

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

    public function updatingVideoTypeFilter(): void
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

    #[On('videoTableRefresh')]
    public function refreshTable(): void
    {
        $this->resetPage();
    }

    public function openCreateModal(): void
    {
        $this->dispatch('openVideoFormModal', lectureId: $this->lectureFilter ? (int) $this->lectureFilter : null);
    }

    public function openEditModal(int $id): void
    {
        $this->dispatch('openVideoFormModal', id: $id);
    }

    public function delete(int $id): void
    {
        LectureVideo::findOrFail($id)->delete();
        $this->selectedIds = array_values(array_diff($this->selectedIds, [$id]));
        $this->resetPage();
        session()->flash('success', 'Video deleted.');
    }

    public function toggleActive(int $id): void
    {
        $video = LectureVideo::findOrFail($id);
        $video->is_active = ! $video->is_active;
        $video->save();
    }

    public function bulkDelete(): void
    {
        if ($this->selectedIds === []) {
            return;
        }

        LectureVideo::whereIn('id', $this->selectedIds)->delete();
        $this->clearSelection();
        $this->resetPage();
        session()->flash('success', 'Selected videos deleted.');
    }

    protected function bulkSetActive(bool $active): void
    {
        if ($this->selectedIds === []) {
            return;
        }

        LectureVideo::whereIn('id', $this->selectedIds)->update(['is_active' => $active]);
        $this->clearSelection();
        session()->flash('success', $active ? 'Selected videos activated.' : 'Selected videos deactivated.');
    }

    public function resetFilters(): void
    {
        parent::resetFilters();
        $this->lectureFilter = null;
        $this->batchFilter = null;
        $this->moduleFilter = null;
        $this->videoTypeFilter = null;
    }

    public function render()
    {
        $query = LectureVideo::query()->with(['lecture.batch', 'lecture.module'])
            ->when($this->search !== '', function ($q) {
                $q->where(function ($inner) {
                    $inner->where('label', 'like', '%'.$this->search.'%')
                        ->orWhereHas('lecture', function ($l) {
                            $l->where('title', 'like', '%'.$this->search.'%')
                                ->orWhere('notes', 'like', '%'.$this->search.'%');
                        });
                });
            })
            ->when($this->lectureFilter, fn ($q) => $q->where('lecture_id', (int) $this->lectureFilter))
            ->when($this->batchFilter, fn ($q) => $q->whereHas('lecture', fn ($l) => $l->where('batch_id', (int) $this->batchFilter)))
            ->when($this->moduleFilter, fn ($q) => $q->whereHas('lecture', fn ($l) => $l->where('module_id', (int) $this->moduleFilter)))
            ->when($this->videoTypeFilter !== null && $this->videoTypeFilter !== '', fn ($q) => $q->where('video_type', $this->videoTypeFilter));

        $query = $this->applyActiveFilter($query);
        $videos = $query->orderBy($this->sortField, $this->sortDirection)->paginate($this->perPage);

        $lectures = Lecture::with(['batch', 'module'])->orderBy('title')->get();
        $selectedLecture = $this->lectureFilter ? Lecture::with(['batch', 'module'])->find((int) $this->lectureFilter) : null;
        $selectedBatch = $this->batchFilter ? Batch::find((int) $this->batchFilter) : null;
        $selectedModule = $this->moduleFilter ? Module::find((int) $this->moduleFilter) : null;
        $batches = Batch::orderBy('name')->get();
        $modules = Module::orderBy('sort_order')->get();

        $videoTypes = LectureVideo::query()
            ->whereNotNull('video_type')
            ->where('video_type', '!=', '')
            ->distinct()
            ->orderBy('video_type')
            ->pluck('video_type');

        return view('livewire.admin.learning.video-table', compact('videos', 'lectures', 'selectedLecture', 'selectedBatch', 'selectedModule', 'batches', 'modules', 'videoTypes'));
    }
}
