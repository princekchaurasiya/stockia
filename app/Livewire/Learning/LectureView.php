<?php

namespace App\Livewire\Learning;

use App\Models\Lecture;
use App\Models\LectureVideo;
use Livewire\Component;

class LectureView extends Component
{
    public ?Lecture $lecture = null;
    /** @var \Illuminate\Support\Collection<int, LectureVideo>|null */
    public $videos = null;
    public ?int $selectedVideoId = null;

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

    protected $listeners = [
        'lectureSelected' => 'loadLecture',
    ];

    public function loadLecture(int $lectureId): void
    {
        $this->lecture = Lecture::where('is_active', true)
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
        $this->selectedVideoId = $this->videos->first()?->id;

        $this->debugLog('E', 'LectureView.php:loadLecture', 'Lecture loaded', [
            'lecture_id' => $lectureId,
            'found' => (bool) $this->lecture,
            'video_count' => $this->videos->count(),
            'document_count' => $this->lecture?->documents?->count() ?? 0,
        ]);
    }

    public function selectVideo(int $videoId): void
    {
        $this->selectedVideoId = $videoId;
    }

    public function render()
    {
        return view('livewire.learning.lecture-view');
    }
}

