<div class="flex justify-between border-b border-b-ui-orange-500/50 py-1 px-2 bg-ui-grey-900">
    <div>{{ $slot }}</div>
    @if (isset($actions) && $actions->hasActualContent())
    <div class="flex items-center text-sm gap-1">
        {{ $actions }}
    </div>
    @endif
</div>
