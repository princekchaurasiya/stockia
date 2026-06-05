<?php

namespace App\Livewire\Portal;

use App\Models\Batch;
use App\Models\LiveClass;
use Livewire\Component;
use Livewire\WithPagination;

class AdminLiveClassManager extends Component
{
    use WithPagination;

    public ?int $liveClassId = null;
    public $batch_id = null;
    public string $title = '';
    public string $description = '';
    public string $meeting_url = '';
    public ?string $scheduled_at = null;
    public ?int $duration_minutes = 60;
    public string $status = 'scheduled';
    public bool $is_active = true;

    protected $rules = [
        'batch_id' => ['nullable', 'exists:batches,id'],
        'title' => ['required', 'string', 'max:255'],
        'description' => ['nullable', 'string'],
        'meeting_url' => ['required', 'url', 'max:500'],
        'scheduled_at' => ['required', 'date'],
        'duration_minutes' => ['nullable', 'integer', 'min:15', 'max:480'],
        'status' => ['required', 'in:scheduled,live,completed,cancelled'],
        'is_active' => ['boolean'],
    ];

    public function edit(int $id): void
    {
        $lc = LiveClass::findOrFail($id);
        $this->liveClassId = $lc->id;
        $this->batch_id = $lc->batch_id;
        $this->title = $lc->title;
        $this->description = (string) $lc->description;
        $this->meeting_url = $lc->meeting_url;
        $this->scheduled_at = $lc->scheduled_at->format('Y-m-d\TH:i');
        $this->duration_minutes = $lc->duration_minutes;
        $this->status = $lc->status;
        $this->is_active = (bool) $lc->is_active;
    }

    public function createNew(): void
    {
        $this->reset(['liveClassId', 'batch_id', 'title', 'description', 'meeting_url', 'scheduled_at', 'duration_minutes', 'status', 'is_active']);
        $this->batch_id = null;
        $this->duration_minutes = 60;
        $this->status = 'scheduled';
        $this->is_active = true;
    }

    public function updatedBatchId($value): void
    {
        $this->batch_id = ($value === '' || $value === null) ? null : (int) $value;
    }

    public function save(): void
    {
        $this->batch_id = ($this->batch_id === '' || $this->batch_id === null) ? null : (int) $this->batch_id;

        $data = $this->validate();
        $data['created_by'] = auth()->id();

        if ($this->liveClassId) {
            LiveClass::whereKey($this->liveClassId)->update(collect($data)->except('created_by')->toArray());
        } else {
            LiveClass::create($data);
        }

        $this->createNew();
        $this->resetPage();
        session()->flash('success', 'Live class saved.');
    }

    public function delete(int $id): void
    {
        LiveClass::whereKey($id)->delete();
        $this->resetPage();
        session()->flash('success', 'Live class deleted.');
    }

    public function render()
    {
        return view('livewire.portal.admin-live-class-manager', [
            'liveClasses' => LiveClass::with('batch')->orderByDesc('scheduled_at')->paginate(10),
            'batches' => Batch::orderBy('name')->get(),
        ]);
    }
}
