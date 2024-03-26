@props([
    'direction' => \App\Types\Sorting::DESC,
])

@if($direction === \App\Types\Sorting::DESC)
<span>▼</span>
@else
<span>▲</span>
@endif
