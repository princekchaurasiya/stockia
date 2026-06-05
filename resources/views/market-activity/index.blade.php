@extends('layouts.app')

@section('title', 'Market Activity')

@section('content')
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
        <h3 class="mb-0">Market Activity</h3>
        <div class="d-flex gap-2">
            @if($dataSource ?? null)
                <a href="{{ route('data_source.open', $dataSource) }}" target="_blank" rel="noopener noreferrer" class="btn btn-outline-primary btn-sm">Download from NSE</a>
            @endif
            @if($sheet ?? null)
                <a href="{{ route('sheet.export', ['sheet' => $sheet->id]) }}" class="btn btn-success btn-sm">Download Excel</a>
                <a href="{{ route('market_activity.download') }}" class="btn btn-outline-success btn-sm">Download CSV</a>
            @endif
            @auth
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadModal">
                    Upload Report
                </button>
            @endauth
        </div>
    </div>

    @include('partials.alerts')

    @if($dataSource ?? null)
        <div class="alert alert-info mb-4 py-2">
            <strong>Source:</strong> <a href="{{ route('data_source.open', $dataSource) }}" target="_blank" rel="noopener">{{ $dataSource->url }}</a>
            <small class="d-block text-muted mt-1">India VIX and Nifty50 PR/TR Leveraged/Inverse excluded. Return (%) = ln(CLOSE / PREVIOUS CLOSE) × 100.</small>
        </div>
    @endif

    @if(app()->environment('local') && isset($debug))
        <div class="alert alert-secondary small mb-4">
            <strong>Debug Info</strong><br>
            Parsed: {{ $debug['total_rows'] ?? 0 }} |
            Accepted: {{ $debug['after_filter'] ?? 0 }} |
            Rejected: {{ $debug['rejected'] ?? 0 }}
        </div>
    @endif

    @include('market.table')

    @auth
        @include('market.upload-modal')
    @endauth

    @guest
        <div class="mt-3">
            <a href="{{ route('login') }}" class="btn btn-primary">Login to Upload</a>
        </div>
    @endguest
@endsection
