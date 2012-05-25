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
// File: ranking.php

include("config.php");
updatecookie();

include("languages/$lang");
$title=$l_ranks_title;
include("header.php");

connectdb();
bigtitle();

$res = $db->Execute("SELECT COUNT(*) AS num_players FROM $dbtables[ships] WHERE ship_destroyed='N' and email NOT LIKE '%@xenobe' AND turns_used >0");
$row = $res->fields;
$num_players = $row['num_players'];

if($sort=="turns")
{
    $by="turns_used DESC,character_name ASC";
}
elseif($sort=="login")
{
    $by="last_login DESC,character_name ASC";
}
elseif($sort=="good")
{
    $by="rating DESC,character_name ASC";
}
elseif($sort=="bad")
{
    $by="rating ASC,character_name ASC";
}
elseif($sort=="alliance")
{
    $by="$dbtables[teams].team_name DESC, character_name ASC";
}
elseif($sort=="efficiency")
{
    $by="efficiency DESC";
}
else
{
    $by="score DESC,character_name ASC";
}

$sql = "SELECT $dbtables[ships].ship_id, $dbtables[ships].ship_destroyed , $dbtables[ships].email,$dbtables[ships].score,$dbtables[ships].character_name,$dbtables[ships].turns_used,$dbtables[ships].last_login, UNIX_TIMESTAMP($dbtables[ships].last_login) as online, $dbtables[ships].rating, $dbtables[teams].team_name, $dbtables[ships].ip_address, IF($dbtables[ships].turns_used<150,0,ROUND($dbtables[ships].score/$dbtables[ships].turns_used)) AS efficiency FROM $dbtables[ships] LEFT JOIN $dbtables[teams] ON $dbtables[ships].team = $dbtables[teams].id  WHERE (ship_destroyed='N' OR ship_destroyed='Y') AND email NOT LIKE '%@xenobe' AND turns_used >0 ORDER BY $by LIMIT $max_rank";
$res = $db->Execute($sql);
#$res = $db->Execute("SELECT $dbtables[ships].email, $dbtables[ships].score, $dbtables[ships].character_name, $dbtables[ships].turns_used, $dbtables[ships].last_login, UNIX_TIMESTAMP($dbtables[ships].last_login) as online, $dbtables[ships].ip_address, $dbtables[ships].rating, $dbtables[teams].team_name, IF($dbtables[ships].turns_used<150,0,ROUND($dbtables[ships].score/$dbtables[ships].turns_used)) AS efficiency FROM $dbtables[ships] LEFT JOIN $dbtables[teams] ON $dbtables[ships].team = $dbtables[teams].id  WHERE ship_destroyed='N' and email NOT LIKE '%@xenobe' AND turns_used >0 ORDER BY $by LIMIT $max_rank");

echo "<div style='height:16px;'></div>\n";
echo "<div>\n";
$i = 0;
if(!$res || $res->RecordCount() <= 0)
{
    echo "$l_ranks_none<br />";
}
else
{
    $players = NUMBER($num_players);
    echo "<div style='font-size:12px;'>$l_ranks_pnum: {$players}</div>";
    echo "<div style='font-size:12px;'>$l_ranks_dships</div>";
    echo "<br />\n";
    echo "<table style='width:100%; border:none; font-size:14px;' cellspacing='0' cellpadding='2'>\n";
    echo "  <tr style='background-color:{$color_header};'>\n";
    echo "    <td style='font-weight:bold;'>{$l_ranks_rank}</td>\n";
    echo "    <td style='font-weight:bold;'><a href='ranking.php'>$l_score</a></td>\n";
    echo "    <td style='font-weight:bold;'>{$l_player}</td>\n";
    echo "    <td style='font-weight:bold;'><a href='ranking.php?sort=turns'>{$l_turns_used}</a></td>\n";
    echo "    <td style='font-weight:bold;'><a href='ranking.php?sort=login'>{$l_ranks_lastlog}</a></td>\n";
    echo "    <td style='font-weight:bold;'><a href='ranking.php?sort=good'>{$l_ranks_good}</a>/<a href='ranking.php?sort=bad'>{$l_ranks_evil}</a></td>\n";
    echo "    <td style='font-weight:bold;'><a href='ranking.php?sort=alliance'>{$l_team_alliance}</a></td>\n";
    echo "    <td style='font-weight:bold; width:100px;'>Status</td>\n";
    echo "    <td style='font-weight:bold;'><a href='ranking.php?sort=efficiency'>Eff. Rating.</a></td>\n";
    echo "  </tr>\n";
    $color = $color_line1;
    $need2add = null;
    while(!$res->EOF)
    {
            $row = $res->fields;

        $is_dead = $row['ship_destroyed'];
        if($is_dead == "Y")
        {
            $text_color = "#FF0000";
        }
        else
        {
            $text_color = "#FFFFFF";
        }

            $i++;
            $rating=round(sqrt( abs($row[rating]) ));
            if(abs($row[rating])!=$row[rating])
            {
                    $rating=-1*$rating;
            }

            $curtime = time();
            $time = $row['online'];
            $difftime = ($curtime - $time) / 60;
            $temp_turns = $row['turns_used'];

            if ($temp_turns <= 0)
            {
            $temp_turns = 1;
            }
            $status = null;

            $player_insignia  = player_insignia_name($row[email]);
            $player_insignia .= "&nbsp;";

            $char_name="<b>{$row[character_name]}</b>";

            if(isAdmin($row))
            {
                    $admin_caption = $admin_list[$row['character_name']]['level'];

            // Remove Insignia for admin players.
            $player_insignia = null;
            $online_image    = null;
            $offline_image    = null;

            switch($admin_list[$row['character_name']]['role'])
            {
                case "tester":
                {
                    // Set Character name Admin Blue
                    $char_name      = "<span style='color:#00FF00; font-size:14px;'>{$row[character_name]}</span>";
                    $alt            = "Blacknova Testing Team";
                    $online_image    = "images/online_tester.png";
                    $offline_image    = "images/offline_tester.png";
                    break;
                }
                case "developer":
                {
                    // Set Character name Admin Blue
                    $char_name      = "<span style='color:#0099FF; font-size:14px;'>{$row[character_name]}</span>";
                    $alt            = "Blacknova Development Team";
                    $online_image    = "images/online_developer.png";
                    $offline_image    = "images/offline_developer.png";
                    break;
                }
                case "admin":
                {
                    // Set Character name Admin Blue
                    $char_name      = "<span style='color:#0099FF; font-size:14px;'>{$row[character_name]}</span>";
                    $alt            = "Blacknova Administration Team";
                    $online_image    = "images/online_admin.png";
                    $offline_image    = "images/offline_admin.png";
                    break;
                }
            }

                    if($difftime <= 5)
                    {
                        $status = "<span class='rank_dev_text' style='color:#0099FF; font-size:14px;'>{$admin_caption}</span>";
                $status = "<div style='padding:0px; padding-top:2px;'><img name='tt' src='{$online_image}' style='width:64px; height:16px; padding:0px;' alt='{$alt}' />&nbsp;<span style='color:#FFCC00; font-size:12px; height:16px;'>{$admin_list[$row['character_name']]['status']}</span></div>";
                    }
                    else
                    {
                        $status = "<span class='rank_dev_text' style='color:#003F50; font-size:14px;'>{$admin_caption}</span>";
                $status= "<div style='padding:0px; padding-top:2px;'><img name='tt' src='{$offline_image}' style='width:64px; height:16px; padding:0px;' alt='{$alt}' />&nbsp;<span style='color:#FFCC00; font-size:12px; height:16px;'>{$admin_list[$row['character_name']]['status']}</span></div>";
                    }
            }
            else
            {
                    if(isLocked($row))
                    {
                        $status = "<span class='rank_dev_text' style='color:#FFFF00; font-size:14px;' title='Standard Lock'>Locked</span>";
            }
                    else
                    {
                        if($difftime <= 3)
                        {
                                $status = "<span class='rank_dev_text' style='color:#00FF00; font-size:14px;'>Online</span>";
                        }
                        elseif($difftime <= 15)
                        {
                            $status = "<span class='rank_dev_text' style='color:#00FF00; font-size:14px;'>* Idle *</span>";
                        }
                        else
                        {
                    $status = "<span class='rank_dev_text' style='color:#005F00; font-size:14px;'>Offline</span>";
                        }
                    }
        }
        if (strlen(trim($row['team_name'])) <=0) $row['team_name'] = "&nbsp;";

        echo "  <tr style='background-color:{$color}; color:{$text_color};'>\n";
    echo "    <td>" . NUMBER($i) . "</td>\n";
    echo "    <td>" . NUMBER($row[score]) . "</td>\n";
    echo "    <td>";
        if ($is_dead == "Y")
    {
        echo "<img src='images/skullancross.png' width='16' height='16' alt='' title='Player is currently dead' /> {$char_name} <img src='images/skullancross.png' width='16' height='16' alt='' title='Player is currently dead' />";
    }
    else
    {
        echo "{$player_insignia}{$char_name}";
    }

        echo "</td>\n";
    echo "    <td>" . NUMBER($row[turns_used]) . "</td>\n";
    echo "    <td>$row[last_login]</td>\n";
    echo "    <td>&nbsp;&nbsp;" . NUMBER($rating) . "</td>\n";
    echo "    <td>{$row['team_name']}</td>\n";
    echo "    <td style='text-align:left;'>$status</td>\n";
    echo "    <td>$row[efficiency]</td>\n";
    echo "  </tr>\n";

        if($color == $color_line1)
        {
            $color = $color_line2;
        }
        else
        {
            $color = $color_line1;
        }
        $res->MoveNext();
    }
    echo "</table>\n";
}

echo "</div>\n";

$db->Execute($sql);

if($ai_enabled == true)
{
    // AI Players

    $AIOrders    = null;
    $AIOrders[0] = "Home Patrol";
    $AIOrders[1] = "Explorer";
    $AIOrders[2] = "Trader";
    $AIOrders[3] = "Hunter";

    $AIMode    = null;
    $AIMode[0] = "Peaceful";
    $AIMode[1] = "Attack";
    $AIMode[2] = "Attack+";

    echo "<div style='height:16px;'></div>\n";
    echo "<div>\n";
    echo "<hr style='height:1px;' />\n";
    echo "<div style='font-size:24px;'>Ja'coni Players</div>\n";

    $res = $db->Execute("SELECT COUNT(*) AS num_players FROM $dbtables[ships] WHERE ship_destroyed='N' and email LIKE '%@xenobe'");
    $row = $res->fields;
    $num_players = $row['num_players'];

    $sql = "SELECT $dbtables[ships].ship_id, $dbtables[ships].email,$dbtables[ships].score,$dbtables[ships].character_name,$dbtables[ships].turns_used,$dbtables[ships].last_login,UNIX_TIMESTAMP($dbtables[ships].last_login) as online,$dbtables[ships].rating, $dbtables[teams].team_name, $dbtables[ships].ip_address, IF($dbtables[ships].turns_used<150,0,ROUND($dbtables[ships].score/$dbtables[ships].turns_used)) AS efficiency FROM $dbtables[ships] LEFT JOIN $dbtables[teams] ON $dbtables[ships].team = $dbtables[teams].id  WHERE ship_destroyed='N' and email LIKE '%@xenobe' ORDER BY $by LIMIT $max_rank";
    $res = $db->Execute($sql);

    #echo "Debug: {$res->sql}<br />\n";

    $i = 0;
    if(!$res)
    {
        echo "$l_ranks_none<br />";
    }
    else
    {
        echo "<br />$l_ranks_pnum: " . NUMBER($num_players);
        echo "<br />$l_ranks_dships<br /><br />";
        echo "<TABLE width=100% BORDER=0 CELLSPACING=0 CELLPADDING=2>";
        echo "<TR BGCOLOR=\"$color_header\"><TD><B>$l_player</B></TD><TD>$l_turns_used</TD><TD>$l_ranks_lastlog</TD><TD>$l_ranks_good/$l_ranks_evil</TD><TD>$l_team_alliance</TD><TD width='100'><B>Status</B></TD><TD>Eff. Rating.</TD></TR>\n";
        $color = $color_line1;

        if ($res->RecordCount() <= 0)
        {
            echo "<tr bgcolor=\"$color\"><td colspan='7' style='text-align:center;'>No Ja'coni AI Players Found.</td></tr>";
        }
        else
        {
            while(!$res->EOF)
            {
                $row = $res->fields;

                $statres = $db->Execute("SELECT aggression as mode, orders FROM $dbtables[xenobe] WHERE xenobe_id = ? LIMIT 1;", array($row['email']));
                $statrow = $statres->fields;

                $i++;
                $rating=round(sqrt( abs($row[rating]) ));
                if(abs($row[rating])!=$row[rating])
                {
                    $rating=-1*$rating;
                }
                $curtime = TIME();
                $time = $row[online];
                $difftime = ($curtime - $time) / 60;
                $temp_turns = $row[turns_used];

                if ($temp_turns <= 0)
                {
                    $temp_turns = 1;
                }
                $status = null;

                $player_insignia  = player_insignia_name($row[email]);
                $player_insignia .= "&nbsp;";

                $char_name          = "<b>{$row[character_name]}</b>";

                $orders = $statrow['orders'];
                $mode = $statrow['mode'];

                switch($mode)
                {
                    case 0:
                        $status = "<span class='rank_dev_text' style='color:#00FF00; font-size:14px;'>{$AIOrders[$statrow['orders']]}</span>";
                        break;

                    case 1:
                        $status = "<span class='rank_dev_text' style='color:#FFFF00; font-size:14px;'>{$AIOrders[$statrow['orders']]}</span>";
                        break;

                    case 2:
                        $status = "<span class='rank_dev_text' style='color:#FF0000; font-size:14px;'>{$AIOrders[$statrow['orders']]}</span>";
                        break;
                }

                echo "<TR BGCOLOR=\"$color\"><TD>";
                echo "&nbsp;";
                echo $player_insignia;
                echo "$char_name</TD><TD>" . NUMBER($row[turns_used]) . "</TD><TD>$row[last_login]</TD><TD>&nbsp;&nbsp;" . NUMBER($rating) . "</TD><TD>$row[team_name]&nbsp;</TD><TD>$status</TD><TD>$row[efficiency]</TD></TR>\n";
                if($color == $color_line1)
                {
                    $color = $color_line2;
                }
                else
                {
                    $color = $color_line1;
                }
                $res->MoveNext();
            }
        }
        echo "</TABLE>";
    }

    echo "<table style='width: 100%; padding:2px; font-size:12px; white-space:nowrap;'>\n";
    echo "  <tr>\n";
    echo "    <td style='width:50%;'>Ja'coni AI Code v0.0.1 (0012) based off Xenobe Code.</td>\n";
    echo "    <td style='width:16px; background-color:#FF0000;'></td><td>Hostile (Will Attack on Site)</td>\n";
    echo "    <td style='width:16px; background-color:#FFFF00;'></td><td>Neutral (But may Attack)</td>\n";
    echo "    <td style='width:16px; background-color:#00FF00;'></td><td>Friendly (Only Attacks if Attacked)</td>\n";
    echo "  </tr>\n";
    echo "</table>\n";
    echo "<hr />\n";
    echo "</div>\n";
}
echo "<div style='height:16px;'></div>\n";

echo "<div>\n";
if(empty($username))
{
    TEXT_GOTOLOGIN();
}
else
{
    TEXT_GOTOMAIN();
}
echo "</div>\n";

echo "<div id='popupbox' style='z-index:99; position:fixed; display:none; color:#FFFFFF; border:#FFFFFF 1px solid; background-color:#000011; background:URL(images/bg2_alpha.png) repeat; padding:4px;'>This should be hidden.</div>\n";
echo "<script type='text/javascript'>\n";
echo "//<![CDATA[\n";
echo "function displayME(dataString)\n";
echo "{\n";
echo "    document.getElementById('popupbox').innerHTML = dataString;\n";
echo "    document.getElementById('popupbox').style.display = 'block';\n";
echo "}\n";
echo "\n";
echo "function hideME()\n";
echo "{\n";
echo "    document.getElementById('popupbox').style.display = 'none';\n";
echo "}\n";
echo "\n";
echo "function movePopup(x,y)\n";
echo "{\n";
echo "    document.getElementById('popupbox').style.left = (x+20)+'px';\n";
echo "    document.getElementById('popupbox').style.top = (y-5)+'px';\n";
echo "}\n";
echo "//]]>\n";
echo "</script>\n";

include("footer.php");
?>
