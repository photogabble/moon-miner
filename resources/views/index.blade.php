<x-app-layout>
    <x-slot:title>Welcome to Moon Miner</x-slot:title>

    <x-slot:sidebar>
        <div class="flex-grow h-20 flex flex-col">
            <!-- needs an initial height in order to grow to available screen height -->
            <div class="uppercase border-y border-ui-orange-500/50 p-2 mb-4 border-partway-tl" style="--border-part-t-height:25%;">
                <span class="text-white">Welcome To &middot;</span> Space Mines
            </div>

            <div class="space-y-5 px-2 overflow-y-auto">
                <p>
                    Welcome to Space Mines a modernised fork of Black Nova Traders.
                </p>
            </div>
        </div>
    </x-slot:sidebar>

    <x-main-panel class="overflow-hidden flex-grow relative" centered>
        <div class="scanlines w-full h-full z-10"></div>
        <virtual-terminal class="absolute top-2 left-2 z-0 text-xs leading-[0.5]"></virtual-terminal>

        <div class="z-20 border border-ui-orange-500 p-2 border-x-8">
            &rarr;&nbsp;<a href="{{ route('login') }}" class="text-white hover:underline">{{ __('login.l_login_title')}}</a>&nbsp;// <a href="{{ route('register') }}" class="text-white hover:underline">{{ __('index.l_new_player') }}</a> &larr;
        </div>
    </x-main-panel>
</x-app-layout>

{{--@section('content')--}}

{{--    <x-header />--}}

{{--    <br>--}}
{{--    <h2 style="display:none">{{ __('index.l_navigation') }}</h2>--}}
{{--    <div class="navigation" role="navigation">--}}
{{--        <ul class="navigation">--}}
{{--            <li class="navigation"><a href="{{ route('register') }}"><span class="button blue"><span class="shine"></span>{{ __('index.l_new_player') }}</span></a></li>--}}
{{--            <li class="navigation"><a href="#"><span class="button gray"><span class="shine"></span>{{ __('login.l_login_emailus') }}</span></a></li>--}}
{{--            <li class="navigation"><a href="ranking.php"><span class="button purple"><span class="shine"></span>{{ __('main.l_rankings') }}</span></a></li>--}}
{{--            <li class="navigation"><a href="faq.php"><span class="button brown"><span class="shine"></span>{{ __('main.l_faq') }}</span></a></li>--}}
{{--            <li class="navigation"><a href="settings.php"><span class="button red"><span class="shine"></span>{{ __('main.l_settings') }}</span></a></li>--}}
{{--            <li class="navigation"><a href="{{ setting('game.link_forums') }}"><span class="button orange"><span class="shine"></span>{{ __('main.l_forums') }}</span></a></li>--}}
{{--        </ul></div><br style="clear:both">--}}
{{--    <div><p></p></div>--}}
{{--    <div class="index-welcome">--}}
{{--        <h1 class="index-h1">{{ __('index.l_welcome_bnt') }}</h1>--}}
{{--        <p>{{ __('index.l_bnt_description') }}<br></p>--}}

{{--        <!-- Session Status -->--}}
{{--        <x-auth-session-status class="mb-4" :status="session('status')" />--}}

{{--        <form class="two-column" method="POST" action="{{ route('login') }}">--}}
{{--            @csrf--}}

{{--            <!-- Email Address -->--}}
{{--            <div>--}}
{{--                <x-input-label for="email" :value="__('login.l_login_email')" />--}}
{{--                <x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />--}}
{{--                <x-input-error :messages="$errors->get('email')" />--}}
{{--            </div>--}}

{{--            <!-- Password -->--}}
{{--            <div class="mt-4">--}}
{{--                <x-input-label for="password" :value="__('login.l_login_pw')" />--}}
{{--                <x-text-input id="password" type="password" name="password" required autocomplete="current-password" />--}}
{{--                <x-input-error :messages="$errors->get('password')" />--}}
{{--            </div>--}}

{{--            <br/>--}}

{{--            <div style="text-align:center">--}}
{{--                <button class="button green">--}}
{{--                    {{ __('login.l_login_title') }}--}}
{{--                </button>--}}
{{--            </div>--}}
{{--        </form>--}}
{{--        <br>--}}
{{--        <p class="cookie-warning">{{ __('index.l_cookie_warning') }}</p></div>--}}
{{--    <br>--}}
{{--@endsection--}}
