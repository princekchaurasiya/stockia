<?php

namespace App\Livewire\Admin\Learning;

use App\Models\Lecture;
use App\Models\LectureVideo;
use App\Support\Youtube;
use Livewire\Attributes\On;
use Livewire\Component;

class VideoFormModal extends Component
{
    public bool $show = false;

    public ?int $videoId = null;

    public $lecture_id = null;

    public string $label = '';

    public string $youtube_url = '';

    public string $video_type = '';

    public int $sort_order = 0;

    public bool $is_active = true;

    protected function rules(): array
    {
        return [
            'lecture_id' => ['required', 'exists:lectures,id'],
            'label' => ['required', 'string', 'max:255'],
            'youtube_url' => ['required', 'url', 'max:255', 'regex:/youtube\.com|youtu\.be/i'],
            'video_type' => ['nullable', 'string', 'max:50'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['boolean'],
        ];
    }

    #[On('openVideoFormModal')]
    public function open(?int $id = null, ?int $lectureId = null): void
    {
        $this->videoId = $id;

        if ($id) {
            $video = LectureVideo::findOrFail($id);
            $this->lecture_id = $video->lecture_id;
            $this->label = $video->label;
            $this->youtube_url = $video->youtube_url;
            $this->video_type = (string) $video->video_type;
            $this->sort_order = $video->sort_order;
            $this->is_active = (bool) $video->is_active;
        } else {
            $this->reset(['label', 'youtube_url', 'video_type', 'sort_order']);
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

        if ($this->videoId) {
            $video = LectureVideo::findOrFail($this->videoId);
            $video->fill($data);
            $video->save();
            $message = 'Video updated.';
        } else {
            LectureVideo::create($data);
            $message = 'Video created.';
        }

        $this->dispatch('videoTableRefresh');
        session()->flash('success', $message);
        $this->close();
    }

    public function close(): void
    {
        $this->show = false;
        $this->reset(['videoId', 'lecture_id', 'label', 'youtube_url', 'video_type', 'sort_order']);
        $this->is_active = true;
    }

    public function render()
    {
        $lectures = Lecture::with(['batch', 'module'])->orderBy('title')->get();
        $selectedLecture = $this->lecture_id ? $lectures->firstWhere('id', (int) $this->lecture_id) : null;
        $previewYoutubeTitle = $this->youtube_url !== '' ? Youtube::fetchTitle($this->youtube_url) : null;

        return view('livewire.admin.learning.video-form-modal', compact('lectures', 'selectedLecture', 'previewYoutubeTitle'));
    }
}
