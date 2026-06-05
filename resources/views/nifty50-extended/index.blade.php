@extends('layouts.app')

@section('title', 'Nifty 50 – Stock Data Table')

@section('content')
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
        <h1 class="h2 mb-0">Nifty 50 – Stock Data</h1>
        <a href="{{ route('nifty50.extended.export') }}" class="btn btn-success">Download Excel</a>
    </div>

    <div class="alert alert-info mb-4">
        <strong>Data as of:</strong> {{ config('stockia.nifty50_extended.data_as_of', 'N/A') }} &nbsp;|&nbsp;
        <strong>Source:</strong> {{ config('stockia.nifty50_extended.source', 'NSE India') }}
    </div>

    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <span>Nifty 50 Constituents with Weightage & Sector</span>
            <a href="{{ route('nifty50.extended.export') }}" class="btn btn-sm btn-success">Download Excel</a>
        </div>
        <div class="card-body">
            @if($rows->isNotEmpty())
                <x-data-table
                    id="nifty50-table"
                    :columns="[
                        ['key' => 'security_symbol', 'label' => 'Security Symbol'],
                        ['key' => 'company_name', 'label' => 'Company Name'],
                        ['key' => 'industry', 'label' => 'Industry'],
                        ['key' => 'nifty_weightage_pct', 'label' => 'Nifty Weightage (%)'],
                        ['key' => 'sector_thematic_index', 'label' => 'Sector & Thematic Index'],
                        ['key' => 'sector_thematic_weightage_pct', 'label' => 'Sector & Thematic Weightage (%)'],
                        ['key' => 'relationship_of_index', 'label' => 'Relationship of Index (Sector/Thematic)'],
                    ]"
                    :rows="$rows"
                    search-placeholder="Search by symbol, company, industry..."
                    header-bg="warning"
                    empty-message="No data found. Run: php artisan db:seed --class=Nifty50ExtendedSeeder"
                />
            @else
                <p class="text-muted mt-3">No data. Run: <code>php artisan db:seed --class=Nifty50ExtendedSeeder</code></p>
            @endif
        </div>
    </div>

    <style>
        .table thead th { background-color: #fff3cd !important; }
    </style>
@endsection
