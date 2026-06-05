<?php

namespace App\Livewire\Tables;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class AdminUsersTable extends BaseTable
{
    protected function baseQuery(): Builder
    {
        return User::query()
            ->where('role', 'admin')
            ->with('account:id,name')
            ->orderBy('name');
    }

    protected function applySearch(Builder $query): Builder
    {
        if ($this->search === '') {
            return $query;
        }

        $term = '%' . trim($this->search) . '%';

        return $query->where(function (Builder $q) use ($term) {
            $q->where('name', 'like', $term)
                ->orWhere('email', 'like', $term);
        });
    }

    public function render()
    {
        return view('livewire.tables.admin-users-table');
    }
}
