<?php

namespace App\Livewire\Portal;

use App\Models\ChartAsset;
use App\Services\ChartAssetService;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class AdminChartManager extends Component
{
    use WithFileUploads;
    use WithPagination;

    public ?int $assetId = null;
    public string $title = '';
    public string $category = '';
    public ?string $report_date = null;
    public int $sort_order = 0;
    public bool $is_active = true;
    public $file;

    protected function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:100'],
            'report_date' => ['nullable', 'date'],
            'sort_order' => ['integer', 'min:0'],
            'is_active' => ['boolean'],
            'file' => [$this->assetId ? 'nullable' : 'required', 'file', 'mimes:png,jpg,jpeg,webp,pdf', 'max:20480'],
        ];
    }

    public function edit(int $id): void
    {
        $asset = ChartAsset::findOrFail($id);
        $this->assetId = $asset->id;
        $this->title = $asset->title;
        $this->category = (string) $asset->category;
        $this->report_date = $asset->report_date?->format('Y-m-d');
        $this->sort_order = $asset->sort_order;
        $this->is_active = (bool) $asset->is_active;
        $this->file = null;
    }

    public function createNew(): void
    {
        $this->reset(['assetId', 'title', 'category', 'report_date', 'sort_order', 'is_active', 'file']);
        $this->sort_order = 0;
        $this->is_active = true;
    }

    public function save(): void
    {
        $data = $this->validate();
        $service = app(ChartAssetService::class);

        if ($this->assetId) {
            $asset = ChartAsset::findOrFail($this->assetId);

            if ($this->file) {
                $oldPath = $asset->file_path;
                $extension = strtolower($this->file->getClientOriginalExtension());
                $fileName = (string) \Illuminate\Support\Str::uuid().'.'.$extension;
                $storedPath = $this->file->storeAs('chart-assets', $fileName, 'public');

                if ($oldPath && \Illuminate\Support\Facades\Storage::disk('public')->exists($oldPath)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($oldPath);
                }

                $asset->update(array_merge(
                    collect($data)->except('file')->toArray(),
                    ['file_path' => $storedPath, 'file_type' => $extension]
                ));
            } else {
                $asset->update(collect($data)->except('file')->toArray());
            }
        } else {
            $service->store($this->file, auth()->user(), $data);
        }

        $this->createNew();
        $this->resetPage();
        session()->flash('success', 'Chart asset saved.');
    }

    public function delete(int $id): void
    {
        $asset = ChartAsset::findOrFail($id);
        app(ChartAssetService::class)->delete($asset);
        $this->resetPage();
        session()->flash('success', 'Chart deleted.');
    }

    public function render()
    {
        return view('livewire.portal.admin-chart-manager', [
            'assets' => ChartAsset::orderByDesc('report_date')->orderBy('sort_order')->paginate(12),
        ]);
    }
}
