@extends('layouts.app')

@section('title', 'Charts Repository')

@section('content')
    <div class="container-fluid">
        <x-ui.page-header title="Charts Repository">
            @unless(in_array(auth()->user()->role ?? '', ['admin', 'superadmin'], true))
                <x-slot:actions>
                    <form method="GET" class="d-flex gap-2">
                        <select name="category" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="">All categories</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat }}" @selected(request('category') === $cat)>{{ $cat }}</option>
                            @endforeach
                        </select>
                    </form>
                </x-slot:actions>
            @endunless
        </x-ui.page-header>

        @if(in_array(auth()->user()->role ?? '', ['admin', 'superadmin'], true))
            @include('portal.partials.admin-crud-header')
            <livewire:portal.admin-chart-manager />
            @include('portal.partials.student-view-header')
        @endif

        <div class="row g-3">
            @forelse($charts as $chart)
                <div class="col-md-4 col-lg-3">
                    <div class="card h-100 shadow-sm border-0">
                        @if(in_array($chart->file_type, ['png', 'jpg', 'jpeg', 'webp']))
                            <img src="{{ asset('storage/'.$chart->file_path) }}" class="card-img-top" alt="{{ $chart->title }}" style="height:160px;object-fit:cover;">
                        @else
                            <div class="bg-light d-flex align-items-center justify-content-center" style="height:160px;">
                                <i class="bi bi-file-pdf fs-1 text-danger"></i>
                            </div>
                        @endif
                        <div class="card-body">
                            <h2 class="h6">{{ $chart->title }}</h2>
                            <p class="small text-muted mb-2">{{ $chart->category }} · {{ $chart->report_date?->format('d M Y') ?? '—' }}</p>
                            <a href="{{ asset('storage/'.$chart->file_path) }}" target="_blank" class="btn btn-outline-primary btn-sm">Download</a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12"><p class="text-muted">No charts available yet.</p></div>
            @endforelse
        </div>

        <div class="mt-3">{{ $charts->withQueryString()->links() }}</div>
    </div>
@endsection
