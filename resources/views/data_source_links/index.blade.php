@extends('layouts.app')

@section('title', __('stockia.data_source.title'))

@section('content')
    <x-ui.page-header :title="__('stockia.data_source.title')" />

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card shadow-sm mb-4">
        <div class="card-header">{{ __('stockia.data_source.add_link') }}</div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.data_source_links.store') }}">
                @csrf
                <div class="row g-2">
                    <div class="col-md-4">
                        <label for="name" class="form-label">{{ __('stockia.data_source.name') }}</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3">
                        <label for="slug" class="form-label">{{ __('stockia.data_source.slug') }}</label>
                        <input type="text" class="form-control @error('slug') is-invalid @enderror" id="slug" name="slug" value="{{ old('slug') }}" required placeholder="e.g. nifty50">
                        @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-5">
                        <label for="url" class="form-label">{{ __('stockia.data_source.url') }}</label>
                        <input type="url" class="form-control @error('url') is-invalid @enderror" id="url" name="url" value="{{ old('url') }}" required>
                        @error('url')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <button type="submit" class="btn btn-primary mt-2">{{ __('stockia.data_source.add_link') }}</button>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header">{{ __('stockia.data_source.existing_links') }}</div>
        <div class="card-body">
            <livewire:tables.data-source-links-table />
        </div>
    </div>
@endsection
