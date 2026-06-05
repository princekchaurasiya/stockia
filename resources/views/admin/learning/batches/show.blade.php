@extends('layouts.app')

@section('title', $batch->name)

@section('content')
    <div class="container-fluid px-0">
        @include('admin.learning.partials.header', [
            'title' => $batch->name,
            'section' => 'batches',
            'trail' => [$batch->name => null],
        ])

        <livewire:admin.learning.batch-detail :batch="$batch" />
        <livewire:admin.learning.batch-form-modal />
    </div>
@endsection
