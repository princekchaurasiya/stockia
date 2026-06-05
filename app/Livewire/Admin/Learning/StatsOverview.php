<?php

namespace App\Livewire\Admin\Learning;

use App\Models\Batch;
use App\Models\Lecture;
use App\Models\LectureDocument;
use App\Models\LectureVideo;
use App\Models\Module;
use Livewire\Component;

class StatsOverview extends Component
{
    public function render()
    {
        $stats = [
            'batches' => [
                'total' => Batch::count(),
                'active' => Batch::where('is_active', true)->count(),
            ],
            'modules' => [
                'total' => Module::count(),
                'active' => Module::where('is_active', true)->count(),
            ],
            'lectures' => [
                'total' => Lecture::count(),
                'active' => Lecture::where('is_active', true)->count(),
            ],
            'videos' => LectureVideo::count(),
            'documents' => LectureDocument::count(),
        ];

        $lectureCount = max(1, $stats['lectures']['total']);
        $stats['avg_videos_per_lecture'] = round($stats['videos'] / $lectureCount, 1);

        $recentLectures = Lecture::with(['batch', 'module'])
            ->latest()
            ->limit(10)
            ->get();

        return view('livewire.admin.learning.stats-overview', compact('stats', 'recentLectures'));
    }
}
