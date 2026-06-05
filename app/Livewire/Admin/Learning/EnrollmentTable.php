<?php

namespace App\Livewire\Admin\Learning;

use App\Livewire\Admin\Learning\Concerns\HasResourceTable;
use App\Models\Batch;
use App\Models\BatchEnrollment;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class EnrollmentTable extends Component
{
    use HasResourceTable;
    use WithPagination;

    public $batchFilter = null;

    public function mount(): void
    {
        $this->sortField = 'enrolled_at';
        $this->sortDirection = 'desc';

        $batchId = request()->integer('batch');
        if ($batchId > 0) {
            $this->batchFilter = $batchId;
        }
    }

    public function updatingBatchFilter(): void
    {
        $this->resetPage();
    }

    public function updatedBatchFilter($value): void
    {
        $this->batchFilter = ($value === '' || $value === null) ? null : (int) $value;
    }

    #[On('enrollmentTableRefresh')]
    public function refreshTable(): void
    {
        $this->resetPage();
    }

    public function openCreateModal(): void
    {
        $this->dispatch('openEnrollmentFormModal', batchId: $this->batchFilter ? (int) $this->batchFilter : null);
    }

    public function openEditModal(int $id): void
    {
        $this->dispatch('openEnrollmentFormModal', id: $id);
    }

    public function delete(int $id): void
    {
        BatchEnrollment::findOrFail($id)->delete();
        $this->selectedIds = array_values(array_diff($this->selectedIds, [$id]));
        $this->resetPage();
        session()->flash('success', 'Enrollment removed.');
    }

    public function toggleActive(int $id): void
    {
        $enrollment = BatchEnrollment::findOrFail($id);
        $enrollment->is_active = ! $enrollment->is_active;
        $enrollment->save();
    }

    public function bulkDelete(): void
    {
        if ($this->selectedIds === []) {
            return;
        }

        BatchEnrollment::whereIn('id', $this->selectedIds)->delete();
        $this->clearSelection();
        $this->resetPage();
        session()->flash('success', 'Selected enrollments removed.');
    }

    protected function bulkSetActive(bool $active): void
    {
        if ($this->selectedIds === []) {
            return;
        }

        BatchEnrollment::whereIn('id', $this->selectedIds)->update(['is_active' => $active]);
        $this->clearSelection();
        session()->flash('success', $active ? 'Selected enrollments activated.' : 'Selected enrollments deactivated.');
    }

    public function resetFilters(): void
    {
        parent::resetFilters();
        $this->batchFilter = null;
    }

    public function render()
    {
        $query = BatchEnrollment::query()
            ->forStudents()
            ->with(['batch', 'user'])
            ->when($this->search !== '', function ($q) {
                $q->where(function ($inner) {
                    $inner->whereHas('user', function ($u) {
                        $u->where('name', 'like', '%'.$this->search.'%')
                            ->orWhere('email', 'like', '%'.$this->search.'%');
                    })->orWhereHas('batch', fn ($b) => $b->where('name', 'like', '%'.$this->search.'%'));
                });
            })
            ->when($this->batchFilter, fn ($q) => $q->where('batch_id', (int) $this->batchFilter));

        $query = $this->applyActiveFilter($query);
        $enrollments = $query->orderBy($this->sortField, $this->sortDirection)->paginate($this->perPage);

        $batches = Batch::orderBy('name')->get();
        $selectedBatch = $this->batchFilter ? Batch::find((int) $this->batchFilter) : null;

        return view('livewire.admin.learning.enrollment-table', compact('enrollments', 'batches', 'selectedBatch'));
    }
}
