{{--
    Blacknova Traders, a Free & Opensource (FOSS), web-based 4X space/strategy game.

    @copyright 2024 Simon Dann, Ron Harwood and the BNT development team
    @license GNU AGPL version 3.0 or (at your option) any later version.

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU Affero General Public License as
    published by the Free Software Foundation, either version 3 of the
    License, or (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU Affero General Public License for more details.

    You should have received a copy of the GNU Affero General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.

    File: index.blade.php
--}}

@extends('layouts.layout', ['body_class' => 'index', 'include_ckeditor' => false])

@section('title', __('index.l_welcome_bnt'))

@section('content')

    <x-header />

    <br>
    <h2 style="display:none">{{ __('index.l_navigation') }}</h2>
    <div class="navigation" role="navigation">
        <ul class="navigation">
            <li class="navigation"><a href="{{ route('register') }}"><span class="button blue"><span class="shine"></span>{{ __('index.l_new_player') }}</span></a></li>
            <li class="navigation"><a href="#"><span class="button gray"><span class="shine"></span>{{ __('login.l_login_emailus') }}</span></a></li>
            <li class="navigation"><a href="ranking.php"><span class="button purple"><span class="shine"></span>{{ __('main.l_rankings') }}</span></a></li>
            <li class="navigation"><a href="faq.php"><span class="button brown"><span class="shine"></span>{{ __('main.l_faq') }}</span></a></li>
            <li class="navigation"><a href="settings.php"><span class="button red"><span class="shine"></span>{{ __('main.l_settings') }}</span></a></li>
            <li class="navigation"><a href="{{ setting('game.link_forums') }}"><span class="button orange"><span class="shine"></span>{{ __('main.l_forums') }}</span></a></li>
        </ul></div><br style="clear:both">
    <div><p></p></div>
    <div class="index-welcome">
        <h1 class="index-h1">{{ __('index.l_welcome_bnt') }}</h1>
        <p>{{ __('index.l_bnt_description') }}<br></p>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form class="two-column" method="POST" action="{{ route('login') }}">
            @csrf

            <!-- Email Address -->
            <div>
                <x-input-label for="email" :value="__('login.l_login_email')" />
                <x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" />
            </div>

            <!-- Password -->
            <div class="mt-4">
                <x-input-label for="password" :value="__('login.l_login_pw')" />
                <x-text-input id="password" type="password" name="password" required autocomplete="current-password" />
                <x-input-error :messages="$errors->get('password')" />
            </div>

            <br/>

            <div style="text-align:center">
                <button class="button green">
                    {{ __('login.l_login_title') }}
                </button>
            </div>
        </form>
        <br>
        <p class="cookie-warning">{{ __('index.l_cookie_warning') }}</p></div>
    <br>
@endsection
