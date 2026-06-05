<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lecture extends Model
{
    use HasFactory;

    protected $fillable = [
        'batch_id',
        'module_id',
        'title',
        'notes',
        'sort_order',
        'is_active',
    ];

    public function batch(): BelongsTo
    {
        return $this->belongsTo(Batch::class);
    }

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    public function videos(): HasMany
    {
        return $this->hasMany(LectureVideo::class)
            ->orderBy('sort_order');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(LectureDocument::class)
            ->orderBy('sort_order');
    }

    public function userNotes(): HasMany
    {
        return $this->hasMany(UserNote::class);
    }

    public function scopeWithNotes($query)
    {
        return $query->whereNotNull('notes')->where('notes', '!=', '');
    }
}

