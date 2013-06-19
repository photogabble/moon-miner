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

    File: bigbang/50.tpl
*}

{extends file="layout.tpl"}
{block name=title}{$langvars['l_cu_step_title']|replace:'[current]':$variables['current_step']|replace:'[total]':$variables['steps']} - {$langvars['l_cu_title']}{/block}
{block name=body}
<form action='bigbang.php' method='post'><div align="center">
<center>
<table border="0" cellpadding="1" width="700" cellspacing="1" bgcolor="#000000">
    <tr>
      <th width="700" colspan="2" bgcolor="#9999cc" align="left"><h1 style="color:#000; height: 0.8em; font-size: 0.8em;font-weight: normal;">{$langvars['l_cu_import_configs_step']}</h1></th>
    </tr>
    {for $i=0 to $variables['language_count']}
    {if $variables['import_lang_results'][$i]['result'] === true}
    <tr title='{$langvars['l_cu_no_errors_found']}'>
    {else}
    <tr title='{$variables['import_lang_results'][$i]['result']}'>
    {/if}
      <td width="600" bgcolor="#ccccff"><font size="1" color="#000000">{$langvars['l_cu_import_langs']|replace:'[language]':$variables['import_lang_results'].$i.name} - {$langvars['l_cu_completed_in']|replace:'[time]':$variables['import_lang_results'].$i.time}</font></td>
      {if $variables['import_lang_results'][$i]['result'] === true}
          <td width="100" align="center" bgcolor="#C0C0C0"><font size="1" color="blue">{$langvars['l_cu_passed']}</font></td>
      {else}
          <td width="100" align="center" bgcolor="#C0C0C0"><font size="1" color="red">{$langvars['l_cu_failed']}</font></td>
      {/if}
    </tr>
    {/for}
    {if $variables['import_config_results']['result'] === true}
    <tr title='{$langvars['l_cu_no_errors_found']}'>
    {else}
    <tr title='{$variables['import_config_results']['result']}'>
    {/if}
      <td width="600" bgcolor="#ccccff"><font size="1" color="#000000">{$langvars['l_cu_import_configs']} - {$langvars['l_cu_completed_in']|replace:'[time]':$variables['import_config_results'].time}</font></td>
      {if $variables['import_config_results']['result'] === true}
          <td width="100" align="center" bgcolor="#C0C0C0"><font size="1" color="blue">{$langvars['l_cu_passed']}</font></td>
      {else}
          <td width="100" align="center" bgcolor="#C0C0C0"><font size="1" color="red">{$langvars['l_cu_failed']}</font></td>
      {/if}
    </tr>
    <tr>
      <th width="700" colspan="2" bgcolor="#9999cc" align="left"><h2 style="color:#000; height: 0.8em; font-size: 0.8em;font-weight: normal;">{$langvars['l_cu_hover_for_more']}</h2></th>
    </tr>
    <tr>
      <td width="100%" colspan="2" bgcolor="#9999cc" align="left"><font color="#000000" size="1"> </font></td>
    </tr>
    <tr>
      <td width="700" colspan="2" bgcolor="#C0C0C0" align="left"><font color="#000000" size="1"><p align='center'><input type=submit value='{$langvars['l_cu_continue']}'></p></font></td>
    </tr>
    <tr>
      <td width="100%" colspan="2" bgcolor="#9999cc" align="left"><font color="#000000" size="1"> </font></td>
    </tr>
    <input type=hidden name=step value={$variables['next_step']}>
    <input type=hidden name=spp value={$variables['spp']}>
    <input type=hidden name=oep value={$variables['oep']}>
    <input type=hidden name=ogp value={$variables['ogp']}>
    <input type=hidden name=gop value={$variables['gop']}>
    <input type=hidden name=enp value={$variables['enp']}>
    <input type=hidden name=initscommod value={$variables['initscommod']}>
    <input type=hidden name=initbcommod value={$variables['initbcommod']}>
    <input type=hidden name=nump value={$variables['nump']}>
    <input type=hidden name=fedsecs value={$variables['fedsecs']}>
    <input type=hidden name=loops value={$variables['loops']}>
    <input type=hidden name=engage value=2>
    <input type=hidden name=swordfish value={$variables['swordfish']}>
  </table>
  </center>
</div><p>
</form>
{/block}
