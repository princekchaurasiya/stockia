<?php

use App\Models\DataSourceLink;
use App\Models\SheetUpload;
use Livewire\Component;

new class extends Component
{
    public $selectedSheetId = null;
    public $chartColumn = null;

    public function mount()
    {
        $sheetId = request()->query('sheet');
        $query = SheetUpload::query();
        if (auth()->check()) {
            $query->where(function ($q) {
                $q->where('user_id', auth()->id())->orWhereNull('user_id');
            });
        }
        if ($sheetId && $query->clone()->find($sheetId)) {
            $this->selectedSheetId = (int) $sheetId;
        }
        $uploads = $this->uploads;
        if ($uploads->isNotEmpty() && ! $this->selectedSheetId) {
            $this->selectedSheetId = $uploads->first()->id;
        }
    }

    public function selectSheet($id)
    {
        $this->selectedSheetId = (int) $id;
        $this->chartColumn = null;
        $this->dispatch('chart-update');
    }

    public function setChartColumn($column)
    {
        $this->chartColumn = $column;
        $this->dispatch('chart-update');
    }

    public function getDataSourceLinksProperty()
    {
        return DataSourceLink::where('is_active', true)->orderBy('name')->get();
    }

    public function getUploadsProperty()
    {
        $query = SheetUpload::with('dataSourceLink')->latest();
        if (auth()->check()) {
            $query->where(function ($q) {
                $q->where('user_id', auth()->id())->orWhereNull('user_id');
            });
        }
        return $query->get();
    }

    public function getCurrentSheetProperty()
    {
        if (! $this->selectedSheetId) {
            return null;
        }
        $query = SheetUpload::with(['rows', 'dataSourceLink']);
        if (auth()->check()) {
            $query->where(function ($q) {
                $q->where('user_id', auth()->id())->orWhereNull('user_id');
            });
        }
        return $query->find($this->selectedSheetId);
    }

    public function getChartDataProperty()
    {
        $sheet = $this->currentSheet;
        if (! $sheet || ! $this->chartColumn) {
            return [];
        }
        return $sheet->rows->map(fn ($row) => [
            'label' => (string) ($row->data[$this->chartColumn] ?? $row->row_index),
            'value' => (float) (is_numeric($row->data[$this->chartColumn] ?? 0) ? $row->data[$this->chartColumn] : 0),
        ])->take(100)->values()->toArray();
    }
};
?>

<div>
    <div class="card shadow-sm mb-4 border-primary">
        <div class="card-header bg-primary text-white">
            <strong>Nifty 50 – Stock Weightage</strong>
        </div>
        <div class="card-body">
            <p class="mb-2">View Nifty 50 constituents with Nifty weightage %, Sector & Thematic Index, and Sector weightage.</p>
            <a href="{{ route('nifty50.extended.index') }}" class="btn btn-primary">Nifty 50 – Weightage % of Stocks</a>
            <small class="d-block mt-2 text-muted">Data as of: {{ config('stockia.nifty50_extended.data_as_of', 'N/A') }}</small>
        </div>
    </div>

    <div class="card shadow-sm mb-4 border-info">
        <div class="card-header bg-info text-white">
            <strong>Market Activity Report</strong>
        </div>
        <div class="card-body">
            <p class="mb-2">View Index Performance table with Return column. Upload from NSE <a href="https://www.nseindia.com/all-reports" target="_blank" rel="noopener" class="text-white text-decoration-underline">all-reports</a>.</p>
            <a href="{{ route('market_activity.index') }}" class="btn btn-info">Market Activity Report</a>
            @auth
                <a href="{{ route('market_activity.download') }}" class="btn btn-outline-info ms-2">Download CSV</a>
            @endauth
        </div>
    </div>

    <div class="card shadow-sm mb-4 border-secondary">
        <div class="card-header bg-secondary text-white">
            <strong>Index Performance Cards</strong>
        </div>
        <div class="card-body">
            @livewire('index-cards')
        </div>
    </div>

    <x-data-source-links-card :links="$this->dataSourceLinks" title="Basic List – Data Sources" />

    <div class="mb-4">
        <p class="small text-muted mb-2"><strong>Update data:</strong> Upload a new sheet to replace existing data. Latest upload always overrides previous for the same source.</p>
        @livewire('upload-sheet')
    </div>

    <div class="row mb-4">
        <div class="col-md-4">
            <h6 class="text-muted mb-2">Your uploaded sheets</h6>
            <div class="list-group">
                @forelse($this->uploads as $upload)
                    <div class="list-group-item {{ $selectedSheetId === $upload->id ? 'active' : '' }} d-flex justify-content-between align-items-start gap-2">
                        <button type="button"
                                class="btn btn-link p-0 text-start flex-grow-1 text-decoration-none {{ $selectedSheetId === $upload->id ? 'text-white' : 'text-dark' }}"
                                wire:click="selectSheet({{ $upload->id }})">
                            <span class="d-block">{{ $upload->original_name }}</span>
                            <small class="d-block {{ $selectedSheetId === $upload->id ? 'text-white-50' : 'text-muted' }}">{{ __('stockia.sheet.rows_count', ['count' => $upload->row_count]) }}</small>
                        </button>
                        <a href="{{ route('sheet.show', $upload) }}" class="btn btn-sm {{ $selectedSheetId === $upload->id ? 'btn-light' : 'btn-primary' }} text-nowrap">{{ __('stockia.sheet.view_table') }}</a>
                    </div>
                @empty
                    <div class="list-group-item text-muted">{{ __('stockia.sheet.no_sheets') }}</div>
                @endforelse
            </div>
        </div>
        <div class="col-md-8">
            @if($this->currentSheet)
                @php
                    $displayColumns = $this->currentSheet->getDisplayColumns();
                    $displayLabels = $this->currentSheet->getDisplayColumnLabels();
                @endphp
                <div class="card shadow-sm mb-3">
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <span>{{ __('stockia.sheet.columns') }}</span>
                        <div class="d-flex align-items-center gap-2">
                            <small>{{ __('stockia.sheet.rows_count', ['count' => $this->currentSheet->row_count]) }}</small>
                            @auth
                                <a href="{{ route('sheet.export', ['sheet' => $this->currentSheet->id]) }}" class="btn btn-sm btn-success">{{ __('stockia.sheet.export_excel') }}</a>
                            @endauth
                        </div>
                    </div>
                    <div class="card-body">
                        <p class="small text-muted">{{ __('stockia.sheet.chart_select_column') }}</p>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($displayColumns as $col)
                                <button type="button"
                                        class="btn btn-sm {{ $chartColumn === $col ? 'btn-primary' : 'btn-outline-primary' }}"
                                        wire:click="setChartColumn('{{ $col }}')">
                                    {{ $col }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="card shadow-sm mb-3">
                    <div class="card-header">{{ __('stockia.sheet.chart') }}</div>
                    <div class="card-body">
                        @if($chartColumn)
                            <div id="stock-chart-root" data-column="{{ $chartColumn }}" data-payload="{{ json_encode($this->chartData) }}"></div>
                        @else
                            <p class="text-muted">{{ __('stockia.sheet.chart_select_above') }}</p>
                        @endif
                    </div>
                </div>
                <div class="card shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <span>{{ __('stockia.sheet.data_preview') }}</span>
                        <a href="{{ route('sheet.show', $this->currentSheet) }}" class="btn btn-sm btn-primary">{{ __('stockia.sheet.view_full_table') }}</a>
                    </div>
                    <div class="card-body overflow-auto" style="max-height: 400px;">
                        <table class="table table-sm table-striped">
                            <thead>
                                <tr>
                                    @foreach($displayColumns as $col)
                                        <th>{{ $displayLabels[$col] ?? $col }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($this->currentSheet->rows->take(50) as $row)
                                    <tr>
                                        @foreach($displayColumns as $col)
                                            <td>{{ $row->data[$col] ?? '-' }}</td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @if($this->currentSheet->rows->count() > 50)
                            <p class="small text-muted">{{ __('stockia.sheet.showing_first_50') }}</p>
                        @endif
                    </div>
                </div>
            @else
                <p class="text-muted">{{ __('stockia.sheet.upload_first') }}</p>
            @endif
        </div>
    </div>
</div>
