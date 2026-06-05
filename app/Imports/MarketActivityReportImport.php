<?php

namespace App\Imports;

use App\Models\SheetUpload;
use App\Models\StockDatum;
use App\Services\IndexDataExtractor;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;

class MarketActivityReportImport implements ToCollection
{
    private ?int $createdUploadId = null;

    private static array $columnAliases = [
        'index' => ['index'],
        'previous_close' => ['previous close', 'previous_close', 'prev. close', 'prev close', 'prev_close'],
        'open' => ['open'],
        'high' => ['high'],
        'low' => ['low'],
        'close' => ['close', 'close price'],
        'gain_loss' => ['gain/loss', 'gain loss', 'gain_loss', 'gainloss'],
    ];

    public function __construct(
        protected string $originalName,
        protected string $storedPath,
        protected ?int $userId = null,
        protected ?int $dataSourceLinkId = null
    ) {}

    public function collection(Collection $rows): void
    {
        $reportDate = $this->extractReportDate($rows);

        $excluded = array_map('strtolower', config('stockia.market_activity.excluded_indices', []));
        $headerRowIdx = null;
        $colMap = [];

        foreach ($rows as $idx => $row) {
            $cells = $this->rowToCells($row);
            $normalized = array_map(fn ($v) => trim(strtolower(str_replace(["\n", "\r", '.'], ' ', (string) $v))), $cells);

            $hasIndex = false;
            $hasPreviousClose = false;
            $hasClose = false;
            foreach ($normalized as $h) {
                if ($h === 'index' || (str_contains($h, 'index') && ! str_contains($h, 'india'))) {
                    $hasIndex = true;
                }
                if (str_contains($h, 'previous') && str_contains($h, 'close')) {
                    $hasPreviousClose = true;
                }
                if (($h === 'close' || $h === 'close price') || (str_contains($h, 'close') && ! str_contains($h, 'previous'))) {
                    $hasClose = true;
                }
            }

            if ($hasIndex && ($hasPreviousClose || $hasClose)) {
                foreach (self::$columnAliases as $key => $aliases) {
                    foreach ($normalized as $colIdx => $h) {
                        if (strlen((string) $h) === 0) {
                            continue;
                        }
                        foreach ($aliases as $a) {
                            $matches = $h === $a || str_contains($h, $a);
                            if ($key === 'close' && str_contains($h, 'previous')) {
                                $matches = false;
                            }
                            if ($key === 'previous_close' && $h === 'close' && ! str_contains($h, 'previous')) {
                                $matches = false;
                            }
                            if ($matches) {
                                $colMap[$key] = $colIdx;
                                break 2;
                            }
                        }
                    }
                }
                if (isset($colMap['index'], $colMap['close'], $colMap['previous_close'], $colMap['open'], $colMap['high'], $colMap['low'])) {
                    $headerRowIdx = $idx;
                    Log::info('MarketActivity header located', [
                        'row' => $idx,
                        'file' => $this->originalName,
                    ]);
                    break;
                }
                $colMap = [];
            }
        }

        if ($headerRowIdx === null || ! isset($colMap['index'], $colMap['previous_close'], $colMap['close'], $colMap['open'], $colMap['high'], $colMap['low'])) {
            throw new \RuntimeException(
                'Could not find Index Performance table in the file. The NSE report has an unstructured layout. ' .
                'Reading each cell: look for the date (e.g. 16-Feb-2026), then find the row with headers INDEX, PREVIOUS CLOSE, OPEN, HIGH, LOW, CLOSE, GAIN/LOSS. ' .
                'Ensure you upload the correct market activity Excel/CSV file.'
            );
        }

        $columns = ['index', 'previous_close', 'open', 'high', 'low', 'close', 'gain_loss'];
        $dataRows = [];
        $sectionBoundaries = array_map('strtolower', config('stockia.market_activity.section_boundaries', []));

        foreach ($rows as $idx => $row) {
            if ($idx <= $headerRowIdx) {
                continue;
            }
            $cells = $this->rowToCells($row);
            $indexName = trim((string) ($cells[$colMap['index']] ?? ''));
            if (empty($indexName)) {
                continue;
            }
            $indexLower = strtolower($indexName);
            if (in_array($indexLower, $excluded, true)) {
                continue;
            }
            if ($this->isSectionBoundary($indexLower, $sectionBoundaries)) {
                break;
            }
            if ($indexLower === 'symbol') {
                break;
            }
            $rowData = [];
            foreach ($colMap as $key => $colIdx) {
                $val = $cells[$colIdx] ?? null;
                $rowData[$key] = $this->normalizeValue($val, $key);
            }
            $extractor = app(IndexDataExtractor::class);
            $normalized = $extractor->normalize($rowData);
            if ($normalized !== null) {
                $rowData['computed'] = $normalized;
            }
            $dataRows[] = $rowData;
        }

        if (empty($dataRows)) {
            throw new \RuntimeException('No index data found after filtering. Check that the table contains index names (e.g. Nifty 50, Nifty Bank).');
        }

        $upload = SheetUpload::create([
            'original_name' => $this->originalName,
            'path' => $this->storedPath,
            'columns' => $columns,
            'row_count' => count($dataRows),
            'report_date' => $reportDate,
            'user_id' => $this->userId,
            'data_source_link_id' => $this->dataSourceLinkId,
        ]);

        $this->createdUploadId = $upload->id;

        foreach ($dataRows as $i => $rowData) {
            StockDatum::create([
                'sheet_upload_id' => $upload->id,
                'row_index' => $i,
                'data' => $rowData,
            ]);
        }
    }

    /**
     * Scan all cells for date pattern like MA160226 (MA + DDMMYY).
     * Returns Carbon date or null if not found.
     */
    private function extractReportDate(Collection $rows): ?\Carbon\Carbon
    {
        foreach ($rows as $row) {
            $cells = $this->rowToCells($row);
            foreach ($cells as $cell) {
                $val = trim((string) $cell);
                if (preg_match('/MA(\d{2})(\d{2})(\d{2})/i', $val, $m)) {
                    $day = (int) $m[1];
                    $month = (int) $m[2];
                    $year = (int) $m[3];
                    $year += $year < 50 ? 2000 : 1900;
                    if ($day >= 1 && $day <= 31 && $month >= 1 && $month <= 12) {
                        return \Carbon\Carbon::createFromDate($year, $month, $day);
                    }
                }
            }
        }
        return null;
    }

    private function isSectionBoundary(string $indexLower, array $boundaries): bool
    {
        foreach ($boundaries as $b) {
            if ($indexLower === $b || str_contains($indexLower, $b)) {
                return true;
            }
        }
        return false;
    }

    /** Convert a row to a zero-indexed array of cell values. Handles sparse layouts (e.g. INDEX in column B). */
    private function rowToCells($row): array
    {
        $arr = $row instanceof Collection ? $row->toArray() : (array) $row;
        return array_values($arr);
    }

    private function normalizeValue($value, string $key): mixed
    {
        if ($value === null || $value === '') {
            return '';
        }
        if (is_string($value)) {
            $trimmed = trim(str_replace([',', ' '], '', $value));
            if ($trimmed === '') {
                return '';
            }
            if (is_numeric($trimmed)) {
                return str_contains($trimmed, '.') ? (float) $trimmed : (int) $trimmed;
            }
        }
        return $value;
    }

    public function getCreatedUploadId(): ?int
    {
        return $this->createdUploadId;
    }
}
