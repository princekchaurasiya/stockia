<?php

namespace App\Livewire\Portal;

use App\Models\Lecture;
use App\Models\UserNote;
use App\Models\UserNoteImage;
use App\Support\UploadLimits;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class NotesManager extends Component
{
    use WithFileUploads;
    use WithPagination;

    public const MAX_ATTACHMENTS_PER_NOTE = 5;

    /** @deprecated Use MAX_ATTACHMENTS_PER_NOTE */
    public const MAX_IMAGES_PER_NOTE = self::MAX_ATTACHMENTS_PER_NOTE;

    public ?int $noteId = null;

    public string $title = '';

    public string $body = '';

    public $lecture_id = null;

    public bool $is_shared = false;

    public string $view = 'list';

    public ?int $viewingId = null;

    /** @var array<int, mixed> */
    public $newImages = [];

    protected function messages(): array
    {
        $maxLabel = UploadLimits::maxFileMegabytesLabel();

        return [
            'newImages.max' => 'You can attach up to '.self::MAX_ATTACHMENTS_PER_NOTE.' files per note.',
            'newImages.*.mimes' => 'Each file must be PNG, JPG, WebP, GIF, or PDF.',
            'newImages.*.max' => "Each file must be {$maxLabel} or smaller.",
            'newImages.*.uploaded' => "Upload failed. Use files of {$maxLabel} or less. If the file is small, restart the dev server with composer dev or raise PHP upload_max_filesize.",
        ];
    }

    protected function rules(): array
    {
        $existingCount = 0;
        if ($this->noteId) {
            $existingCount = UserNoteImage::query()
                ->where('user_note_id', $this->noteId)
                ->count();
        }
        $maxNew = max(0, self::MAX_ATTACHMENTS_PER_NOTE - $existingCount);
        $maxKb = UploadLimits::maxFileKilobytes();

        $rules = [
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
            'lecture_id' => ['nullable', 'exists:lectures,id'],
            'newImages' => ['nullable', 'array', 'max:'.$maxNew],
            'newImages.*' => ['file', 'max:'.$maxKb, 'mimes:jpeg,jpg,png,webp,gif,pdf'],
        ];

        if ($this->canShareNotes()) {
            $rules['is_shared'] = ['boolean'];
        }

        return $rules;
    }

    public function canShareNotes(): bool
    {
        return in_array(auth()->user()->role ?? '', ['admin', 'superadmin'], true);
    }

    public function createNew(): void
    {
        $this->reset(['noteId', 'title', 'body', 'lecture_id', 'is_shared', 'viewingId', 'newImages']);
        $this->view = 'form';
        $this->is_shared = false;
        $this->lecture_id = null;
    }

    public function updatedLectureId($value): void
    {
        $this->lecture_id = ($value === '' || $value === null) ? null : (int) $value;
    }

    public function edit(int $id): void
    {
        $note = UserNote::query()
            ->with('images')
            ->whereKey($id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $this->noteId = $note->id;
        $this->title = $note->title;
        $this->body = $note->body;
        $this->lecture_id = $note->lecture_id;
        $this->is_shared = (bool) $note->is_shared;
        $this->newImages = [];
        $this->view = 'form';
    }

    public function viewNote(int $id): void
    {
        $note = $this->findAccessibleNote($id);
        $this->viewingId = $note->id;
        $this->view = 'read';
    }

    public function backToList(): void
    {
        $this->reset(['noteId', 'title', 'body', 'lecture_id', 'is_shared', 'viewingId', 'newImages']);
        $this->lecture_id = null;
        $this->view = 'list';
    }

    public function removeNewImage(int $index): void
    {
        if (! array_key_exists($index, $this->newImages)) {
            return;
        }

        unset($this->newImages[$index]);
        $this->newImages = array_values($this->newImages);
    }

    public function removeExistingImage(int $imageId): void
    {
        $image = UserNoteImage::query()
            ->whereKey($imageId)
            ->whereHas('userNote', fn ($query) => $query->where('user_id', auth()->id()))
            ->firstOrFail();

        Storage::disk('public')->delete($image->file_path);
        $image->delete();
    }

    public function save(): void
    {
        if ($this->lecture_id === '') {
            $this->lecture_id = null;
        }

        $data = $this->validate();
        $data['user_id'] = auth()->id();
        $data['lecture_id'] = $data['lecture_id'] ?? null;

        if (! $this->canShareNotes()) {
            $data['is_shared'] = false;
        }

        DB::beginTransaction();

        try {
            if ($this->noteId) {
                $note = UserNote::query()
                    ->whereKey($this->noteId)
                    ->where('user_id', auth()->id())
                    ->firstOrFail();

                $note->update(collect($data)->except(['user_id', 'newImages'])->toArray());
            } else {
                $note = UserNote::create(collect($data)->except('newImages')->toArray());
            }

            $this->storeNewImages($note);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }

        $this->backToList();
        $this->resetPage('myPage');
        $this->resetPage('sharedPage');
        session()->flash('success', 'Note saved.');
    }

    public function delete(int $id): void
    {
        $note = UserNote::query()
            ->with('images')
            ->whereKey($id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        foreach ($note->images as $image) {
            Storage::disk('public')->delete($image->file_path);
        }

        $note->delete();

        $this->backToList();
        $this->resetPage('myPage');
        $this->resetPage('sharedPage');
        session()->flash('success', 'Note deleted.');
    }

    protected function storeNewImages(UserNote $note): void
    {
        $sortOrder = (int) $note->images()->max('sort_order');

        foreach ($this->newImages as $upload) {
            $extension = strtolower($upload->getClientOriginalExtension());
            $fileName = (string) Str::uuid().'.'.$extension;
            $storedPath = $upload->storeAs('note-images', $fileName, 'public');

            if (! $storedPath || ! Storage::disk('public')->exists($storedPath)) {
                throw new \RuntimeException('Failed to store note attachment.');
            }

            $sortOrder++;

            UserNoteImage::create([
                'user_note_id' => $note->id,
                'file_path' => $storedPath,
                'original_name' => $upload->getClientOriginalName(),
                'sort_order' => $sortOrder,
            ]);
        }
    }

    protected function findAccessibleNote(int $id): UserNote
    {
        $note = UserNote::with(['user', 'lecture.batch', 'lecture.module', 'images'])->findOrFail($id);

        if ($note->user_id === auth()->id()) {
            return $note;
        }

        if ($note->is_shared && in_array($note->user->role ?? '', ['admin', 'superadmin'], true)) {
            return $note;
        }

        abort(403);
    }

    public function render()
    {
        $viewingNote = null;
        if ($this->view === 'read' && $this->viewingId) {
            $viewingNote = $this->findAccessibleNote($this->viewingId);
        }

        $editingNote = null;
        if ($this->view === 'form' && $this->noteId) {
            $editingNote = UserNote::query()
                ->with('images')
                ->whereKey($this->noteId)
                ->where('user_id', auth()->id())
                ->first();
        }

        $sharedNotes = UserNote::shared()
            ->with(['user', 'lecture.batch', 'lecture.module', 'images'])
            ->where('user_id', '!=', auth()->id())
            ->whereHas('user', fn ($q) => $q->whereIn('role', ['admin', 'superadmin']))
            ->orderByDesc('updated_at')
            ->paginate(10, pageName: 'sharedPage');

        return view('livewire.portal.notes-manager', [
            'myNotes' => UserNote::ownedBy(auth()->id())
                ->with(['lecture.batch', 'lecture.module', 'images'])
                ->orderByDesc('updated_at')
                ->paginate(10, pageName: 'myPage'),
            'sharedNotes' => $sharedNotes,
            'lectures' => Lecture::where('is_active', true)
                ->with(['batch', 'module'])
                ->orderBy('title')
                ->get(['id', 'title', 'batch_id', 'module_id']),
            'viewingNote' => $viewingNote,
            'editingNote' => $editingNote,
            'maxImageSizeLabel' => UploadLimits::maxFileMegabytesLabel(),
            'phpUploadLimitLow' => UploadLimits::isPhpLimitBelowAppMax(),
        ]);
    }
}
