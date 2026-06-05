@props([
    'title',
    'section' => null,
    'trail' => [],
    'breadcrumbs' => null,
])

@php
    use App\Support\LearningAdminNav;

    $breadcrumbs = $breadcrumbs ?? ($section
        ? LearningAdminNav::breadcrumbs($section, $trail)
        : []);
@endphp

<x-ui.page-header :title="$title" :breadcrumbs="$breadcrumbs" />

@include('admin.learning.partials.subnav')
