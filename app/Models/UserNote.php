<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserNote extends Model
{
    protected $fillable = [
        'user_id',
        'lecture_id',
        'title',
        'body',
        'is_shared',
    ];

    protected $casts = [
        'is_shared' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function lecture(): BelongsTo
    {
        return $this->belongsTo(Lecture::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(UserNoteImage::class)->orderBy('sort_order');
    }

    public function scopeShared($query)
    {
        return $query->where('is_shared', true);
    }

    public function scopeOwnedBy($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }
}
