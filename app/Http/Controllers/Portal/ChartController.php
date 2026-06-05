<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\ChartAsset;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ChartController extends Controller
{
    public function index(Request $request): View
    {
        $query = ChartAsset::active()->orderByDesc('report_date')->orderBy('sort_order');

        if ($request->filled('category')) {
            $query->where('category', $request->string('category'));
        }

        $charts = $query->paginate(18);
        $categories = ChartAsset::active()
            ->whereNotNull('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        return view('portal.charts.index', compact('charts', 'categories'));
    }
}
