@extends('layouts.app')

@section('title', __('stockia.settings.page_title'))

@section('content')
    <div class="container-fluid px-0">
        <x-ui.page-header :title="__('stockia.settings.page_title')">
            <x-slot:meta>{{ __('stockia.settings.page_subtitle') }}</x-slot:meta>
        </x-ui.page-header>

        @foreach ($groups as $group)
                <section class="mb-4">
                    <div class="mb-3">
                        <h2 class="h5 mb-1">{{ $group['title'] }}</h2>
                        <p class="text-muted small mb-0">{{ $group['description'] }}</p>
                    </div>

                    <div class="row g-3">
                        @foreach ($group['items'] as $item)
                            <div class="col-md-6 col-xl-4">
                                <x-ui.settings-map-card
                                    :label="$item['label']"
                                    :description="$item['description']"
                                    :href="route($item['route'])"
                                    :icon="$item['icon']"
                                />
                            </div>
                        @endforeach
                    </div>
                </section>
            @endforeach
    </div>
@endsection
