<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <span>Index Performance</span>
        @if($reportDate ?? null)
            <span class="badge bg-primary fs-6">{{ $reportDate }}</span>
        @endif
        @if($sheet ?? null)
            <div class="d-flex gap-2">
                <a href="{{ route('sheet.export', ['sheet' => $sheet->id]) }}" class="btn btn-sm btn-success">Download Excel</a>
                <a href="{{ route('market_activity.download') }}" class="btn btn-sm btn-outline-success">Download CSV</a>
            </div>
        @endif
    </div>
    <div class="card-body">
        @if($rows->isNotEmpty())
            <div class="mb-3">
                <input type="text" class="form-control" id="market-activity-search" placeholder="Search by index name..." autocomplete="off">
            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-sm mb-0 data-table" id="market-activity-table">
                    <thead>
                        <tr>
                            <th class="text-nowrap bg-warning bg-opacity-75 fw-bold" style="width: 40px;">#</th>
                            <th class="text-nowrap bg-warning bg-opacity-75 fw-bold">INDEX</th>
                            <th class="text-nowrap bg-warning bg-opacity-75 fw-bold">Previous Close</th>
                            <th class="text-nowrap bg-warning bg-opacity-75 fw-bold">OPEN</th>
                            <th class="text-nowrap bg-warning bg-opacity-75 fw-bold">HIGH</th>
                            <th class="text-nowrap bg-warning bg-opacity-75 fw-bold">LOW</th>
                            <th class="text-nowrap bg-warning bg-opacity-75 fw-bold">CLOSE</th>
                            <th class="text-nowrap bg-warning bg-opacity-75 fw-bold">Gain/Loss</th>
                            <th class="text-nowrap bg-warning bg-opacity-75 fw-bold">Return (%)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rows as $i => $row)
                            @php
                                $data = $row->data ?? [];
                                $c = $data['computed'] ?? null;
                                $returnVal = $data['return'] ?? $c['return_pct'] ?? null;
                                $gainLoss = $data['gain_loss'] ?? $c['gain_loss'] ?? null;
                                $pct = is_numeric($returnVal) ? (float) $returnVal : null;
                                $isGain = $pct !== null && $pct > 0;
                                $isLoss = $pct !== null && $pct < 0;
                                $absPct = $pct !== null ? abs($pct) : 0;
                                $intensity = min(1, $absPct / 3);
                                $opacity = 0.15 + ($intensity * 0.5);
                                $indexName = $data['index'] ?? $c['name'] ?? '—';
                                $prevClose = $data['previous_close'] ?? $c['prev_close'] ?? null;
                                $openVal = $data['open'] ?? $c['open'] ?? null;
                                $highVal = $data['high'] ?? $c['high'] ?? null;
                                $lowVal = $data['low'] ?? $c['low'] ?? null;
                                $closeVal = $data['close'] ?? $c['close'] ?? null;
                            @endphp
                            <tr>
                                <td class="text-muted">{{ $i + 1 }}</td>
                                <td>{{ $indexName ?: '—' }}</td>
                                <td>{{ is_numeric($prevClose) ? number_format((float)$prevClose, 2) : ($prevClose ?: '—') }}</td>
                                <td>{{ is_numeric($openVal) ? number_format((float)$openVal, 2) : ($openVal ?: '—') }}</td>
                                <td>{{ is_numeric($highVal) ? number_format((float)$highVal, 2) : ($highVal ?: '—') }}</td>
                                <td>{{ is_numeric($lowVal) ? number_format((float)$lowVal, 2) : ($lowVal ?: '—') }}</td>
                                <td>{{ is_numeric($closeVal) ? number_format((float)$closeVal, 2) : ($closeVal ?: '—') }}</td>
                                <td class="{{ $isGain ? 'table-success' : ($isLoss ? 'table-danger' : '') }}" style="{{ $isGain ? 'background-color: rgba(25,135,84,' . $opacity . ') !important' : ($isLoss ? 'background-color: rgba(220,53,69,' . $opacity . ') !important' : '') }}">
                                    {{ $gainLoss !== '' && $gainLoss !== null ? (is_numeric($gainLoss) ? number_format((float)$gainLoss, 2) : $gainLoss) : '—' }}
                                </td>
                                <td class="{{ $isGain ? 'table-success fw-bold' : ($isLoss ? 'table-danger fw-bold' : '') }}" style="{{ $isGain ? 'background-color: rgba(25,135,84,' . $opacity . ') !important' : ($isLoss ? 'background-color: rgba(220,53,69,' . $opacity . ') !important' : '') }}">
                                    {{ $returnVal !== null ? number_format((float)$returnVal, 2) . '%' : '—' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-muted mb-0">No market activity data yet. Upload a report using the button above.</p>
        @endif
    </div>
</div>

@if($rows->isNotEmpty())
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var search = document.getElementById('market-activity-search');
    var rows = document.querySelectorAll('#market-activity-table tbody tr');
    if (search) search.addEventListener('input', function() {
        var term = (this.value || '').toLowerCase();
        rows.forEach(function(tr) {
            tr.style.display = !term || tr.textContent.toLowerCase().includes(term) ? '' : 'none';
        });
    });
});
</script>
@endpush
@endif

<style>
.table thead th { background-color: #fff3cd !important; }
</style>
