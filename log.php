<?php
// Blacknova Traders - A web-based massively multiplayer space combat and trading game
// Copyright (C) 2001-2012 Ron Harwood and the BNT development team
//
//  This program is free software: you can redistribute it and/or modify
//  it under the terms of the GNU Affero General Public License as
//  published by the Free Software Foundation, either version 3 of the
//  License, or (at your option) any later version.
//
//  This program is distributed in the hope that it will be useful,
//  but WITHOUT ANY WARRANTY; without even the implied warranty of
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//  GNU Affero General Public License for more details.
//
//  You should have received a copy of the GNU Affero General Public License
//  along with this program.  If not, see <http://www.gnu.org/licenses/>.
//
// File: log.php

include "config.php";
updatecookie();
include "languages/$lang";
$title = $l_log_titlet;
$no_body = 1;

if (checklogin())
{
    die();
}

include "header.php";

$res = $db->Execute("SELECT character_name, ship_id, dhtml FROM $dbtables[ships] WHERE email='$username'");
$playerinfo = $res->fields;

if (!isset($_GET['swordfish']))
{
    $_GET['swordfish'] = '';
}

$swordfish = $_GET['swordfish'];

if ($swordfish == $adminpass) // Check if called by admin script
{
    $playerinfo[ship_id] = $player;

  if ($player == 0)
    $playerinfo[character_name] = 'Administrator';
  else
  {
    $res = $db->Execute("SELECT character_name FROM $dbtables[ships] WHERE ship_id=$player");
    $targetname = $res->fields;
    $playerinfo[character_name] = $targetname[character_name];
  }
}

$mode = 'compat';

$yres = 558;

if ($mode == 'full')
  echo "#divScroller1 {position:relative; overflow:hidden; overflow-y:scroll; z-index:9; left:0px; top:0px; width:100%; height:{$yres}px; visbility:visible; border-width:1px 1px 1px 1px; border-color:#C6D6E7; border-style:solid; scrollbar-track-color: #DEDEEF; scrollbar-face-color:#040658; scrollbar-arrow-color:#DEDEEF}";
elseif ($mode == 'moz')
  echo "#divScroller1 {position:relative; overflow:visible; overflow-y:scroll; z-index:9; left:0px; top:0px; width:100%; height:{$yres}px; visbility:visible; scrollbar-track-color: #DEDEEF; scrollbar-face-color:#040658; scrollbar-arrow-color:#DEDEEF}";

echo '<body background="images/bgoutspace1.png" bgcolor=#000 text="#c0c0c0" link="#040658" vlink="#040658" alink="#040658">';

echo '<center>';

echo "<table width=80% border=0 cellspacing=0 cellpadding=0>";

$logline = str_replace("[player]", "$playerinfo[character_name]", $l_log_log);
?>

<tr><td><td width=100%><td></tr>
<tr><td><td align='left' height=20 style="background-image: url(images/top_panel.png); background-repeat:no-repeat">
<font size=2 color=#040658><b>&nbsp;&nbsp;&nbsp;<?php echo $logline; ?></b></font>
</td><td><td></tr>
<tr><td valign=bottom>

<?php
if ($mode == 'moz')
  echo '<td colspan=2 style="border-width:1px 1px 1px 1px; border-color:#C6D6E7; border-style:solid;" bgcolor=#63639C>';
elseif ($mode == 'full')
  echo '<td colspan=2 bgcolor=#63639C>';
else
  echo "<td colspan=2><table border=1 width=100%><tr><td  bgcolor=#63639C>";

if (empty($startdate))
  $startdate = date("Y-m-d");


$res = $db->Execute("SELECT * FROM $dbtables[logs] WHERE ship_id=$playerinfo[ship_id] AND time LIKE '$startdate%' ORDER BY time DESC, type DESC");
//echo "SELECT * FROM $dbtables[logs] WHERE ship_id=$playerinfo[ship_id] AND time LIKE '$startdate%' ORDER BY time DESC, type DESC";
while (!$res->EOF)
{
  $logs[] = $res->fields;
  $res->MoveNext();
}

$log_months_temp = "l_log_months_" . (substr($startdate, 5, 2) - 1);
$entry = $$l_log_months_temp . " " . substr($startdate, 8, 2) . " " . substr($startdate, 0, 4);

echo "<div id=\"divScroller1\">" .
     "\n<div id=\"dynPage0\" class=\"dynPage\">" .
     "<center>" .
     "<br>" .
     "<font size=2 color=#DEDEEF><b>$l_log_start $entry<b></font>" .
     "<p>" .
     "<hr width=80% size=1 NOSHADE style=\"color: #040658\">" .
     "</center>\n";

if (!empty($logs))
{
  foreach ($logs as $log)
  {
    $event = log_parse($log);
    $log_months_temp = "l_log_months_" . (substr($log['time'], 5, 2) - 1);
    $time = $$l_log_months_temp . " " . substr($log['time'], 8, 2) . " " . substr($log['time'], 0, 4) . " " . substr($log['time'], 11);

    echo "<table border=0 cellspacing=5 width=100%>\n" .
         "  <tr>\n" .
         "    <td style='text-align:left; font-size:12px; color:#040658; font-weight:bold;'>{$event['title']}</td>\n" .
         "    <td style='text-align:right; font-size:12px; color:#040658; font-weight:bold;'>{$time}</td>\n" .
         "  </tr>\n" .
         "  <tr>\n".
         "    <td colspan=2 style='text-align:left; font-size:12px; color:#DEDEEF;'>{$event['text']}</td>\n".
         "  </tr>\n" .
         "</table>\n" .
         "<center><hr width='80%' size='1' NOSHADE style='color: #040658;'></center>\n";
  }
}

echo "<center>" .
     "<br>" .
     "<font size=2 color=#DEDEEF><b>$l_log_end $entry<b></font>" .
     "<p>" .
     "</center>" .
     "</div>\n";

$month = substr($startdate, 5, 2);
$day = substr($startdate, 8, 2) - 1;
$year = substr($startdate, 0, 4);

$yesterday = mktime (0,0,0,$month,$day,$year);
$yesterday = date("Y-m-d", $yesterday);

$day = substr($startdate, 8, 2) - 2;

$yesterday2 = mktime (0,0,0,$month,$day,$year);
$yesterday2 = date("Y-m-d", $yesterday2);

if ($mode == 'compat')
  echo "</td></tr></table>";

if ($mode != 'compat')
{
  $log_months_temp = "l_log_months_" . (substr($yesterday, 5, 2) - 1);
  $entry = $$l_log_months_temp . " " . substr($yesterday, 8, 2) . " " . substr($yesterday, 0, 4);

  unset($logs);
  $res = $db->Execute("SELECT * FROM $dbtables[logs] WHERE ship_id=$playerinfo[ship_id] AND time LIKE '$yesterday%' ORDER BY time DESC, type DESC");
  while (!$res->EOF)
  {
    $logs[] = $res->fields;
    $res->MoveNext();
  }

  echo "<div id=\"dynPage1\" class=\"dynPage\">" .
       "<center>" .
       "<br>" .
       "<font size=2 color=#DEDEEF><b>$l_log_start $entry<b></font>" .
       "<p>" .
       "</center>" .
       "<hr width=80% size=1 NOSHADE style=\"color: #040658\">";

  if (!empty($logs))
  {
    foreach ($logs as $log)
    {
      $event = log_parse($log);
      $log_months_temp = "l_log_months_" . (substr($log['time'], 5, 2) - 1);
      $time = $$l_log_months_temp . " " . substr($log['time'], 8, 2) . " " . substr($log['time'], 0, 4) . " " . substr($log['time'], 11);

      echo "<table border=0 cellspacing=5 width=100%>\n" .
           "  <tr>\n" .
           "    <td align='left'><font size='2' color='#040658'><b>{$event['title']}</b></td>\n" .
           "    <td align='right'><font size='2' color='#040658'><b>{$time}</b></td>\n" .
           "  <tr><td colspan='2' align='left'><font size='2' color='#DEDEEF'>{$event['text']}</td></tr>\n" .
           "</table>\n" .
           "<hr width='80%' size='1' NOSHADE style='color: #040658;'>\n";
    }
  }

  echo "<center>" .
       "<br>" .
       "<font size=2 color=#DEDEEF><b>$l_log_end $entry<b></font>" .
       "<p>" .
       "</center>" .
       "</div>\n";

  $log_months_temp = "l_log_months_" . (substr($yesterday2, 5, 2) - 1);
  $entry = $$l_log_months_temp . " " . substr($yesterday2, 8, 2) . " " . substr($yesterday2, 0, 4);

  unset($logs);
  $res = $db->Execute("SELECT * FROM $dbtables[logs] WHERE ship_id=$playerinfo[ship_id] AND time LIKE '$yesterday2%' ORDER BY time DESC, type DESC");
  while (!$res->EOF)
  {
    $logs[] = $res->fields;
    $res->MoveNext();
  }

  echo "<div id=\"dynPage2\" class=\"dynPage\">" .
       "<center>" .
       "<br>" .
       "<font size=2 color=#DEDEEF><b>$l_log_start $entry<b></font>" .
       "<p>" .
       "</center>" .
       "<hr width=80% size=1 NOSHADE style=\"color: #040658\">";

  if (!empty($logs))
  {
    foreach ($logs as $log)
    {
      $event = log_parse($log);
      $log_months_temp = "l_log_months_" . (substr($log['time'], 5, 2) - 1);
      $time = $$l_log_months_temp . " " . substr($log['time'], 8, 2) . " " . substr($log['time'], 0, 4) . " " . substr($log['time'], 11);

      echo "<table border=0 cellspacing=5 width=100%>\n" .
           "<tr>\n" .
           "<td style='text-align:left;'><font size=2 color=#040658><b>$event[title]</b></td>\n" .
           "<td align=right><font size=2 color=#040658><b>$time</b></td>\n" .
           "</tr>\n".
           "<tr>\n<td colspan=2 align=left>\n" .
           "<font size=2 color=#DEDEEF>" .
           "$event[text]" .
           "</td>\n</tr>\n" .
           "</table>\n" .
           "<hr width=80% size=1 NOSHADE style=\"color: #040658\">";
    }
  }

  echo "<center>" .
       "<br>" .
       "<font size=2 color=#DEDEEF><b>$l_log_end $entry<b></font>" .
       "<p>" .
       "</center>" .
       "</div>";

}

echo "</div>";

$log_months_short_temp = "l_log_months_short_" . (substr($startdate, 5, 2) - 1);
$date1 = $$l_log_months_short_temp . " " . substr($startdate, 8, 2);

$log_months_short_temp = "l_log_months_short_" . (substr($startdate, 5, 2) - 1);
$date2 = $$l_log_months_short_temp . " " . substr($yesterday, 8, 2);

$log_months_short_temp = "l_log_months_short_" . (substr($startdate, 5, 2) - 1);
$date3 = $$l_log_months_short_temp . " " . substr($yesterday2, 8, 2);

$month = substr($startdate, 5, 2);
$day = substr($startdate, 8, 2) - 3;
$year = substr($startdate, 0, 4);

$backlink = mktime (0,0,0,$month,$day,$year);
$backlink = date("Y-m-d", $backlink);

$day = substr($startdate, 8, 2) + 3;

$nextlink = mktime (0,0,0,$month,$day,$year);
if ($nextlink > time())
  $nextlink = time();
$nextlink = date("Y-m-d", $nextlink);

if ($startdate == date("Y-m-d"))
  $nonext = 1;

if ($swordfish == $adminpass) // Fix for admin log view
  $postlink = "&swordfish=" . urlencode($swordfish) . "&player=$player";
else
  $postlink = "";

if ($mode != 'compat')
{
  echo "<td valign=bottom>" .
       "<tr><td><td align=right>" .
       "<img src=images/bottom_panel.png>" .
       "<br>" .
       "<div style=\"position:relative; top:-23px;\">" .
       "<font size=2><b>" .
       "<a href=log.php?startdate={$backlink}$postlink><<</a>&nbsp;&nbsp;&nbsp;" .
       "<a href=\"#\" onclick=\"activate(2); return false;\" onfocus=\"if (this.blur)this.blur()\">$date3</a>" .
       " | " .
       "<a href=\"#\" onclick=\"activate(1); return false;\" onfocus=\"if (this.blur)this.blur()\">$date2</a>" .
       " | " .
       "<a href=\"#\" onclick=\"activate(0); return false;\" onfocus=\"if (this.blur)this.blur()\">$date1</a>";

  if ($nonext != 1)
    echo "&nbsp;&nbsp;&nbsp;<a href=log.php?startdate={$nextlink}$postlink>>>></a>";

  echo "&nbsp;&nbsp;&nbsp;";
}
else
{
  echo "<tr><td><td align=right>" .
       "<a href=log.php?startdate={$backlink}$postlink><font color=white size =3><b><<</b></font></a>&nbsp;&nbsp;&nbsp;" .
       "<a href=log.php?startdate={$yesterday2}$postlink><font color=white size=3><b>$date3</b></font></a>" .
       "&nbsp;|&nbsp;" .
       "<a href=log.php?startdate={$yesterday}$postlink><font color=white size=3><b>$date2</b></font></a>" .
       " | " .
       "<a href=log.php?startdate={$startdate}$postlink><font color=white size=3><b>$date1</b></font></a>";

  if ($nonext != 1)
    echo "&nbsp;&nbsp;&nbsp;<a href=log.php?startdate={$nextlink}$postlink><font color=white size=3><b>>></b></font></a>";

  echo "&nbsp;&nbsp;&nbsp;";

}

if ($swordfish == $adminpass)
  echo "<tr><td><td>" .
       "<FORM action=admin.php method=POST>" .
       "<input type=hidden name=swordfish value=\"$swordfish\">" .
       "<input type=hidden name=menu value=logview>" .
       "<input type=submit value=\"Return to Admin\"></td></tr>";
else
{

  $l_log_click = str_replace("[here]", "<a href=main.php><font color=#00ff00>" . $l_here . "</font></a>", $l_log_click);
  echo "<tr><td><td style='text-align:left;'><p style='font-size:2;'>$l_log_click</p></td></tr>";
}


if ($mode != 'compat')
{
  $l_log_note = str_replace("[disable them]", "<a href=options.php><font color=#00FF00>" . $l_log_note_disable . "</font></a>", $l_log_note);
  echo "<tr><td><td align=center><br><font size=2 color=white>$l_log_note</td></tr>";
}
echo "</table>" .
     "</center>";

include("footer.php");

function log_parse($entry)
{
  global $l_log_title;
  global $l_log_text;
  global $l_log_pod;
  global $l_log_nopod;
  $l_log_nopod = "<font color=yellow><b>" . $l_log_nopod . "</b></font>"; // This should be done better, but I needed it moved out of the language file.

    $texttemp = "l_log_text_" . $entry['type'];
    $titletemp = "l_log_title_" . $entry['type'];

  switch ($entry['type'])
  {
    case LOG_LOGIN: //data args are : [ip]
    case LOG_LOGOUT:
    case LOG_BADLOGIN:
    case LOG_HARAKIRI:
    $retvalue['text'] = str_replace("[ip]", "<font color=white><b>$entry[data]</b></font>", $$texttemp);
    $retvalue['title'] = $$titletemp;
    $retvalue['title'] = "<font color=red>" . $retvalue['title'] . "</font>";
    break;

    case LOG_ATTACK_OUTMAN: //data args are : [player]
    case LOG_ATTACK_OUTSCAN:
    case LOG_ATTACK_EWD:
    case LOG_ATTACK_EWDFAIL:
    case LOG_SHIP_SCAN:
    case LOG_SHIP_SCAN_FAIL:
    case LOG_Xenobe_ATTACK:
    case LOG_TEAM_NOT_LEAVE:
    $retvalue['text'] = str_replace("[player]", "<font color=white><b>$entry[data]</b></font>", $$texttemp);
    $retvalue['title'] = $$titletemp;
    $retvalue['title'] = "<font color=red>" . $retvalue['title'] . "</font>";
    break;

    case LOG_ATTACK_LOSE: //data args are : [player] [pod]
    list($name,$pod) = split ("\|", $entry['data']);

    $retvalue['text'] = str_replace("[player]", "<font color=white><b>$name</b></font>", $$texttemp);
    $retvalue['title'] = $$titletemp;
    $retvalue['title'] = "<font color=red>" . $retvalue['title'] . "</font>";
    if ($pod == 'Y')
      $retvalue['text'] = $retvalue['text'] . $l_log_pod;
    else
      $retvalue['text'] = $retvalue['text'] . $l_log_nopod;
    break;

    case LOG_ATTACKED_WIN: //data args for text are : [player] [armor] [fighters]
    list($name, $armor, $fighters)= split ("\|", $entry[data]);
    $retvalue['text'] = str_replace("[player]", "<font color=white><b>$name</b></font>", $$texttemp);
    $retvalue['text'] = str_replace("[armor]", "<font color=white><b>$armor</b></font>", $retvalue['text']);
    $retvalue['text'] = str_replace("[fighters]", "<font color=white><b>$fighters</b></font>", $retvalue['text']);
    $retvalue['title'] = $$titletemp;
    $retvalue['title'] = "<font color=yellow>" . $retvalue['title'] . "</font>";
    break;

    case LOG_TOLL_PAID: //data args are : [toll] [sector]
    case LOG_TOLL_RECV:
    list($toll, $sector)= split ("\|", $entry[data]);
    $retvalue['text'] = str_replace("[toll]", "<font color=white><b>$toll</b></font>", $$texttemp);
    $retvalue['text'] = str_replace("[sector]", "<font color=white><b>$sector</b></font>", $retvalue['text']);
    $retvalue['title'] = $$titletemp;
    break;

    case LOG_HIT_MINES: //data args are : [mines] [sector]
    list($mines, $sector)= split ("\|", $entry[data]);
    $retvalue['text'] = str_replace("[mines]", "<font color=white><b>$mines</b></font>", $$texttemp);
    $retvalue['text'] = str_replace("[sector]", "<font color=white><b>$sector</b></font>", $retvalue['text']);
    $retvalue['title'] = $$titletemp;
    $retvalue['title'] = "<font color=yellow>" . $retvalue['title'] . "</font>";
    break;

    case LOG_SHIP_DESTROYED_MINES: //data args are : [sector] [pod]
    list($sector, $pod)= split ("\|", $entry[data]);
    $retvalue['text'] = str_replace("[sector]", "<font color=white><b>$sector</b></font>", $$texttemp);
    $retvalue['title'] = $$titletemp;
    $retvalue['title'] = "<font color=red>" . $retvalue['title'] . "</font>";
    if ($pod == 'Y')
      $retvalue['text'] = $retvalue['text'] . $l_log_pod;
    else
      $retvalue['text'] = $retvalue['text'] . $l_log_nopod;
    break;

    case LOG_DEFS_KABOOM: //data args are : [sector] [pod]
    list($sector, $pod)= split ("\|", $entry[data]);
    $retvalue['text'] = str_replace("[sector]", "<font color=white><b>$sector</b></font>", $$texttemp);
    $retvalue['title'] = $$titletemp;
    $retvalue['title'] = "<font color=red>" . $retvalue['title'] . "</font>";
    if ($pod == 'Y')
      $retvalue['text'] = $retvalue['text'] . $l_log_pod;
    else
      $retvalue['text'] = $retvalue['text'] . $l_log_nopod;
    break;

    case LOG_PLANET_DEFEATED_D: //data args are :[planet_name] [sector] [name]
    list($planet_name, $sector, $name)= split ("\|", $entry[data]);
    $retvalue['text'] = str_replace("[planet_name]", "<font color=white><b>$planet_name</b></font>", $$texttemp);
    $retvalue['text'] = str_replace("[sector]", "<font color=white><b>$sector</b></font>", $retvalue['text']);
    $retvalue['text'] = str_replace("[name]", "<font color=white><b>$name</b></font>", $retvalue['text']);
    $retvalue['title'] = $$titletemp;
    $retvalue['title'] = "<font color=yellow>" . $retvalue['title'] . "</font>";
    break;

    case LOG_PLANET_DEFEATED:
    list($planet_name, $sector, $name)= split ("\|", $entry[data]);
    $retvalue['text'] = str_replace("[planet_name]", "<font color=white><b>$planet_name</b></font>", $$texttemp);
    $retvalue['text'] = str_replace("[sector]", "<font color=white><b>$sector</b></font>", $retvalue['text']);
    $retvalue['text'] = str_replace("[name]", "<font color=white><b>$name</b></font>", $retvalue['text']);
    $retvalue['title'] = $$titletemp;
    $retvalue['title'] = "<font color=red>" . $retvalue['title'] . "</font>";
    break;

    case LOG_PLANET_SCAN:
    case LOG_PLANET_SCAN_FAIL:
    list($planet_name, $sector, $name)= split ("\|", $entry[data]);
    $retvalue['text'] = str_replace("[planet_name]", "<font color=white><b>$planet_name</b></font>", $$texttemp);
    $retvalue['text'] = str_replace("[sector]", "<font color=white><b>$sector</b></font>", $retvalue['text']);
    $retvalue['text'] = str_replace("[name]", "<font color=white><b>$name</b></font>", $retvalue['text']);
    $retvalue['title'] = $$titletemp;
    break;

    case LOG_PLANET_NOT_DEFEATED: //data args are : [planet_name] [sector] [name] [ore] [organics] [goods] [salvage] [credits]
    list($planet_name, $sector, $name, $ore, $organics, $goods, $salvage, $credits)= split ("\|", $entry[data]);
    $retvalue['text'] = str_replace("[planet_name]", "<font color=white><b>$planet_name</b></font>", $$texttemp);
    $retvalue['text'] = str_replace("[sector]", "<font color=white><b>$sector</b></font>", $retvalue['text']);
    $retvalue['text'] = str_replace("[name]", "<font color=white><b>$name</b></font>", $retvalue['text']);
    $retvalue['text'] = str_replace("[ore]", "<font color=white><b>$ore</b></font>", $retvalue['text']);
    $retvalue['text'] = str_replace("[goods]", "<font color=white><b>$goods</b></font>", $retvalue['text']);
    $retvalue['text'] = str_replace("[organics]", "<font color=white><b>$organics</b></font>", $retvalue['text']);
    $retvalue['text'] = str_replace("[salvage]", "<font color=white><b>$salvage</b></font>", $retvalue['text']);
    $retvalue['text'] = str_replace("[credits]", "<font color=white><b>$credits</b></font>", $retvalue['text']);
    $retvalue['title'] = $$titletemp;
    break;

    case LOG_RAW: //data is stored as a message
    $retvalue['title'] = $$titletemp;
    $retvalue['text'] = $entry[data];
    break;

    case LOG_DEFS_DESTROYED: //data args are : [quantity] [type] [sector]
    list($quantity, $type, $sector)= split ("\|", $entry[data]);
    $retvalue['text'] = str_replace("[quantity]", "<font color=white><b>$quantity</b></font>", $$texttemp);
    $retvalue['text'] = str_replace("[type]", "<font color=white><b>$type</b></font>", $retvalue['text']);
    $retvalue['text'] = str_replace("[sector]", "<font color=white><b>$sector</b></font>", $retvalue['text']);
    $retvalue['title'] = $$titletemp;
    break;

    case LOG_PLANET_EJECT: //data args are : [sector] [player]
    list($sector, $name)= split ("\|", $entry[data]);
    $retvalue['text'] = str_replace("[sector]", "<font color=white><b>$sector</b></font>", $$texttemp);
    $retvalue['text'] = str_replace("[name]", "<font color=white><b>$name</b></font>", $retvalue['text']);
    $retvalue['title'] = $$titletemp;
    break;

    case LOG_STARVATION: //data args are : [sector] [starvation]
    list($sector, $starvation)= split ("\|", $entry[data]);
    $retvalue['text'] = str_replace("[sector]", "<font color=white><b>$sector</b></font>", $$texttemp);
    $retvalue['text'] = str_replace("[starvation]", "<font color=white><b>$starvation</b></font>", $retvalue['text']);
    $retvalue['title'] = $$titletemp;
    $retvalue['title'] = "<font color=yellow>" . $retvalue['title'] . "</font>";
    break;

    case LOG_TOW: //data args are : [sector] [newsector] [hull]
    list($sector, $newsector, $hull)= split ("\|", $entry[data]);
    $retvalue['text'] = str_replace("[sector]", "<font color=white><b>$sector</b></font>", $$texttemp);
    $retvalue['text'] = str_replace("[newsector]", "<font color=white><b>$newsector</b></font>", $retvalue['text']);
    $retvalue['text'] = str_replace("[hull]", "<font color=white><b>$hull</b></font>", $retvalue['text']);
    $retvalue['title'] = $$titletemp;
    break;

    case LOG_DEFS_DESTROYED_F: //data args are : [fighters] [sector]
    list($fighters, $sector)= split ("\|", $entry[data]);
    $retvalue['text'] = str_replace("[sector]", "<font color=white><b>$sector</b></font>", $$texttemp);
    $retvalue['text'] = str_replace("[fighters]", "<font color=white><b>$fighters</b></font>", $retvalue['text']);
    $retvalue['title'] = $$titletemp;
    break;

    case LOG_TEAM_REJECT: //data args are : [player] [teamname]
    list($player, $teamname)= split ("\|", $entry[data]);
    $retvalue['text'] = str_replace("[player]", "<font color=white><b>$player</b></font>", $$texttemp);
    $retvalue['text'] = str_replace("[teamname]", "<font color=white><b>$teamname</b></font>", $retvalue['text']);
    $retvalue['title'] = $$titletemp;
    break;

    case LOG_TEAM_RENAME: //data args are : [team]
    case LOG_TEAM_M_RENAME:
    case LOG_TEAM_KICK:
    case LOG_TEAM_CREATE:
    case LOG_TEAM_LEAVE:
    case LOG_TEAM_LEAD:
    case LOG_TEAM_JOIN:
    case LOG_TEAM_INVITE:
    $retvalue['text'] = str_replace("[team]", "<font color=white><b>$entry[data]</b></font>", $$texttemp);
    $retvalue['title'] = $$titletemp;
    break;

    case LOG_TEAM_NEWLEAD: //data args are : [team] [name]
    case LOG_TEAM_NEWMEMBER:
    list($team, $name)= split ("\|", $entry[data]);
    $retvalue['text'] = str_replace("[team]", "<font color=white><b>$team</b></font>", $$texttemp);
    $retvalue['text'] = str_replace("[name]", "<font color=white><b>$name</b></font>", $retvalue['text']);
    $retvalue['title'] = $$titletemp;
    break;

    case LOG_ADMIN_HARAKIRI: //data args are : [player] [ip]
    list($player, $ip)= split ("\|", $entry[data]);
    $retvalue['text'] = str_replace("[player]", "<font color=white><b>$player</b></font>", $$texttemp);
    $retvalue['text'] = str_replace("[ip]", "<font color=white><b>$ip</b></font>", $retvalue['text']);
    $retvalue['title'] = $$titletemp;
    break;

    case LOG_ADMIN_ILLEGVALUE: //data args are : [player] [quantity] [type] [holds]
    list($player, $quantity, $type, $holds)= split ("\|", $entry[data]);
    $retvalue['text'] = str_replace("[player]", "<font color=white><b>$player</b></font>", $$texttemp);
    $retvalue['text'] = str_replace("[quantity]", "<font color=white><b>$quantity</b></font>", $retvalue['text']);
    $retvalue['text'] = str_replace("[type]", "<font color=white><b>$type</b></font>", $retvalue['text']);
    $retvalue['text'] = str_replace("[holds]", "<font color=white><b>$holds</b></font>", $retvalue['text']);
    $retvalue['title'] = $$titletemp;
    break;

    case LOG_ADMIN_PLANETDEL: //data args are : [attacker] [defender] [sector]
    list($attacker, $defender, $sector)= split ("\|", $entry[data]);
    $retvalue['text'] = str_replace("[attacker]", "<font color=white><b>$attacker</b></font>", $$texttemp);
    $retvalue['text'] = str_replace("[defender]", "<font color=white><b>$defender</b></font>", $retvalue['text']);
    $retvalue['text'] = str_replace("[sector]", "<font color=white><b>$sector</b></font>", $retvalue['text']);
    $retvalue['title'] = $$titletemp;
    break;

    case LOG_DEFENCE_DEGRADE: //data args are : [sector] [degrade]
    list($sector, $degrade)= split ("\|", $entry[data]);
    $retvalue['text'] = str_replace("[sector]", "<font color=white><b>$sector</b></font>", $$texttemp);
    $retvalue['text'] = str_replace("[degrade]", "<font color=white><b>$degrade</b></font>", $retvalue['text']);
    $retvalue['title'] = $$titletemp;
    break;

    case LOG_PLANET_CAPTURED: //data args are : [cols] [credits] [owner]
    list($cols, $credits, $owner)= split ("\|", $entry[data]);
    $retvalue['text'] = str_replace("[cols]", "<font color=white><b>$cols</b></font>", $$texttemp);
    $retvalue['text'] = str_replace("[credits]", "<font color=white><b>$credits</b></font>", $retvalue['text']);
    $retvalue['text'] = str_replace("[owner]", "<font color=white><b>$owner</b></font>", $retvalue['text']);
    $retvalue['title'] = $$titletemp;
    break;
    case LOG_BOUNTY_CLAIMED:
    list($amount,$bounty_on,$placed_by) = split ("\|", $entry[data]);
    $retvalue['text'] = str_replace("[amount]", "<font color=white><b>$amount</b></font>", $$texttemp);
    $retvalue['text'] = str_replace("[bounty_on]", "<font color=white><b>$bounty_on</b></font>", $retvalue['text']);
    $retvalue['text'] = str_replace("[placed_by]", "<font color=white><b>$placed_by</b></font>", $retvalue['text']);
    $retvalue['title'] = $$titletemp;
    break;
 case LOG_BOUNTY_PAID:
    list($amount,$bounty_on) = split ("\|", $entry[data]);
    $retvalue['text'] = str_replace("[amount]", "<font color=white><b>$amount</b></font>", $$texttemp);
    $retvalue['text'] = str_replace("[bounty_on]", "<font color=white><b>$bounty_on</b></font>", $retvalue['text']);
    $retvalue['title'] = $$titletemp;
    break;
 case LOG_BOUNTY_CANCELLED:
    list($amount,$bounty_on) = split ("\|", $entry[data]);
    $retvalue['text'] = str_replace("[amount]", "<font color=white><b>$amount</b></font>", $$texttemp);
    $retvalue['text'] = str_replace("[bounty_on]", "<font color=white><b>$bounty_on</b></font>", $retvalue['text']);
    $retvalue['title'] = $$titletemp;
    break;
case LOG_BOUNTY_FEDBOUNTY:
    $retvalue['text'] = str_replace("[amount]", "<font color=white><b>$entry[data]</b></font>", $$texttemp);
    $retvalue['title'] = $$titletemp;
    break;
 case LOG_SPACE_PLAGUE:
    list($name, $sector, $percentage) = split ("\|", $entry[data]);
    $retvalue['text'] = str_replace("[name]", "<font color=white><b>$name</b></font>", $$texttemp);
    $retvalue['text'] = str_replace("[sector]", "<font color=white><b>$sector</b></font>", $retvalue['text']);
    $retvalue['text'] = str_replace("[percentage]", "<font color=white><b>$percentage</b></font>", $retvalue['text']);
    $retvalue['title'] = $$titletemp;
    break;
 case LOG_PLASMA_STORM:
    list($name,$sector,$percentage) = split ("\|", $entry[data]);
    $retvalue['text'] = str_replace("[name]", "<font color=white><b>$name</b></font>", $$texttemp);
    $retvalue['text'] = str_replace("[sector]", "<font color=white><b>$sector</b></font>", $retvalue['text']);
    $retvalue['text'] = str_replace("[percentage]", "<font color=white><b>$percentage</b></font>", $retvalue['text']);
    $retvalue['title'] = $$titletemp;
    break;
 case LOG_PLANET_BOMBED:
    list($planet_name, $sector, $name, $beams, $torps, $figs)= split ("\|", $entry[data]);
    $retvalue['text'] = str_replace("[planet_name]", "<font color=white><b>$planet_name</b></font>", $$texttemp);
    $retvalue['text'] = str_replace("[sector]", "<font color=white><b>$sector</b></font>", $retvalue['text']);
    $retvalue['text'] = str_replace("[name]", "<font color=white><b>$name</b></font>", $retvalue['text']);
    $retvalue['text'] = str_replace("[beams]", "<font color=white><b>$beams</b></font>", $retvalue['text']);
    $retvalue['text'] = str_replace("[torps]", "<font color=white><b>$torps</b></font>", $retvalue['text']);
    $retvalue['text'] = str_replace("[figs]", "<font color=white><b>$figs</b></font>", $retvalue['text']);
    $retvalue['title'] = $$titletemp;
    $retvalue['title'] = "<font color=red>" . $retvalue['title'] . "</font>";
    break;

 case 57:
    // Multi Browser Logs.
    list($ship_ip, $ship_id, $info)= split ("\|", $entry[data]);
    $retvalue['text'] = "Account: <span style='color:#ff0;'>{$ship_id}</span> with IP: '<span style='color:#ff0;'>{$ship_ip}</span>' <span style='color:#fff;'>{$info}</span>";
    $retvalue['title'] = "Possible Multi Browser Attempt.";
    break;

 case 901:
    // Multi Hash Logs debug info
    list($ship_id, $last_hash, $this_hash, $status)= split ("\|", $entry[data]);
    $last_hash = strtoupper($last_hash);
    $this_hash = strtoupper($this_hash);
    $retvalue['text'] = "Account: <span style='color:#ff0;'>{$ship_id}</span> last used Hash: '<span style='color:#ff0;'>{$last_hash}</span>' and now is using Hash: '<span style='color:#ff0;'>{$this_hash}</span>', Status: <span style='color:#ff0;'>{$status}</span>";
    $retvalue['title'] = "Multi Hash Logs [Debug].";
    break;

 case 950:
    // Attack logs debug info
    list($step, $attacker_armor, $target_armor, $attacker_fighters, $target_fighters, $attacker_id, $target_id)= split ("\|", $entry[data]);
    $retvalue['text']  = "Attacker Ship: {$attacker_id}, Armor: {$attacker_armor}, Fighters: {$attacker_fighters}<br>\n";
    $retvalue['text'] .= "Target Ship: {$target_id}, Armor: {$target_armor}, Fighters: {$target_fighters}\n";
    $retvalue['title'] = "Attack Logs Stage: {$step} [Debug].";
    break;

 case 1019:
    // Invalid login try (wrong password etc)
    list($ship_ip, $ship_email, $used_password, $used_hash)= split ("\|", $entry[data]);
    $retvalue['text'] = "Someone using IP: <span style='color:#ff0;'>{$ship_ip}</span> tried to login into Account: '<span style='color:#ff0;'>{$ship_email}</span>' with Password: '<span style='color:#ff0;'>{$used_password}</span>' and had the following Hash: '<span style='color:#ff0;'>{$used_hash}</span>'";
    $retvalue['title'] = "Invalid Login Attempt.";
    break;

 default:
    $retvalue['text'] = $entry[data];
    $retvalue['title'] = $entry[type];
    break;
  }

  return $retvalue;
}

?>
