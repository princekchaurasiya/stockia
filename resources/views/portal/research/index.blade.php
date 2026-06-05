@extends('layouts.app')

@section('title', 'Market Research Hub')

@section('content')
    <div class="container-fluid">
        <x-ui.page-header title="Market Research Hub" />

        @if(in_array(auth()->user()->role ?? '', ['admin', 'superadmin'], true))
            @include('portal.partials.admin-crud-header')
            <livewire:portal.admin-research-moderator />
            @include('portal.partials.student-view-header')
        @endif

        <livewire:portal.student-research-hub />
    </div>
@endsection
