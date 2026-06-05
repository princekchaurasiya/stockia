<?php

use App\Models\DataSourceLink;
use App\Models\SheetUpload;
use App\Services\SheetImportService;
use Livewire\Component;
use Livewire\WithFileUploads;

new class extends Component
{
    use WithFileUploads;

    public $file = null;
    public $uploadedId = null;
    public $error = null;
    public $dataSourceLinkId = null;

    protected $rules = [
        'file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
    ];

    public function getDataSourceLinksProperty()
    {
        return DataSourceLink::where('is_active', true)->orderBy('name')->get();
    }

    public function save(SheetImportService $sheetImportService)
    {
        $this->error = null;
        $this->validate();

        try {
            $upload = $sheetImportService->import(
                $this->file,
                auth()->id(),
                $this->dataSourceLinkId ? (int) $this->dataSourceLinkId : null
            );
            $this->uploadedId = $upload->id;
            $this->file = null;
            $this->dispatch('sheet-uploaded');
        } catch (\Throwable $e) {
            $this->error = $e->getMessage();
        }
    }

    public function clearUpload()
    {
        $this->uploadedId = null;
        $this->error = null;
    }
};
?>

<div>
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">{{ __('stockia.sheet.upload_title') }}</h5>
        </div>
        <div class="card-body">
            @if($error)
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ $error }}
                    <button type="button" class="btn-close" wire:click="clearUpload" aria-label="Close"></button>
                </div>
            @endif
            @if($uploadedId)
                @php
                    $uploadedSheet = SheetUpload::with('dataSourceLink')->find($uploadedId);
                    $isMarketActivity = $uploadedSheet?->dataSourceLink?->slug === 'market_activity';
                @endphp
                <div class="alert alert-success">
                    <p class="mb-2">{{ __('stockia.sheet.upload_success') }}</p>
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        @if($isMarketActivity)
                            <a href="{{ route('market_activity.index') }}" class="btn btn-sm btn-primary">View Market Activity</a>
                        @else
                            <a href="{{ route('sheet.show', $uploadedId) }}" class="btn btn-sm btn-primary">{{ __('stockia.sheet.view_table') }}</a>
                        @endif
                        <a href="{{ route('dashboard') }}?sheet={{ $uploadedId }}" class="btn btn-sm btn-success">{{ __('stockia.sheet.view_data') }}</a>
                        <button type="button" class="btn btn-sm btn-outline-secondary" wire:click="clearUpload">{{ __('stockia.sheet.upload_another') }}</button>
                    </div>
                    @guest
                        <p class="small text-muted mb-0 mt-2">{{ __('stockia.sheet.login_to_view') }}</p>
                    @endguest
                </div>
            @else
                <form wire:submit="save">
                    @if($this->dataSourceLinks->isNotEmpty())
                        <div class="mb-3">
                            <label for="dataSourceLinkId" class="form-label">{{ __('stockia.sheet.source_optional') }}</label>
                            <select class="form-select" id="dataSourceLinkId" wire:model="dataSourceLinkId">
                                <option value="">{{ __('stockia.sheet.source_generic') }}</option>
                                @foreach($this->dataSourceLinks as $link)
                                    <option value="{{ $link->id }}">{{ $link->name }}</option>
                                @endforeach
                            </select>
                            <small class="text-muted">{{ __('stockia.sheet.source_nifty_hint') }}</small>
                        </div>
                    @endif
                    <div class="mb-3">
                        <label for="sheetFile" class="form-label">{{ __('stockia.sheet.file_label') }}</label>
                        <input type="file" class="form-control" id="sheetFile" wire:model="file" accept=".xlsx,.xls,.csv">
                        @error('file')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                        <span wire:loading.remove>{{ __('stockia.sheet.import') }}</span>
                        <span wire:loading>{{ __('stockia.sheet.importing') }}</span>
                    </button>
                </form>
            @endif
        </div>
    </div>
</div>
