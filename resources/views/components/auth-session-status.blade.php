@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'flash-status']) }}>
        {{ $status }}
    </div>
@endif
