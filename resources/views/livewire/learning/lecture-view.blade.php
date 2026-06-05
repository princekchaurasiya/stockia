<div>
    <h2 class="h6 mb-3">Lecture</h2>

    @if(!$lecture)
        <p class="text-muted small mb-0">Select a lecture to start learning.</p>
    @else

        <h3 class="h5 mb-1">{{ $lecture->title }}</h3>
        @include('livewire.learning.partials.lecture-context', ['lecture' => $lecture])

        <div class="row g-3 mt-1">
            <div class="col-md-5">
                <h4 class="h6">Videos</h4>
                @if(!$videos || $videos->isEmpty())
                    <p class="text-muted small mb-0">No videos added yet.</p>
                @else
                    <ul class="list-group">
                        @foreach($videos as $index => $video)
                            <li class="list-group-item d-flex justify-content-between align-items-center {{ $selectedVideoId === $video->id ? 'active' : '' }}">
                                <button type="button"
                                        class="btn btn-link p-0 text-start text-truncate text-decoration-none {{ $selectedVideoId === $video->id ? 'text-white' : '' }}"
                                        wire:click="selectVideo({{ $video->id }})">
                                    {{ $index + 1 }}. {{ $video->label }}
                                </button>
                                @if($video->video_type)
                                    <span class="badge {{ $selectedVideoId === $video->id ? 'text-bg-light text-dark' : 'text-bg-light' }}">
                                        {{ $video->video_type }}
                                    </span>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
            <div class="col-md-7">
                @php
                    $currentVideo = $videos && $videos->isNotEmpty()
                        ? $videos->firstWhere('id', $selectedVideoId) ?? $videos->first()
                        : null;
                @endphp

                @if($currentVideo)
                    <div class="ratio ratio-16x9 mb-3">
                        <iframe
                            src="{{ \App\Support\Youtube::embedUrl($currentVideo->youtube_url) ?? $currentVideo->youtube_url }}"
                            title="YouTube video player"
                            frameborder="0"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                            allowfullscreen>
                        </iframe>
                    </div>
                @else
                    <p class="text-muted small">Add a video to start.</p>
                @endif

                @if($lecture->documents && $lecture->documents->isNotEmpty())
                    <hr class="my-3">
                    <h4 class="h6">Lecture resources</h4>
                    <ul class="list-group small">
                        @foreach($lecture->documents as $index => $document)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <a href="{{ asset('storage/'.$document->file_path) }}" target="_blank" class="text-decoration-none">
                                    @if($document->file_type === 'pdf')
                                        <i class="bi bi-file-earmark-pdf text-danger me-1"></i>
                                    @elseif(in_array($document->file_type, ['ppt', 'pptx']))
                                        <i class="bi bi-file-earmark-slides text-warning me-1"></i>
                                    @else
                                        <i class="bi bi-file-earmark-text me-1"></i>
                                    @endif
                                    {{ $index + 1 }}. {{ $document->title }}
                                </a>
                                <span class="badge text-bg-light text-uppercase">
                                    {{ $document->file_type }}
                                </span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>

        @if($lecture && $lecture->notes)
            <hr class="my-3">
            <h4 class="h6">Lecture notes</h4>
            <div class="border rounded p-3 bg-light">
                <p class="small mb-0">{!! nl2br(e($lecture->notes)) !!}</p>
            </div>
        @endif
    @endif
</div>

