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
// File: classes/BntPlayer.php

if (strpos ($_SERVER['PHP_SELF'], 'BntPlayer.php')) // Prevent direct access to this file
{
    $error_file = $_SERVER['SCRIPT_NAME'];
    include_once './error.php';
}

class BntPlayer
{
    static function kill ($db, $ship_id, $remove_planets = false, $langvars)
    {
        $resa = $db->Execute("UPDATE {$db->prefix}ships SET ship_destroyed='Y', on_planet='N', sector=0, cleared_defences=' ' WHERE ship_id=?", array ($ship_id));
        DbOp::dbResult ($db, $resa, __LINE__, __FILE__);
        $resb = $db->Execute("DELETE FROM {$db->prefix}bounty WHERE placed_by = ?", array ($ship_id));
        DbOp::dbResult ($db, $resb, __LINE__, __FILE__);

        $res = $db->Execute("SELECT DISTINCT sector_id FROM {$db->prefix}planets WHERE owner=? AND base='Y'", array ($ship_id));
        DbOp::dbResult ($db, $res, __LINE__, __FILE__);
        $i = 0;

        $sectors = null;

        if ($res instanceof ADORecordSet)
        {
            while (!$res->EOF && $res)
            {
                $sectors[$i] = $res->fields['sector_id'];
                $i++;
                $res->MoveNext();
            }
        }

        if ($remove_planets == true && $ship_id > 0)
        {
            $resc = $db->Execute("DELETE FROM {$db->prefix}planets WHERE owner = ?", array ($ship_id));
            DbOp::dbResult ($db, $resc, __LINE__, __FILE__);
        }
        else
        {
            $resd = $db->Execute("UPDATE {$db->prefix}planets SET owner=0, corp=0, fighters=0, base='N' WHERE owner=?", array ($ship_id));
            DbOp::dbResult ($db, $resd, __LINE__, __FILE__);
        }

        if (!empty($sectors))
        {
            foreach ($sectors as $sector)
            {
                BntOwnership::calc ($db, $sector, $min_bases_to_own, $langvars);
            }
        }

        $rese = $db->Execute("DELETE FROM {$db->prefix}sector_defence WHERE ship_id=?", array ($ship_id));
        DbOp::dbResult ($db, $rese, __LINE__, __FILE__);

        $res = $db->Execute("SELECT zone_id FROM {$db->prefix}zones WHERE corp_zone='N' AND owner=?", array ($ship_id));
        DbOp::dbResult ($db, $res, __LINE__, __FILE__);
        $zone = $res->fields;

        $resf = $db->Execute("UPDATE {$db->prefix}universe SET zone_id=1 WHERE zone_id=?", array ($zone['zone_id']));
        DbOp::dbResult ($db, $resf, __LINE__, __FILE__);

        $query = $db->Execute("SELECT character_name FROM {$db->prefix}ships WHERE ship_id=?", array ($ship_id));
        DbOp::dbResult ($db, $query, __LINE__, __FILE__);
        $name = $query->fields;

        $headline = $name['character_name'] ." ". $langvars['l_killheadline'];

        $newstext = str_replace("[name]", $name['character_name'], $langvars['l_news_killed']);

        $news = $db->Execute("INSERT INTO {$db->prefix}news (headline, newstext, user_id, date, news_type) VALUES (?,?,?,NOW(), 'killed')", array ($headline, $newstext, $ship_id));
        DbOp::dbResult ($db, $news, __LINE__, __FILE__);
    }

	function getInsignia ($db, $a_username, $langvars)
	{
    	unset ($player_insignia);

	    // Lookup players score.
    	$res = $db->Execute ("SELECT score FROM {$db->prefix}ships WHERE email=?", array ($a_username));
    	DbOp::dbResult ($db, $res, __LINE__, __FILE__);
	    $playerinfo = $res->fields;

	    for ($i = 0; $i < 20; $i++)
    	{
        	$value = pow (2, $i*2 );
	        if (!$value)
    	    {
        	    // Pow returned false so we need to return an error.
            	$player_insignia = "<span style='color:#f00;'>ERR</span> [<span style='color:#09f; font-size:12px; cursor:help;' title='Error looking up insignia, please report this error.'>?</span>]";
	            break;
    	    }

        	$value *= (500 * 2);
	        if ($playerinfo['score'] <= $value)
    	    {
        	    // Ok we have found our Insignia, now set and break out of the for loop.
            	$temp_insignia = "l_insignia_" . $i;
	            $player_insignia = $langvars[$temp_insignia];
    	        break;
        	}
    	}

    	if (!isset ($player_insignia))
    	{
        	// Hmm, player has out ranked out highest rank, so just return that.
	        $player_insignia = $langvars['l_insignia_19'];
    	}
	    return $player_insignia;
	}
}
?>
