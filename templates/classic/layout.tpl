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

    File: layout.tpl
*}
<!DOCTYPE html>
<html lang="{$langvars['l_lang_attribute']}">
  <head>
    <meta charset="utf-8">
    <meta name="Description" content="A free online game - Open source, web game, with multiplayer space exploration">
    <meta name="Keywords" content="Free, online, game, Open source, web game, multiplayer, space, exploration, blacknova, traders">
    <meta name="Rating" content="General">
    <link rel="shortcut icon" href="images/bntfavicon.ico">
    <title>{block name=title}Default Page Title{/block}</title>
    <link rel="stylesheet" type="text/css" href="{$template_dir}/styles/main.css">
    <link href="http://fonts.googleapis.com/css?family=Ubuntu" rel="stylesheet" type="text/css">
  </head>

{if !isset($variables['display_newsticker'])}
{$variables['body_class'] = "bnt"}
{/if}
  <body class="{$variables['body_class']}">

<!-- START OF BODY -->
{block name=body_title}{/block}
{block name=body}{/block}
<!-- END OF BODY -->

<!-- START OF FOOTER -->
{if isset($variables['display_newsticker']) && $variables['display_newsticker'] == true}
    <div style="width:600px; margin:auto; text-align:center;">[NEWS TICKER GOES HERE]</div>
{/if}

{* Handle the Servers Update Ticker here *}
{if isset($variables['update_ticker']['display']) && $variables['update_ticker']['display'] == true}
    <script type='text/javascript' src='{$template_dir}/scripts/updateticker.js'></script>
    <script>
        var seconds = {$variables['update_ticker']['seconds_left']};
        var nextInterval = new Date().getTime();
	    var maxTicks = ({$variables['update_ticker']['sched_ticks']} * 60);
	    var l_running_update = '{$langvars['l_running_update']}';
	    var l_footer_until_update = '{$langvars['l_footer_until_update']}';

        setTimeout("NextUpdate();", 100);
    </script>
    <div style="width:600px; margin:auto; text-align:center;"><strong><span id=update_ticker>{$langvars['l_please_wait']}</span></strong></div>
{/if}
{* End of Servers Update Ticker *}

    <div style='clear:both'></div>
    <div style="text-align:center">
      <div style="width:600px; margin:auto; text-align:center;">
{* Handle the Online Players Counter *}
{if isset($variables['players_online']) && $variables['players_online'] == 1}
{$langvars['l_footer_one_player_on']}
{else}
{$langvars['l_footer_players_on_1']} {$variables['players_online']} {$langvars['l_footer_players_on_2']}
{/if}
{* End of Online Players Counter *}
	  </div>
    </div>

    <div style='position:absolute; float:left; text-align:left'><a href='http://www.sourceforge.net/projects/blacknova'><img style="border:none;" src="http://sflogo.sourceforge.net/sflogo.php?group_id=14248&amp;type={$variables['sf_logo_type']}" alt="Blacknova Traders at SourceForge.net"></a></div>
    <div style="font-size:smaller; text-align:right"><a class="new_link" href="news.php">{$langvars['l_local_news']}</a></div>
    <div style='font-size:smaller; text-align:right'>&copy;2000-2012 Ron Harwood &amp; the BNT Dev team</div>

{if isset($variables['footer_show_debug']) && $variables['footer_show_debug'] == true}
    <div style="font-size:smaller; text-align:right">{10|number_format:2} {$langvars['l_seconds']} {$langvars['l_time_gen_page']} / {$variables['mem_peak_usage']} {$langvars['l_peak_mem']}</div>
{/if}
<!-- END OF FOOTER -->

  </body>
</html>
