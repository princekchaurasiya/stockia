@extends('layouts.app')

@section('title', __('stockia.account.admins'))

@section('content')
    <x-ui.page-header :title="__('stockia.account.admins')" />

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <p class="mb-3">
        <a href="{{ route('admin.admins.create') }}" class="btn btn-primary">{{ __('stockia.account.admins') }} &rarr; Create</a>
    </p>

    <div class="card shadow-sm">
        <div class="card-body">
            <livewire:tables.admin-users-table />
        </div>
    </div>
@endsection
