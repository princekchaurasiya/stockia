@extends('layouts.app')

@section('title', 'Uploads')

@section('content')
    <h3 class="mb-4">Uploads</h3>
    <p class="text-muted mb-4">Upload sheets and market reports.</p>

    @livewire('upload-sheet')
    <p class="mt-3 small text-muted">Supported: Excel (.xlsx, .xls) and CSV. For Market Activity, use the upload modal on the Market Activity page.</p>
@endsection
