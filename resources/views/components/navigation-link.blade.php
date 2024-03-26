@props(['disabled' => false, 'active' => false, 'title', 'href'])

@if($disabled === true)
    <button class="nav-link aspect-square w-12 h-12" aria-label="{{ $title }}" disabled>
        <span>{{ $slot }}</span>
    </button>
@else
    <a href="{{ $href }}" class="nav-link aspect-square w-12 h-12 {{ $active ? 'active' : '' }}" aria-label="{{ $title }}">
        <span>{{ $slot }}</span>
    </a>
@endif
