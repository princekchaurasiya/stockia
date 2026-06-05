@php
    use App\Support\LearningAdminNav;

    $sections = LearningAdminNav::sections();
@endphp

<nav class="learning-admin-subnav mb-4" aria-label="Learning admin sections">
    <div class="learning-admin-subnav-scroll">
        @foreach ($sections as $key => $section)
            <a href="{{ route($section['route']) }}"
               class="learning-admin-subnav-link {{ request()->routeIs($section['pattern']) ? 'active' : '' }}">
                {{ $section['label'] }}
            </a>
        @endforeach
    </div>
</nav>
