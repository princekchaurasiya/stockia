<?php

namespace App\Livewire\Tables;

use App\Models\InformationLink;
use Illuminate\Database\Eloquent\Builder;

class InformationLinksTable extends BaseTable
{
    protected function baseQuery(): Builder
    {
        $user = auth()->user();

        if ($user->role === 'superadmin') {
            return InformationLink::query()
                ->with('creator:id,name', 'account:id,name')
                ->orderByRaw('account_id IS NOT NULL')
                ->orderBy('account_id')
                ->orderBy('sort_order');
        }

        $accountId = $user->account_id;
        if ($accountId === null) {
            return InformationLink::query()->whereRaw('1 = 0');
        }

        return InformationLink::query()
            ->with('creator:id,name')
            ->forAccount($accountId)
            ->orderByRaw('account_id IS NULL DESC')
            ->orderBy('sort_order');
    }

    protected function applySearch(Builder $query): Builder
    {
        if ($this->search === '') {
            return $query;
        }

        $term = '%' . trim($this->search) . '%';

        return $query->where(function (Builder $q) use ($term) {
            $q->where('title', 'like', $term)
                ->orWhere('url', 'like', $term);
        });
    }

    public function render()
    {
        return view('livewire.tables.information-links-table');
    }
}
