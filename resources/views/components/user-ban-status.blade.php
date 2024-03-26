@props(['user'])
@if($user->isBanned())
    <img class="cursor-help" src="/images/ban_status_banned.png" alt="Player Status: Banned" />
@else
    <img class="cursor-help" src="/images/ban_status_ok.png" alt="Player Status: OK" />
@endif
{{-- Unsure what the difference between banned and locked is... --}}
{{-- <img class="cursor-help" src="/images/ban_status_locked.png" alt="Player Status: Locked" /> --}}
