@props([
    'url',
    'name' => 'Attachment',
    'isPdf' => false,
    'thumbClass' => 'note-image-thumb',
])

<a href="{{ $url }}"
   target="_blank"
   rel="noopener"
   class="{{ $thumbClass }}"
   title="{{ $name }}">
    @if ($isPdf)
        <span class="note-file-thumb">
            <i class="bi bi-file-earmark-pdf" aria-hidden="true"></i>
            <span class="note-file-thumb-label">{{ $name }}</span>
        </span>
    @else
        <img src="{{ $url }}" alt="{{ $name }}" loading="lazy">
    @endif
</a>
