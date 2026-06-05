<div>
    <div class="learning-hub-section">
        <div class="learning-hub-section-head">
            <h2 class="learning-hub-section-title">Curriculum</h2>
            <p class="learning-hub-section-meta mb-0">
                @if(!$batchId)
                    Select a batch to browse modules and lectures.
                @else
                    Pick a lecture to open videos and resources.
                @endif
            </p>
        </div>

        @if(!$batchId)
            <div class="learning-hub-empty">
                <i class="bi bi-book" aria-hidden="true"></i>
                <p class="mb-0">Choose a batch first.</p>
            </div>
        @else
            @php $hasLectures = false; @endphp

            @foreach($modules as $module)
                @php
                    $lectures = $lecturesByModule[$module->id] ?? collect();
                @endphp
                @if($lectures->isNotEmpty())
                    @php $hasLectures = true; @endphp
                    <div class="learning-module-block" wire:key="module-{{ $module->id }}">
                        <div class="learning-module-head">
                            <div class="min-w-0">
                                <div class="learning-module-name">{{ $module->name }}</div>
                                @if ($module->description)
                                    <p class="learning-module-desc mb-0">{{ $module->description }}</p>
                                @endif
                            </div>
                            @if ($module->timeframe)
                                <span class="badge learning-style-badge">{{ $module->timeframe }}</span>
                            @endif
                        </div>

                        <div class="learning-lecture-list">
                            @foreach($lectures as $lecture)
                                <button type="button"
                                        wire:click="selectLecture({{ $lecture->id }})"
                                        wire:key="lecture-{{ $lecture->id }}"
                                        class="learning-lecture-item {{ $selectedLectureId === $lecture->id ? 'is-active' : '' }}">
                                    <span class="learning-lecture-icon">
                                        <i class="bi {{ $selectedLectureId === $lecture->id ? 'bi-play-fill' : 'bi-play-circle' }}" aria-hidden="true"></i>
                                    </span>
                                    <span class="learning-lecture-title">{{ $lecture->title }}</span>
                                    <i class="bi bi-chevron-right learning-lecture-chevron" aria-hidden="true"></i>
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endforeach

            @unless($hasLectures)
                <div class="learning-hub-empty">
                    <i class="bi bi-journal-x" aria-hidden="true"></i>
                    <p class="mb-0">No lectures in this batch yet.</p>
                </div>
            @endunless
        @endif
    </div>
</div>
