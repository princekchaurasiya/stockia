@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="container-fluid">
        <x-ui.page-header title="Dashboard" />

        <div class="row g-3">
            {{-- LEFT: Spiritual Banner --}}
            <div class="col-lg-8">
                <div class="card shadow-sm border-0 overflow-hidden rounded-3">
                    @php
                        $bannerSrc = file_exists(public_path('images/banner/spritual2.png'))
                            ? asset('images/banner/spritual2.png')
                            : (file_exists(public_path('images/banner/spritual.png')) ? asset('images/banner/spritual.png') : null);
                    @endphp
                    @if($bannerSrc)
                        <img
                            src="{{ $bannerSrc }}"
                            class="img-fluid w-100 h-auto rounded"
                            alt="Krishna Arjun Saraswati Banner"
                        >
                    @else
                        <div class="bg-light d-flex align-items-center justify-content-center small text-muted py-5 rounded">
                            Place <code>spritual2.png</code> in <code>public/images/banner/</code>
                        </div>
                    @endif
                </div>
            </div>

            {{-- RIGHT: Market info sidebar (sticky) --}}
            <div class="col-lg-4" style="position:sticky; top:20px; align-self:start;">
                {{-- Market Status --}}
                <div class="card shadow-sm border-0 mb-3">
                    <div class="card-body">
                        <small class="text-muted">Market Status</small>
                        <div class="mt-1">
                            @if($marketStatus === 'bullish')
                                <span class="badge bg-success fs-6">Bullish</span>
                            @elseif($marketStatus === 'bearish')
                                <span class="badge bg-danger fs-6">Bearish</span>
                            @else
                                <span class="badge bg-secondary fs-6">Sideways</span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Latest Upload --}}
                <div class="card shadow-sm border-0 mb-3">
                    <div class="card-body">
                        <small class="text-muted">Latest Upload</small>
                        <div class="mt-1 fw-medium">{{ $latestUploadDate ?? '—' }}</div>
                    </div>
                </div>

                {{-- Market Sentiment --}}
                <div class="card shadow-sm border-0 mb-3">
                    <div class="card-body">
                        <small class="text-muted">Market Sentiment</small>
                        <div class="progress mt-2" style="height:20px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width:{{ $sentimentBullishPct ?? 50 }}%">
                                Bullish {{ $sentimentBullishPct ?? 50 }}%
                            </div>
                            <div class="progress-bar bg-danger" role="progressbar" style="width:{{ 100 - ($sentimentBullishPct ?? 50) }}%">
                                Bearish {{ 100 - ($sentimentBullishPct ?? 50) }}%
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- MVP portal widgets --}}
        <div class="row g-3 mt-1">
            <div class="col-md-6 col-lg-3">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <h2 class="h6 text-muted mb-2">Today's Live Class</h2>
                        @if($todayLiveClass ?? null)
                            <p class="fw-medium mb-1">{{ $todayLiveClass->title }}</p>
                            <p class="small text-muted mb-2">{{ $todayLiveClass->scheduled_at->format('H:i') }}</p>
                            <a href="{{ $todayLiveClass->meeting_url }}" target="_blank" class="btn btn-primary btn-sm">Join</a>
                            <a href="{{ route('live_classes.index') }}" class="btn btn-link btn-sm">All classes</a>
                        @else
                            <p class="text-muted small mb-2">No class scheduled today.</p>
                            <a href="{{ route('live_classes.index') }}" class="btn btn-outline-primary btn-sm">View schedule</a>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <h2 class="h6 text-muted mb-2">Latest Announcement</h2>
                        @if($latestAnnouncement ?? null)
                            @if($latestAnnouncement->is_pinned)
                                <span class="badge text-bg-primary mb-1">Pinned</span>
                            @endif
                            <p class="fw-medium mb-1">{{ $latestAnnouncement->title }}</p>
                            <p class="small text-muted mb-2">{{ \Illuminate\Support\Str::limit($latestAnnouncement->body, 80) }}</p>
                            <a href="{{ route('announcements.index') }}" class="btn btn-outline-primary btn-sm">View all</a>
                        @else
                            <p class="text-muted small">No announcements yet.</p>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <h2 class="h6 text-muted mb-2">Latest Recorded Lecture</h2>
                        @if($latestLecture ?? null)
                            <p class="fw-medium mb-2">{{ $latestLecture->title }}</p>
                            <a href="{{ route('learning.index') }}" class="btn btn-outline-primary btn-sm">Trading Learning</a>
                        @else
                            <p class="text-muted small">No lectures yet.</p>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <h2 class="h6 text-muted mb-2">Quick Links</h2>
                        <div class="d-flex flex-column gap-2">
                            <a href="{{ route('research.index') }}" class="btn btn-outline-secondary btn-sm text-start">
                                Research Hub
                                @if(($approvedResearchCount ?? 0) > 0)
                                    <span class="badge text-bg-light ms-1">{{ $approvedResearchCount }}</span>
                                @endif
                            </a>
                            <a href="{{ route('calendar.index') }}" class="btn btn-outline-secondary btn-sm text-start">Trading Calendar</a>
                            <a href="{{ route('charts.index') }}" class="btn btn-outline-secondary btn-sm text-start">Charts Repository</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
