@extends('layouts.app')

@section('title', __('stockia.data_source.edit_link'))

@section('content')
    <x-ui.page-header :title="__('stockia.data_source.edit_link')" />

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.data_source_links.update', $link) }}">
                @csrf
                @method('PUT')
                <div class="row g-2">
                    <div class="col-md-4">
                        <label for="name" class="form-label">{{ __('stockia.data_source.name') }}</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $link->name) }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3">
                        <label for="slug" class="form-label">{{ __('stockia.data_source.slug') }}</label>
                        <input type="text" class="form-control @error('slug') is-invalid @enderror" id="slug" name="slug" value="{{ old('slug', $link->slug) }}" required placeholder="e.g. nifty50">
                        @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-5">
                        <label for="url" class="form-label">{{ __('stockia.data_source.url') }}</label>
                        <input type="url" class="form-control @error('url') is-invalid @enderror" id="url" name="url" value="{{ old('url', $link->url) }}" required>
                        @error('url')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="mt-3">
                    <div class="form-check">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" {{ old('is_active', $link->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">{{ __('stockia.data_source.active') }}</label>
                    </div>
                </div>
                <div class="mt-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">{{ __('stockia.data_source.update') }}</button>
                    <a href="{{ route('admin.data_source_links.index') }}" class="btn btn-outline-secondary">{{ __('stockia.data_source.back') }}</a>
                </div>
            </form>
        </div>
    </div>
@endsection
