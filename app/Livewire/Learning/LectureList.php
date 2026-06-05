<?php

namespace App\Livewire\Learning;

use App\Models\Lecture;
use App\Models\Module;
use Livewire\Component;

class LectureList extends Component
{
    public ?int $batchId = null;

    protected $listeners = [
        'batchSelected' => 'onBatchSelected',
    ];

    public function onBatchSelected(int $batchId): void
    {
        $this->batchId = $batchId;
    }

    public function selectLecture(int $lectureId): void
    {
        $this->dispatch('lectureSelected', $lectureId);
    }

    public function render()
    {
        $modules = Module::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $lecturesByModule = [];

        if ($this->batchId) {
            $lectures = Lecture::where('batch_id', $this->batchId)
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get()
                ->groupBy('module_id');

            $lecturesByModule = $lectures;
        }

        return view('livewire.learning.lecture-list', [
            'modules' => $modules,
            'lecturesByModule' => $lecturesByModule,
        ]);
    }
}

