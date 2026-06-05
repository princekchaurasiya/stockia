@extends('layouts.app')

@section('title', __('stockia.sheet.sheet_data_title'))

@section('content')
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
        <h1 class="h2 mb-0">{{ __('stockia.sheet.stock_data_title', ['name' => $sheet->original_name]) }}</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('dashboard') }}?sheet={{ $sheet->id }}" class="btn btn-outline-primary">{{ __('stockia.sheet.dashboard_charts') }}</a>
            <a href="{{ route('sheet.export', ['sheet' => $sheet->id]) }}" class="btn btn-success">{{ __('stockia.sheet.export_excel') }}</a>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header">{{ __('stockia.sheet.sheet_info') }}</div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <strong class="text-muted d-block small">{{ __('stockia.sheet.original_file') }}</strong>
                    <span>{{ $sheet->original_name }}</span>
                </div>
                <div class="col-md-2">
                    <strong class="text-muted d-block small">{{ __('stockia.sheet.row_count_label') }}</strong>
                    <span>{{ $sheet->row_count }}</span>
                </div>
                <div class="col-md-2">
                    <strong class="text-muted d-block small">{{ __('stockia.sheet.column_count_label') }}</strong>
                    <span>{{ count($columns) }}</span>
                </div>
                <div class="col-md-12">
                    <strong class="text-muted d-block small mb-1">{{ __('stockia.sheet.mapped_columns') }}</strong>
                    <p class="mb-0 small">{{ implode(', ', array_map(fn($c) => $labels[$c] ?? $c, $columns)) }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <span>{{ __('stockia.sheet.table_data') }}</span>
            <a href="{{ route('sheet.export', ['sheet' => $sheet->id]) }}" class="btn btn-sm btn-success">{{ __('stockia.sheet.export_excel') }}</a>
        </div>
        <div class="card-body">
            @if($sheet->rows->isNotEmpty())
                @php
                    $tableColumns = collect($columns)->map(fn($c) => ['key' => $c, 'label' => $labels[$c] ?? $c])->values()->all();
                @endphp
                <x-data-table
                    id="sheet-table-{{ $sheet->id }}"
                    :columns="$tableColumns"
                    :rows="$sheet->rows"
                    search-placeholder="Search by symbol, company..."
                    header-bg="warning"
                    :empty-message="__('stockia.sheet.no_rows')"
                />
            @else
                <p class="text-muted mt-3">{{ __('stockia.sheet.no_rows') }}</p>
            @endif
        </div>
    </div>

    <style>
        .data-table thead th { background-color: #fff3cd !important; }
        .data-table { font-size: 0.9rem; }
    </style>
@endsection
