@extends('layouts.app')

@section('title', __('stockia.information_link.add_link'))

@section('content')
    <x-ui.page-header :title="__('stockia.information_link.add_link')" />

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.information_links.store') }}">
                @csrf
                <div class="mb-3">
                    <label for="title" class="form-label">{{ __('stockia.information_link.title') }}</label>
                    <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" required>
                    @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label for="url" class="form-label">{{ __('stockia.information_link.url') }}</label>
                    <input type="url" class="form-control @error('url') is-invalid @enderror" id="url" name="url" value="{{ old('url') }}" required>
                    @error('url')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label for="sort_order" class="form-label">{{ __('stockia.information_link.sort_order') }}</label>
                    <input type="number" class="form-control @error('sort_order') is-invalid @enderror" id="sort_order" name="sort_order" value="{{ old('sort_order', 0) }}" min="0" step="1">
                    @error('sort_order')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <div class="form-check">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">{{ __('stockia.information_link.active') }}</label>
                    </div>
                </div>
                @if(!($isGlobal ?? false) && isset($accountId))
                    <input type="hidden" name="account_id" value="{{ $accountId }}">
                @endif
                <button type="submit" class="btn btn-primary">{{ __('stockia.information_link.add_link') }}</button>
                <a href="{{ route('admin.information_links.index') }}" class="btn btn-outline-secondary">{{ __('stockia.information_link.back') }}</a>
            </form>
        </div>
    </div>
@endsection
