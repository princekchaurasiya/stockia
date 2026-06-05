@extends('layouts.app')

@section('title', 'Manage Documents')

@section('content')
    <div class="container-fluid px-0">
        @include('admin.learning.partials.header', [
            'title' => 'Documents',
            'section' => 'documents',
        ])

        <livewire:admin.learning.document-table />
        <livewire:admin.learning.document-form-modal />
    </div>
@endsection
