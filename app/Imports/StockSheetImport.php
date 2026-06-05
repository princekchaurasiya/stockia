<?php

namespace App\Imports;

use App\Models\SheetUpload;
use App\Models\StockDatum;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class StockSheetImport implements ToCollection, WithHeadingRow
{
    private ?int $createdUploadId = null;

    public function __construct(
        protected string $originalName,
        protected string $storedPath,
        protected ?int $userId = null,
        protected ?int $dataSourceLinkId = null
    ) {}

    public function collection(Collection $rows): void
    {
        if ($rows->isEmpty()) {
            return;
        }

        $first = $rows->first();
        $columns = array_keys($first->toArray());

        $upload = SheetUpload::create([
            'original_name' => $this->originalName,
            'path' => $this->storedPath,
            'columns' => $columns,
            'row_count' => $rows->count(),
            'user_id' => $this->userId,
            'data_source_link_id' => $this->dataSourceLinkId,
        ]);

        $this->createdUploadId = $upload->id;

        foreach ($rows->values() as $index => $row) {
            StockDatum::create([
                'sheet_upload_id' => $upload->id,
                'row_index' => $index,
                'data' => $this->normalizeRowData($row->toArray()),
            ]);
        }
    }

    public function getCreatedUploadId(): ?int
    {
        return $this->createdUploadId;
    }

    /**
     * Normalize cell values: Excel often stores numbers as strings when copy-pasted.
     * Handles numeric strings (including with comma thousand separators) for correct sorting/charts.
     */
    protected function normalizeRowData(array $data): array
    {
        foreach ($data as $key => $value) {
            if ($value === null || $value === '') {
                continue;
            }
            if (is_string($value)) {
                $trimmed = trim(str_replace([',', ' '], '', $value));
                if ($trimmed === '') {
                    continue;
                }
                if (is_numeric($trimmed)) {
                    $data[$key] = str_contains($trimmed, '.') ? (float) $trimmed : (int) $trimmed;
                }
            }
        }
        return $data;
    }
}
