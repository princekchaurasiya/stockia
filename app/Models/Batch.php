<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Batch extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'is_active',
    ];

    public function lectures(): HasMany
    {
        return $this->hasMany(Lecture::class);
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

    public function enrollments(): HasMany
    {
        return $this->hasMany(BatchEnrollment::class);
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'batch_enrollments')
            ->withPivot(['is_active', 'enrolled_at'])
            ->withTimestamps();
    }
}

