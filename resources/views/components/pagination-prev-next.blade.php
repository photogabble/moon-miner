@if ($paginator->hasPages())
    <span class="mr-2">Page {{ $paginator->currentPage() }}/{{ $paginator->lastPage() }}</span>
    @if($paginator->lastPage() > 1)
        <nav class="space-x-2">
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    {{$element}}
                @endif
                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        <a href="{{ $url }}" class="{{ $page == $paginator->currentPage() ? 'text-ui-yellow' : 'hover:text-ui-yellow'  }}">{{ $page }}</a>
                    @endforeach
                @endif
            @endforeach
        </nav>
    @endif
@endif
