@extends('layouts.app')

@section('title', 'Live Classes')

@section('content')
    <div class="container-fluid">
        <x-ui.page-header title="Live Classes" />

        @if(in_array(auth()->user()->role ?? '', ['admin', 'superadmin'], true))
            @include('portal.partials.admin-crud-header')
            <livewire:portal.admin-live-class-manager />
            @include('portal.partials.student-view-header')
        @endif

        <h2 class="h6 mb-3">Upcoming & live</h2>
        <div class="row g-3 mb-4">
            @forelse($upcoming as $class)
                <div class="col-md-6">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h3 class="h6 mb-0">{{ $class->title }}</h3>
                                <span class="badge {{ $class->status === 'live' ? 'text-bg-danger' : 'text-bg-primary' }}">
                                    {{ ucfirst($class->status) }}
                                </span>
                            </div>
                            @if($class->batch)
                                <p class="text-muted small mb-1">Batch: {{ $class->batch->name }}</p>
                            @endif
                            <p class="small mb-2">
                                <i class="bi bi-calendar-event me-1"></i>
                                {{ $class->scheduled_at->format('d M Y, H:i') }}
                                @if($class->duration_minutes)
                                    · {{ $class->duration_minutes }} min
                                @endif
                            </p>
                            @if($class->description)
                                <p class="small text-muted mb-3">{{ $class->description }}</p>
                            @endif
                            <a href="{{ $class->meeting_url }}" target="_blank" rel="noopener" class="btn btn-primary btn-sm">
                                Join meeting
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12"><p class="text-muted">No upcoming live classes.</p></div>
            @endforelse
        </div>

        @if($past->isNotEmpty())
            <h2 class="h6 mb-3">Past classes</h2>
            <div class="list-group">
                @foreach($past as $class)
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <strong>{{ $class->title }}</strong>
                            <span class="text-muted small ms-2">{{ $class->scheduled_at->format('d M Y') }}</span>
                        </div>
                        <span class="badge text-bg-secondary">{{ $class->status }}</span>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection
