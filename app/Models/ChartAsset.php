<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChartAsset extends Model
{
    protected $fillable = [
        'title',
        'category',
        'file_path',
        'file_type',
        'report_date',
        'sort_order',
        'is_active',
        'uploaded_by',
    ];

    protected $casts = [
        'report_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
