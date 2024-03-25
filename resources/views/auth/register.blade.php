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
--}}

@extends('layouts.layout', ['body_class' => 'index', 'include_ckeditor' => false])

@section('title', __('index.l_welcome_bnt'))

@section('content')

    <x-header />

    <br>
    <div class="index-welcome">
        <h1 style='text-align:center'>{{ __('new.l_new_title') }}</h1>
        <form class="two-column" method="post">
            @csrf

            <div>
                <x-input-label for="email" :value="__('login.l_login_email')" />
                <x-text-input id="email" type="email" name="email" placeholder='someone@example.com' :value="old('email')" required autofocus autocomplete="email" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="ship_name" :value="__('new.l_new_shipname')" />
                <x-text-input id="ship_name" type="text" name="ship_name" :value="old('ship_name')" required />
                <x-input-error :messages="$errors->get('ship_name')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="character_name" :value="__('new.l_new_pname')" />
                <x-text-input id="character_name" type="text" name="character_name" :value="old('character_name')" required />
                <x-input-error :messages="$errors->get('character_name')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="password" :value="__('login.l_login_pw')" />
                <x-text-input id="password" type="password" name="password" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="password_confirmation" :value="__('login.l_login_pw_confirm')" />
                <x-text-input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="locale" :value="__('options.l_opt_lang')" />
                <select id="locale" name="locale">
                    @foreach(\App\Helpers\Languages::listAvailable() as $id => $lang)
                        <option value='{{ $id }}' @if(!old('locale') && app()->getLocale() === $id || old('locale') === $id) selected @endif>{{ $lang['name'] }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('locale')" class="mt-2" />
            </div>

            <br style="clear:both">
            <div style="text-align:center">
                <button class="button green"><span class="shine"></span>{{ __('common.l_submit') }}</button>
                <button type="reset" class="button red"><span class="shine"></span>{{ __('common.l_reset') }}</button>
            </div>
        </form>
        <br>
        {{ __('new.l_new_info') }}<br></div>
    <br>
@endsection

