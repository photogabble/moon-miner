<div class="bg-slate-700/10 w-16 p-2 border-partway-y flex flex-col" style="--border-part-b-height: 12px; --border-part-t-height: 12px;">
    <div class="flex flex-col space-y-2 mb-2 flex-grow">
        <div class="space-y-1 text-2xl">
            <x-navigation-link :href="Auth::check() ? route('dashboard') : route('home')" :active="request()->routeIs('dashboard', 'home')" title="Overview"><x-icons.dashboard/></x-navigation-link>
            <x-navigation-link href="/explore" :disabled="!Auth::check()" :active="request()->routeIs('explore')" title="Explore universe"><x-icons.explore/></x-navigation-link>
            <x-navigation-link href="/research" :disabled="!Auth::check()" :active="request()->routeIs('research')" title="Research and Development"><x-icons.research/></x-navigation-link>
            <x-navigation-link href="/harvesting" :disabled="!Auth::check()" :active="request()->routeIs('harvesting')" title="Manage Resource Harvesting"><x-icons.harvest/></x-navigation-link>
            <x-navigation-link href="/manufacturing" :disabled="!Auth::check()" :active="request()->routeIs('manufacturing')" title="Manage Manufacturing"><x-icons.manufacturing/></x-navigation-link>
        </div>

        <hr class="border-ui-orange-500"/>

        <div class="space-y-1 text-2xl">
            <x-navigation-link href="/market" :disabled="!Auth::check()" :active="request()->routeIs('market')" title="Buy/Sell in the Marketplace"><x-icons.market/></x-navigation-link>
            <x-navigation-link href="/ranking" :active="request()->routeIs('ranking')" title="View Player Rankings"><x-icons.ranking/></x-navigation-link>
        </div>

        <!-- Decoration -->
        <div aria-hidden="true" class="decoration flex-grow border-partway-y p-1 relative text-xs overflow-hidden min-h-[100px]" style="--border-part-b-height: 12px; --border-part-t-height: 12px;">
            <small class="block right-1 top-2 absolute h-[240px]" style="writing-mode: vertical-rl;">X000.69 //////////////////////////////...</small>
            <small class="animate-pulse block absolute bottom-0 left-1">&middot;&middot;&middot;</small>
        </div>
        <!-- ./ decoration -->

        <div class="space-y-1 text-2xl">
            <x-navigation-link href="/profile" :disabled="!Auth::check()" :active="request()->routeIs('profile')" title="View and modify your profile"><x-icons.user-profile/></x-navigation-link>
            <x-navigation-link href="/help" title="How to play" :active="request()->routeIs('help')"><x-icons.help/></x-navigation-link>
            <form action="{{ route('logout') }}" method="post">
                @csrf
                <x-navigation-button :disabled="!Auth::check()" title="Logout"><x-icons.logout/></x-navigation-button>
            </form>
        </div>
    </div>
</div>
