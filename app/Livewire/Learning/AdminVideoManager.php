<?php

namespace App\Livewire\Learning;

use App\Models\Lecture;
use App\Models\LectureVideo;
use Livewire\Component;

class AdminVideoManager extends Component
{
    public $lecture_id = null;
    public ?int $video_id = null;
    public string $label = '';
    public string $youtube_url = '';
    public string $video_type = '';
    public int $sort_order = 0;
    public bool $is_active = true;

    protected $rules = [
        'lecture_id' => ['required', 'exists:lectures,id'],
        'label' => ['required', 'string', 'max:255'],
        'youtube_url' => ['required', 'url', 'max:255', 'regex:/youtube\.com|youtu\.be/i'],
        'video_type' => ['nullable', 'string', 'max:50'],
        'sort_order' => ['nullable', 'integer', 'min:0'],
        'is_active' => ['boolean'],
    ];

    public function selectLecture(int $lectureId): void
    {
        $this->lecture_id = $lectureId;
        $this->clearVideoForm();
    }

    public function edit(int $id): void
    {
        $video = LectureVideo::findOrFail($id);
        $this->video_id = $video->id;
        $this->lecture_id = $video->lecture_id;
        $this->label = $video->label;
        $this->youtube_url = $video->youtube_url;
        $this->video_type = (string) $video->video_type;
        $this->sort_order = $video->sort_order;
        $this->is_active = (bool) $video->is_active;
    }

    public function clearVideoForm(): void
    {
        $this->reset(['video_id', 'label', 'youtube_url', 'video_type', 'sort_order', 'is_active']);
        $this->is_active = true;
    }

    public function updatedLectureId($value): void
    {
        $this->lecture_id = ($value === '' || $value === null) ? null : (int) $value;
    }

    public function save(): void
    {
        $this->lecture_id = ($this->lecture_id === '' || $this->lecture_id === null) ? null : (int) $this->lecture_id;

        $data = $this->validate();

        if ($this->video_id) {
            LectureVideo::whereKey($this->video_id)->update($data);
        } else {
            LectureVideo::create($data);
        }

        session()->flash('success_video', 'Video saved.');
    }

    public function delete(int $id): void
    {
        LectureVideo::whereKey($id)->delete();
    }

    public function toggleActive(int $id): void
    {
        $video = LectureVideo::findOrFail($id);
        $video->is_active = ! $video->is_active;
        $video->save();
    }

    public function render()
    {
        $lectures = Lecture::orderBy('created_at')->get();

        $videos = collect();
        $lectureId = $this->lecture_id !== null && $this->lecture_id !== '' ? (int) $this->lecture_id : null;
        if ($lectureId) {
            $videos = LectureVideo::where('lecture_id', $lectureId)
                ->orderBy('sort_order')
                ->get();
        }

        return view('livewire.learning.admin-video-manager', compact('lectures', 'videos'));
    }
}

