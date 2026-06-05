@props([
    'title',
    'breadcrumbs' => [],
])

<div {{ $attributes->merge(['class' => 'stockia-page-header']) }}>
    @if ($breadcrumbs !== [])
        <x-ui.breadcrumb :items="$breadcrumbs" class="mb-2" />
    @endif

    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
        <div>
            <x-ui.page-title>{{ $title }}</x-ui.page-title>

            @if (isset($meta))
                <p class="stockia-page-meta mb-0">{{ $meta }}</p>
            @endif
        </div>

        @if (isset($actions))
            <div class="d-flex flex-wrap align-items-center gap-2">{{ $actions }}</div>
        @endif
    </div>
</div>
