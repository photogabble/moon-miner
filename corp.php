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
// File: corp.php

include "config.php";
updatecookie();
include "languages/$lang";
$title = $l_corpm_title;
include "header.php" ;

if (checklogin())
{
    die();
}

$result = $db->Execute("SELECT * FROM $dbtables[ships] WHERE email='$username'");
$playerinfo = $result->fields;

$planet_id = stripnum($planet_id);

$result2 = $db->Execute("SELECT * FROM $dbtables[planets] WHERE planet_id=$planet_id");
if ($result2)
{
    $planetinfo = $result2->fields;
}

if ($planetinfo[owner] == $playerinfo[ship_id] || ($planetinfo[corp] == $playerinfo[team] && $playerinfo[team] > 0))
{
    bigtitle();
    if ($action == "planetcorp")
    {
        echo $l_corpm_tocorp . "<br>";
        $result = $db->Execute("UPDATE $dbtables[planets] SET corp='$playerinfo[team]', owner=$playerinfo[ship_id] WHERE planet_id=$planet_id");
        $ownership = calc_ownership($playerinfo[sector]);
        if (!empty($ownership))
        {
            echo "<p>$ownership<p>";
        }
    }

    if ($action == "planetpersonal")
    {
        echo ("$l_corpm_topersonal<BR>");
        $result = $db->Execute("UPDATE $dbtables[planets] SET corp='0', owner=$playerinfo[ship_id] WHERE planet_id=$planet_id");
        $ownership = calc_ownership($playerinfo[sector]);
        // Kick other players off the planet
        $result = $db->Execute("UPDATE $dbtables[ships] SET on_planet='N' WHERE on_planet='Y' AND planet_id = $planet_id AND ship_id <> $playerinfo[ship_id]");
        if (!empty($ownership))
        {
            echo "<p>" . $ownership . "<p>";
        }
    }
    TEXT_GOTOMAIN();
}
else
{
    echo "<br>" . $l_corpm_exploit . "<br>";
    TEXT_GOTOMAIN();
}

include "footer.php";
?>
