<div>
    @if ($show)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,.45);">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ $videoId ? 'Edit video' : 'Add video' }}</h5>
                        <button type="button" class="btn-close" wire:click="close"></button>
                    </div>
                    <form wire:submit.prevent="save">
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <x-ui.form-label :help="\App\Support\FieldHelp::videoLecture()">Lecture</x-ui.form-label>
                                    <select class="form-select @error('lecture_id') is-invalid @enderror" wire:model.live="lecture_id">
                                        <option value="">Select lecture</option>
                                        @foreach($lectures as $lecture)
                                            <option value="{{ $lecture->id }}">
                                                {{ $lecture->batch->name ?? '—' }} · {{ $lecture->module->name ?? '—' }} · {{ $lecture->title }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('lecture_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    @if ($selectedLecture)
                                        @include('livewire.admin.learning.partials.lecture-notes-snippet', [
                                            'lecture' => $selectedLecture,
                                            'max' => 160,
                                            'block' => true,
                                        ])
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    <x-ui.form-label :help="\App\Support\FieldHelp::videoLabel()">Label</x-ui.form-label>
                                    <input type="text" class="form-control @error('label') is-invalid @enderror" wire:model="label" placeholder="e.g. Part 1, Main session">
                                    @error('label')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-12">
                                    <x-ui.form-label :help="\App\Support\FieldHelp::videoYoutubeUrl()">YouTube URL</x-ui.form-label>
                                    <input type="url" class="form-control @error('youtube_url') is-invalid @enderror" wire:model.live="youtube_url" placeholder="https://www.youtube.com/watch?v=...">
                                    @error('youtube_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    @if ($youtube_url)
                                        @if ($previewYoutubeTitle)
                                            <p class="small text-muted mb-2">
                                                <i class="bi bi-youtube text-danger"></i>
                                                YouTube title: <span class="fw-medium">{{ $previewYoutubeTitle }}</span>
                                            </p>
                                        @endif
                                        <div class="mt-2">
                                            <x-ui.youtube-preview
                                                :url="$youtube_url"
                                                :label="$label ?: 'Video preview'"
                                                :youtube-title="$previewYoutubeTitle"
                                                size="lg"
                                                modal-id="youtube-preview-form"
                                            />
                                        </div>
                                    @endif
                                </div>
                                <div class="col-md-4">
                                    <x-ui.form-label :help="\App\Support\FieldHelp::videoType()">Video type</x-ui.form-label>
                                    <input type="text" class="form-control @error('video_type') is-invalid @enderror" wire:model="video_type" placeholder="Main, Supplementary">
                                    @error('video_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-4">
                                    <x-ui.form-label-sort-order context="videos within a lecture" />
                                    <input type="number" class="form-control @error('sort_order') is-invalid @enderror" wire:model="sort_order" min="0">
                                    @error('sort_order')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-4 d-flex align-items-end">
                                    <x-ui.form-check-field id="video_modal_active" wire:model="is_active" :help="\App\Support\FieldHelp::active('video')">
                                        Active
                                    </x-ui.form-check-field>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" wire:click="close">Cancel</button>
                            <button type="submit" class="btn btn-primary">{{ $videoId ? 'Save changes' : 'Add video' }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
