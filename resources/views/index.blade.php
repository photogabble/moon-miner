{{--
    Blacknova Traders - A web-based massively multiplayer space combat and trading game
    Copyright (C) 2001-2024 Ron Harwood and the BNT development team.

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

@extends('layouts.layout', ['body_class' => 'index', 'include_ckeditor' => false, 'news' => null, 'suppress_logo' => false, 'footer_show_debug' => true, 'update_ticker' => null, 'players_online' => 1])

@section('title', __('index.l_welcome_bnt'))

@section('content')
    <div class="index-header"><img height="150" width="994" style="width:100%" class="index" src="/images/header1.png" alt="{$langvars['l_bnt']}"></div>

    <div class="index-flags">
        @foreach(\App\Helpers\Languages::listAvailable() as $id => $lang)
            <a href="index.php?lang={{ $id }}"><img width="24" height="16" src="/images/flags/{{ $lang['flag'] }}.png" alt="{{ $lang['name'] }}"></a>
        @endforeach
    </div>

    <div class="index-header-text">{{ __('index.l_bnt') }}</div>
    <br>
    <h2 style="display:none">{{ __('index.l_navigation') }}</h2>
    <div class="navigation" role="navigation">
        <ul class="navigation">
            <li class="navigation"><a href="new.php"><span class="button blue"><span class="shine"></span>{{ __('index.l_new_player') }}</span></a></li>
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
        <form accept-charset="utf-8" action="login2.php" method="post">
            <dl class="twocolumn-form">
                <dt><label for="email">{{ __('login.l_login_email') }}</label></dt>
                <dd><input type="email" id="email" name="email" size="20" maxlength="40" placeholder="someone@example.com"></dd>
                <dt><label for="pass">{{ __('login.l_login_pw') }}:</label></dt>
                <dd><input type="password" id="pass" name="pass" size="20" maxlength="20"></dd>
            </dl>
            <br style="clear:both">
            <div style="text-align:center">{{ __('login.l_login_forgotpw') }}</div><br>
            <div style="text-align:center">
                <span class="button green"><a class="nocolor" href="#" onclick="document.forms[0].submit();return false;"><span class="shine"></span>{{ __('login.l_login_title') }}</a></span>
                <div style="width: 0; height: 0; overflow: hidden;"><input type="submit" value="{{ __('login.l_login_title') }}"></div>
            </div>
        </form>
        <br>
        <p class="cookie-warning">{{ __('index.l_cookie_warning') }}</p></div>
    <br>
@endsection
