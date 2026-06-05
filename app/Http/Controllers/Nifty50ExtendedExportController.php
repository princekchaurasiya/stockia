<?php

namespace App\Http\Controllers;

use App\Exports\Nifty50ExtendedExport;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class Nifty50ExtendedExportController extends Controller
{
    public function __invoke(): BinaryFileResponse
    {
        $filename = 'Nifty50_Extended_' . now()->format('Y-m-d') . '.xlsx';
        return Excel::download(new Nifty50ExtendedExport(), $filename, \Maatwebsite\Excel\Excel::XLSX);
    }
}
