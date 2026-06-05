<?php

namespace App\Livewire\Tables;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Centralized base for list tables. Project standard: Livewire tables with
 * Bootstrap pagination, per-page dropdown, and search — not DataTables/jQuery.
 */
abstract class BaseTable extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public int $perPage = 10;

    /** @var array<int> */
    public array $perPageOptions = [5, 10, 20, 50, 100];

    public string $search = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'perPage' => ['except' => 10],
    ];

    /**
     * Return the base query builder. Child classes implement this.
     */
    abstract protected function baseQuery(): Builder;

    /**
     * Apply search to the query. Override in child for custom search.
     */
    protected function applySearch(Builder $query): Builder
    {
        return $query;
    }

    /**
     * Apply sorting to the query. Override in child for custom sort.
     */
    protected function applySort(Builder $query): Builder
    {
        return $query;
    }

    public function getRowsProperty(): LengthAwarePaginator
    {
        $query = $this->baseQuery();
        $query = $this->applySearch($query);
        $query = $this->applySort($query);

        return $query->paginate($this->perPage);
    }

    public function updatedPerPage(): void
    {
        $this->resetPage();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }
}
