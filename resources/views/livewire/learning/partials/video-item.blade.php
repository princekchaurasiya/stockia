@props(['video', 'modalIdPrefix' => 'learning-video'])

<div {{ $attributes->merge(['class' => 'linked-video-item d-flex align-items-center gap-3']) }}>
    <x-ui.youtube-preview
        :url="$video->youtube_url"
        :label="$video->label"
        :youtube-title="$video->displayYoutubeTitle()"
        compact
        :modal-id="$modalIdPrefix.'-'.$video->id"
    />
    <div class="min-w-0 flex-grow-1">
        <div class="fw-medium">{{ $video->label }}</div>
        @if ($youtubeTitle = $video->displayYoutubeTitle())
            <div class="small text-muted youtube-title-line" title="{{ $youtubeTitle }}">
                <i class="bi bi-youtube text-danger"></i> {{ $youtubeTitle }}
            </div>
        @endif
        @if ($video->video_type)
            <span class="badge text-bg-light border mt-1">{{ $video->video_type }}</span>
        @endif
    </div>
</div>
