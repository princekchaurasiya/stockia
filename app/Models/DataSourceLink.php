<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DataSourceLink extends Model
{
    protected $fillable = ['name', 'slug', 'url', 'display_columns', 'is_active'];

    protected $casts = [
        'display_columns' => 'array',
        'is_active' => 'boolean',
    ];

    public function sheetUploads(): HasMany
    {
        return $this->hasMany(SheetUpload::class, 'data_source_link_id');
    }

    public function getDisplayColumns(): array
    {
        return $this->display_columns ?? config('stockia.nifty50.display_columns', []);
    }
}
