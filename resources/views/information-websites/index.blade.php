@extends('layouts.app')

@section('title', __('stockia.information_websites.page_title'))

@section('content')
<div class="container-fluid">
    <x-ui.page-header :title="__('stockia.information_websites.page_title')">
        @if($links->isNotEmpty())
            <x-slot:meta>{{ $links->count() }} websites</x-slot:meta>
            <x-slot:actions>
                @if (auth()->check() && in_array(auth()->user()->role ?? '', ['admin', 'superadmin'], true))
                    <a href="{{ route('admin.information_links.index') }}" class="btn btn-sm btn-outline-secondary me-2">
                        {{ __('stockia.information_websites.manage') }}
                    </a>
                @endif
                <div class="btn-group" role="group" aria-label="View mode">
                    <button type="button" class="btn btn-sm btn-primary" id="view-mode-card">Card view</button>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="view-mode-list">List view</button>
                </div>
            </x-slot:actions>
        @endif
    </x-ui.page-header>

    <div class="mb-4">
        <input type="text"
               id="search-websites"
               class="form-control form-control-lg"
               placeholder="{{ __('stockia.information_websites.search_placeholder') }}"
               autocomplete="off">
    </div>

    @if($links->isEmpty())
        <p class="text-muted">{{ __('stockia.information_websites.no_links') }}</p>
        @if (auth()->check() && in_array(auth()->user()->role ?? '', ['admin', 'superadmin'], true))
            <a href="{{ route('admin.information_links.index') }}" class="btn btn-sm btn-outline-secondary">
                {{ __('stockia.information_websites.manage') }}
            </a>
        @endif
    @else
        <div class="row" id="website-cards">
            @foreach($links as $index => $link)
                @php
                    $host = parse_url($link->url, PHP_URL_HOST) ?: '';
                    $faviconUrl = $host ? 'https://www.google.com/s2/favicons?domain=' . e($host) . '&sz=64' : '';
                @endphp
                <div class="col-md-4 col-lg-3 mb-4 website-card" data-title="{{ strtolower(e($link->title)) }}">
                    <div class="card shadow-sm h-100">
                        <div class="card-body text-center d-flex flex-column">
                            @if($faviconUrl)
                                <img src="{{ $faviconUrl }}"
                                     alt=""
                                     width="48"
                                     height="48"
                                     class="mx-auto mb-2 rounded"
                                     loading="lazy"
                                     onerror="this.style.display='none'">
                            @endif
                            <h5 class="card-title mb-2">{{ $index + 1 }}. {{ $link->title }}</h5>
                            <a href="{{ $link->url }}"
                               target="_blank"
                               rel="noopener noreferrer"
                               class="btn btn-primary mt-auto">
                                {{ __('stockia.information_websites.visit_website') }}
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="table-responsive d-none" id="website-table-wrapper">
            <table class="table table-striped align-middle mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Title</th>
                        <th>URL</th>
                        <th class="text-end">{{ __('stockia.information_websites.visit_website') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($links as $index => $link)
                        <tr class="website-row" data-title="{{ strtolower(e($link->title)) }}">
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $link->title }}</td>
                            <td><a href="{{ $link->url }}" target="_blank" rel="noopener noreferrer">{{ $link->url }}</a></td>
                            <td class="text-end">
                                <a href="{{ $link->url }}"
                                   target="_blank"
                                   rel="noopener noreferrer"
                                   class="btn btn-sm btn-primary">
                                    {{ __('stockia.information_websites.visit_website') }}
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

@if($links->isNotEmpty())
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var search = document.getElementById('search-websites');
    var cards = document.querySelectorAll('.website-card');
    var rows = document.querySelectorAll('.website-row');
    var cardsContainer = document.getElementById('website-cards');
    var tableWrapper = document.getElementById('website-table-wrapper');
    var btnCard = document.getElementById('view-mode-card');
    var btnList = document.getElementById('view-mode-list');

    if (btnCard && btnList && cardsContainer && tableWrapper) {
        btnCard.addEventListener('click', function () {
            btnCard.classList.replace('btn-outline-primary', 'btn-primary');
            btnList.classList.replace('btn-primary', 'btn-outline-primary');
            cardsContainer.classList.remove('d-none');
            tableWrapper.classList.add('d-none');
        });

        btnList.addEventListener('click', function () {
            btnList.classList.replace('btn-outline-primary', 'btn-primary');
            btnCard.classList.replace('btn-primary', 'btn-outline-primary');
            cardsContainer.classList.add('d-none');
            tableWrapper.classList.remove('d-none');
        });
    }

    if (!search || !cards.length) return;
    search.addEventListener('input', function () {
        var q = (this.value || '').trim().toLowerCase();
        cards.forEach(function (card) {
            var title = (card.getAttribute('data-title') || '');
            card.style.display = q === '' || title.indexOf(q) !== -1 ? '' : 'none';
        });
        rows.forEach(function (row) {
            var title = (row.getAttribute('data-title') || '');
            row.style.display = q === '' || title.indexOf(q) !== -1 ? '' : 'none';
        });
    });
});
</script>
@endpush
@endif
@endsection
