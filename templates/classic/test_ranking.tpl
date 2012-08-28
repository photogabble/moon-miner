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

    File: ranking.tpl
*}

{extends file="layout.tpl"}
{block name=title}{$langvars['l_ranks_title']}{/block}
{block name=body_title}<h1>{$langvars['l_ranks_title']}</h1>{/block}

{block name=body}
    <br>
    {$langvars['l_ranks_pnum']}: {$variables['num_players']|number_format}<br>
    {$langvars['l_ranks_dships']}<br>
    <br>

{if isset($players)}
    <!-- Display the Rank Table -->
    <table style="border-collapse:separate; border-spacing:0px; border:none;">
      <tr style="padding:2px; background-color:{$variables['color_header']};">
        <td style="padding:2px;"><strong>{$langvars['l_ranks_rank']}</strong></td>
        <td style="padding:2px;"><strong><a href="{$variables['link']}">{$langvars['l_score']}</a></strong></td>
        <td style="padding:2px;"><strong>{$langvars['l_player']}</strong></td>
        <td style="padding:2px;"><strong><a href="{$variables['link']}?sort=turns">{$langvars['l_turns_used']}</a></strong></td>
        <td style="padding:2px;"><strong><a href="{$variables['link']}?sort=login">{$langvars['l_ranks_lastlog']}</a></strong></td>
        <td style="padding:2px;"><strong><a href="{$variables['link']}?sort=good">{$langvars['l_ranks_good']}</a>/<a href="{$variables['link']}?sort=bad">{$langvars['l_ranks_evil']}</a></strong></td>
        <td style="padding:2px;"><strong><a href="{$variables['link']}?sort=team">{$langvars['l_team_team']}</a></strong></td>
        <td style="padding:2px;"><strong><a href="{$variables['link']}?sort=online">Online</a></strong></td>
        <td style="padding:2px;"><strong><a href="{$variables['link']}?sort=efficiency">Eff. Rating.</a></strong></td>
      </tr>

{* Cycle through the player list *}
{foreach $players as $player}
      <!-- Adding Ranking for player {$player['character_name']} -->
      <tr style="padding:2px; background-color:{cycle values="{$variables['color_line1']},{$variables['color_line2']}"};">
        <td style="padding:2px;">{$player['rank']}</td>
        <td style="padding:2px;">{$player['score']|number_format:0:".":","}</td>

{* Check to see if they are an admin, admins do not have an insignia, and they are diplayed in a blue colour *}
{if isset($player['type']) && $player['type'] == "admin"}
        <td style="padding:2px; color:#0099FF;"><span style="font-weight:bold;">{$player['character_name']}</span></td>
{elseif isset($player['type']) && $player['type'] == "npc"}
        <td style="padding:2px; color:#009900;"><span style="font-weight:bold;">{$player['character_name']}</span></td>
{else}
        <td style="padding:2px;">&nbsp;{$player['insignia']} <span style="font-weight:bold;">{$player['character_name']}</span></td>
{/if}

        <td style="padding:2px;">{$player['turns_used']|number_format:0:".":","}</td>
        <td style="padding:2px;">{$player['last_login']}</td>
        <td style="padding:2px;">&nbsp;&nbsp;{$player['rating']}</td>

{* Check to see if they are an admin, if so diplay in a blue colour *}
{if isset($player['type']) && $player['type'] == "admin"}
        <td style="padding:2px; color:#0099FF;">{$player['team_name']}</td>
{elseif isset($player['type']) && $player['type'] == "npc"}
        <td style="padding:2px; color:#009900;">{$player['team_name']}</td>
{else}
        <td style="padding:2px;">{$player['team_name']}&nbsp;</td>
{/if}

        <td style="padding:2px;">{$player['online']}</td>
        <td style="padding:2px;">{$player['efficiency']|number_format:0:".":","}</td>
      </tr>
{/foreach}
    </table>

{else}
    {$langvars['l_ranks_none']}<br />
{/if}
    <br />

<!-- Display link back (index, main) -->
{* $articleTitle|replace:'Garden':'Vineyard' *}
    {$variables['linkback']['caption']|replace:"[here]":"<a href='{$variables['linkback']['link']}'>{$langvars['l_here']}</a>"}
{/block}
