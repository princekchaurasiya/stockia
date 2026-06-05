<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SheetUpload extends Model
{
    protected $fillable = [
        'name',
        'original_name',
        'path',
        'columns',
        'row_count',
        'report_date',
        'user_id',
        'data_source_link_id',
    ];

    protected $casts = [
        'columns' => 'array',
        'report_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function dataSourceLink(): BelongsTo
    {
        return $this->belongsTo(DataSourceLink::class, 'data_source_link_id');
    }

    public function rows(): HasMany
    {
        return $this->hasMany(StockDatum::class, 'sheet_upload_id')->orderBy('row_index');
    }

    public function getDisplayColumns(): array
    {
        $columns = $this->data_source_link_id && $this->dataSourceLink
            ? $this->dataSourceLink->getDisplayColumns()
            : ($this->columns ?? []);

        return $this->filterExcludedColumns($columns);
    }

    /**
     * Remove excluded columns (e.g. Series, ISIN Code) from the list.
     */
    protected function filterExcludedColumns(array $columns): array
    {
        $excluded = array_map('strtolower', config('stockia.excluded_columns', []));

        return array_values(array_filter($columns, function ($col) use ($excluded) {
            return ! in_array(strtolower((string) $col), $excluded);
        }));
    }

    public function isNifty50Type(): bool
    {
        return $this->dataSourceLink && $this->dataSourceLink->slug === config('stockia.nifty50.slug', 'nifty50');
    }

    /**
     * @return array<string, string> column_key => label for display
     */
    public function getDisplayColumnLabels(): array
    {
        if ($this->isNifty50Type()) {
            return config('stockia.nifty50.export_headers', []);
        }
        $cols = $this->getDisplayColumns();
        return array_combine($cols, $cols);
    }
}
