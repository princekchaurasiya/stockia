@extends('layouts.app')

@section('title', 'Trading Learning')

@section('content')
    <div class="container-fluid">
        <x-ui.page-header title="Trading Learning" />

        <div class="alert alert-info small">
            <strong>Timeframe categories:</strong><br>
            Intraday: 1 min, 5 min<br>
            Swing: 15 min – 2 hour<br>
            Short Term: 15 trading days<br>
            Medium Term: 1–3 months<br>
            Long Term: 1–2 years<br>
            <hr class="my-2">
            <strong>Exit rule:</strong> Trades can exit anytime when target or stop loss is hit.<br>
            If the upper wick is bigger than the body, treat the candle as bearish (green or red, it does not matter).<br>
            If the lower wick is bigger than the body, treat the candle as bullish (green or red, it does not matter).
        </div>

        <div class="row g-3">
            <div class="col-md-3">
                <livewire:learning.batch-list />
            </div>
            <div class="col-md-4">
                <livewire:learning.lecture-list />
            </div>
            <div class="col-md-5">
                <livewire:learning.lecture-view />
            </div>
        </div>
    </div>
@endsection

