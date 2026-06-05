<?php

namespace App\Http\Controllers;

use App\Models\DataSourceLink;
use App\Models\SheetUpload;
use App\Services\IndexClassifier;
use Illuminate\Http\Request;
use Illuminate\View\View;

class IndexController extends Controller
{
    public function index(): View
    {
        return view('indices.index', [
            'indices' => $this->normalizedIndices(),
        ]);
    }

    public function show(Request $request, string $slug): View
    {
        $indexMeta = $this->normalizedIndices()->firstWhere('slug', $slug);

        if (! $indexMeta) {
            abort(404, 'Index not found.');
        }

        $indexName = $indexMeta['name'];
        $dataSource = DataSourceLink::where('slug', 'market_activity')->where('is_active', true)->first();
        $indexData = null;
        $sheet = null;

        if ($dataSource) {
            $query = SheetUpload::with(['rows' => fn ($q) => $q->orderBy('row_index')])
                ->where('data_source_link_id', $dataSource->id);
            if ($request->user()) {
                $query->where('user_id', $request->user()->id);
            } else {
                $query->whereNull('user_id');
            }
            $sheet = $query->latest()->first();
        }

        if ($sheet && $sheet->rows->isNotEmpty()) {
            $prevCol = config('stockia.market_activity.previous_close_column', 'previous_close');
            $closeCol = config('stockia.market_activity.close_column', 'close');

            foreach ($sheet->rows as $row) {
                $data = $row->data ?? [];
                $name = trim((string) ($data['index'] ?? $data['computed']['name'] ?? ''));
                if (! IndexClassifier::isBroadMarket($name)) {
                    continue;
                }
                $rowSlug = strtolower(preg_replace('/\s+/', '-', $name));
                if ($rowSlug !== $slug) {
                    continue;
                }

                $prev = (float) ($data[$prevCol] ?? $data['computed']['prev_close'] ?? 0);
                $close = (float) ($data[$closeCol] ?? $data['computed']['close'] ?? 0);
                $returnVal = ($prev > 0 && $close > 0) ? round(log($close / $prev) * 100, 4) : ($data['computed']['return_pct'] ?? null);

                $indexData = [
                    'name' => $name,
                    'slug' => $slug,
                    'previous_close' => $prev,
                    'open' => (float) ($data['open'] ?? $data['computed']['open'] ?? 0),
                    'high' => (float) ($data['high'] ?? $data['computed']['high'] ?? 0),
                    'low' => (float) ($data['low'] ?? $data['computed']['low'] ?? 0),
                    'close' => $close,
                    'return_pct' => $returnVal,
                    'report_date' => $sheet->report_date?->format('d-M-Y'),
                ];
                break;
            }
        }

        return view('indices.show', [
            'index' => $indexData,
            'indexMeta' => $indexMeta,
            'reportDate' => $indexData['report_date'] ?? null,
        ]);
    }

    private function normalizedIndices()
    {
        return collect(config('indices.broad_market', []))->map(function ($item) {
            $name = is_array($item) ? ($item['name'] ?? '') : (string) $item;
            $nseUrl = is_array($item) ? ($item['nse_url'] ?? null) : null;

            return [
                'name' => $name,
                'slug' => strtolower(preg_replace('/\s+/', '-', $name)),
                'nse_url' => $nseUrl,
            ];
        })->filter(fn ($idx) => $idx['name'] !== '')->values();
    }
}
