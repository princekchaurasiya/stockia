<?php

namespace App\Livewire\Admin\Learning;

use App\Models\Batch;
use App\Models\BatchEnrollment;
use App\Models\Lecture;
use App\Models\LectureDocument;
use App\Models\LectureVideo;
use App\Models\Module;
use App\Models\UserNote;
use Livewire\Attributes\On;
use Livewire\Component;

class BatchDetail extends Component
{
    public Batch $batch;

    public function mount(Batch $batch): void
    {
        $this->batch = $batch;
    }

    #[On('batchTableRefresh')]
    public function refreshBatch(): void
    {
        $this->batch->refresh();
    }

    public function render()
    {
        $batch = $this->batch->loadCount([
            'lectures',
            'videos',
            'documents',
            'userNotes',
            'enrollments',
            'enrollments as active_enrollments_count' => fn ($q) => $q->where('is_active', true),
            'lectures as lectures_with_notes_count' => fn ($q) => $q->withNotes(),
        ]);

        $enrollments = BatchEnrollment::query()
            ->forStudents()
            ->with('user')
            ->where('batch_id', $batch->id)
            ->orderByDesc('enrolled_at')
            ->get();

        $lectures = Lecture::query()
            ->with(['module', 'userNotes' => fn ($q) => $q->with('user')->orderByDesc('updated_at')])
            ->withCount(['videos', 'documents', 'userNotes'])
            ->where('batch_id', $batch->id)
            ->orderBy('sort_order')
            ->orderBy('title')
            ->get();

        $modules = Module::query()
            ->whereHas('lectures', fn ($q) => $q->where('batch_id', $batch->id))
            ->withCount(['lectures' => fn ($q) => $q->where('batch_id', $batch->id)])
            ->orderBy('sort_order')
            ->get();

        $videos = LectureVideo::query()
            ->with(['lecture.module'])
            ->whereHas('lecture', fn ($q) => $q->where('batch_id', $batch->id))
            ->orderBy('sort_order')
            ->get();

        $documents = LectureDocument::query()
            ->with(['lecture.module'])
            ->whereHas('lecture', fn ($q) => $q->where('batch_id', $batch->id))
            ->orderBy('sort_order')
            ->get();

        $linkedNotes = UserNote::query()
            ->with(['user', 'lecture.module'])
            ->whereHas('lecture', fn ($q) => $q->where('batch_id', $batch->id))
            ->orderByDesc('updated_at')
            ->get();

        return view('livewire.admin.learning.batch-detail', compact(
            'batch',
            'enrollments',
            'lectures',
            'modules',
            'videos',
            'documents',
            'linkedNotes',
        ));
    }
}
