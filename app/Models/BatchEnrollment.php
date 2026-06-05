<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BatchEnrollment extends Model
{
    protected $fillable = [
        'batch_id',
        'user_id',
        'is_active',
        'enrolled_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'enrolled_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (BatchEnrollment $enrollment) {
            if ($enrollment->enrolled_at === null) {
                $enrollment->enrolled_at = now();
            }
        });
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(Batch::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeForStudents($query)
    {
        return $query->whereHas('user', fn ($q) => $q->where('role', 'user'));
    }
}
