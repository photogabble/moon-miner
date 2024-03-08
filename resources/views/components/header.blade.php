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

    File: header.blade.php
--}}

<div class="index-header"><img height="150" width="994" style="width:100%" class="index" src="/images/header1.png" alt="{$langvars['l_bnt']}"></div>

<div class="index-flags">
    @foreach(\App\Helpers\Languages::listAvailable() as $id => $lang)
        <a href="?lang={{ $id }}"><img width="24" height="16" src="/images/flags/{{ $lang['flag'] }}.png" alt="{{ $lang['name'] }}"></a>
    @endforeach
</div>

<div class="index-header-text">{{ __('index.l_bnt') }}</div>
