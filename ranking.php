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

include "config.php";
updatecookie();
include "languages/$lang";

$l_ranks_title = str_replace("[max_rank]", $max_ranks, $l_ranks_title);
$title = $l_ranks_title;
include "header.php";
bigtitle();

$res = $db->Execute("SELECT COUNT(*) AS num_players FROM {$db->prefix}ships WHERE ship_destroyed='N' and email NOT LIKE '%@xenobe'");
$row = $res->fields;
$num_players = $row['num_players'];

if (!isset($_GET['sort']))
{
    $_GET['sort'] = '';
}
$sort = $_GET['sort'];

if ($sort=="turns")
{
    $by="turns_used DESC,character_name ASC";
}
elseif ($sort=="login")
{
    $by="last_login DESC,character_name ASC";
}
elseif ($sort=="good")
{
    $by="rating DESC,character_name ASC";
}
elseif ($sort=="bad")
{
    $by="rating ASC,character_name ASC";
}
elseif ($sort=="team")
{
    $by="{$db->prefix}teams.team_name DESC, character_name ASC";
}
elseif ($sort=="efficiency")
{
    $by="efficiency DESC";
}
else
{
    $by="score DESC,character_name ASC";
}

$res = $db->Execute("SELECT {$db->prefix}ships.email,{$db->prefix}ships.score,{$db->prefix}ships.character_name,{$db->prefix}ships.turns_used,{$db->prefix}ships.last_login,UNIX_TIMESTAMP({$db->prefix}ships.last_login) as online,{$db->prefix}ships.rating, {$db->prefix}teams.team_name, if ({$db->prefix}ships.turns_used<150,0,ROUND({$db->prefix}ships.score/{$db->prefix}ships.turns_used)) AS efficiency FROM {$db->prefix}ships LEFT JOIN {$db->prefix}teams ON {$db->prefix}ships.team = {$db->prefix}teams.id  WHERE ship_destroyed='N' and email NOT LIKE '%@xenobe' ORDER BY $by LIMIT $max_ranks");

if (!$res)
{
    echo "$l_ranks_none<br>";
}
else
{
    echo "<br>$l_ranks_pnum: " . NUMBER($num_players);
    echo "<br>$l_ranks_dships<br><br>";
    echo "<table border=0 cellspacing=0 cellpadding=2>";
    echo "<tr bgcolor=\"$color_header\"><td><strong>$l_ranks_rank</strong></td><td><strong><a href=\"ranking.php\">$l_score</A></strong></td><td><strong>$l_player</strong></td><td><strong><a href=\"ranking.php?sort=turns\">$l_turns_used</A></strong></td><td><strong><a href=\"ranking.php?sort=login\">$l_ranks_lastlog</A></strong></td><td><strong><a href=\"ranking.php?sort=good\">$l_ranks_good</A>/<a href=\"ranking.php?sort=bad\">$l_ranks_evil</A></strong></td><td><strong><a href=\"ranking.php?sort=team\">$l_team_team</A></strong></td><td><strong><a href=\"ranking.php?sort=online\">Online</A></strong></td><td><strong><a href=\"ranking.php?sort=efficiency\">Eff. Rating.</A></strong></td></tr>\n";
    $color = $color_line1;
    $i = '';
    while (!$res->EOF)
    {
        $row = $res->fields;
        $i++;
        $rating=round(sqrt( abs($row['rating']) ));
        if (abs($row['rating'])!=$row['rating'])
        {
            $rating=-1*$rating;
        }

        $curtime = TIME();
        $time = $row['online'];
        $difftime = ($curtime - $time) / 60;
        $temp_turns = $row['turns_used'];
        if ($temp_turns <= 0)
        {
            $temp_turns = 1;
        }

        $online = " ";
        if ($difftime <= 5)
        {
            $online = "Online";
        }

        echo "<tr bgcolor=\"$color\"><td>" . NUMBER($i) . "</td><td>" . NUMBER($row['score']) . "</td><td>";
        echo "&nbsp;";
        echo player_insignia_name ($db, $row['email']);
        echo "&nbsp;";
        echo "<strong>$row[character_name]</strong></td><td>" . NUMBER($row['turns_used']) . "</td><td>$row[last_login]</td><td>&nbsp;&nbsp;" . NUMBER($rating) . "</td><td>$row[team_name]&nbsp;</td><td>$online</td><td>$row[efficiency]</td></tr>\n";
        if ($color == $color_line1)
        {
            $color = $color_line2;
        }
        else
        {
            $color = $color_line1;
        }

        $res->MoveNext();
    }
    echo "</table>";
}

echo "<br>";

if (empty($username))
{
    TEXT_GOTOLOGIN();
}
else
{
    TEXT_GOTOMAIN();
}

include "footer.php";
?>
