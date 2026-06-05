<?php

namespace App\Livewire\Admin\Learning;

use App\Livewire\Admin\Learning\Concerns\HasResourceTable;
use App\Models\Batch;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class BatchTable extends Component
{
    use HasResourceTable;
    use WithPagination;

    #[On('batchTableRefresh')]
    public function refreshTable(): void
    {
        $this->resetPage();
    }

    public function openCreateModal(): void
    {
        $this->dispatch('openBatchFormModal');
    }

    public function openEditModal(int $id): void
    {
        $this->dispatch('openBatchFormModal', id: $id);
    }

    public function delete(int $id): void
    {
        Batch::findOrFail($id)->delete();
        $this->selectedIds = array_values(array_diff($this->selectedIds, [$id]));
        $this->resetPage();
        session()->flash('success', 'Batch deleted.');
    }

    public function toggleActive(int $id): void
    {
        $batch = Batch::findOrFail($id);
        $batch->is_active = ! $batch->is_active;
        $batch->save();
    }

    public function bulkDelete(): void
    {
        if ($this->selectedIds === []) {
            return;
        }

        Batch::whereIn('id', $this->selectedIds)->delete();
        $this->clearSelection();
        $this->resetPage();
        session()->flash('success', 'Selected batches deleted.');
    }

    protected function bulkSetActive(bool $active): void
    {
        if ($this->selectedIds === []) {
            return;
        }

        Batch::whereIn('id', $this->selectedIds)->update(['is_active' => $active]);
        $this->clearSelection();
        session()->flash('success', $active ? 'Selected batches activated.' : 'Selected batches deactivated.');
    }

    public function render()
    {
        $query = Batch::query()
            ->withCount([
                'lectures',
                'videos',
                'userNotes',
                'enrollments',
                'enrollments as active_enrollments_count' => fn ($q) => $q->where('is_active', true),
                'lectures as lectures_with_notes_count' => fn ($q) => $q->withNotes(),
            ])
            ->when($this->search !== '', fn ($q) => $q->where('name', 'like', '%'.$this->search.'%'));

        $query = $this->applyActiveFilter($query);
        $batches = $query->orderBy($this->sortField, $this->sortDirection)->paginate($this->perPage);

        return view('livewire.admin.learning.batch-table', compact('batches'));
    }
}
