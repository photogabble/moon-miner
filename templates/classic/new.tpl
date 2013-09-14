{*
    Blacknova Traders - A web-based massively multiplayer space combat and trading game
    Copyright (C) 2001-2012 Ron Harwood and the BNT development team.

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

    File: new.tpl
*}

{extends file="layout.tpl"}
{block name=title}{$langvars['l_welcome_bnt']}{/block}

{block name=body}
<h1 style='text-align:center'>{$langvars['l_new_title']}</h1>
<form action='new2.php{$variables['link']}' method='post'>
    <dl class='twocolumn-form'>
        <dt style='padding:3px'><label for='username'>{$langvars['l_login_email']}:</label></dt>
        <dd style='padding:3px'><input type='email' id='username' name='username' size='20' maxlength='40' value='' placeholder='someone@example.com' style='width:200px'></dd>
        <dt style='padding:3px'><label for='shipname'>{$langvars['l_new_shipname']}:</label></dt>
        <dd style='padding:3px'><input type='text' id='shipname' name='shipname' size='20' maxlength='20' value='' style='width:200px'></dd>
        <dt style='padding:3px'><label for='character'>{$langvars['l_new_pname']}:</label></dt>
        <dd style='padding:3px'><input type='text' id='character' name='character' size='20' maxlength='20' value='' style='width:200px'></dd>
    </dl>
    <br style='clear:both;'><br>
    <div style='text-align:center'><input type='submit' value='{$langvars['l_submit']}'>&nbsp;<input type='reset' value='{$langvars['l_reset']}'><br><br>
        {$langvars['l_new_info']}<br></div>
</form>
{/block}
