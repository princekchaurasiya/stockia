@props(['video', 'modalIdPrefix' => 'youtube-preview'])

@php
    $videoUrl = route('admin.learning.videos.index', array_filter([
        'lecture' => $video->lecture_id,
        'batch' => $video->lecture->batch_id ?? null,
    ]));
@endphp

<div {{ $attributes->merge(['class' => 'linked-video-item d-flex align-items-center gap-3']) }}>
    <x-ui.youtube-preview
        :url="$video->youtube_url"
        :label="$video->label"
        :youtube-title="$video->displayYoutubeTitle()"
        compact
        :modal-id="$modalIdPrefix.'-'.$video->id"
    />
    <div class="min-w-0 flex-grow-1">
        <a href="{{ $videoUrl }}" class="fw-medium text-decoration-none d-block">{{ $video->label }}</a>
        @if ($youtubeTitle = $video->displayYoutubeTitle())
            <div class="small text-muted youtube-title-line" title="{{ $youtubeTitle }}">
                <i class="bi bi-youtube text-danger"></i> {{ $youtubeTitle }}
            </div>
        @endif
        @if ($video->video_type)
            <span class="badge text-bg-light border mt-1">{{ $video->video_type }}</span>
        @endif
        @if ($video->lecture)
            <div class="small text-muted mt-1">
                Lecture:
                <a href="{{ route('admin.learning.lectures.index', array_filter(['batch' => $video->lecture->batch_id, 'lecture' => $video->lecture_id])) }}" class="text-decoration-none">
                    {{ $video->lecture->title }}
                </a>
            </div>
        @endif
    </div>
</div>
