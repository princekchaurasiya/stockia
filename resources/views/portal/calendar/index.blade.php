@extends('layouts.app')

@section('title', 'Trading Calendar')

@section('content')
    <div class="container-fluid">
        <x-ui.page-header title="Trading Calendar" />

        @if(in_array(auth()->user()->role ?? '', ['admin', 'superadmin'], true))
            @include('portal.partials.admin-crud-header')
            <livewire:portal.admin-calendar-manager />
            @include('portal.partials.student-view-header')
        @endif

        @forelse($events as $month => $monthEvents)
            <h2 class="h6 text-muted mb-3">{{ \Carbon\Carbon::parse($month.'-01')->format('F Y') }}</h2>
            <div class="list-group mb-4">
                @foreach($monthEvents as $event)
                    <div class="list-group-item">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <strong>{{ $event->title }}</strong>
                                <span class="text-muted small ms-2">{{ $event->event_date->format('d M') }}</span>
                                @if($event->description)
                                    <p class="small text-muted mb-0 mt-1">{{ $event->description }}</p>
                                @endif
                            </div>
                            <span class="badge text-bg-light">{{ str_replace('_', ' ', $event->event_type) }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        @empty
            <p class="text-muted">No calendar events scheduled.</p>
        @endforelse
    </div>
@endsection
