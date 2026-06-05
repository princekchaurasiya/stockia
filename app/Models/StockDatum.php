<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockDatum extends Model
{
    protected $table = 'stock_data';

    protected $fillable = ['sheet_upload_id', 'row_index', 'data'];

    protected $casts = [
        'data' => 'array',
    ];

    public function sheetUpload(): BelongsTo
    {
        return $this->belongsTo(SheetUpload::class);
    }
}
