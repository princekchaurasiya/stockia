<?php

namespace App\Exports;

use App\Models\Nifty50Extended;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class Nifty50ExtendedExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize
{
    public function collection()
    {
        return Nifty50Extended::orderBy('sort_order')->get()->map(fn ($r) => [
            $r->security_symbol,
            $r->company_name,
            $r->industry,
            $r->nifty_weightage_pct,
            $r->sector_thematic_index,
            $r->sector_thematic_weightage_pct,
            $r->relationship_of_index,
        ]);
    }

    public function headings(): array
    {
        return [
            'Security Symbol',
            'Company Name',
            'Industry',
            'Nifty Weightage (%)',
            'Sector & Thematic Index',
            'Sector & Thematic Weightage (%)',
            'Relationship of Index (Sector/Thematic)',
        ];
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
            ],
        ];
    }
}
