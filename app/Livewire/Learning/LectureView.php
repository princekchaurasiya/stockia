<?php

namespace App\Livewire\Learning;

use App\Models\Lecture;
use App\Models\UserNote;
use Illuminate\Support\Collection;
use Livewire\Component;

class LectureView extends Component
{
    public ?Lecture $lecture = null;

    /** @var Collection<int, \App\Models\LectureVideo> */
    public $videos;

    /** @var Collection<int, UserNote> */
    public $linkedNotes;

    protected $listeners = [
        'lectureSelected' => 'loadLecture',
    ];

    public function mount(): void
    {
        $this->videos = collect();
        $this->linkedNotes = collect();
    }

    public function loadLecture(int $lectureId): void
    {
        $this->lecture = Lecture::query()
            ->where('is_active', true)
            ->with([
                'batch',
                'module',
                'videos' => function ($q) {
                    $q->where('is_active', true)->orderBy('sort_order');
                },
                'documents' => function ($q) {
                    $q->where('is_active', true)->orderBy('sort_order');
                },
            ])
            ->find($lectureId);

        $this->videos = $this->lecture?->videos ?? collect();
        $this->linkedNotes = $this->lecture ? $this->notesLinkedToLecture($this->lecture->id) : collect();
    }

    /**
     * @return Collection<int, UserNote>
     */
    private function notesLinkedToLecture(int $lectureId): Collection
    {
        $userId = auth()->id();

        return UserNote::query()
            ->with(['user', 'images'])
            ->where('lecture_id', $lectureId)
            ->where(function ($query) use ($userId) {
                $query->where('user_id', $userId)
                    ->orWhere(function ($shared) {
                        $shared->where('is_shared', true)
                            ->whereHas('user', fn ($user) => $user->whereIn('role', ['admin', 'superadmin']));
                    });
            })
            ->orderByDesc('updated_at')
            ->get();
    }

    public function render()
    {
        return view('livewire.learning.lecture-view');
    }
}
