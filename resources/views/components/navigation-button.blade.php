@props(['disabled' => false, 'title'])

<button class="nav-link aspect-square w-12 h-12" aria-label="{{ $title }}" {{ $disabled === true ? 'disabled' : '' }}>
    <span>{{ $slot }}</span>
</button>
