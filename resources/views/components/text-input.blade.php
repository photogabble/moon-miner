@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'border-orange-500 bg-gray-900 focus:border-orange-500 focus:ring-orange-500 rounded-sm shadow-sm w-full']) !!}>
