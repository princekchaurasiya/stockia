@extends('layouts.app')

@section('title', 'Trading Learning')

@section('content')
    <div class="container-fluid px-0 learning-hub">
        <x-ui.page-header title="Trading Learning">
            <x-slot:meta>Browse your batch, pick a lecture, and watch lessons with notes and resources.</x-slot:meta>
        </x-ui.page-header>

        <div class="card border-0 shadow-sm learning-reference-card mb-4">
            <div class="card-body py-3">
                <details class="learning-reference-details">
            <summary class="learning-reference-toggle">
                <span class="learning-reference-icon">
                    <i class="bi bi-lightbulb" aria-hidden="true"></i>
                </span>
                <span class="fw-semibold text-body">Trading reference</span>
                <span class="text-muted small ms-1">Timeframes and exit rules</span>
                <i class="bi bi-chevron-down ms-2 text-muted learning-reference-chevron" aria-hidden="true"></i>
            </summary>

            <div class="learning-reference-content mt-3">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="learning-reference-block h-100">
                            <h3 class="h6 mb-2">Timeframe categories</h3>
                            <ul class="list-unstyled small mb-0 learning-reference-list">
                                <li><span class="fw-medium">Intraday</span> — 1 min, 5 min</li>
                                <li><span class="fw-medium">Swing</span> — 15 min to 2 hour</li>
                                <li><span class="fw-medium">Short term</span> — 15 trading days</li>
                                <li><span class="fw-medium">Medium term</span> — 1–3 months</li>
                                <li><span class="fw-medium">Long term</span> — 1–2 years</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="learning-reference-block h-100">
                            <h3 class="h6 mb-2">Exit rule</h3>
                            <ul class="list-unstyled small mb-0 learning-reference-list">
                                <li>Trades can exit anytime when target or stop loss is hit.</li>
                                <li>If the upper wick is bigger than the body, treat the candle as bearish.</li>
                                <li>If the lower wick is bigger than the body, treat the candle as bullish.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
                </details>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-4 col-xl-3">
                <div class="card border-0 shadow-sm learning-hub-panel h-100">
                    <div class="card-body">
                        <livewire:learning.batch-list />
                        <div class="learning-hub-divider"></div>
                        <livewire:learning.lecture-list />
                    </div>
                </div>
            </div>
            <div class="col-lg-8 col-xl-9">
                <div class="card border-0 shadow-sm learning-hub-panel h-100">
                    <div class="card-body">
                        <livewire:learning.lecture-view />
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
