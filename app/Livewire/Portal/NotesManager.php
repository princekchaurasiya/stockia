<?php

namespace App\Livewire\Portal;

use App\Models\Lecture;
use App\Models\UserNote;
use Livewire\Component;
use Livewire\WithPagination;

class NotesManager extends Component
{
    use WithPagination;

    public ?int $noteId = null;
    public string $title = '';
    public string $body = '';
    public $lecture_id = null;
    public bool $is_shared = false;
    public string $view = 'list';
    public ?int $viewingId = null;

    protected function rules(): array
    {
        $rules = [
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
            'lecture_id' => ['nullable', 'exists:lectures,id'],
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
        $this->reset(['noteId', 'title', 'body', 'lecture_id', 'is_shared', 'viewingId']);
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
        $note = UserNote::whereKey($id)->where('user_id', auth()->id())->firstOrFail();
        $this->noteId = $note->id;
        $this->title = $note->title;
        $this->body = $note->body;
        $this->lecture_id = $note->lecture_id;
        $this->is_shared = (bool) $note->is_shared;
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
        $this->reset(['noteId', 'title', 'body', 'lecture_id', 'is_shared', 'viewingId']);
        $this->lecture_id = null;
        $this->view = 'list';
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

        if ($this->noteId) {
            UserNote::whereKey($this->noteId)
                ->where('user_id', auth()->id())
                ->update(collect($data)->except('user_id')->toArray());
        } else {
            UserNote::create($data);
        }

        $this->backToList();
        $this->resetPage('myPage');
        $this->resetPage('sharedPage');
        session()->flash('success', 'Note saved.');
    }

    public function delete(int $id): void
    {
        UserNote::whereKey($id)->where('user_id', auth()->id())->delete();
        $this->backToList();
        $this->resetPage('myPage');
        $this->resetPage('sharedPage');
        session()->flash('success', 'Note deleted.');
    }

    protected function findAccessibleNote(int $id): UserNote
    {
        $note = UserNote::with(['user', 'lecture.batch', 'lecture.module'])->findOrFail($id);

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

        $sharedNotes = UserNote::shared()
            ->with(['user', 'lecture.batch', 'lecture.module'])
            ->where('user_id', '!=', auth()->id())
            ->whereHas('user', fn ($q) => $q->whereIn('role', ['admin', 'superadmin']))
            ->orderByDesc('updated_at')
            ->paginate(10, pageName: 'sharedPage');

        return view('livewire.portal.notes-manager', [
            'myNotes' => UserNote::ownedBy(auth()->id())
                ->with(['lecture.batch', 'lecture.module'])
                ->orderByDesc('updated_at')
                ->paginate(10, pageName: 'myPage'),
            'sharedNotes' => $sharedNotes,
            'lectures' => Lecture::where('is_active', true)
                ->with(['batch', 'module'])
                ->orderBy('title')
                ->get(['id', 'title', 'batch_id', 'module_id']),
            'viewingNote' => $viewingNote,
        ]);
    }
}
