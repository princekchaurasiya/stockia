@extends('layouts.app')

@section('title', 'Batch Students')

@section('content')
    <div class="container-fluid px-0">
        @include('admin.learning.partials.header', [
            'title' => 'Students',
            'section' => 'enrollments',
        ])

        <livewire:admin.learning.enrollment-table />
        <livewire:admin.learning.enrollment-form-modal />
    </div>
@endsection
