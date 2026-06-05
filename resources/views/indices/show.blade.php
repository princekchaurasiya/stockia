@extends('layouts.app')

@section('title', $index['name'] ?? ($indexMeta['name'] ?? 'Index'))

@section('content')
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('indices.index') }}">Indices</a></li>
            <li class="breadcrumb-item active">{{ $index['name'] ?? ($indexMeta['name'] ?? '—') }}</li>
        </ol>
    </nav>

    @if($index)
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-4">
            <h3 class="mb-0">{{ $index['name'] }}</h3>
            @if($indexMeta['nse_url'] ?? null)
                <a href="{{ $indexMeta['nse_url'] }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-primary">
                    View on NSE <i class="bi bi-box-arrow-up-right ms-1"></i>
                </a>
            @endif
        </div>

        @if($reportDate)
            <p class="text-muted mb-3">Report date: {{ $reportDate }}</p>
        @endif

        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h6 class="text-muted small">Previous Close</h6>
                        <h5>{{ number_format($index['previous_close'], 2) }}</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h6 class="text-muted small">Open</h6>
                        <h5>{{ number_format($index['open'], 2) }}</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h6 class="text-muted small">High</h6>
                        <h5>{{ number_format($index['high'], 2) }}</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h6 class="text-muted small">Low</h6>
                        <h5>{{ number_format($index['low'], 2) }}</h5>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <h6 class="text-muted small">Close</h6>
                <h4 class="mb-2">{{ number_format($index['close'], 2) }}</h4>
                @if(isset($index['return_pct']) && $index['return_pct'] !== null)
                    <span class="badge {{ $index['return_pct'] >= 0 ? 'bg-success' : 'bg-danger' }} fs-6">
                        {{ $index['return_pct'] >= 0 ? '+' : '' }}{{ number_format($index['return_pct'], 2) }}%
                    </span>
                @endif
            </div>
        </div>

        <p class="mt-3 small text-muted">Upload a Market Activity report to see historical data and charts.</p>
    @else
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-4">
            <h3 class="mb-0">{{ $indexMeta['name'] ?? 'Index' }}</h3>
            @if($indexMeta['nse_url'] ?? null)
                <a href="{{ $indexMeta['nse_url'] }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-primary">
                    View on NSE <i class="bi bi-box-arrow-up-right ms-1"></i>
                </a>
            @endif
        </div>

        <div class="alert alert-warning">
            No uploaded data for this index yet. Upload a Market Activity report from the Market Activity page, or view live data on NSE.
        </div>
    @endif
@endsection
