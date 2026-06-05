@props([
    'items' => [],
])

    @if ($items !== [])
    <nav aria-label="breadcrumb" {{ $attributes->merge(['class' => 'stockia-breadcrumb-wrap']) }}>
        <ol class="breadcrumb stockia-breadcrumb mb-0">
            @foreach ($items as $label => $url)
                @if ($url)
                    <li class="breadcrumb-item"><a href="{{ $url }}">{{ $label }}</a></li>
                @else
                    <li class="breadcrumb-item active" aria-current="page">{{ $label }}</li>
                @endif
            @endforeach
        </ol>
    </nav>
@endif
