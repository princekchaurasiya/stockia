@props([
    'title' => null,
    'links' => [],
])

@php
    $title = $title ?? __('stockia.data_source.title');
@endphp

@if(isset($links) && $links->isNotEmpty())
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-secondary text-white">
            {{ $title }}
        </div>
        <div class="card-body">
            <p class="small text-muted mb-2">{{ __('stockia.sheet.home_links_hint') }}</p>
            <ul class="list-group list-group-flush">
                @foreach($links as $link)
                    <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <span>{{ $link->name }}</span>
                        <div class="btn-group btn-group-sm">
                            <a href="{{ route('data_source.open', $link) }}" target="_blank" rel="noopener noreferrer" class="btn btn-outline-primary">{{ __('stockia.data_source.open') }}</a>
                            <a href="{{ route('data_source.download', $link) }}" class="btn btn-outline-success">{{ __('stockia.data_source.download') }}</a>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
@endif
