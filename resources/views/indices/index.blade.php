@extends('layouts.app')

@section('title', 'Indices')

@section('content')
    <h3 class="mb-4">Broad Market Indices</h3>

    <div class="row g-3">
        @foreach($indices as $idx)
            <div class="col-md-6 col-lg-4">
                <div class="card shadow-sm p-3 h-100">
                    <h5 class="mb-2">{{ $idx['name'] }}</h5>
                    <div class="d-flex flex-wrap gap-2">
                        @if($idx['nse_url'] ?? null)
                            <a href="{{ $idx['nse_url'] }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-primary">
                                View on NSE <i class="bi bi-box-arrow-up-right ms-1"></i>
                            </a>
                        @endif
                        <a href="{{ route('indices.show', $idx['slug']) }}" class="btn btn-sm btn-outline-secondary">
                            Uploaded data
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection
