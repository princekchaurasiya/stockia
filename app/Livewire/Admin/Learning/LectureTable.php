<?php

namespace App\Livewire\Admin\Learning;

use App\Livewire\Admin\Learning\Concerns\HasResourceTable;
use App\Models\Batch;
use App\Models\Lecture;
use App\Models\Module;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class LectureTable extends Component
{
    use HasResourceTable;
    use WithPagination;

    public $batchFilter = null;

    public $moduleFilter = null;

    public $lectureFilter = null;

    public function mount(): void
    {
        $batchId = request()->integer('batch');
        if ($batchId > 0) {
            $this->batchFilter = $batchId;
        }

        $moduleId = request()->integer('module');
        if ($moduleId > 0) {
            $this->moduleFilter = $moduleId;
        }

        $lectureId = request()->integer('lecture');
        if ($lectureId > 0) {
            $lecture = Lecture::query()->find($lectureId);
            if ($lecture) {
                $this->batchFilter ??= $lecture->batch_id;
                $this->moduleFilter ??= $lecture->module_id;
                $this->lectureFilter = $lectureId;
            }
        }

        $editId = request()->integer('edit');
        if ($editId > 0) {
            $this->dispatch('openLectureFormModal', id: $editId);
        }
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

    #[On('lectureTableRefresh')]
    public function refreshTable(): void
    {
        $this->resetPage();
    }

    public function openCreateModal(): void
    {
        $this->dispatch('openLectureFormModal');
    }

    public function openEditModal(int $id): void
    {
        $this->dispatch('openLectureFormModal', id: $id);
    }

    public function delete(int $id): void
    {
        Lecture::findOrFail($id)->delete();
        $this->selectedIds = array_values(array_diff($this->selectedIds, [$id]));
        $this->resetPage();
        session()->flash('success', 'Lecture deleted.');
    }

    public function toggleActive(int $id): void
    {
        $lecture = Lecture::findOrFail($id);
        $lecture->is_active = ! $lecture->is_active;
        $lecture->save();
    }

    public function bulkDelete(): void
    {
        if ($this->selectedIds === []) {
            return;
        }

        Lecture::whereIn('id', $this->selectedIds)->delete();
        $this->clearSelection();
        $this->resetPage();
        session()->flash('success', 'Selected lectures deleted.');
    }

    protected function bulkSetActive(bool $active): void
    {
        if ($this->selectedIds === []) {
            return;
        }

        Lecture::whereIn('id', $this->selectedIds)->update(['is_active' => $active]);
        $this->clearSelection();
        session()->flash('success', $active ? 'Selected lectures activated.' : 'Selected lectures deactivated.');
    }

    public function resetFilters(): void
    {
        parent::resetFilters();
        $this->batchFilter = null;
        $this->moduleFilter = null;
        $this->lectureFilter = null;
    }

    public function render()
    {
        $query = Lecture::query()->with(['batch', 'module'])
            ->with(['userNotes' => fn ($q) => $q->with('user')->orderByDesc('updated_at')])
            ->withCount(['videos', 'documents', 'userNotes'])
            ->when($this->search !== '', function ($q) {
                $q->where(function ($inner) {
                    $inner->where('title', 'like', '%'.$this->search.'%')
                        ->orWhere('notes', 'like', '%'.$this->search.'%')
                        ->orWhereHas('batch', fn ($b) => $b->where('name', 'like', '%'.$this->search.'%'))
                        ->orWhereHas('module', fn ($m) => $m->where('name', 'like', '%'.$this->search.'%'))
                        ->orWhereHas('userNotes', function ($n) {
                            $n->where('title', 'like', '%'.$this->search.'%')
                                ->orWhere('body', 'like', '%'.$this->search.'%');
                        });
                });
            })
            ->when($this->batchFilter, fn ($q) => $q->where('batch_id', (int) $this->batchFilter))
            ->when($this->moduleFilter, fn ($q) => $q->where('module_id', (int) $this->moduleFilter))
            ->when($this->lectureFilter, fn ($q) => $q->whereKey((int) $this->lectureFilter));

        $query = $this->applyActiveFilter($query);
        $lectures = $query->orderBy($this->sortField, $this->sortDirection)->paginate($this->perPage);

        $batches = Batch::orderBy('name')->get();
        $modules = Module::orderBy('sort_order')->get();
        $selectedBatch = $this->batchFilter ? Batch::find((int) $this->batchFilter) : null;
        $selectedModule = $this->moduleFilter ? Module::find((int) $this->moduleFilter) : null;

        return view('livewire.admin.learning.lecture-table', compact('lectures', 'batches', 'modules', 'selectedBatch', 'selectedModule'));
    }
}
