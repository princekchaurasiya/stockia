@extends('layouts.app')

@section('title', 'Announcements')

@section('content')
    <div class="container-fluid">
        <x-ui.page-header title="Announcements" />

        @if(in_array(auth()->user()->role ?? '', ['admin', 'superadmin'], true))
            @include('portal.partials.admin-crud-header')
            <livewire:portal.admin-announcement-manager />
            @include('portal.partials.student-view-header')
        @endif

        <div class="row g-3">
            @forelse($announcements as $announcement)
                <div class="col-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h2 class="h6 mb-0">{{ $announcement->title }}</h2>
                                <div class="d-flex gap-2">
                                    @if($announcement->is_pinned)
                                        <span class="badge text-bg-primary">Pinned</span>
                                    @endif
                                    <span class="badge text-bg-light">{{ str_replace('_', ' ', $announcement->type) }}</span>
                                </div>
                            </div>
                            <p class="text-muted small mb-2">{{ $announcement->published_at?->format('d M Y, H:i') }}</p>
                            <p class="mb-0">{!! nl2br(e($announcement->body)) !!}</p>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <p class="text-muted">No announcements yet.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-3">{{ $announcements->links() }}</div>
    </div>
@endsection
