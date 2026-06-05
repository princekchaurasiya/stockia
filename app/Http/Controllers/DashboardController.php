<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\DataSourceLink;
use App\Models\Lecture;
use App\Models\LiveClass;
use App\Models\ResearchUpload;
use App\Models\SheetUpload;
use App\Services\IndexClassifier;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
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
                    $data = $row->data ?? [];
                    $prev = (float) ($data[$prevCol] ?? $data['computed']['prev_close'] ?? 0);
                    $close = (float) ($data[$closeCol] ?? $data['computed']['close'] ?? 0);
                    $returnVal = ($prev > 0 && $close > 0) ? round(log($close / $prev) * 100, 4) : ($data['computed']['return_pct'] ?? null);
                    $data['return'] = $returnVal;

                    $indexName = trim((string) ($data['index'] ?? $data['computed']['name'] ?? ''));
                    if (! IndexClassifier::isBroadMarket($indexName)) {
                        continue;
                    }

                    $rowsWithReturn[] = array_merge($data, ['name' => $indexName ?: ($data['computed']['name'] ?? '—')]);
                }
            }
        }

        $sorted = collect($rowsWithReturn)->sortByDesc(fn ($r) => $r['return'] ?? -999)->values();
        $gainers = $sorted->filter(fn ($r) => ($r['return'] ?? 0) > 0)->take(5)->values();
        $losers = $sorted->filter(fn ($r) => ($r['return'] ?? 0) < 0)->take(5)->values();

        $gainCount = $sorted->filter(fn ($r) => ($r['return'] ?? 0) > 0)->count();
        $lossCount = $sorted->filter(fn ($r) => ($r['return'] ?? 0) < 0)->count();
        $marketStatus = $gainCount > $lossCount ? 'bullish' : ($lossCount > $gainCount ? 'bearish' : 'sideways');
        $total = $gainCount + $lossCount;
        $sentimentBullishPct = $total > 0 ? (int) round($gainCount / $total * 100) : 50;

        $todayLiveClass = LiveClass::active()
            ->whereIn('status', ['scheduled', 'live'])
            ->whereDate('scheduled_at', today())
            ->orderBy('scheduled_at')
            ->first();

        $latestAnnouncement = Announcement::published()
            ->orderByDesc('is_pinned')
            ->orderByDesc('published_at')
            ->first();

        $latestLecture = Lecture::where('is_active', true)
            ->orderByDesc('updated_at')
            ->first();

        $approvedResearchCount = ResearchUpload::approved()->count();

        return view('dashboard', [
            'marketStatus' => $marketStatus,
            'gainers' => $gainers,
            'losers' => $losers,
            'latestUploadDate' => $sheet?->report_date?->format('d-M-Y'),
            'sentimentBullishPct' => $sentimentBullishPct,
            'todayLiveClass' => $todayLiveClass,
            'latestAnnouncement' => $latestAnnouncement,
            'latestLecture' => $latestLecture,
            'approvedResearchCount' => $approvedResearchCount,
        ]);
    }
}
