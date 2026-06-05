@extends('layouts.app')

@section('title', 'Manage Lectures')

@section('content')
    <div class="container-fluid px-0">
        @include('admin.learning.partials.header', [
            'title' => 'Lectures',
            'section' => 'lectures',
        ])

        <livewire:admin.learning.lecture-table />
        <livewire:admin.learning.lecture-form-modal />
    </div>
@endsection
