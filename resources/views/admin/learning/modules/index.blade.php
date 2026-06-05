@extends('layouts.app')

@section('title', 'Manage Modules')

@section('content')
    <div class="container-fluid px-0">
        @include('admin.learning.partials.header', [
            'title' => 'Modules',
            'section' => 'modules',
        ])

        <livewire:admin.learning.module-table />
        <livewire:admin.learning.module-form-modal />
    </div>
@endsection
