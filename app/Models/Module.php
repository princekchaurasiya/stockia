<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Module extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'timeframe',
        'description',
        'sort_order',
        'is_active',
    ];

    public function lectures(): HasMany
    {
        return $this->hasMany(Lecture::class);
    }

    public function batches(): BelongsToMany
    {
        return $this->belongsToMany(Batch::class, 'lectures', 'module_id', 'batch_id')
            ->distinct();
    }

    public function videos(): HasManyThrough
    {
        return $this->hasManyThrough(LectureVideo::class, Lecture::class);
    }

    public function documents(): HasManyThrough
    {
        return $this->hasManyThrough(LectureDocument::class, Lecture::class);
    }

    public function userNotes(): HasManyThrough
    {
        return $this->hasManyThrough(UserNote::class, Lecture::class);
    }
}

