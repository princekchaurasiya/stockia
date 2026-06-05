<div>
    @if ($show)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,.45);">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ $documentId ? 'Edit document' : 'Add document' }}</h5>
                        <button type="button" class="btn-close" wire:click="close"></button>
                    </div>
                    <form wire:submit.prevent="save">
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <x-ui.form-label :help="\App\Support\FieldHelp::documentLecture()">Lecture</x-ui.form-label>
                                    <select class="form-select @error('lecture_id') is-invalid @enderror" wire:model="lecture_id">
                                        <option value="">Select lecture</option>
                                        @foreach($lectures as $lecture)
                                            <option value="{{ $lecture->id }}">{{ $lecture->title }}</option>
                                        @endforeach
                                    </select>
                                    @error('lecture_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <x-ui.form-label :help="\App\Support\FieldHelp::documentTitle()">Title</x-ui.form-label>
                                    <input type="text" class="form-control @error('title') is-invalid @enderror" wire:model="title" placeholder="e.g. Session slides">
                                    @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <x-ui.form-label :help="\App\Support\FieldHelp::documentFile()">File (PDF, PPT, PPTX)</x-ui.form-label>
                                    <input type="file" class="form-control @error('file') is-invalid @enderror" wire:model="file" accept=".pdf,.ppt,.pptx">
                                    @error('file')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    <div wire:loading wire:target="file" class="small text-muted mt-1">Uploading file...</div>
                                </div>
                                <div class="col-md-3">
                                    <x-ui.form-label-sort-order context="documents within a lecture" />
                                    <input type="number" class="form-control @error('sort_order') is-invalid @enderror" wire:model="sort_order" min="0">
                                    @error('sort_order')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-3 d-flex align-items-end">
                                    <x-ui.form-check-field id="document_modal_active" wire:model="is_active" :help="\App\Support\FieldHelp::active('document')">
                                        Active
                                    </x-ui.form-check-field>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" wire:click="close">Cancel</button>
                            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled" wire:target="save,file">
                                {{ $documentId ? 'Save changes' : 'Add document' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
