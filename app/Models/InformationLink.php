<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InformationLink extends Model
{
    protected $fillable = ['title', 'url', 'sort_order', 'is_active', 'created_by', 'account_id'];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope: global links (visible to all) — account_id is null.
     */
    public function scopeGlobal(Builder $query): Builder
    {
        return $query->whereNull('account_id');
    }

    /**
     * Scope: links for a specific account (or global).
     */
    public function scopeForAccount(Builder $query, ?int $accountId): Builder
    {
        if ($accountId === null) {
            return $query->global();
        }

        return $query->where(function (Builder $q) use ($accountId) {
            $q->whereNull('account_id')->orWhere('account_id', $accountId);
        });
    }

    /**
     * Scope: active only.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
