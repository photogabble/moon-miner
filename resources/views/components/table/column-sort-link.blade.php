@props([
    'direction' => \App\Types\Sorting::DESC,
    'isSorting' => false,
    'title',
    'href'
])

<a href="{{ $href }}" class="{{ $isSorting ? 'text-ui-yellow' : 'hover:text-ui-yellow' }}">
    {{ $slot }} @if($isSorting)<x-up-down-indicator :direction="$direction" />@endif
</a>
