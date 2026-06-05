@props([
    'url',
    'label' => 'YouTube video',
    'youtubeTitle' => null,
    'size' => 'md',
    'compact' => false,
    'modalId' => null,
])

@php
    use App\Support\Youtube;

    $thumbnail = Youtube::thumbnailUrl($url);
    $embed = Youtube::embedUrl($url);
    $watch = Youtube::watchUrl($url) ?? $url;
    $modalId = $modalId ?? 'youtube-preview-' . md5((string) $url);
    $modalTitle = $youtubeTitle ?: $label;
    $sizeClass = match (true) {
        $compact => 'youtube-preview--table',
        $size === 'sm' => 'youtube-preview--sm',
        $size === 'lg' => 'youtube-preview--lg',
        default => 'youtube-preview--md',
    };
@endphp

@if ($thumbnail && $embed)
    <div {{ $attributes->merge(['class' => "youtube-preview {$sizeClass}" . ($compact ? ' youtube-preview--compact' : '')]) }}>
        <button
            type="button"
            class="youtube-preview__trigger border-0 p-0 bg-transparent"
            data-bs-toggle="modal"
            data-bs-target="#{{ $modalId }}"
            aria-label="Play {{ $modalTitle }}"
            @if ($compact) title="Play {{ $modalTitle }}" @endif
        >
            <img
                src="{{ $thumbnail }}"
                alt="{{ $modalTitle }} thumbnail"
                class="youtube-preview__image"
                loading="lazy"
                width="96"
                height="54"
            >
            <span class="youtube-preview__play"><i class="bi bi-play-fill"></i></span>
        </button>
        @unless ($compact)
            <div class="youtube-preview__actions">
                <button
                    type="button"
                    class="btn btn-link btn-sm p-0 text-decoration-none"
                    data-bs-toggle="modal"
                    data-bs-target="#{{ $modalId }}"
                >
                    Play here
                </button>
                <span class="text-muted">·</span>
                <a href="{{ $watch }}" target="_blank" rel="noopener noreferrer" class="small text-muted">
                    Open on YouTube
                </a>
            </div>
        @endunless
    </div>

    <div class="modal fade" id="{{ $modalId }}" tabindex="-1" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow">
                <div class="modal-header">
                    <h5 class="modal-title">{{ $modalTitle }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="ratio ratio-16x9">
                        <iframe
                            src=""
                            data-src="{{ $embed }}"
                            title="{{ $modalTitle }}"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                            allowfullscreen
                        ></iframe>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="{{ $watch }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-secondary">
                        Open on YouTube
                    </a>
                    <button type="button" class="btn btn-sm btn-primary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@elseif ($url)
    <a href="{{ $watch }}" target="_blank" rel="noopener noreferrer" class="small text-muted">
        Preview on YouTube
    </a>
@endif

@once
    @push('scripts')
        <script>
            document.addEventListener('shown.bs.modal', (event) => {
                const iframe = event.target.querySelector('iframe[data-src]');
                if (iframe && iframe.dataset.src) {
                    iframe.src = iframe.dataset.src;
                }
            });

            document.addEventListener('hidden.bs.modal', (event) => {
                const iframe = event.target.querySelector('iframe[data-src]');
                if (iframe) {
                    iframe.src = '';
                }
            });
        </script>
    @endpush
@endonce
