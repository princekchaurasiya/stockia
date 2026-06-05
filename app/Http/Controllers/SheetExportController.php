<?php

namespace App\Http\Controllers;

use App\Exports\Nifty50Export;
use App\Models\SheetUpload;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class SheetExportController extends Controller
{
    /**
     * Export sheet as Excel. For Nifty 50 type, only Company Name, Industry, Symbol are exported.
     */
    public function __invoke(Request $request): BinaryFileResponse
    {
        $request->validate([
            'sheet' => 'required|integer|exists:sheet_uploads,id',
        ]);

        $upload = SheetUpload::with(['rows', 'dataSourceLink'])->findOrFail($request->integer('sheet'));

        if ($upload->user_id !== null && $upload->user_id !== auth()->id()) {
            abort(403);
        }

        $filename = pathinfo($upload->original_name ?: 'export.xlsx', PATHINFO_FILENAME) . '.xlsx';

        return Excel::download(new Nifty50Export($upload), $filename, \Maatwebsite\Excel\Excel::XLSX);
    }
}
