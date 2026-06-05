<?php

namespace App\Models;

use App\Support\Youtube;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LectureVideo extends Model
{
    use HasFactory;

    protected $fillable = [
        'lecture_id',
        'label',
        'youtube_url',
        'youtube_title',
        'video_type',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saving(function (LectureVideo $video) {
            if ($video->isDirty('youtube_url')) {
                $video->youtube_title = Youtube::fetchTitle($video->youtube_url);
            }
        });
    }

    public function displayYoutubeTitle(): ?string
    {
        if (filled($this->youtube_title)) {
            return $this->youtube_title;
        }

        return Youtube::fetchTitle($this->youtube_url);
    }

    public function lecture(): BelongsTo
    {
        return $this->belongsTo(Lecture::class);
    }
}

