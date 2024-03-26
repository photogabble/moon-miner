@props(['centered' => false])

<div {{ $attributes->merge(['class' => ('h-20 flex-grow border mt-1 border-ui-orange-500/50' . ($centered ? ' flex flex-row items-center justify-center' : ''))]) }}>
    {{ $slot }}
</div>
