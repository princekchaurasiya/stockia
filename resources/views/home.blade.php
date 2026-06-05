@extends('layouts.app')

@section('title', __('stockia.app.nav.upload'))

@section('content')
    <h1 class="h2 mb-4">{{ __('stockia.app.name') }}</h1>
    <p class="text-muted mb-4">{{ __('stockia.sheet.home_intro') }}</p>

    <div class="card shadow-sm mb-4 border-primary">
        <div class="card-header bg-primary text-white"><strong>Quick links</strong></div>
        <div class="card-body">
            <div class="row g-2">
                <div class="col-md-6 col-lg-4">
                    <a href="{{ route('nifty50.extended.index') }}" class="btn btn-outline-primary w-100 text-start">
                        Nifty 50 – Weightage % of Stocks
                    </a>
                </div>
                @auth
                <div class="col-md-6 col-lg-4">
                    <a href="{{ route('market_activity.index') }}" class="btn btn-outline-primary w-100 text-start">
                        Market Activity Report
                    </a>
                </div>
                <div class="col-md-6 col-lg-4">
                    <a href="{{ route('dashboard') }}" class="btn btn-outline-primary w-100 text-start">
                        Dashboard & charts
                    </a>
                </div>
                @endauth
            </div>
        </div>
    </div>

    <x-data-source-links-card :links="$dataSourceLinks ?? collect()" />

    @livewire('upload-sheet')
    <p class="mt-3 small text-muted">{{ __('stockia.sheet.supported_files') }}</p>
@endsection
