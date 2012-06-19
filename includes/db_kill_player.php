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
// File: includes/db_kill_player.php

if (preg_match("/db_kill_player.php/i", $_SERVER['PHP_SELF'])) {
      echo "You can not access this file directly!";
      die();
}

function db_kill_player ($ship_id, $remove_planets = false)
{
    global $default_prod_ore;
    global $default_prod_organics;
    global $default_prod_goods;
    global $default_prod_energy;
    global $default_prod_fighters;
    global $default_prod_torp;
    global $gameroot, $db, $dbtables;

    include "languages/english.inc";

    $db->Execute("UPDATE $dbtables[ships] SET ship_destroyed='Y', on_planet='N', sector=0, cleared_defences=' ' WHERE ship_id=$ship_id");
    $db->Execute("DELETE from $dbtables[bounty] WHERE placed_by = $ship_id");

    $res = $db->Execute("SELECT DISTINCT sector_id FROM $dbtables[planets] WHERE owner='$ship_id' AND base='Y'");
    $i = 0;

    while (!$res->EOF && $res)
    {
        $sectors[$i] = $res->fields['sector_id'];
        $i++;
        $res->MoveNext();
    }

    if ($remove_planets == true && $ship_id > 0)
    {
        $db->Execute("DELETE from $dbtables[planets] WHERE owner = $ship_id");
    }
    else
    {
        $db->Execute("UPDATE $dbtables[planets] SET owner=0, corp=0, fighters=0, base='N' WHERE owner=$ship_id");
    }

    if (!empty($sectors))
    {
        foreach ($sectors as $sector)
        {
            calc_ownership ($sector);
        }
    }

    $db->Execute("DELETE FROM $dbtables[sector_defence] where ship_id=$ship_id");

    $res = $db->Execute("SELECT zone_id FROM $dbtables[zones] WHERE corp_zone='N' AND owner=$ship_id");
    $zone = $res->fields;

    $db->Execute("UPDATE $dbtables[universe] SET zone_id=1 WHERE zone_id=$zone[zone_id]");

    $query = $db->Execute("select character_name from $dbtables[ships] where ship_id='$ship_id'");
    $name = $query->fields;

    $headline = $name['character_name'] . $l_killheadline;

    $newstext = str_replace("[name]", $name['character_name'], $l_news_killed);

    $news = $db->Execute("INSERT INTO $dbtables[news] (headline, newstext, user_id, date, news_type) VALUES ('$headline','$newstext','$ship_id',NOW(), 'killed')");
}
?>
