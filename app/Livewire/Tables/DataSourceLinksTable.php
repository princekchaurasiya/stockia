<?php

namespace App\Livewire\Tables;

use App\Models\DataSourceLink;
use Illuminate\Database\Eloquent\Builder;

class DataSourceLinksTable extends BaseTable
{
    protected function baseQuery(): Builder
    {
        return DataSourceLink::query()->orderBy('name');
    }

    protected function applySearch(Builder $query): Builder
    {
        if ($this->search === '') {
            return $query;
        }

        $term = '%' . trim($this->search) . '%';

        return $query->where(function (Builder $q) use ($term) {
            $q->where('name', 'like', $term)
                ->orWhere('slug', 'like', $term);
        });
    }

    public function render()
    {
        return view('livewire.tables.data-source-links-table');
    }
}
