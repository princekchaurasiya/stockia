<?php

use App\Models\DataSourceLink;
use App\Models\StockDatum;
use App\Services\IndexClassifier;
use Livewire\Component;

new class extends Component
{
    public $date;

    public function mount()
    {
        $this->date = today()->toDateString();
    }

    public function getRowsProperty()
    {
        $marketActivitySourceId = DataSourceLink::where('slug', 'market_activity')
            ->where('is_active', true)
            ->value('id');

        if (! $marketActivitySourceId) {
            return collect();
        }

        $query = StockDatum::with('sheetUpload')->whereHas('sheetUpload', function ($q) use ($marketActivitySourceId) {
            $q->whereDate('report_date', $this->date)
                ->where('data_source_link_id', $marketActivitySourceId);
            if (auth()->check()) {
                $q->where(function ($sq) {
                    $sq->where('user_id', auth()->id())->orWhereNull('user_id');
                });
            }
        });

        return $query->get()
            ->pluck('data')
            ->filter(fn ($r) => isset($r['computed']) && IndexClassifier::isBroadMarket($r['computed']['name'] ?? null))
            ->sortByDesc(fn ($r) => $r['computed']['return_pct'] ?? 0)
            ->values();
    }
};
?>

<div>
    @if($this->rows->isNotEmpty())
        <div class="row">
            @foreach($this->rows as $row)
                @php $c = $row['computed']; @endphp
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="card shadow border-0 h-100">
                        <div class="card-body">
                            <h6 class="text-muted small mb-1">{{ $c['name'] }}</h6>
                            <h4 class="fw-bold mb-2">{{ number_format($c['close'], 2) }}</h4>
                            <span class="badge {{ ($c['return_pct'] ?? 0) >= 0 ? 'bg-success' : 'bg-danger' }}">
                                {{ number_format($c['return_pct'] ?? 0, 2) }}%
                            </span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <p class="text-muted small mb-0">No index data for this date. Upload a Market Activity report.</p>
    @endif
</div>