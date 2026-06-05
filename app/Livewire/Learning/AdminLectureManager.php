<?php

namespace App\Livewire\Learning;

use App\Models\Batch;
use App\Models\Lecture;
use App\Models\Module;
use Livewire\Component;
use Livewire\WithPagination;

class AdminLectureManager extends Component
{
    use WithPagination;

    public ?int $lectureId = null;
    public $batch_id = null;
    public $module_id = null;
    public string $title = '';
    public string $notes = '';
    public int $sort_order = 0;
    public bool $is_active = true;

    protected $rules = [
        'batch_id' => ['required', 'exists:batches,id'],
        'module_id' => ['required', 'exists:modules,id'],
        'title' => ['required', 'string', 'max:255'],
        'notes' => ['nullable', 'string'],
        'sort_order' => ['nullable', 'integer', 'min:0'],
        'is_active' => ['boolean'],
    ];

    public function edit(int $id): void
    {
        $lecture = Lecture::findOrFail($id);
        $this->lectureId = $lecture->id;
        $this->batch_id = $lecture->batch_id;
        $this->module_id = $lecture->module_id;
        $this->title = $lecture->title;
        $this->notes = (string) $lecture->notes;
        $this->sort_order = $lecture->sort_order;
        $this->is_active = (bool) $lecture->is_active;
    }

    public function createNew(): void
    {
        $this->reset(['lectureId', 'batch_id', 'module_id', 'title', 'notes', 'sort_order', 'is_active']);
        $this->batch_id = null;
        $this->module_id = null;
        $this->is_active = true;
    }

    public function updatedBatchId($value): void
    {
        $this->batch_id = ($value === '' || $value === null) ? null : (int) $value;
    }

    public function updatedModuleId($value): void
    {
        $this->module_id = ($value === '' || $value === null) ? null : (int) $value;
    }

    public function save(): void
    {
        $this->batch_id = ($this->batch_id === '' || $this->batch_id === null) ? null : (int) $this->batch_id;
        $this->module_id = ($this->module_id === '' || $this->module_id === null) ? null : (int) $this->module_id;

        $data = $this->validate();

        if ($this->lectureId) {
            Lecture::whereKey($this->lectureId)->update($data);
        } else {
            Lecture::create($data);
        }

        $this->resetPage();
        session()->flash('success', 'Lecture saved.');
    }

    public function toggleActive(int $id): void
    {
        $lecture = Lecture::findOrFail($id);
        $lecture->is_active = ! $lecture->is_active;
        $lecture->save();
        $this->resetPage();
    }

    public function deleteLecture(int $id): void
    {
        $lecture = Lecture::findOrFail($id);
        $lecture->delete();
        $this->resetPage();
        session()->flash('success', 'Lecture deleted.');
    }

    public function render()
    {
        $lectures = Lecture::with(['batch', 'module'])
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('livewire.learning.admin-lecture-manager', compact('lectures'));
    }
}

