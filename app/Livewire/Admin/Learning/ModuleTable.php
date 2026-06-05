<?php

namespace App\Livewire\Admin\Learning;

use App\Livewire\Admin\Learning\Concerns\HasResourceTable;
use App\Models\Module;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class ModuleTable extends Component
{
    use HasResourceTable;
    use WithPagination;

    public $batchFilter = null;

    public function mount(): void
    {
        $this->sortField = 'sort_order';
        $this->sortDirection = 'asc';
    }

    public function updatingBatchFilter(): void
    {
        $this->resetPage();
    }

    public function updatedBatchFilter($value): void
    {
        $this->batchFilter = ($value === '' || $value === null) ? null : (int) $value;
    }

    #[On('moduleTableRefresh')]
    public function refreshTable(): void
    {
        $this->resetPage();
    }

    public function openCreateModal(): void
    {
        $this->dispatch('openModuleFormModal');
    }

    public function openEditModal(int $id): void
    {
        $this->dispatch('openModuleFormModal', id: $id);
    }

    public function delete(int $id): void
    {
        Module::findOrFail($id)->delete();
        $this->selectedIds = array_values(array_diff($this->selectedIds, [$id]));
        $this->resetPage();
        session()->flash('success', 'Module deleted.');
    }

    public function toggleActive(int $id): void
    {
        $module = Module::findOrFail($id);
        $module->is_active = ! $module->is_active;
        $module->save();
    }

    public function bulkDelete(): void
    {
        if ($this->selectedIds === []) {
            return;
        }

        Module::whereIn('id', $this->selectedIds)->delete();
        $this->clearSelection();
        $this->resetPage();
        session()->flash('success', 'Selected modules deleted.');
    }

    protected function bulkSetActive(bool $active): void
    {
        if ($this->selectedIds === []) {
            return;
        }

        Module::whereIn('id', $this->selectedIds)->update(['is_active' => $active]);
        $this->clearSelection();
        session()->flash('success', $active ? 'Selected modules activated.' : 'Selected modules deactivated.');
    }

    public function resetFilters(): void
    {
        parent::resetFilters();
        $this->batchFilter = null;
    }

    public function render()
    {
        $query = Module::query()
            ->with(['batches'])
            ->withCount([
                'lectures',
                'videos',
                'userNotes',
                'lectures as lectures_with_notes_count' => fn ($q) => $q->withNotes(),
            ])
            ->when($this->search !== '', function ($q) {
                $q->where(function ($inner) {
                    $inner->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('timeframe', 'like', '%'.$this->search.'%')
                        ->orWhere('description', 'like', '%'.$this->search.'%');
                });
            })
            ->when($this->batchFilter, fn ($q) => $q->whereHas('lectures', fn ($l) => $l->where('batch_id', (int) $this->batchFilter)));

        $query = $this->applyActiveFilter($query);
        $modules = $query
            ->orderBy($this->sortField, $this->sortDirection)
            ->when($this->sortField === 'sort_order', fn ($q) => $q->orderBy('name'))
            ->paginate($this->perPage);

        $batches = \App\Models\Batch::orderBy('name')->get();

        return view('livewire.admin.learning.module-table', compact('modules', 'batches'));
    }
}
