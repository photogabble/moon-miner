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

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <button type="submit">
                {{ __('Email Password Reset Link') }}
            </button>
        </div>
    </form>

@endsection
