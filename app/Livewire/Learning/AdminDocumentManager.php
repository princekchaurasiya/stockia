<?php

namespace App\Livewire\Learning;

use App\Models\Lecture;
use App\Models\LectureDocument;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;

class AdminDocumentManager extends Component
{
    use WithFileUploads;

    private function debugLog(string $hypothesisId, string $location, string $message, array $data = []): void
    {
        // #region agent log
        @file_put_contents(
            base_path('.cursor/debug-484d05.log'),
            json_encode([
                'sessionId' => '484d05',
                'hypothesisId' => $hypothesisId,
                'location' => $location,
                'message' => $message,
                'data' => $data,
                'timestamp' => (int) round(microtime(true) * 1000),
            ]).PHP_EOL,
            FILE_APPEND
        );
        // #endregion
    }

    public $lecture_id = null;
    public ?int $document_id = null;
    public string $title = '';
    public int $sort_order = 0;
    public bool $is_active = true;
    public $file;

    protected $rules = [
        'lecture_id' => ['required', 'exists:lectures,id'],
        'title' => ['required', 'string', 'max:255'],
        'sort_order' => ['integer', 'min:0'],
        'is_active' => ['boolean'],
        'file' => ['nullable', 'file', 'mimes:pdf,ppt,pptx', 'max:20480'],
    ];

    public function selectLecture(int $lectureId): void
    {
        $this->lecture_id = $lectureId;
        $this->clearForm();
    }

    public function edit(int $id): void
    {
        $document = LectureDocument::findOrFail($id);
        $this->document_id = $document->id;
        $this->lecture_id = $document->lecture_id;
        $this->title = $document->title;
        $this->sort_order = $document->sort_order;
        $this->is_active = (bool) $document->is_active;
    }

    public function clearForm(): void
    {
        $this->reset(['document_id', 'title', 'sort_order', 'is_active', 'file']);
        $this->sort_order = 0;
        $this->is_active = true;
    }

    public function updatedLectureId($value): void
    {
        $this->lecture_id = ($value === '' || $value === null) ? null : (int) $value;
    }

    public function save(): void
    {
        $this->lecture_id = ($this->lecture_id === '' || $this->lecture_id === null) ? null : (int) $this->lecture_id;

        $this->rules['file'][0] = $this->document_id ? 'nullable' : 'required';
        $data = $this->validate();

        $lectureId = (int) $data['lecture_id'];
        $uploadedFile = $this->file;

        $this->debugLog('D', 'AdminDocumentManager.php:save:entry', 'Document save started', [
            'document_id' => $this->document_id,
            'lecture_id' => $lectureId,
            'has_file' => (bool) $uploadedFile,
        ]);

        DB::beginTransaction();

        try {
            $document = $this->document_id
                ? LectureDocument::lockForUpdate()->findOrFail($this->document_id)
                : new LectureDocument();

            $oldPath = $document->file_path ?? null;

            if ($uploadedFile) {
                $extension = strtolower($uploadedFile->getClientOriginalExtension());
                $fileName = (string) Str::uuid().'.'.$extension;
                $storedPath = $uploadedFile->storeAs('lecture-documents', $fileName, 'public');

                if (! $storedPath) {
                    throw new \RuntimeException('Failed to store lecture document.');
                }

                if (! Storage::disk('public')->exists($storedPath)) {
                    throw new \RuntimeException('Stored lecture document is missing on disk.');
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

            if (isset($storedPath) && $oldPath && $oldPath !== $storedPath && Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }

            $this->document_id = $document->id;
            session()->flash('success_document', 'Document saved.');

            $this->debugLog('D', 'AdminDocumentManager.php:save:success', 'Document saved', [
                'document_id' => $document->id,
                'file_path' => $document->file_path,
                'file_exists' => Storage::disk('public')->exists($document->file_path),
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            if (isset($storedPath) && Storage::disk('public')->exists($storedPath)) {
                Storage::disk('public')->delete($storedPath);
            }

            $this->debugLog('D', 'AdminDocumentManager.php:save:error', 'Document save failed', [
                'error' => $e->getMessage(),
            ]);

            Log::error('Failed to save lecture document', [
                'lecture_id' => $this->lecture_id,
                'document_id' => $this->document_id,
                'error' => $e->getMessage(),
            ]);

            session()->flash('error_document', 'Unable to save document. Please try again.');
        }
    }

    public function delete(int $id): void
    {
        DB::beginTransaction();

        try {
            $document = LectureDocument::lockForUpdate()->findOrFail($id);
            $path = $document->file_path;
            $document->delete();
            DB::commit();

            if ($path && Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('Failed to delete lecture document', [
                'document_id' => $id,
                'error' => $e->getMessage(),
            ]);

            session()->flash('error_document', 'Unable to delete document.');
        }
    }

    public function toggleActive(int $id): void
    {
        $document = LectureDocument::findOrFail($id);
        $document->is_active = ! $document->is_active;
        $document->save();
    }

    public function render()
    {
        $lectures = Lecture::orderBy('created_at')->get();

        $documents = collect();
        $lectureId = $this->lecture_id !== null && $this->lecture_id !== '' ? (int) $this->lecture_id : null;
        if ($lectureId) {
            $documents = LectureDocument::where('lecture_id', $lectureId)
                ->orderBy('sort_order')
                ->get();
        }

        return view('livewire.learning.admin-document-manager', compact('lectures', 'documents'));
    }
}

