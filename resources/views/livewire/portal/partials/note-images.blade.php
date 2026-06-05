@if ($note->images->isNotEmpty())
    <div class="note-images-grid mt-3">
        @foreach ($note->images as $image)
            @include('livewire.portal.partials.note-attachment-thumb', [
                'url' => $image->url(),
                'name' => $image->original_name ?: ($image->isPdf() ? 'Example PDF' : 'Example image'),
                'isPdf' => $image->isPdf(),
            ])
        @endforeach
    </div>
@endif
