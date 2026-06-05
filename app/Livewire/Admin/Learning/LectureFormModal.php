<?php

namespace App\Livewire\Admin\Learning;

use App\Models\Lecture;
use Livewire\Attributes\On;
use Livewire\Component;

class LectureFormModal extends Component
{
    public bool $show = false;

    public ?int $lectureId = null;

    public $batch_id = null;

    public $module_id = null;

    public string $title = '';

    public string $notes = '';

    public int $sort_order = 0;

    public bool $is_active = true;

    public int $videosCount = 0;

    public int $documentsCount = 0;

    protected function rules(): array
    {
        return [
            'batch_id' => ['required', 'exists:batches,id'],
            'module_id' => ['required', 'exists:modules,id'],
            'title' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['boolean'],
        ];
    }

    #[On('openLectureFormModal')]
    public function open(?int $id = null): void
    {
        $this->lectureId = $id;

        if ($id) {
            $lecture = Lecture::withCount(['videos', 'documents'])->findOrFail($id);
            $this->batch_id = $lecture->batch_id;
            $this->module_id = $lecture->module_id;
            $this->title = $lecture->title;
            $this->notes = (string) $lecture->notes;
            $this->sort_order = $lecture->sort_order;
            $this->is_active = (bool) $lecture->is_active;
            $this->videosCount = $lecture->videos_count;
            $this->documentsCount = $lecture->documents_count;
        } else {
            $this->reset(['batch_id', 'module_id', 'title', 'notes', 'sort_order']);
            $this->is_active = true;
            $this->videosCount = 0;
            $this->documentsCount = 0;
        }

        $this->show = true;
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
            $message = 'Lecture updated.';
        } else {
            Lecture::create($data);
            $message = 'Lecture created.';
        }

        $this->dispatch('lectureTableRefresh');
        session()->flash('success', $message);
        $this->close();
    }

    public function close(): void
    {
        $this->show = false;
        $this->reset(['lectureId', 'batch_id', 'module_id', 'title', 'notes', 'sort_order', 'videosCount', 'documentsCount']);
        $this->is_active = true;
    }

    public function render()
    {
        $linkedVideos = collect();
        $linkedDocuments = collect();
        $linkedUserNotes = collect();

        if ($this->lectureId) {
            $lecture = Lecture::with([
                'videos' => fn ($query) => $query->orderBy('sort_order'),
                'documents' => fn ($query) => $query->orderBy('sort_order'),
                'userNotes' => fn ($query) => $query->with('user')->orderByDesc('updated_at'),
            ])->find($this->lectureId);

            $linkedVideos = $lecture?->videos ?? collect();
            $linkedDocuments = $lecture?->documents ?? collect();
            $linkedUserNotes = $lecture?->userNotes ?? collect();
        }

        return view('livewire.admin.learning.lecture-form-modal', compact('linkedVideos', 'linkedDocuments', 'linkedUserNotes'));
    }
}
