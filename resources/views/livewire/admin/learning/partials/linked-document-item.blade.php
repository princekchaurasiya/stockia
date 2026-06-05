@props(['document'])

@php
    $documentUrl = route('admin.learning.documents.index', array_filter([
        'lecture' => $document->lecture_id,
        'batch' => $document->lecture->batch_id ?? null,
    ]));
@endphp

<div {{ $attributes->merge(['class' => 'linked-document-item d-flex align-items-center gap-3']) }}>
    <div class="linked-document-item__icon flex-shrink-0">
        @if ($document->file_type === 'pdf')
            <i class="bi bi-file-earmark-pdf text-danger fs-4"></i>
        @elseif (in_array($document->file_type, ['ppt', 'pptx']))
            <i class="bi bi-file-earmark-slides text-warning fs-4"></i>
        @else
            <i class="bi bi-file-earmark-text text-secondary fs-4"></i>
        @endif
    </div>
    <div class="min-w-0 flex-grow-1">
        <a href="{{ $documentUrl }}" class="fw-medium text-decoration-none d-block text-truncate">{{ $document->title }}</a>
        <div class="small text-muted text-uppercase">{{ $document->file_type ?: 'file' }}</div>
        @if ($document->lecture)
            <div class="small text-muted mt-1">
                Lecture:
                <a href="{{ route('admin.learning.lectures.index', array_filter(['batch' => $document->lecture->batch_id, 'lecture' => $document->lecture_id])) }}" class="text-decoration-none">
                    {{ $document->lecture->title }}
                </a>
            </div>
        @endif
    </div>
    @if ($document->file_path)
        <a href="{{ asset('storage/'.$document->file_path) }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-secondary flex-shrink-0">
            Open file
        </a>
    @endif
</div>
