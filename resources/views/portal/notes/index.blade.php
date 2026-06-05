@extends('layouts.app')

@section('title', 'My Notes')

@section('content')
    <div class="container-fluid">
        <x-ui.page-header title="My Notes" />
        <livewire:portal.notes-manager />
    </div>
@endsection
