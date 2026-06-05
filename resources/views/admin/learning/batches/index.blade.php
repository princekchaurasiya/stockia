@extends('layouts.app')

@section('title', 'Manage Batches')

@section('content')
    <div class="container-fluid px-0">
        @include('admin.learning.partials.header', [
            'title' => 'Batches',
            'section' => 'batches',
        ])

        <livewire:admin.learning.batch-table />
        <livewire:admin.learning.batch-form-modal />
    </div>
@endsection
