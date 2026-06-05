<?php

namespace App\Http\Controllers;

use App\Models\SheetUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SheetViewController extends Controller
{
    /**
     * Show uploaded sheet data in tabular format.
     */
    public function show(Request $request, SheetUpload $sheet): View
    {
        if ($sheet->user_id !== null && $sheet->user_id !== $request->user()?->id) {
            abort(403);
        }

        $sheet->load(['rows' => fn ($q) => $q->orderBy('row_index')]);

        $columns = $sheet->getDisplayColumns();
        $labels = [];
        $displayNames = config('stockia.column_display_names', []);
        foreach ($columns as $col) {
            $key = preg_replace('/_+/', '_', strtolower(str_replace([' ', '&', '/', '(', ')'], '_', $col)));
            $labels[$col] = $displayNames[$key] ?? Str::title(str_replace('_', ' ', $col));
        }

        return view('sheet.show', [
            'sheet' => $sheet,
            'columns' => $columns,
            'labels' => $labels,
        ]);
    }
}
