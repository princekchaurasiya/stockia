@extends('layouts.app')

@section('title', __('stockia.information_link.section_title'))

@section('content')
    <x-ui.page-header :title="__('stockia.information_link.section_title')" />

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <p class="mb-3">
        <a href="{{ route('admin.information_links.create') }}" class="btn btn-primary">{{ __('stockia.information_link.add_link') }}</a>
    </p>

    <div class="card shadow-sm">
        <div class="card-header">{{ __('stockia.information_link.existing_links') }}</div>
        <div class="card-body">
            <livewire:tables.information-links-table />
        </div>
    </div>
@endsection
