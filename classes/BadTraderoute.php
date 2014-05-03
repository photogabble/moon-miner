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
// File: classes/BadTraderoute.php
//
// TODO: These are horribly bad. But in the interest of saying goodbye to the includes directory, and raw functions, this
// will at least allow us to auto-load and use classes instead. Plenty to do in the future, though!

if (strpos ($_SERVER['PHP_SELF'], 'BadTraderoute.php')) // Prevent direct access to this file
{
    $error_file = $_SERVER['SCRIPT_NAME'];
    include_once './error.php';
}

class BadTraderoute
{
    public static function traderouteEngage($db, $lang, $j, $langvars)
    {
        global $playerinfo, $color_line1, $color_line2, $color_header;
        global $engage, $dist, $servertimezone;
        global $color_line2;
        global $color_line1;
        global $traderoutes;
        global $fighter_price;
        global $torpedo_price;
        global $colonist_price;
        global $colonist_limit;
        global $inventory_factor;
        global $ore_price;
        global $ore_delta;
        global $ore_limit;
        global $organics_price;
        global $organics_delta;
        global $organics_limit;
        global $goods_price;
        global $goods_delta;
        global $goods_limit;
        global $energy_price;
        global $energy_delta;
        global $energy_limit;
        global $mine_hullsize;
        global $portfull;
        global $level_factor;

        foreach ($traderoutes as $testroute)
        {
            if ($testroute['traderoute_id'] == $engage)
            {
                $traderoute = $testroute;
            }
        }

        if (!isset ($traderoute))
        {
            traderoute_die ($db, $lang, $langvars, $bntreg, $langvars['l_tdr_engagenonexist'], $template);
        }

        if ($traderoute['owner'] != $playerinfo['ship_id'])
        {
            traderoute_die ($db, $lang, $langvars, $bntreg, $langvars['l_tdr_notowntdr'], $template);
        }

        // Source Check
        if ($traderoute['source_type'] == 'P')
        {
            // Retrieve port info here, we'll need it later anyway
            $result = $db->Execute ("SELECT * FROM {$db->prefix}universe WHERE sector_id=?", array ($traderoute['source_id']));
            BntDb::logDbErrors ($db, $result, __LINE__, __FILE__);

            if (!$result || $result->EOF)
            {
                traderoute_die ($db, $lang, $langvars, $bntreg, $langvars['l_tdr_invalidspoint'], $template);
            }

            $source = $result->fields;

            if ($traderoute['source_id'] != $playerinfo['sector'])
            {
                $langvars['l_tdr_inittdr'] = str_replace ("[tdr_source_id]", $traderoute['source_id'], $langvars['l_tdr_inittdr']);
                traderoute_die ($db, $lang, $langvars, $bntreg, $langvars['l_tdr_inittdr'], $template);
            }
        }
        elseif ($traderoute['source_type'] == 'L' || $traderoute['source_type'] == 'C')  // Get data from planet table
        {
            $result = $db->Execute ("SELECT * FROM {$db->prefix}planets WHERE planet_id=? AND (owner = ? OR (corp <> 0 AND corp = ?));", array ($traderoute['source_id'], $playerinfo['ship_id'], $playerinfo['team']));
            BntDb::logDbErrors ($db, $result, __LINE__, __FILE__);
            if (!$result || $result->EOF)
            {
                traderoute_die ($db, $lang, $langvars, $bntreg, $langvars['l_tdr_invalidsrc'], $template);
            }

            $source = $result->fields;

            if ($source['sector_id'] != $playerinfo['sector'])
            {
                // Check for valid Owned Source Planet
                // $langvars['l_tdr_inittdrsector'] = str_replace ("[tdr_source_sector_id]", $source['sector_id'], $langvars['l_tdr_inittdrsector']);
                // traderoute_die ($db, $lang, $langvars, $bntreg, $langvars['l_tdr_inittdrsector'], $template);
                traderoute_die ($db, $lang, $langvars, $bntreg, 'You must be in starting sector before you initiate a trade route!', $template);
            }

            if ($traderoute['source_type'] == 'L')
            {
                if ($source['owner'] != $playerinfo['ship_id'])
                {
                    // $langvars['l_tdr_notyourplanet'] = str_replace ("[tdr_source_name]", $source[name], $langvars['l_tdr_notyourplanet']);
                    // $langvars['l_tdr_notyourplanet'] = str_replace ("[tdr_source_sector_id]", $source[sector_id], $langvars['l_tdr_notyourplanet']);
                    // traderoute_die ($db, $lang, $langvars, $bntreg, $langvars['l_tdr_notyourplanet'], $template);
                    traderoute_die ($db, $lang, $langvars, $bntreg, $langvars['l_tdr_invalidsrc'], $template);
                }
            }
            elseif ($traderoute['source_type'] == 'C')   // Check to make sure player and planet are in the same corp.
            {
                if ($source['corp'] != $playerinfo['team'])
                {
                    // $langvars['l_tdr_notyourplanet'] = str_replace ("[tdr_source_name]", $source[name], $langvars['l_tdr_notyourplanet']);
                    // $langvars['l_tdr_notyourplanet'] = str_replace ("[tdr_source_sector_id]", $source[sector_id], $langvars['l_tdr_notyourplanet']);
                    // $not_corp_planet = "$source[name] in $source[sector_id] not a Copporate Planet";
                    // traderoute_die ($db, $lang, $langvars, $bntreg, $not_corp_planet, $template);
                    traderoute_die ($db, $lang, $langvars, $bntreg, $langvars['l_tdr_invalidsrc'], $template);
                }
            }

            // Store starting port info, we'll need it later
            $result = $db->Execute ("SELECT * FROM {$db->prefix}universe WHERE sector_id=?", array ($source['sector_id']));
            BntDb::logDbErrors ($db, $result, __LINE__, __FILE__);

            if (!$result || $result->EOF)
            {
                traderoute_die ($db, $lang, $langvars, $bntreg, $langvars['l_tdr_invalidssector'], $template);
            }

            $sourceport = $result->fields;
        }

        // Destination Check
        if ($traderoute['dest_type'] == 'P')
        {
            $result = $db->Execute ("SELECT * FROM {$db->prefix}universe WHERE sector_id=?", array ($traderoute['dest_id']));
            BntDb::logDbErrors ($db, $result, __LINE__, __FILE__);

            if (!$result || $result->EOF)
            {
                traderoute_die ($db, $lang, $langvars, $bntreg, $langvars['l_tdr_invaliddport'], $template);
            }

            $dest = $result->fields;
        }
        elseif (($traderoute['dest_type'] == 'L') || ($traderoute['dest_type'] == 'C'))  // Get data from planet table
        {
            // Check for valid Owned Source Planet
            // This now only returns Planets that the player owns or planets that belong to the team and set as corp planets..
            $result = $db->Execute ("SELECT * FROM {$db->prefix}planets WHERE planet_id=? AND (owner = ? OR (corp <> 0 AND corp = ?));", array ($traderoute['dest_id'], $playerinfo['ship_id'], $playerinfo['team']));
            BntDb::logDbErrors ($db, $result, __LINE__, __FILE__);

            if (!$result || $result->EOF)
            {
                traderoute_die ($db, $lang, $langvars, $bntreg, $langvars['l_tdr_invaliddplanet'], $template);
            }

            $dest = $result->fields;

            if ($traderoute['dest_type'] == 'L')
            {
                if ($dest['owner'] != $playerinfo['ship_id'])
                {
                    $langvars['l_tdr_notyourplanet'] = str_replace ("[tdr_source_name]", $dest['name'], $langvars['l_tdr_notyourplanet']);
                    $langvars['l_tdr_notyourplanet'] = str_replace ("[tdr_source_sector_id]", $dest['sector_id'], $langvars['l_tdr_notyourplanet']);
                    traderoute_die ($db, $lang, $langvars, $bntreg, $langvars['l_tdr_notyourplanet'], $template);
                }
            }
            elseif ($traderoute['dest_type'] == 'C')   // Check to make sure player and planet are in the same corp.
            {
                if ($dest['corp'] != $playerinfo['team'])
                {
                    $langvars['l_tdr_notyourplanet'] = str_replace ("[tdr_source_name]", $dest['name'], $langvars['l_tdr_notyourplanet']);
                    $langvars['l_tdr_notyourplanet'] = str_replace ("[tdr_source_sector_id]", $dest['sector_id'], $langvars['l_tdr_notyourplanet']);
                    traderoute_die ($db, $lang, $langvars, $bntreg, $langvars['l_tdr_notyourplanet'], $template);
                }
            }

            $result = $db->Execute ("SELECT * FROM {$db->prefix}universe WHERE sector_id=?", array ($dest['sector_id']));
            BntDb::logDbErrors ($db, $result, __LINE__, __FILE__);
            if (!$result || $result->EOF)
            {
                traderoute_die ($db, $lang, $langvars, $bntreg, $langvars['l_tdr_invaliddsector'], $template);
            }

            $destport = $result->fields;
        }

        if (!isset ($sourceport))
        {
            $sourceport= $source;
        }

        if (!isset ($destport))
        {
            $destport= $dest;
        }

        // Warp or RealSpace and generate distance
        if ($traderoute['move_type'] == 'W')
        {
            $query = $db->Execute ("SELECT link_id FROM {$db->prefix}links WHERE link_start=? AND link_dest=?", array ($source['sector_id'], $dest['sector_id']));
            BntDb::logDbErrors ($db, $query, __LINE__, __FILE__);
            if ($query->EOF)
            {
                $langvars['l_tdr_nowlink1'] = str_replace ("[tdr_src_sector_id]", $source['sector_id'], $langvars['l_tdr_nowlink1']);
                $langvars['l_tdr_nowlink1'] = str_replace ("[tdr_dest_sector_id]", $dest['sector_id'], $langvars['l_tdr_nowlink1']);
                traderoute_die ($db, $lang, $langvars, $bntreg, $langvars['l_tdr_nowlink1'], $template);
            }

            if ($traderoute['circuit'] == '2')
            {
                $query = $db->Execute ("SELECT link_id FROM {$db->prefix}links WHERE link_start=? AND link_dest=?", array ($dest['sector_id'], $source['sector_id']));
                BntDb::logDbErrors ($db, $query, __LINE__, __FILE__);
                if ($query->EOF)
                {
                    $langvars['l_tdr_nowlink2'] = str_replace ("[tdr_src_sector_id]", $source['sector_id'], $langvars['l_tdr_nowlink2']);
                    $langvars['l_tdr_nowlink2'] = str_replace ("[tdr_dest_sector_id]", $dest['sector_id'], $langvars['l_tdr_nowlink2']);
                    traderoute_die ($db, $lang, $langvars, $bntreg, $langvars['l_tdr_nowlink2'], $template);
                }
                $dist['triptime'] = 4;
            }
            else
            {
                $dist['triptime'] = 2;
            }

            $dist['scooped'] = 0;
            $dist['scooped1'] = 0;
            $dist['scooped2'] = 0;
        }
        else
        {
            $dist = traderoute_distance($db, 'P', 'P', $sourceport, $destport, $traderoute['circuit']);
        }

        // Check if player has enough turns
        if ($playerinfo['turns'] < $dist['triptime'])
        {
            $langvars['l_tdr_moreturnsneeded'] = str_replace ("[tdr_dist_triptime]", $dist['triptime'], $langvars['l_tdr_moreturnsneeded']);
            $langvars['l_tdr_moreturnsneeded'] = str_replace ("[tdr_playerinfo_turns]", $playerinfo['turns'], $langvars['l_tdr_moreturnsneeded']);
            traderoute_die ($db, $lang, $langvars, $bntreg, $langvars['l_tdr_moreturnsneeded'], $template);
        }

        // Sector Defense Check
        $hostile = 0;

        $result99 = $db->Execute ("SELECT * FROM {$db->prefix}sector_defence WHERE sector_id = ? AND ship_id <> ?", array ($source['sector_id'], $playerinfo['ship_id']));
        BntDb::logDbErrors ($db, $result99, __LINE__, __FILE__);
        if (!$result99->EOF)
        {
            $fighters_owner = $result99->fields;
            $nsresult = $db->Execute ("SELECT * FROM {$db->prefix}ships WHERE ship_id=?", array ($fighters_owner['ship_id']));
            BntDb::logDbErrors ($db, $nsresult, __LINE__, __FILE__);
            $nsfighters = $nsresult->fields;

            if ($nsfighters['team'] != $playerinfo['team'] || $playerinfo['team']==0)
            {
                $hostile = 1;
            }
        }

        $result98 = $db->Execute ("SELECT * FROM {$db->prefix}sector_defence WHERE sector_id = ? AND ship_id <> ?", array ($dest['sector_id'], $playerinfo['ship_id']));
        BntDb::logDbErrors ($db, $result98, __LINE__, __FILE__);
        if (!$result98->EOF)
        {
            $fighters_owner = $result98->fields;
            $nsresult = $db->Execute ("SELECT * FROM {$db->prefix}ships WHERE ship_id=?", array ($fighters_owner['ship_id']));
            BntDb::logDbErrors ($db, $nsresult, __LINE__, __FILE__);
            $nsfighters = $nsresult->fields;

            if ($nsfighters['team'] != $playerinfo['team'] || $playerinfo['team']==0)
            {
                $hostile = 1;
            }
        }

        if ($hostile > 0 && $playerinfo['hull'] > $mine_hullsize)
        {
            traderoute_die ($db, $lang, $langvars, $bntreg, $langvars['l_tdr_hostdef'], $template);
        }

        // Special Port Nothing to do
        if ($traderoute['source_type'] == 'P' && $source['port_type'] == 'special' && $playerinfo['trade_colonists'] == 'N' && $playerinfo['trade_fighters'] == 'N' && $playerinfo['trade_torps'] == 'N')
        {
            traderoute_die ($db, $lang, $langvars, $bntreg, $langvars['l_tdr_globalsetbuynothing'], $template);
        }

        // Check if zone allows trading  SRC
        if ($traderoute['source_type'] == 'P')
        {
            $res = $db->Execute ("SELECT * FROM {$db->prefix}zones,{$db->prefix}universe WHERE {$db->prefix}universe.sector_id=? AND {$db->prefix}zones.zone_id={$db->prefix}universe.zone_id;", array ($traderoute['source_id']));
            BntDb::logDbErrors ($db, $res, __LINE__, __FILE__);
            $zoneinfo = $res->fields;
            if ($zoneinfo['allow_trade'] == 'N')
            {
                traderoute_die ($db, $lang, $langvars, $bntreg, $langvars['l_tdr_nosrcporttrade'], $template);
            }
            elseif ($zoneinfo['allow_trade'] == 'L')
            {
                if ($zoneinfo['corp_zone'] == 'N')
                {
                    $res = $db->Execute ("SELECT team FROM {$db->prefix}ships WHERE ship_id=?", array ($zoneinfo['owner']));
                    BntDb::logDbErrors ($db, $res, __LINE__, __FILE__);
                    $ownerinfo = $res->fields;

                    if ($playerinfo['ship_id'] != $zoneinfo['owner'] && $playerinfo['team'] == 0 || $playerinfo['team'] != $ownerinfo['team'])
                    {
                        traderoute_die ($db, $lang, $langvars, $bntreg, $langvars['l_tdr_tradesrcportoutsider'], $template);
                    }
                }
                else
                {
                    if ($playerinfo['team'] != $zoneinfo['owner'])
                    {
                        traderoute_die ($db, $lang, $langvars, $bntreg, $langvars['l_tdr_tradesrcportoutsider'], $template);
                    }
                }
            }
        }

        // Check if zone allows trading  DEST
        if ($traderoute['dest_type'] == 'P')
        {
            $res = $db->Execute ("SELECT * FROM {$db->prefix}zones,{$db->prefix}universe WHERE {$db->prefix}universe.sector_id=? AND {$db->prefix}zones.zone_id={$db->prefix}universe.zone_id;", array ($traderoute['dest_id']));
            BntDb::logDbErrors ($db, $res, __LINE__, __FILE__);
            $zoneinfo = $res->fields;
            if ($zoneinfo['allow_trade'] == 'N')
            {
                traderoute_die ($db, $lang, $langvars, $bntreg, $langvars['l_tdr_nodestporttrade'], $template);
            }
            elseif ($zoneinfo['allow_trade'] == 'L')
            {
                if ($zoneinfo['corp_zone'] == 'N')
                {
                    $res = $db->Execute ("SELECT team FROM {$db->prefix}ships WHERE ship_id=?", array ($zoneinfo['owner']));
                    BntDb::logDbErrors ($db, $res, __LINE__, __FILE__);
                    $ownerinfo = $res->fields;

                    if ($playerinfo['ship_id'] != $zoneinfo['owner'] && $playerinfo['team'] == 0 || $playerinfo['team'] != $ownerinfo['team'])
                    {
                        traderoute_die ($db, $lang, $langvars, $bntreg, $langvars['l_tdr_tradedestportoutsider'], $template);
                    }
                }
                else
                {
                    if ($playerinfo['team'] != $zoneinfo['owner'])
                    {
                        traderoute_die ($db, $lang, $langvars, $bntreg, $langvars['l_tdr_tradedestportoutsider'], $template);
                    }
                }
            }
        }

        traderoute_results_table_top ($db, $lang, $langvars);
        // Determine if Source is Planet or Port
        if ($traderoute['source_type'] == 'P')
        {
            echo $langvars['l_tdr_portin'] . " " . $source['sector_id'];
        }
        elseif (($traderoute['source_type'] == 'L') || ($traderoute['source_type'] == 'C'))
        {
            echo $langvars['l_tdr_planet'] . " " . $source['name'] . " in " . $sourceport['sector_id'];
        }
        traderoute_results_source();

        // Determine if Destination is Planet or Port
        if ($traderoute['dest_type'] == 'P')
        {
            echo $langvars['l_tdr_portin'] . " " . $dest['sector_id'];
        }
        elseif (($traderoute['dest_type'] == 'L') || ($traderoute['dest_type'] == 'C'))
        {
            echo $langvars['l_tdr_planet'] . " " . $dest['name'] . " in " . $destport['sector_id'];
        }
        traderoute_results_destination();

        $sourcecost=0;

        // Source is Port
        if ($traderoute['source_type'] == 'P')
        {
            // Special Port Section (begin)
            if ($source['port_type'] == 'special')
            {
                $ore_buy = 0;
                $goods_buy = 0;
                $organics_buy = 0;
                $energy_buy = 0;

                $total_credits = $playerinfo['credits'];

                if ($playerinfo['trade_colonists'] == 'Y')
                {
                    $free_holds = BntCalcLevels::Holds ($playerinfo['hull'], $level_factor) - $playerinfo['ship_ore'] - $playerinfo['ship_organics'] - $playerinfo['ship_goods'] - $playerinfo['ship_colonists'];
                    $colonists_buy = $free_holds;

                    if ($playerinfo['credits'] < $colonist_price * $colonists_buy)
                    {
                        $colonists_buy = $playerinfo['credits'] / $colonist_price;
                    }

                    if ($colonists_buy != 0)
                    {
                        echo $langvars['l_tdr_bought'] . " " . number_format ($colonists_buy, 0, $langvars['local_number_dec_point'], $langvars['local_number_thousands_sep']) . " " . $langvars['l_tdr_colonists'] . "<br>";
                    }

                    $sourcecost-= $colonists_buy * $colonist_price;
                    $total_credits-= $colonists_buy * $colonist_price;
                }
                else
                {
                    $colonists_buy = 0;
                }

                if ($playerinfo['trade_fighters'] == 'Y')
                {
                    $free_fighters = BntCalcLevels::Fighters ($playerinfo['computer'], $level_factor) - $playerinfo['ship_fighters'];
                    $fighters_buy = $free_fighters;

                    if ($total_credits < $fighters_buy * $fighter_price)
                    {
                        $fighters_buy = $total_credits / $fighter_price;
                    }

                    if ($fighters_buy != 0)
                    {
                        echo $langvars['l_tdr_bought'] . " " . number_format ($fighters_buy, 0, $langvars['local_number_dec_point'], $langvars['local_number_thousands_sep']) . " " . $langvars['l_tdr_fighters'] . "<br>";
                    }

                    $sourcecost-= $fighters_buy * $fighter_price;
                    $total_credits-= $fighters_buy * $fighter_price;
                }
                else
                {
                    $fighters_buy = 0;
                }

                if ($playerinfo['trade_torps'] == 'Y')
                {
                    $free_torps = BntCalcLevels::Fighters ($playerinfo['torp_launchers'], $level_factor) - $playerinfo['torps'];
                    $torps_buy = $free_torps;

                    if ($total_credits < $torps_buy * $torpedo_price)
                    {
                        $torps_buy = $total_credits / $torpedo_price;
                    }

                    if ($torps_buy != 0)
                    {
                        echo $langvars['l_tdr_bought'] . " " . number_format ($torps_buy, 0, $langvars['local_number_dec_point'], $langvars['local_number_thousands_sep']) . " " . $langvars['l_tdr_torps'] . "<br>";
                    }

                    $sourcecost-= $torps_buy * $torpedo_price;
                }
                else
                {
                    $torps_buy = 0;
                }

                if ($torps_buy == 0 && $colonists_buy == 0 && $fighters_buy == 0)
                {
                    echo $langvars['l_tdr_nothingtotrade'] . "<br>";
                }

                if ($traderoute['circuit'] == '1')
                {
                    $resb = $db->Execute ("UPDATE {$db->prefix}ships SET ship_colonists=ship_colonists+?, ship_fighters=ship_fighters+?,torps=torps+?, ship_energy=ship_energy+? WHERE ship_id=?", array ($colonists_buy, $fighters_buy, $torps_buy, $dist['scooped1'], $playerinfo['ship_id']));
                    BntDb::logDbErrors ($db, $resb, __LINE__, __FILE__);
                }
            }
            // Normal Port Section
            else
            {
                // Sells commodities
                // Added below initializations, for traderoute bug
                $ore_buy = 0;
                $goods_buy = 0;
                $organics_buy = 0;
                $energy_buy = 0;

                if ($source['port_type'] != 'ore')
                {
                    $ore_price1 = $ore_price + $ore_delta * $source['port_ore'] / $ore_limit * $inventory_factor;
                    if ($source['port_ore'] - $playerinfo['ship_ore'] < 0)
                    {
                        $ore_buy = $source['port_ore'];
                        $portfull = 1;
                    }
                    else
                    {
                        $ore_buy = $playerinfo['ship_ore'];
                    }

                    $sourcecost += $ore_buy * $ore_price1;
                    if ($ore_buy != 0)
                    {
                        if ($portfull == 1)
                        {
                            echo $langvars['l_tdr_sold'] . " " . number_format ($ore_buy, 0, $langvars['local_number_dec_point'], $langvars['local_number_thousands_sep']) . " " . $langvars['l_tdr_ore'] . " (" . $langvars['l_tdr_portisfull'] . ")<br>";
                        }
                        else
                        {
                            echo $langvars['l_tdr_sold'] . " " . number_format ($ore_buy, 0, $langvars['local_number_dec_point'], $langvars['local_number_thousands_sep']) . " " . $langvars['l_tdr_ore'] . "<br>";
                        }
                    }
                    $playerinfo['ship_ore'] -= $ore_buy;
                }

                $portfull = 0;
                if ($source['port_type'] != 'goods')
                {
                    $goods_price1 = $goods_price + $goods_delta * $source['port_goods'] / $goods_limit * $inventory_factor;
                    if ($source['port_goods'] - $playerinfo['ship_goods'] < 0)
                    {
                        $goods_buy = $source['port_goods'];
                        $portfull = 1;
                    }
                    else
                    {
                        $goods_buy = $playerinfo['ship_goods'];
                    }

                    $sourcecost += $goods_buy * $goods_price1;
                    if ($goods_buy != 0)
                    {
                        if ($portfull == 1)
                        {
                            echo $langvars['l_tdr_sold'] . " " . number_format ($goods_buy, 0, $langvars['local_number_dec_point'], $langvars['local_number_thousands_sep']) . " " . $langvars['l_tdr_goods'] . " (" . $langvars['l_tdr_portisfull'] . ")<br>";
                        }
                        else
                        {
                            echo $langvars['l_tdr_sold'] . " " . number_format ($goods_buy, 0, $langvars['local_number_dec_point'], $langvars['local_number_thousands_sep']) . " " . $langvars['l_tdr_goods'] . "<br>";
                        }
                    }
                    $playerinfo['ship_goods'] -= $goods_buy;
                }

                $portfull = 0;
                if ($source['port_type'] != 'organics')
                {
                    $organics_price1 = $organics_price + $organics_delta * $source['port_organics'] / $organics_limit * $inventory_factor;
                    if ($source['port_organics'] - $playerinfo['ship_organics'] < 0)
                    {
                        $organics_buy = $source['port_organics'];
                        $portfull = 1;
                    }
                    else
                    {
                        $organics_buy = $playerinfo['ship_organics'];
                    }

                    $sourcecost += $organics_buy * $organics_price1;
                    if ($organics_buy != 0)
                    {
                        if ($portfull == 1)
                        {
                            echo $langvars['l_tdr_sold'] . " " . number_format ($organics_buy, 0, $langvars['local_number_dec_point'], $langvars['local_number_thousands_sep']) . " " . $langvars['l_tdr_organics'] . " (" . $langvars['l_tdr_portisfull'] . ")<br>";
                        }
                        else
                        {
                            echo $langvars['l_tdr_sold'] . " " . number_format ($organics_buy, 0, $langvars['local_number_dec_point'], $langvars['local_number_thousands_sep']) . " " . $langvars['l_tdr_organics'] . "<br>";
                        }
                    }
                    $playerinfo['ship_organics'] -= $organics_buy;
                }

                $portfull = 0;
                if ($source['port_type'] != 'energy' && $playerinfo['trade_energy'] == 'Y')
                {
                    $energy_price1 = $energy_price + $energy_delta * $source['port_energy'] / $energy_limit * $inventory_factor;
                    if ($source['port_energy'] - $playerinfo['ship_energy'] < 0)
                    {
                        $energy_buy = $source['port_energy'];
                        $portfull = 1;
                    }
                    else
                    {
                        $energy_buy = $playerinfo['ship_energy'];
                    }
                    $sourcecost += $energy_buy * $energy_price1;
                    if ($energy_buy != 0)
                    {
                        if ($portfull == 1)
                        {
                            echo $langvars['l_tdr_sold'] . " " . number_format ($energy_buy, 0, $langvars['local_number_dec_point'], $langvars['local_number_thousands_sep']) . " " . $langvars['l_tdr_energy'] . " (" . $langvars['l_tdr_portisfull'] . ")<br>";
                        }
                        else
                        {
                            echo $langvars['l_tdr_sold'] . " " . number_format ($energy_buy, 0, $langvars['local_number_dec_point'], $langvars['local_number_thousands_sep']) . " " . $langvars['l_tdr_energy'] . "<br>";
                        }
                    }
                    $playerinfo['ship_energy'] -= $energy_buy;
                }

                $free_holds = BntCalcLevels::Holds ($playerinfo['hull'], $level_factor) - $playerinfo['ship_ore'] - $playerinfo['ship_organics'] - $playerinfo['ship_goods'] - $playerinfo['ship_colonists'];

                // Time to buy
                if ($source['port_type'] == 'ore')
                {
                    $ore_price1 = $ore_price - $ore_delta * $source['port_ore'] / $ore_limit * $inventory_factor;
                    $ore_buy = $free_holds;
                    if ($playerinfo['credits'] + $sourcecost < $ore_buy * $ore_price1)
                    {
                        $ore_buy = ($playerinfo['credits'] + $sourcecost) / $ore_price1;
                    }

                    if ($source['port_ore'] < $ore_buy)
                    {
                        $ore_buy = $source['port_ore'];
                        if ($source['port_ore'] == 0)
                        {
                            echo $langvars['l_tdr_bought'] . " " . number_format ($ore_buy, 0, $langvars['local_number_dec_point'], $langvars['local_number_thousands_sep']) . " " . $langvars['l_tdr_ore'] . "(" . $langvars['l_tdr_portisempty'] . ")<br>";
                        }
                    }

                    if ($ore_buy != 0)
                    {
                        echo $langvars['l_tdr_bought'] . " " . number_format ($ore_buy, 0, $langvars['local_number_dec_point'], $langvars['local_number_thousands_sep']) . " " . $langvars['l_tdr_ore'] . "<br>";
                    }
                    $playerinfo['ship_ore'] += $ore_buy;
                    $sourcecost -= $ore_buy * $ore_price1;
                    $resc = $db->Execute ("UPDATE {$db->prefix}universe SET port_ore=port_ore-?, port_energy=port_energy-?, port_goods=port_goods-?, port_organics=port_organics-? WHERE sector_id=?", array ($ore_buy, $energy_buy, $goods_buy, $organics_buy, $source['sector_id']));
                    BntDb::logDbErrors ($db, $resc, __LINE__, __FILE__);
                }

                if ($source['port_type'] == 'goods')
                {
                    $goods_price1 = $goods_price - $goods_delta * $source['port_goods'] / $goods_limit * $inventory_factor;
                    $goods_buy = $free_holds;
                    if ($playerinfo['credits'] + $sourcecost < $goods_buy * $goods_price1)
                    {
                        $goods_buy = ($playerinfo['credits'] + $sourcecost) / $goods_price1;
                    }

                    if ($source['port_goods'] < $goods_buy)
                    {
                        $goods_buy = $source['port_goods'];
                        if ($source['port_goods'] == 0)
                        {
                            echo $langvars['l_tdr_bought'] . " " . number_format ($goods_buy, 0, $langvars['local_number_dec_point'], $langvars['local_number_thousands_sep']) . " " . $langvars['l_tdr_goods'] . " (" . $langvars['l_tdr_portisempty'] . ")<br>";
                        }
                    }

                    if ($goods_buy != 0)
                    {
                        echo $langvars['l_tdr_bought'] . " " . number_format ($goods_buy, 0, $langvars['local_number_dec_point'], $langvars['local_number_thousands_sep']) . " " . $langvars['l_tdr_goods'] . "<br>";
                    }

                    $playerinfo['ship_goods'] += $goods_buy;
                    $sourcecost -= $goods_buy * $goods_price1;

                    $resd = $db->Execute ("UPDATE {$db->prefix}universe SET port_ore=port_ore-?, port_energy=port_energy-?, port_goods=port_goods-?, port_organics=port_organics-? WHERE sector_id=?", array ($ore_buy, $energy_buy, $goods_buy, $organics_buy, $source['sector_id']));
                    BntDb::logDbErrors ($db, $resd, __LINE__, __FILE__);
                }

                if ($source['port_type'] == 'organics')
                {
                    $organics_price1 = $organics_price - $organics_delta * $source['port_organics'] / $organics_limit * $inventory_factor;
                    $organics_buy = $free_holds;

                    if ($playerinfo['credits'] + $sourcecost < $organics_buy * $organics_price1)
                    {
                        $organics_buy = ($playerinfo['credits'] + $sourcecost) / $organics_price1;
                    }

                    if ($source['port_organics'] < $organics_buy)
                    {
                        $organics_buy = $source['port_organics'];
                        if ($source['port_organics'] == 0)
                        {
                            echo $langvars['l_tdr_bought'] . " " . number_format ($organics_buy, 0, $langvars['local_number_dec_point'], $langvars['local_number_thousands_sep']) . " " . $langvars['l_tdr_organics'] . " (" . $langvars['l_tdr_portisempty'] . ")<br>";
                        }
                    }

                    if ($organics_buy != 0)
                    {
                        echo $langvars['l_tdr_bought'] . " " . number_format ($organics_buy, 0, $langvars['local_number_dec_point'], $langvars['local_number_thousands_sep']) . " " . $langvars['l_tdr_organics'] . "<br>";
                    }

                    $playerinfo['ship_organics'] += $organics_buy;
                    $sourcecost -= $organics_buy * $organics_price1;
                    $rese = $db->Execute ("UPDATE {$db->prefix}universe SET port_ore=port_ore-?, port_energy=port_energy-?, port_goods=port_goods-?, port_organics=port_organics-? WHERE sector_id=?", array ($ore_buy, $energy_buy, $goods_buy, $organics_buy, $source['sector_id']));
                    BntDb::logDbErrors ($db, $rese, __LINE__, __FILE__);
                }

                if ($source['port_type'] == 'energy')
                {
                    $energy_price1 = $energy_price - $energy_delta * $source['port_energy'] / $energy_limit * $inventory_factor;
                    $energy_buy = BntCalcLevels::Energy ($playerinfo['power'], $level_factor) - $playerinfo['ship_energy'] - $dist['scooped1'];

                    if ($playerinfo['credits'] + $sourcecost < $energy_buy * $energy_price1)
                    {
                        $energy_buy = ($playerinfo['credits'] + $sourcecost) / $energy_price1;
                    }

                    if ($source['port_energy'] < $energy_buy)
                    {
                        $energy_buy = $source['port_energy'];
                        if ($source['port_energy'] == 0)
                        {
                            echo $langvars['l_tdr_bought'] . " " . number_format ($energy_buy, 0, $langvars['local_number_dec_point'], $langvars['local_number_thousands_sep']) . " " . $langvars['l_tdr_energy'] . " (" . $langvars['l_tdr_portisempty'] . ")<br>";
                        }
                    }

                    if ($energy_buy != 0)
                    {
                        echo $langvars['l_tdr_bought'] . " " . number_format ($energy_buy, 0, $langvars['local_number_dec_point'], $langvars['local_number_thousands_sep']) . " " . $langvars['l_tdr_energy'] . "<br>";
                    }
                    $playerinfo['ship_energy'] += $energy_buy;
                    $sourcecost -= $energy_buy * $energy_price1;
                    $resf = $db->Execute ("UPDATE {$db->prefix}universe SET port_ore=port_ore-?, port_energy=port_energy-?, port_goods=port_goods-?, port_organics=port_organics-? WHERE sector_id=?", array ($ore_buy, $energy_buy, $goods_buy, $organics_buy, $source['sector_id']));
                    BntDb::logDbErrors ($db, $resf, __LINE__, __FILE__);
                }

                if ($dist['scooped1'] > 0)
                {
                    $playerinfo['ship_energy']+= $dist['scooped1'];
                    if ($playerinfo['ship_energy'] > BntCalcLevels::Energy ($playerinfo['power'], $level_factor))
                    {
                        $playerinfo['ship_energy'] = BntCalcLevels::Energy ($playerinfo['power'], $level_factor);
                    }
                }

                if ($ore_buy == 0 && $goods_buy == 0 && $energy_buy == 0 && $organics_buy == 0)
                {
                    echo $langvars['l_tdr_nothingtotrade'] . "<br>";
                }

                if ($traderoute['circuit'] == '1')
                {
                    $resf = $db->Execute ("UPDATE {$db->prefix}ships SET ship_ore=?, ship_goods=?, ship_organics=?, ship_energy=? WHERE ship_id=?", array ($playerinfo['ship_ore'], $playerinfo['ship_goods'], $playerinfo['ship_organics'], $playerinfo['ship_energy'], $playerinfo['ship_id']));
                    BntDb::logDbErrors ($db, $resf, __LINE__, __FILE__);
                }
            }
        }
        // Source is planet
        elseif (($traderoute['source_type'] == 'L') || ($traderoute['source_type'] == 'C'))
        {
            $free_holds = BntCalcLevels::Holds ($playerinfo['hull'], $level_factor) - $playerinfo['ship_ore'] - $playerinfo['ship_organics'] - $playerinfo['ship_goods'] - $playerinfo['ship_colonists'];
            if ($traderoute['dest_type'] == 'P')
            {
                // Pick stuff up to sell at port
                if (($playerinfo['ship_id'] == $source['owner']) || ($playerinfo['team'] == $source['corp']))
                {
                    if ($source['goods'] > 0 && $free_holds > 0 && $dest['port_type'] != 'goods')
                    {
                        if ($source['goods'] > $free_holds)
                        {
                            $goods_buy = $free_holds;
                        }
                        else
                        {
                            $goods_buy = $source['goods'];
                        }

                        $free_holds -= $goods_buy;
                        $playerinfo['ship_goods'] += $goods_buy;
                        echo $langvars['l_tdr_loaded'] . " " . number_format ($goods_buy, 0, $langvars['local_number_dec_point'], $langvars['local_number_thousands_sep']) . " " . $langvars['l_tdr_goods'] . "<br>";
                    }
                    else
                    {
                        $goods_buy = 0;
                    }

                    if ($source['ore'] > 0 && $free_holds > 0 && $dest['port_type'] != 'ore')
                    {
                        if ($source['ore'] > $free_holds)
                        {
                            $ore_buy = $free_holds;
                        }
                        else
                        {
                            $ore_buy = $source['ore'];
                        }

                        $free_holds -= $ore_buy;
                        $playerinfo['ship_ore'] += $ore_buy;
                        echo $langvars['l_tdr_loaded'] . " " . number_format ($ore_buy, 0, $langvars['local_number_dec_point'], $langvars['local_number_thousands_sep']) . " " . $langvars['l_tdr_ore'] . "<br>";
                    }
                    else
                    {
                        $ore_buy = 0;
                    }

                    if ($source['organics'] > 0 && $free_holds > 0 && $dest['port_type'] != 'organics')
                    {
                        if ($source['organics'] > $free_holds)
                        {
                            $organics_buy = $free_holds;
                        }
                        else
                        {
                            $organics_buy = $source['organics'];
                        }

                        $free_holds -= $organics_buy;
                        $playerinfo['ship_organics'] += $organics_buy;
                        echo $langvars['l_tdr_loaded'] . " " . number_format ($organics_buy, 0, $langvars['local_number_dec_point'], $langvars['local_number_thousands_sep']) . " " . $langvars['l_tdr_organics'] . "<br>";
                    }
                    else
                    {
                        $organics_buy = 0;
                    }

                    if ($ore_buy == 0 && $goods_buy == 0 && $organics_buy == 0)
                    {
                        echo $langvars['l_tdr_nothingtoload'] . "<br>";
                    }

                    if ($traderoute['circuit'] == '1')
                    {
                        $resg = $db->Execute ("UPDATE {$db->prefix}ships SET ship_ore=?, ship_goods=?, ship_organics=? WHERE ship_id=?", array ($playerinfo['ship_ore'], $playerinfo['ship_goods'], $playerinfo['ship_organics'], $playerinfo['ship_id']));
                        BntDb::logDbErrors ($db, $resg, __LINE__, __FILE__);
                    }
                }
                else  // Buy from planet - not implemented yet
                {
                }

                $resh = $db->Execute ("UPDATE {$db->prefix}planets SET ore=ore-?, goods=goods-?, organics=organics-? WHERE planet_id=?", array ($ore_buy, $goods_buy, $organics_buy, $source['planet_id']));
                BntDb::logDbErrors ($db, $resh, __LINE__, __FILE__);
            }
            // Destination is a planet, so load cols and weapons
            elseif (($traderoute['dest_type'] == 'L') || ($traderoute['dest_type'] == 'C'))
            {
                if ($source['colonists'] > 0 && $free_holds > 0 && $playerinfo['trade_colonists'] == 'Y')
                {
                    if ($source['colonists'] > $free_holds)
                    {
                        $colonists_buy = $free_holds;
                    }
                    else
                    {
                        $colonists_buy = $source['colonists'];
                    }

                    $free_holds -= $colonists_buy;
                    $playerinfo['ship_colonists'] += $colonists_buy;
                    echo $langvars['l_tdr_loaded'] . " " . number_format ($colonists_buy, 0, $langvars['local_number_dec_point'], $langvars['local_number_thousands_sep']) . " " . $langvars['l_tdr_colonists'] . "<br>";
                }
                else
                {
                    $colonists_buy = 0;
                }

                $free_torps = BntCalcLevels::Torpedoes ($playerinfo['torp_launchers'], $level_factor) - $playerinfo['torps'];
                if ($source['torps'] > 0 && $free_torps > 0 && $playerinfo['trade_torps'] == 'Y')
                {
                    if ($source['torps'] > $free_torps)
                    {
                        $torps_buy = $free_torps;
                    }
                    else
                    {
                        $torps_buy = $source['torps'];
                    }

                    $free_torps -= $torps_buy;
                    $playerinfo['torps'] += $torps_buy;
                    echo $langvars['l_tdr_loaded'] . " " . number_format ($torps_buy, 0, $langvars['local_number_dec_point'], $langvars['local_number_thousands_sep']) . " " . $langvars['l_tdr_torps'] . "<br>";
                }
                else
                {
                    $torps_buy = 0;
                }

                $free_fighters = BntCalcLevels::Fighters ($playerinfo['computer'], $level_factor) - $playerinfo['ship_fighters'];
                if ($source['fighters'] > 0 && $free_fighters > 0 && $playerinfo['trade_fighters'] == 'Y')
                {
                    if ($source['fighters'] > $free_fighters)
                    {
                        $fighters_buy = $free_fighters;
                    }
                    else
                    {
                        $fighters_buy = $source['fighters'];
                    }

                    $free_fighters -= $fighters_buy;
                    $playerinfo['ship_fighters'] += $fighters_buy;
                    echo $langvars['l_tdr_loaded'] . " " . number_format ($fighters_buy, 0, $langvars['local_number_dec_point'], $langvars['local_number_thousands_sep']) . " " . $langvars['l_tdr_fighters'] . "<br>";
                }
                else
                {
                    $fighters_buy = 0;
                }

                if ($fighters_buy == 0 && $torps_buy == 0 && $colonists_buy == 0)
                {
                    echo $langvars['l_tdr_nothingtoload'] . "<br>";
                }

                if ($traderoute['circuit'] == '1')
                {
                    $resi = $db->Execute ("UPDATE {$db->prefix}ships SET torps=?, ship_fighters=?, ship_colonists=? WHERE ship_id=?", array ($playerinfo['torps'], $playerinfo['ship_fighters'], $playerinfo['ship_colonists'], $playerinfo['ship_id']));
                    BntDb::logDbErrors ($db, $resi, __LINE__, __FILE__);
                }

                $resj = $db->Execute ("UPDATE {$db->prefix}planets SET colonists=colonists-?, torps=torps-?, fighters=fighters-? WHERE planet_id=?", array ($colonists_buy, $torps_buy, $fighters_buy, $source['planet_id']));
                BntDb::logDbErrors ($db, $resj, __LINE__, __FILE__);
            }
        }

        if ($dist['scooped1'] != 0)
        {
            echo $langvars['l_tdr_scooped'] . " " . number_format ($dist['scooped1'], 0, $langvars['local_number_dec_point'], $langvars['local_number_thousands_sep']) . " " . $langvars['l_tdr_energy'] . "<br>";
        }

        traderoute_results_close_cell();

        if ($traderoute['circuit'] == '2')
        {
            $playerinfo['credits'] += $sourcecost;
            $destcost = 0;
            if ($traderoute['dest_type'] == 'P')
            {
                // Added the below for traderoute bug
                $ore_buy = 0;
                $goods_buy = 0;
                $organics_buy = 0;
                $energy_buy = 0;

                // Sells commodities
                $portfull = 0;
                if ($dest['port_type'] != 'ore')
                {
                    $ore_price1 = $ore_price + $ore_delta * $dest['port_ore'] / $ore_limit * $inventory_factor;
                    if ($dest['port_ore'] - $playerinfo['ship_ore'] < 0)
                    {
                        $ore_buy = $dest['port_ore'];
                        $portfull = 1;
                    }
                    else
                    {
                        $ore_buy = $playerinfo['ship_ore'];
                    }

                    $destcost += $ore_buy * $ore_price1;

                    if ($ore_buy != 0)
                    {
                        if ($portfull == 1)
                        {
                            echo $langvars['l_tdr_sold'] . " " . number_format ($ore_buy, 0, $langvars['local_number_dec_point'], $langvars['local_number_thousands_sep']) . " " . $langvars['l_tdr_ore'] . " (" . $langvars['l_tdr_portisfull'] . ")<br>";
                        }
                        else
                        {
                            echo $langvars['l_tdr_sold'] . " " . number_format ($ore_buy, 0, $langvars['local_number_dec_point'], $langvars['local_number_thousands_sep']) . " " . $langvars['l_tdr_ore'] . "<br>";
                        }
                    }
                    $playerinfo['ship_ore'] -= $ore_buy;
                }

                $portfull = 0;
                if ($dest['port_type'] != 'goods')
                {
                    $goods_price1 = $goods_price + $goods_delta * $dest['port_goods'] / $goods_limit * $inventory_factor;
                    if ($dest['port_goods'] - $playerinfo['ship_goods'] < 0)
                    {
                        $goods_buy = $dest['port_goods'];
                        $portfull = 1;
                    }
                    else
                    {
                        $goods_buy = $playerinfo['ship_goods'];
                    }

                    $destcost += $goods_buy * $goods_price1;
                    if ($goods_buy != 0)
                    {
                        if ($portfull == 1)
                        {
                            echo $langvars['l_tdr_sold'] . " " . number_format ($goods_buy, 0, $langvars['local_number_dec_point'], $langvars['local_number_thousands_sep']) . " " . $langvars['l_tdr_goods'] . " (" . $langvars['l_tdr_portisfull'] . ")<br>";
                        }
                        else
                        {
                            echo $langvars['l_tdr_sold'] . " " . number_format ($goods_buy, 0, $langvars['local_number_dec_point'], $langvars['local_number_thousands_sep']) . " " . $langvars['l_tdr_goods'] . "<br>";
                        }
                    }
                    $playerinfo['ship_goods'] -= $goods_buy;
                }

                $portfull = 0;
                if ($dest['port_type'] != 'organics')
                {
                    $organics_price1 = $organics_price + $organics_delta * $dest['port_organics'] / $organics_limit * $inventory_factor;
                    if ($dest['port_organics'] - $playerinfo['ship_organics'] < 0)
                    {
                        $organics_buy = $dest['port_organics'];
                        $portfull = 1;
                    }
                    else
                    {
                        $organics_buy = $playerinfo['ship_organics'];
                    }

                    $destcost += $organics_buy * $organics_price1;
                    if ($organics_buy != 0)
                    {
                        if ($portfull == 1)
                        {
                            echo $langvars['l_tdr_sold'] . " " . number_format ($organics_buy, 0, $langvars['local_number_dec_point'], $langvars['local_number_thousands_sep']) . " " . $langvars['l_tdr_organics'] . " (" . $langvars['l_tdr_portisfull'] . ")<br>";
                        }
                        else
                        {
                            echo $langvars['l_tdr_sold'] . " " . number_format ($organics_buy, 0, $langvars['local_number_dec_point'], $langvars['local_number_thousands_sep']) . " " . $langvars['l_tdr_organics'] . "<br>";
                        }
                    }
                    $playerinfo['ship_organics'] -= $organics_buy;
                }

                $portfull = 0;
                if ($dest['port_type'] != 'energy' && $playerinfo['trade_energy'] == 'Y')
                {
                    $energy_price1 = $energy_price + $energy_delta * $dest['port_energy'] / $energy_limit * $inventory_factor;
                    if ($dest['port_energy'] - $playerinfo['ship_energy'] < 0)
                    {
                        $energy_buy = $dest['port_energy'];
                        $portfull = 1;
                    }
                    else
                    {
                        $energy_buy = $playerinfo['ship_energy'];
                    }

                    $destcost += $energy_buy * $energy_price1;
                    if ($energy_buy != 0)
                    {
                        if ($portfull == 1)
                        {
                            echo $langvars['l_tdr_sold'] . " " . number_format ($energy_buy, 0, $langvars['local_number_dec_point'], $langvars['local_number_thousands_sep']) . " " . $langvars['l_tdr_energy'] . " (" . $langvars['l_tdr_portisfull'] . ")<br>";
                        }
                        else
                        {
                            echo $langvars['l_tdr_sold'] . " " . number_format ($energy_buy, 0, $langvars['local_number_dec_point'], $langvars['local_number_thousands_sep']) . " " . $langvars['l_tdr_energy'] . "<br>";
                        }
                    }
                    $playerinfo['ship_energy'] -= $energy_buy;
                }
                else
                {
                    $energy_buy = 0;
                }

                $free_holds = BntCalcLevels::Holds ($playerinfo['hull'], $level_factor) - $playerinfo['ship_ore'] - $playerinfo['ship_organics'] - $playerinfo['ship_goods'] - $playerinfo['ship_colonists'];

                // Time to buy
                if ($dest['port_type'] == 'ore')
                {
                    $ore_price1 = $ore_price - $ore_delta * $dest['port_ore'] / $ore_limit * $inventory_factor;
                    if ($traderoute['source_type'] == 'L')
                    {
                        $ore_buy = 0;
                    }
                    else
                    {
                        $ore_buy = $free_holds;
                        if ($playerinfo['credits'] + $destcost < $ore_buy * $ore_price1)
                        {
                            $ore_buy = ($playerinfo['credits'] + $destcost) / $ore_price1;
                        }

                        if ($dest['port_ore'] < $ore_buy)
                        {
                            $ore_buy = $dest['port_ore'];
                            if ($dest['port_ore'] == 0)
                            {
                                echo $langvars['l_tdr_bought'] . " " . number_format ($ore_buy, 0, $langvars['local_number_dec_point'], $langvars['local_number_thousands_sep']) . " " . $langvars['l_tdr_ore'] . " (" . $langvars['l_tdr_portisempty'] . ")<br>";
                            }
                        }

                        if ($ore_buy != 0)
                        {
                            echo $langvars['l_tdr_bought'] . " " . number_format ($ore_buy, 0, $langvars['local_number_dec_point'], $langvars['local_number_thousands_sep']) . " " . $langvars['l_tdr_ore'] . "<br>";
                        }

                        $playerinfo['ship_ore'] += $ore_buy;
                        $destcost -= $ore_buy * $ore_price1;
                    }
                    $resk = $db->Execute ("UPDATE {$db->prefix}universe SET port_ore=port_ore-?, port_energy=port_energy-?, port_goods=port_goods-?, port_organics=port_organics-? WHERE sector_id=?", array ($ore_buy, $energy_buy, $goods_buy, $organics_buy, $dest['sector_id']));
                    BntDb::logDbErrors ($db, $resk, __LINE__, __FILE__);
                }

                if ($dest['port_type'] == 'goods')
                {
                    $goods_price1 = $goods_price - $goods_delta * $dest['port_goods'] / $goods_limit * $inventory_factor;
                    if ($traderoute['source_type'] == 'L')
                    {
                        $goods_buy = 0;
                    }
                    else
                    {
                        $goods_buy = $free_holds;
                        if ($playerinfo['credits'] + $destcost < $goods_buy * $goods_price1)
                        {
                            $goods_buy = ($playerinfo['credits'] + $destcost) / $goods_price1;
                        }

                        if ($dest['port_goods'] < $goods_buy)
                        {
                            $goods_buy = $dest['port_goods'];
                            if ($dest['port_goods'] == 0)
                            {
                                echo $langvars['l_tdr_bought'] . " " . number_format ($goods_buy, 0, $langvars['local_number_dec_point'], $langvars['local_number_thousands_sep']) . " " . $langvars['l_tdr_goods'] . " (" . $langvars['l_tdr_portisempty'] . ")<br>";
                            }
                        }

                        if ($goods_buy != 0)
                        {
                            echo $langvars['l_tdr_bought'] . " " . number_format ($goods_buy, 0, $langvars['local_number_dec_point'], $langvars['local_number_thousands_sep']) . " " . $langvars['l_tdr_goods'] . "<br>";
                        }

                        $playerinfo['ship_goods'] += $goods_buy;
                        $destcost -= $goods_buy * $goods_price1;
                    }
                    $resl = $db->Execute ("UPDATE {$db->prefix}universe SET port_ore=port_ore-?, port_energy=port_energy-?, port_goods=port_goods-?, port_organics=port_organics-? WHERE sector_id=?", array ($ore_buy, $energy_buy, $goods_buy, $organics_buy, $dest['sector_id']));
                    BntDb::logDbErrors ($db, $resl, __LINE__, __FILE__);
                }

                if ($dest['port_type'] == 'organics')
                {
                    $organics_price1 = $organics_price - $organics_delta * $dest['port_organics'] / $organics_limit * $inventory_factor;
                    if ($traderoute['source_type'] == 'L')
                    {
                        $organics_buy = 0;
                    }
                    else
                    {
                        $organics_buy = $free_holds;
                        if ($playerinfo['credits'] + $destcost < $organics_buy * $organics_price1)
                        {
                            $organics_buy = ($playerinfo['credits'] + $destcost) / $organics_price1;
                        }
                        if ($dest['port_organics'] < $organics_buy)
                        {
                            $organics_buy = $dest['port_organics'];

                            if ($dest['port_organics'] == 0)
                            {
                                echo $langvars['l_tdr_bought'] . " " . number_format ($organics_buy, 0, $langvars['local_number_dec_point'], $langvars['local_number_thousands_sep']) . " " . $langvars['l_tdr_organics'] . " (" . $langvars['l_tdr_portisempty'] . ")<br>";
                            }
                        }

                        if ($organics_buy != 0)
                        {
                            echo $langvars['l_tdr_bought'] . " " . number_format ($organics_buy, 0, $langvars['local_number_dec_point'], $langvars['local_number_thousands_sep']) . " " . $langvars['l_tdr_organics'] . "<br>";
                        }

                        $playerinfo['ship_organics'] += $organics_buy;
                        $destcost -= $organics_buy * $organics_price1;
                    }
                    $resm = $db->Execute ("UPDATE {$db->prefix}universe SET port_ore=port_ore-?, port_energy=port_energy-?, port_goods=port_goods-?, port_organics=port_organics-? WHERE sector_id=?", array ($ore_buy, $energy_buy, $goods_buy, $organics_buy, $dest['sector_id']));
                    BntDb::logDbErrors ($db, $resm, __LINE__, __FILE__);
                }

                if ($dest['port_type'] == 'energy')
                {
                    $energy_price1 = $energy_price - $energy_delta * $dest['port_energy'] / $energy_limit * $inventory_factor;
                    if ($traderoute['source_type'] == 'L')
                    {
                        $energy_buy = 0;
                    }
                    else
                    {
                        $energy_buy = BntCalcLevels::Energy ($playerinfo['power'], $level_factor) - $playerinfo['ship_energy'] - $dist['scooped1'];
                        if ($playerinfo['credits'] + $destcost < $energy_buy * $energy_price1)
                        {
                            $energy_buy = ($playerinfo['credits'] + $destcost) / $energy_price1;
                        }

                        if ($dest['port_energy'] < $energy_buy)
                        {
                            $energy_buy = $dest['port_energy'];
                            if ($dest['port_energy'] == 0)
                            {
                                echo $langvars['l_tdr_bought'] . " " . number_format ($energy_buy, 0, $langvars['local_number_dec_point'], $langvars['local_number_thousands_sep']) . " " . $langvars['l_tdr_energy'] . " (" . $langvars['l_tdr_portisempty'] . ")<br>";
                            }
                        }

                        if ($energy_buy != 0)
                        {
                            echo $langvars['l_tdr_bought'] . " " . number_format ($energy_buy, 0, $langvars['local_number_dec_point'], $langvars['local_number_thousands_sep']) . " " . $langvars['l_tdr_energy'] . "<br>";
                        }

                        $playerinfo['ship_energy'] += $energy_buy;
                        $destcost -= $energy_buy * $energy_price1;
                    }

                    if ($ore_buy == 0 && $goods_buy == 0 && $energy_buy == 0 && $organics_buy == 0)
                    {
                        echo $langvars['l_tdr_nothingtotrade'] . "<br>";
                    }

                    $resn = $db->Execute ("UPDATE {$db->prefix}universe SET port_ore=port_ore-?, port_energy=port_energy-?, port_goods=port_goods-?, port_organics=port_organics-? WHERE sector_id=?", array ($ore_buy, $energy_buy, $goods_buy, $organics_buy, $dest['sector_id']));
                    BntDb::logDbErrors ($db, $resn, __LINE__, __FILE__);
                }

                if ($dist['scooped2'] > 0)
                {
                    $playerinfo['ship_energy']+= $dist['scooped2'];

                    if ($playerinfo['ship_energy'] > BntCalcLevels::Energy ($playerinfo['power'], $level_factor))
                    {
                        $playerinfo['ship_energy'] = BntCalcLevels::Energy ($playerinfo['power'], $level_factor);
                    }
                }
                $reso = $db->Execute ("UPDATE {$db->prefix}ships SET ship_ore=?, ship_goods=?, ship_organics=?, ship_energy=? WHERE ship_id=?", array ($playerinfo['ship_ore'], $playerinfo['ship_goods'], $playerinfo['ship_organics'], $playerinfo['ship_energy'], $playerinfo['ship_id']));
                BntDb::logDbErrors ($db, $reso, __LINE__, __FILE__);
            }
            else // Dest is planet
            {
                if ($traderoute['source_type'] == 'L' || $traderoute['source_type'] == 'C')
                {
                    $colonists_buy = 0;
                    $fighters_buy = 0;
                    $torps_buy = 0;
                }

                $setcol = 0;

                if ($playerinfo['trade_colonists'] == 'Y')
                {
                    $colonists_buy += $playerinfo['ship_colonists'];
                    $col_dump = $playerinfo['ship_colonists'];
                    if ($dest['colonists'] + $colonists_buy >= $colonist_limit)
                    {
                        $exceeding = $dest['colonists'] + $colonists_buy - $colonist_limit;
                        $col_dump = $exceeding;
                        $setcol = 1;
                        $colonists_buy-= $exceeding;
                        if ($colonists_buy < 0)
                        {
                            $colonists_buy = 0;
                        }
                    }
                }
                else
                {
                    $col_dump = 0;
                }

                if ($colonists_buy != 0)
                {
                    if ($setcol == 1)
                    {
                        echo $langvars['l_tdr_dumped'] . " " . number_format ($colonists_buy, 0, $langvars['local_number_dec_point'], $langvars['local_number_thousands_sep']) . " " . $langvars['l_tdr_colonists'] . " (" . $langvars['l_tdr_planetisovercrowded'] . ")<br>";
                    }
                    else
                    {
                        echo $langvars['l_tdr_dumped'] . " " . number_format ($colonists_buy, 0, $langvars['local_number_dec_point'], $langvars['local_number_thousands_sep']) . " " . $langvars['l_tdr_colonists'] . "<br>";
                    }
                }

                if ($playerinfo['trade_fighters'] == 'Y')
                {
                    $fighters_buy += $playerinfo['ship_fighters'];
                    $fight_dump = $playerinfo['ship_fighters'];
                }
                else
                {
                    $fight_dump = 0;
                }

                if ($fighters_buy != 0)
                {
                    echo $langvars['l_tdr_dumped'] . " " . number_format ($fighters_buy, 0, $langvars['local_number_dec_point'], $langvars['local_number_thousands_sep']) . " " . $langvars['l_tdr_fighters'] . "<br>";
                }

                if ($playerinfo['trade_torps'] == 'Y')
                {
                    $torps_buy += $playerinfo['torps'];
                    $torps_dump = $playerinfo['torps'];
                }
                else
                {
                    $torps_dump = 0;
                }

                if ($torps_buy != 0)
                {
                    echo $langvars['l_tdr_dumped'] . " " . number_format ($torps_buy, 0, $langvars['local_number_dec_point'], $langvars['local_number_thousands_sep']) . " " . $langvars['l_tdr_torps'] . "<br>";
                }

                if ($torps_buy == 0 && $fighters_buy == 0 && $colonists_buy == 0)
                {
                    echo $langvars['l_tdr_nothingtodump'] . "<br>";
                }

                if ($traderoute['source_type'] == 'L' || $traderoute['source_type'] == 'C')
                {
                    if ($playerinfo['trade_colonists'] == 'Y')
                    {
                        if ($setcol != 1)
                        {
                            $col_dump = 0;
                        }
                    }
                    else
                    {
                        $col_dump = $playerinfo['ship_colonists'];
                    }

                    if ($playerinfo['trade_fighters'] == 'Y')
                    {
                        $fight_dump = 0;
                    }
                    else
                    {
                        $fight_dump = $playerinfo['ship_fighters'];
                    }

                    if ($playerinfo['trade_torps'] == 'Y')
                    {
                        $torps_dump = 0;
                    }
                    else
                    {
                        $torps_dump = $playerinfo['torps'];
                    }
                }

                $resp = $db->Execute ("UPDATE {$db->prefix}planets SET colonists=colonists+?, fighters=fighters+?, torps=torps+? WHERE planet_id=?", array ($colonists_buy, $fighters_buy, $torps_buy, $traderoute['dest_id']));
                BntDb::logDbErrors ($db, $resp, __LINE__, __FILE__);

                if ($traderoute['source_type'] == 'L' || $traderoute['source_type'] == 'C')
                {
                    $resq = $db->Execute ("UPDATE {$db->prefix}ships SET ship_colonists=?, ship_fighters=?, torps=?, ship_energy=ship_energy+? WHERE ship_id=?", array ($col_dump, $fight_dump, $torps_dump, $dist['scooped'], $playerinfo['ship_id']));
                    BntDb::logDbErrors ($db, $resq, __LINE__, __FILE__);
                }
                else
                {
                    if ($setcol == 1)
                    {
                        $resr = $db->Execute ("UPDATE {$db->prefix}ships SET ship_colonists=?, ship_fighters=ship_fighters-?, torps=torps-?, ship_energy=ship_energy+? WHERE ship_id=?", array ($col_dump, $fight_dump, $torps_dump, $dist['scooped'], $playerinfo['ship_id']));
                        BntDb::logDbErrors ($db, $resr, __LINE__, __FILE__);
                    }
                    else
                    {
                        $ress = $db->Execute ("UPDATE {$db->prefix}ships SET ship_colonists=ship_colonists-?, ship_fighters=ship_fighters-?, torps=torps-?, ship_energy=ship_energy+? WHERE ship_id=?", array ($col_dump, $fight_dump, $torps_dump, $dist['scooped'], $playerinfo['ship_id']));
                        BntDb::logDbErrors ($db, $ress, __LINE__, __FILE__);
                    }
                }
            }
            if ($dist['scooped2'] != 0)
            {
                echo $langvars['l_tdr_scooped'] . " " . number_format ($dist['scooped1'], 0, $langvars['local_number_dec_point'], $langvars['local_number_thousands_sep']) . " " . $langvars['l_tdr_energy'] . "<br>";
            }
        }
        else
        {
            echo $langvars['l_tdr_onlyonewaytdr'];
            $destcost = 0;
        }
        traderoute_results_show_cost();

        if ($sourcecost > 0)
        {
            echo $langvars['l_tdr_profit'] . " : " . number_format (abs($sourcecost), 0, $langvars['local_number_dec_point'], $langvars['local_number_thousands_sep']);
        }
        else
        {
            echo $langvars['l_tdr_cost'] . " : " . number_format (abs($sourcecost), 0, $langvars['local_number_dec_point'], $langvars['local_number_thousands_sep']);
        }
        traderoute_results_close_cost();

        if ($destcost > 0)
        {
            echo $langvars['l_tdr_profit'] . " : " . number_format (abs($destcost), 0, $langvars['local_number_dec_point'], $langvars['local_number_thousands_sep']);
        }
        else
        {
            echo $langvars['l_tdr_cost'] . " : " . number_format (abs($destcost), 0, $langvars['local_number_dec_point'], $langvars['local_number_thousands_sep']);
        }

        traderoute_results_close_table();

        $total_profit = $sourcecost + $destcost;
        traderoute_results_display_totals ($db, $lang, $langvars, $total_profit);

        if ($traderoute['circuit'] == '1')
        {
            $newsec = $destport['sector_id'];
        }
        else
        {
            $newsec = $sourceport['sector_id'];
        }
        $rest = $db->Execute ("UPDATE {$db->prefix}ships SET turns=turns-?, credits=credits+?, turns_used=turns_used+?, sector=? WHERE ship_id=?", array ($dist['triptime'], $total_profit, $dist['triptime'], $newsec, $playerinfo['ship_id']));
        BntDb::logDbErrors ($db, $rest, __LINE__, __FILE__);
        $playerinfo['credits']+= $total_profit - $sourcecost;
        $playerinfo['turns']-= $dist['triptime'];

        $tdr_display_creds =   number_format ($playerinfo['credits'], 0, $langvars['local_number_dec_point'], $langvars['local_number_thousands_sep']);
        traderoute_results_display_summary ($db, $lang, $langvars, $tdr_display_creds);
        // echo $j." -- ";
        if ($traderoute['circuit'] == 2)
        {
            $langvars['l_tdr_engageagain'] = str_replace ("[here]", "<a href=\"traderoute.php?engage=[tdr_engage]\">" . $langvars['l_here'] . "</a>", $langvars['l_tdr_engageagain']);
            $langvars['l_tdr_engageagain'] = str_replace ("[five]", "<a href=\"traderoute.php?engage=[tdr_engage]&amp;tr_repeat=5\">" . $langvars['l_tdr_five'] . "</a>", $langvars['l_tdr_engageagain']);
            $langvars['l_tdr_engageagain'] = str_replace ("[ten]", "<a href=\"traderoute.php?engage=[tdr_engage]&amp;tr_repeat=10\">" . $langvars['l_tdr_ten'] . "</a>", $langvars['l_tdr_engageagain']);
            $langvars['l_tdr_engageagain'] = str_replace ("[fifty]", "<a href=\"traderoute.php?engage=[tdr_engage]&amp;tr_repeat=50\">" . $langvars['l_tdr_fifty'] . "</a>", $langvars['l_tdr_engageagain']);
            $langvars['l_tdr_engageagain'] = str_replace ("[tdr_engage]", $engage, $langvars['l_tdr_engageagain']);
            if ($j == 1)
            {
                echo $langvars['l_tdr_engageagain'] . "\n";
                traderoute_results_show_repeat ($engage);
            }
        }
        if ($j == 1)
        {
            traderoute_die ($db, $lang, $langvars, $bntreg, null, $template);
        }
    }

    public static function traderouteNew($db, $lang, $langvars, $bntreg, $traderoute_id, $template)
    {
        global $playerinfo, $color_line1, $color_line2, $color_header;
        global $num_traderoutes, $servertimezone;
        $langvars = BntTranslate::load ($db, $lang, array ('traderoutes', 'common', 'global_includes', 'global_funcs', 'footer'));
        $editroute = null;

        if (!empty ($traderoute_id))
        {
            $result = $db->Execute ("SELECT * FROM {$db->prefix}traderoutes WHERE traderoute_id=?", array ($traderoute_id));
            BntDb::logDbErrors ($db, $result, __LINE__, __FILE__);

            if (!$result || $result->EOF)
            {
                traderoute_die ($db, $lang, $langvars, $bntreg, $langvars['l_tdr_editerr'], $template);
            }

            $editroute = $result->fields;

            if ($editroute['owner'] != $playerinfo['ship_id'])
            {
                traderoute_die ($db, $lang, $langvars, $bntreg, $langvars['l_tdr_notowner'], $template);
            }
        }

        if ($num_traderoutes >= $bntreg->max_traderoutes_player && is_null ($editroute))
        {
            traderoute_die ($db, $lang, $langvars, $bntreg, '<p>' . $langvars['l_tdr_maxtdr'] . '<p>', $template);
        }

        echo "<p><font size=3 color=blue><strong>";

        if (is_null ($editroute))
        {
            echo $langvars['l_tdr_createnew'];
        }
        else
        {
            echo $langvars['l_tdr_editinga'] . " ";
        }

        echo $langvars['l_tdr_traderoute'] . "</strong></font><p>";

        // Get Planet info Corp and Personal

        $result = $db->Execute ("SELECT * FROM {$db->prefix}planets WHERE owner=? ORDER BY sector_id", array ($playerinfo['ship_id']));
        BntDb::logDbErrors ($db, $result, __LINE__, __FILE__);

        $num_planets = $result->RecordCount();
        $i = 0;
        while (!$result->EOF)
        {
            $planets[$i] = $result->fields;

            if ($planets[$i]['name'] == "")
            {
                $planets[$i]['name'] = $langvars['l_tdr_unnamed'];
            }

            $i++;
            $result->MoveNext();
        }

        $result = $db->Execute ("SELECT * FROM {$db->prefix}planets WHERE corp=? AND corp!=0 AND owner<>? ORDER BY sector_id", array ($playerinfo['team'], $playerinfo['ship_id']));
        BntDb::logDbErrors ($db, $result, __LINE__, __FILE__);

        $num_corp_planets = $result->RecordCount();
        $i = 0;
        while (!$result->EOF)
        {
            $planets_corp[$i] = $result->fields;

            if ($planets_corp[$i]['name'] == "")
            {
                $planets_corp[$i]['name'] = $langvars['l_tdr_unnamed'];
            }

            $i++;
            $result->MoveNext();
        }

        // Display Current Sector
        echo $langvars['l_tdr_cursector'] . " " . $playerinfo['sector'] . "<br>";

        // Start of form for starting location
        echo "
            <form action=traderoute.php?command=create method=post>
            <table border=0><tr>
            <td align=right><font size=2><strong>" . $langvars['l_tdr_selspoint'] . " <br>&nbsp;</strong></font></td>
            <tr>
            <td align=right><font size=2>" . $langvars['l_tdr_port'] . " : </font></td>
            <td><input type=radio name=\"ptype1\" value=\"port\"
            ";

        if (is_null ($editroute) || (!is_null ($editroute) && $editroute['source_type'] == 'P'))
        {
            echo " checked";
        }

        echo "
            ></td>
            <td>&nbsp;&nbsp;<input type=text name=port_id1 size=20 align='center'
            ";

        if (!is_null ($editroute) && $editroute['source_type'] == 'P')
        {
            echo " value=\"$editroute[source_id]\"";
        }

        echo "
            ></td>
            </tr><tr>
            ";

        // Personal Planet
        echo "
            <td align=right><font size=2>Personal " . $langvars['l_tdr_planet'] . " : </font></td>
            <td><input type=radio name=\"ptype1\" value=\"planet\"
            ";

        if (!is_null ($editroute) && $editroute['source_type'] == 'L')
        {
            echo " checked";
        }

        echo '
            ></td>
            <td>&nbsp;&nbsp;<select name=planet_id1>
            ';

        if ($num_planets == 0)
        {
            echo "<option value=none>" . $langvars['l_tdr_none'] . "</option>";
        }
        else
        {
            $i = 0;
            while ($i < $num_planets)
            {
                echo "<option ";

                if ($planets[$i]['planet_id'] == $editroute['source_id'])
                {
                    echo "selected ";
                }

                echo "value=" . $planets[$i]['planet_id'] . ">" . $planets[$i]['name'] . " " . $langvars['l_tdr_insector'] . " " . $planets[$i]['sector_id'] . "</option>";
                $i++;
            }
        }

        // Corp Planet
        echo "
            </tr><tr>
            <td align=right><font size=2>Corporate " . $langvars['l_tdr_planet'] . " : </font></td>
            <td><input type=radio name=\"ptype1\" value=\"corp_planet\"
            ";

        if (!is_null ($editroute) && $editroute['source_type'] == 'C')
        {
            echo " checked";
        }

        echo '
            ></td>
            <td>&nbsp;&nbsp;<select name=corp_planet_id1>
            ';

        if ($num_corp_planets == 0)
        {
            echo "<option value=none>" . $langvars['l_tdr_none'] . "</option>";
        }
        else
        {
            $i = 0;
            while ($i < $num_corp_planets)
            {
                echo "<option ";

                if ($planets_corp[$i]['planet_id'] == $editroute['source_id'])
                {
                    echo "selected ";
                }

                echo "value=" . $planets_corp[$i]['planet_id'] . ">" . $planets_corp[$i]['name'] . " " . $langvars['l_tdr_insector'] . " " . $planets_corp[$i]['sector_id'] . "</option>";
                $i++;
            }
        }

        echo "
            </select>
            </tr>";

        // Begin Ending point selection
        echo "
            <tr><td>&nbsp;
            </tr><tr>
            <td align=right><font size=2><strong>" . $langvars['l_tdr_selendpoint'] . " : <br>&nbsp;</strong></font></td>
            <tr>
            <td align=right><font size=2>" . $langvars['l_tdr_port'] . " : </font></td>
            <td><input type=radio name=\"ptype2\" value=\"port\"
            ";

        if (is_null ($editroute) || (!is_null ($editroute) && $editroute['dest_type'] == 'P'))
        {
            echo " checked";
        }

        echo '
            ></td>
            <td>&nbsp;&nbsp;<input type=text name=port_id2 size=20 align="center"
            ';

        if (!is_null ($editroute) && $editroute['dest_type'] == 'P')
        {
            echo " value=\"$editroute[dest_id]\"";
        }

        echo "
            ></td>
            </tr>";

        // Personal Planet
        echo "
            <tr>
            <td align=right><font size=2>Personal " . $langvars['l_tdr_planet'] . " : </font></td>
            <td><input type=radio name=\"ptype2\" value=\"planet\"
            ";

        if (!is_null ($editroute) && $editroute['dest_type'] == 'L')
        {
            echo " checked";
        }

        echo '
            ></td>
            <td>&nbsp;&nbsp;<select name=planet_id2>
            ';

        if ($num_planets == 0)
        {
            echo "<option value=none>" . $langvars['l_tdr_none'] . "</option>";
        }
        else
        {
            $i = 0;
            while ($i < $num_planets)
            {
                echo "<option ";

                if ($planets[$i]['planet_id'] == $editroute['dest_id'])
                {
                    echo "selected ";
                }

                echo "value=" . $planets[$i]['planet_id'] . ">" . $planets[$i]['name'] . " " . $langvars['l_tdr_insector'] . " " . $planets[$i]['sector_id'] . "</option>";
                $i++;
            }
        }

        // Corp Planet
        echo "
            </tr><tr>
            <td align=right><font size=2>Corporate " . $langvars['l_tdr_planet'] . " : </font></td>
            <td><input type=radio name=\"ptype2\" value=\"corp_planet\"
            ";

        if (!is_null ($editroute) && $editroute['dest_type'] == 'C')
        {
            echo " checked";
        }

        echo '
            ></td>
            <td>&nbsp;&nbsp;<select name=corp_planet_id2>
            ';

        if ($num_corp_planets == 0)
        {
            echo "<option value=none>" . $langvars['l_tdr_none'] . "</option>";
        }
        else
        {
            $i = 0;
            while ($i < $num_corp_planets)
            {
                echo "<option ";

                if ($planets_corp[$i]['planet_id'] == $editroute['dest_id'])
                {
                    echo "selected ";
                }

                echo "value=" . $planets_corp[$i]['planet_id'] . ">" . $planets_corp[$i]['name'] . " " . $langvars['l_tdr_insector'] . " " . $planets_corp[$i]['sector_id'] . "</option>";
                $i++;
            }
        }
        echo "
            </select>
            </tr>";

        echo "
            </select>
            </tr><tr>
            <td>&nbsp;
            </tr><tr>
            <td align=right><font size=2><strong>" . $langvars['l_tdr_selmovetype'] . " : </strong></font></td>
            <td colspan=2 valign=top><font size=2><input type=radio name=\"move_type\" value=\"realspace\"
            ";

        if (is_null ($editroute) || (!is_null ($editroute) && $editroute['move_type'] == 'R'))
        {
            echo " checked";
        }

        echo "
            >&nbsp;" . $langvars['l_tdr_realspace'] . "&nbsp;&nbsp<font size=2><input type=radio name=\"move_type\" value=\"warp\"
            ";

        if (!is_null ($editroute) && $editroute['move_type'] == 'W')
        {
            echo " checked";
        }

        echo "
            >&nbsp;" . $langvars['l_tdr_warp'] . "</font></td>
            </tr><tr>
            <td align=right><font size=2><strong>" . $langvars['l_tdr_selcircuit'] . " : </strong></font></td>
            <td colspan=2 valign=top><font size=2><input type=radio name=\"circuit_type\" value=\"1\"
            ";

        if (is_null ($editroute) || (!empty ($editroute) && $editroute['circuit'] == '1'))
        {
            echo " checked";
        }

        echo "
            >&nbsp;" . $langvars['l_tdr_oneway'] . "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type=radio name=\"circuit_type\" value=\"2\"
            ";

        if (!is_null ($editroute) && $editroute['circuit'] == '2')
        {
            echo " checked";
        }

        echo "
            >&nbsp;" . $langvars['l_tdr_bothways'] . "</font></td>
            </tr><tr>
            <td>&nbsp;
            </tr><tr>
            <td><td><td align='center'>
            ";

        if (is_null ($editroute))
        {
            echo "<input type=submit value=\"" . $langvars['l_tdr_create'] . "\">";
        }
        else
        {
            echo "<input type=hidden name=editing value=$editroute[traderoute_id]>";
            echo "<input type=submit value=\"" . $langvars['l_tdr_modify'] . "\">";
        }

        $langvars['l_tdr_returnmenu'] = str_replace ("[here]", "<a href='traderoute.php'>" . $langvars['l_here'] . "</a>", $langvars['l_tdr_returnmenu']);

        echo "
            </table>
            " . $langvars['l_tdr_returnmenu'] . "<br>
            </form>
            ";

        echo "<div style='text-align:left;'>\n";
        BntText::gotoMain ($db, $lang, $langvars);
        echo "</div>\n";

        include './footer.php';
        die ();
    }
}
?>
