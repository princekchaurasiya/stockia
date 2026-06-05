@extends('layouts.app')

@section('title', 'Manage Videos')

@section('content')
    <div class="container-fluid px-0">
        @include('admin.learning.partials.header', [
            'title' => 'Videos',
            'section' => 'videos',
        ])

        <livewire:admin.learning.video-table />
        <livewire:admin.learning.video-form-modal />
    </div>
@endsection
