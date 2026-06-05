@extends('layouts.app')

@section('title', 'Learning Admin Dashboard')

@push('styles')
<style>
    .learning-stat-card {
        transition: transform 0.15s ease, box-shadow 0.15s ease, border-color 0.15s ease;
    }
    .learning-stat-card-link:hover .learning-stat-card {
        transform: translateY(-2px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.08) !important;
        border-color: rgba(13, 110, 253, 0.25) !important;
    }
</style>
@endpush

@section('content')
    <div class="container-fluid px-0">
        @include('admin.learning.partials.header', [
            'title' => 'Learning Admin',
            'section' => 'dashboard',
        ])

        <livewire:admin.learning.stats-overview />
    </div>
@endsection
