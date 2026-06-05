<?php

namespace App\Exports;

use App\Models\SheetUpload;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class Nifty50Export implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize
{
    public function __construct(
        protected SheetUpload $sheetUpload
    ) {}

    public function collection(): Collection
    {
        $columns = $this->sheetUpload->getDisplayColumns();
        $exportHeaders = $this->getExportHeaders();

        return $this->sheetUpload->rows->map(function ($row) use ($columns, $exportHeaders) {
            $out = [];
            foreach ($columns as $key) {
                $heading = $exportHeaders[$key] ?? $key;
                $out[$heading] = $row->data[$key] ?? '';
            }
            return $out;
        });
    }

    public function headings(): array
    {
        $columns = $this->sheetUpload->getDisplayColumns();
        $exportHeaders = $this->getExportHeaders();
        $headings = [];
        foreach ($columns as $key) {
            $headings[] = $exportHeaders[$key] ?? $key;
        }
        return $headings;
    }

    protected function getExportHeaders(): array
    {
        if ($this->sheetUpload->isNifty50Type()) {
            return config('stockia.nifty50.export_headers', []);
        }
        $columns = $this->sheetUpload->getDisplayColumns();
        $displayNames = config('stockia.column_display_names', []);
        $headers = [];
        foreach ($columns as $col) {
            $key = preg_replace('/_+/', '_', strtolower(str_replace([' ', '&', '/', '(', ')'], '_', $col)));
            $headers[$col] = $displayNames[$key] ?? \Illuminate\Support\Str::title(str_replace('_', ' ', $col));
        }
        return $headers;
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FFFFFF00'],
                ],
                'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT],
            ],
        ];
    }
}
