<?php

namespace App\Livewire\Admin\Learning;

use App\Models\Lecture;
use App\Models\LectureDocument;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;

class DocumentFormModal extends Component
{
    use WithFileUploads;

    public bool $show = false;

    public ?int $documentId = null;

    public $lecture_id = null;

    public string $title = '';

    public int $sort_order = 0;

    public bool $is_active = true;

    public $file;

    protected function rules(): array
    {
        return [
            'lecture_id' => ['required', 'exists:lectures,id'],
            'title' => ['required', 'string', 'max:255'],
            'sort_order' => ['integer', 'min:0'],
            'is_active' => ['boolean'],
            'file' => [$this->documentId ? 'nullable' : 'required', 'file', 'mimes:pdf,ppt,pptx', 'max:20480'],
        ];
    }

    #[On('openDocumentFormModal')]
    public function open(?int $id = null, ?int $lectureId = null): void
    {
        $this->documentId = $id;
        $this->resetValidation();
        $this->reset('file');

        if ($id) {
            $document = LectureDocument::findOrFail($id);
            $this->lecture_id = $document->lecture_id;
            $this->title = $document->title;
            $this->sort_order = $document->sort_order;
            $this->is_active = (bool) $document->is_active;
        } else {
            $this->reset(['title', 'sort_order']);
            $this->lecture_id = $lectureId;
            $this->is_active = true;
        }

        $this->show = true;
    }

    public function updatedLectureId($value): void
    {
        $this->lecture_id = ($value === '' || $value === null) ? null : (int) $value;
    }

    public function save(): void
    {
        $this->lecture_id = ($this->lecture_id === '' || $this->lecture_id === null) ? null : (int) $this->lecture_id;

        $data = $this->validate();
        $lectureId = (int) $data['lecture_id'];
        $uploadedFile = $this->file;

        DB::beginTransaction();

        try {
            $document = $this->documentId
                ? LectureDocument::lockForUpdate()->findOrFail($this->documentId)
                : new LectureDocument();

            $oldPath = $document->file_path ?? null;
            $storedPath = null;

            if ($uploadedFile) {
                $extension = strtolower($uploadedFile->getClientOriginalExtension());
                $fileName = (string) Str::uuid().'.'.$extension;
                $storedPath = $uploadedFile->storeAs('lecture-documents', $fileName, 'public');

                if (! $storedPath || ! Storage::disk('public')->exists($storedPath)) {
                    throw new \RuntimeException('Failed to store lecture document.');
                }

                $document->file_path = $storedPath;
                $document->file_type = $extension;
            }

            $document->lecture_id = $lectureId;
            $document->title = $data['title'];
            $document->sort_order = $data['sort_order'] ?? 0;
            $document->is_active = (bool) ($data['is_active'] ?? true);
            $document->save();

            DB::commit();

            if ($storedPath && $oldPath && $oldPath !== $storedPath && Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }

            $this->dispatch('documentTableRefresh');
            session()->flash('success', $this->documentId ? 'Document updated.' : 'Document created.');
            $this->close();
        } catch (\Throwable $e) {
            DB::rollBack();

            if (isset($storedPath) && Storage::disk('public')->exists($storedPath)) {
                Storage::disk('public')->delete($storedPath);
            }

            Log::error('Failed to save lecture document', [
                'lecture_id' => $this->lecture_id,
                'document_id' => $this->documentId,
                'error' => $e->getMessage(),
            ]);

            session()->flash('error', 'Unable to save document. Please try again.');
        }
    }

    public function close(): void
    {
        $this->show = false;
        $this->reset(['documentId', 'lecture_id', 'title', 'sort_order', 'file']);
        $this->is_active = true;
    }

    public function render()
    {
        $lectures = Lecture::orderBy('title')->get();

        return view('livewire.admin.learning.document-form-modal', compact('lectures'));
    }
}
