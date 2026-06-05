<?php

namespace App\Livewire\Admin\Learning\Concerns;

trait HasResourceTable
{
    public string $search = '';

    public string $sortField = 'created_at';

    public string $sortDirection = 'desc';

    public int $perPage = 10;

    public array $selectedIds = [];

    public ?string $activeFilter = null;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingPerPage(): void
    {
        $this->resetPage();
    }

    public function updatingActiveFilter(): void
    {
        $this->resetPage();
    }

    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->activeFilter = null;
        $this->resetPage();
    }

    public function toggleSelectAllOnPage(array $pageIds): void
    {
        $pageIds = array_map('intval', $pageIds);

        if ($pageIds !== [] && count(array_intersect($pageIds, $this->selectedIds)) === count($pageIds)) {
            $this->selectedIds = array_values(array_diff($this->selectedIds, $pageIds));
        } else {
            $this->selectedIds = array_values(array_unique(array_merge($this->selectedIds, $pageIds)));
        }
    }

    public function bulkActivate(): void
    {
        $this->bulkSetActive(true);
    }

    public function bulkDeactivate(): void
    {
        $this->bulkSetActive(false);
    }

    protected function applyActiveFilter($query, string $column = 'is_active')
    {
        if ($this->activeFilter === 'active') {
            $query->where($column, true);
        } elseif ($this->activeFilter === 'inactive') {
            $query->where($column, false);
        }

        return $query;
    }

    protected function clearSelection(): void
    {
        $this->selectedIds = [];
    }

    abstract protected function bulkSetActive(bool $active): void;
}
