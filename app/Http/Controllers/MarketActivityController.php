<?php

namespace App\Http\Controllers;

use App\Models\DataSourceLink;
use App\Models\SheetUpload;
use App\Models\StockDatum;
use App\Services\IndexClassifier;
use App\Services\SheetImportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\View\View;

class MarketActivityController extends Controller
{
    public function index(Request $request): View
    {
        $dataSource = DataSourceLink::where('slug', 'market_activity')->where('is_active', true)->first();
        $sheet = null;
        $rowsWithReturn = [];

        if ($dataSource) {
            $query = SheetUpload::with(['rows' => fn ($q) => $q->orderBy('row_index')])
                ->where('data_source_link_id', $dataSource->id);
            if ($request->user()) {
                $query->where('user_id', $request->user()->id);
            } else {
                $query->whereNull('user_id');
            }
            $sheet = $query->latest()->first();

            if ($sheet && $sheet->rows->isNotEmpty()) {
                $prevCol = config('stockia.market_activity.previous_close_column', 'previous_close');
                $closeCol = config('stockia.market_activity.close_column', 'close');

                foreach ($sheet->rows as $row) {
                    $data = $row->data;
                    $prev = (float) ($data[$prevCol] ?? $data['computed']['prev_close'] ?? 0);
                    $close = (float) ($data[$closeCol] ?? $data['computed']['close'] ?? 0);
                    $returnVal = ($prev > 0 && $close > 0) ? round(log($close / $prev) * 100, 4) : ($data['computed']['return_pct'] ?? null);
                    $data['return'] = $returnVal;

                    $indexName = trim((string) ($data['index'] ?? $data['computed']['name'] ?? ''));
                    if (! IndexClassifier::isBroadMarket($indexName)) {
                        continue;
                    }

                    $row->data = $data;
                    $rowsWithReturn[] = $row;
                }
            }
        }

        $reportDate = $sheet?->report_date?->format('d-M-Y');

        $totalParsed = $sheet ? $sheet->rows->count() : 0;
        $afterFilter = count($rowsWithReturn);

        return view('market-activity.index', [
            'sheet' => $sheet,
            'rows' => collect($rowsWithReturn),
            'reportDate' => $reportDate,
            'dataSource' => $dataSource,
            'debug' => [
                'total_rows' => $totalParsed,
                'after_filter' => $afterFilter,
                'rejected' => $totalParsed - $afterFilter,
            ],
        ]);
    }

    public function upload(Request $request, SheetImportService $sheetImportService): RedirectResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
        ]);

        $dataSource = DataSourceLink::where('slug', 'market_activity')->where('is_active', true)->first();
        if (! $dataSource) {
            return redirect()->route('market_activity.index')
                ->withErrors(['file' => 'Market Activity data source is not configured.']);
        }

        try {
            $sheetImportService->import(
                $request->file('file'),
                $request->user()?->id,
                $dataSource->id
            );
            return redirect()->route('market_activity.index')
                ->with('success', 'Report uploaded successfully. Table updated.');
        } catch (\Throwable $e) {
            return redirect()->route('market_activity.index')
                ->withErrors(['file' => $e->getMessage()]);
        }
    }

    public function download(Request $request)
    {
        $min = (float) ($request->query('min_return', -999));

        $marketActivitySourceId = DataSourceLink::where('slug', 'market_activity')
            ->where('is_active', true)
            ->value('id');

        if (! $marketActivitySourceId) {
            return Response::make('No market activity source configured.', 404);
        }

        $query = StockDatum::with('sheetUpload')
            ->whereHas('sheetUpload', fn ($q) => $q->where('data_source_link_id', $marketActivitySourceId));

        if ($request->user()) {
            $query->whereHas('sheetUpload', fn ($q) => $q->where(function ($sq) use ($request) {
                $sq->where('user_id', $request->user()->id)->orWhereNull('user_id');
            }));
        }

        $rows = $query->get()
            ->pluck('data')
            ->filter(fn ($r) => isset($r['computed'])
                && IndexClassifier::isBroadMarket($r['computed']['name'] ?? null)
                && ($r['computed']['return_pct'] ?? -999) >= $min);

        $csv = "Index,Close,Return\n";
        foreach ($rows as $row) {
            $c = $row['computed'];
            $name = str_replace('"', '""', $c['name']);
            $csv .= "\"{$name}\",{$c['close']},{$c['return_pct']}\n";
        }

        $filename = 'index_' . now()->format('Ymd_His') . '.csv';

        return Response::make($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=' . $filename,
        ]);
    }
}
