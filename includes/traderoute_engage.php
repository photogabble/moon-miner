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
// File: includes/traderoute_engage.php

if (strpos ($_SERVER['PHP_SELF'], 'traderoute_engage.php')) // Prevent direct access to this file
{
    $error_file = $_SERVER['SCRIPT_NAME'];
    include './error.php';
}

function traderoute_engage ($db, $j)
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
    global $l_tdr_turnsused, $l_tdr_turnsleft, $l_tdr_credits, $l_tdr_profit, $l_tdr_cost, $l_tdr_totalprofit, $l_tdr_totalcost;
    global $l_tdr_planetisovercrowded, $l_tdr_engageagain, $l_tdr_onlyonewaytdr, $l_tdr_engagenonexist, $l_tdr_notowntdr;
    global $l_tdr_invalidspoint, $l_tdr_inittdr, $l_tdr_invalidsrc, $l_tdr_inittdrsector, $l_tdr_organics, $l_tdr_energy, $l_tdr_loaded;
    global $l_tdr_nothingtoload, $l_tdr_scooped, $l_tdr_dumped, $l_tdr_portisempty, $l_tdr_portisfull, $l_tdr_ore, $l_tdr_sold;
    global $l_tdr_goods, $l_tdr_notyourplanet, $l_tdr_invalidssector, $l_tdr_invaliddport, $l_tdr_invaliddplanet;
    global $l_tdr_invaliddsector, $l_tdr_nowlink1, $l_tdr_nowlink2, $l_tdr_moreturnsneeded, $l_tdr_hostdef;
    global $l_tdr_globalsetbuynothing, $l_tdr_nosrcporttrade, $l_tdr_tradesrcportoutsider, $l_tdr_res, $l_tdr_torps;
    global $l_tdr_nodestporttrade, $l_tdr_tradedestportoutsider, $l_tdr_portin, $l_tdr_planet, $l_tdr_bought, $l_tdr_colonists;
    global $l_tdr_fighters, $l_tdr_nothingtotrade, $l_here, $l_tdr_five, $l_tdr_ten, $l_tdr_fifty;
    global $l_tdr_nothingtodump;
    global $portfull;

    foreach ($traderoutes as $testroute)
    {
        if ($testroute['traderoute_id'] == $engage)
        {
            $traderoute = $testroute;
        }
    }

    if (!isset($traderoute))
    {
        traderoute_die ($l_tdr_engagenonexist);
    }

    if ($traderoute['owner'] != $playerinfo['ship_id'])
    {
        traderoute_die ($l_tdr_notowntdr);
    }

    // Source Check
    if ($traderoute['source_type'] == 'P')
    {
        // Retrieve port info here, we'll need it later anyway
        $result = $db->Execute("SELECT * FROM {$db->prefix}universe WHERE sector_id=?", array ($traderoute['source_id']));
        db_op_result ($db, $result, __LINE__, __FILE__);

        if (!$result || $result->EOF)
        {
            traderoute_die ($l_tdr_invalidspoint);
        }

        $source = $result->fields;

        if ($traderoute['source_id'] != $playerinfo['sector'])
        {
            $l_tdr_inittdr = str_replace("[tdr_source_id]", $traderoute['source_id'], $l_tdr_inittdr);
            traderoute_die ($l_tdr_inittdr);
        }
    }
    elseif ($traderoute['source_type'] == 'L' || $traderoute['source_type'] == 'C')  // Get data from planet table
    {
        $result = $db->Execute("SELECT * FROM {$db->prefix}planets WHERE planet_id=? AND (owner = ? OR (corp <> 0 AND corp = ?));", array ($traderoute['source_id'], $playerinfo['ship_id'], $playerinfo['team']));
        db_op_result ($db, $result, __LINE__, __FILE__);
        if (!$result || $result->EOF)
        {
            traderoute_die ($l_tdr_invalidsrc);
        }

        $source = $result->fields;

        if ($source['sector_id'] != $playerinfo['sector'])
        {
            // Check for valid Owned Source Planet
            // $l_tdr_inittdrsector = str_replace("[tdr_source_sector_id]", $source['sector_id'], $l_tdr_inittdrsector);
            // traderoute_die ($l_tdr_inittdrsector);
            traderoute_die ("You must be in starting sector before you initiate a trade route!");
        }

        if ($traderoute['source_type'] == 'L')
        {
            if ($source['owner'] != $playerinfo['ship_id'])
            {
                // $l_tdr_notyourplanet = str_replace("[tdr_source_name]", $source[name], $l_tdr_notyourplanet);
                // $l_tdr_notyourplanet = str_replace("[tdr_source_sector_id]", $source[sector_id], $l_tdr_notyourplanet);
                // traderoute_die ($l_tdr_notyourplanet);
                traderoute_die ($l_tdr_invalidsrc);
            }
        }
        elseif ($traderoute['source_type'] == 'C')   // Check to make sure player and planet are in the same corp.
        {
            if ($source['corp'] != $playerinfo['team'])
            {
                // $l_tdr_notyourplanet = str_replace("[tdr_source_name]", $source[name], $l_tdr_notyourplanet);
                // $l_tdr_notyourplanet = str_replace("[tdr_source_sector_id]", $source[sector_id], $l_tdr_notyourplanet);
                // $not_corp_planet = "$source[name] in $source[sector_id] not a Copporate Planet";
                // traderoute_die ($not_corp_planet);
                traderoute_die ($l_tdr_invalidsrc);
            }
        }

        // Store starting port info, we'll need it later
        $result = $db->Execute("SELECT * FROM {$db->prefix}universe WHERE sector_id=?", array ($source['sector_id']));
        db_op_result ($db, $result, __LINE__, __FILE__);

        if (!$result || $result->EOF)
        {
            traderoute_die ($l_tdr_invalidssector);
        }

        $sourceport = $result->fields;
    }

    // Destination Check
    if ($traderoute['dest_type'] == 'P')
    {
        $result = $db->Execute("SELECT * FROM {$db->prefix}universe WHERE sector_id=?", array ($traderoute['dest_id']));
        db_op_result ($db, $result, __LINE__, __FILE__);

        if (!$result || $result->EOF)
        {
            traderoute_die ($l_tdr_invaliddport);
        }

        $dest = $result->fields;
    }
    elseif (($traderoute['dest_type'] == 'L') || ($traderoute['dest_type'] == 'C'))  // Get data from planet table
    {
        // Check for valid Owned Source Planet
        // This now only returns Planets that the player owns or planets that belong to the team and set as corp planets..
        $result = $db->Execute("SELECT * FROM {$db->prefix}planets WHERE planet_id=? AND (owner = ? OR (corp <> 0 AND corp = ?));", array ($traderoute['dest_id'], $playerinfo['ship_id'], $playerinfo['team']));
        db_op_result ($db, $result, __LINE__, __FILE__);

        if (!$result || $result->EOF)
        {
            traderoute_die ($l_tdr_invaliddplanet);
        }

        $dest = $result->fields;

        if ($traderoute['dest_type'] == 'L')
        {
            if ($dest['owner'] != $playerinfo['ship_id'])
            {
                $l_tdr_notyourplanet = str_replace("[tdr_source_name]", $dest['name'], $l_tdr_notyourplanet);
                $l_tdr_notyourplanet = str_replace("[tdr_source_sector_id]", $dest['sector_id'], $l_tdr_notyourplanet);
                traderoute_die ($l_tdr_notyourplanet);
            }
        }
        elseif ($traderoute['dest_type'] == 'C')   // Check to make sure player and planet are in the same corp.
        {
            if ($dest['corp'] != $playerinfo['team'])
            {
                $l_tdr_notyourplanet = str_replace("[tdr_source_name]", $dest['name'], $l_tdr_notyourplanet);
                $l_tdr_notyourplanet = str_replace("[tdr_source_sector_id]", $dest['sector_id'], $l_tdr_notyourplanet);
                traderoute_die ($l_tdr_notyourplanet);
            }
        }

        $result = $db->Execute("SELECT * FROM {$db->prefix}universe WHERE sector_id=?", array ($dest['sector_id']));
        db_op_result ($db, $result, __LINE__, __FILE__);
        if (!$result || $result->EOF)
        {
            traderoute_die ($l_tdr_invaliddsector);
        }

        $destport = $result->fields;
    }

    if (!isset($sourceport))
    {
        $sourceport= $source;
    }

    if (!isset($destport))
    {
        $destport= $dest;
    }

    // Warp or RealSpace and generate distance
    if ($traderoute['move_type'] == 'W')
    {
        $query = $db->Execute("SELECT link_id FROM {$db->prefix}links WHERE link_start=? AND link_dest=?", array ($source['sector_id'], $dest['sector_id']));
        db_op_result ($db, $query, __LINE__, __FILE__);
        if ($query->EOF)
        {
            $l_tdr_nowlink1 = str_replace("[tdr_src_sector_id]", $source['sector_id'], $l_tdr_nowlink1);
            $l_tdr_nowlink1 = str_replace("[tdr_dest_sector_id]", $dest['sector_id'], $l_tdr_nowlink1);
            traderoute_die ($l_tdr_nowlink1);
        }

        if ($traderoute['circuit'] == '2')
        {
            $query = $db->Execute("SELECT link_id FROM {$db->prefix}links WHERE link_start=? AND link_dest=?", array ($dest['sector_id'], $source['sector_id']));
            db_op_result ($db, $query, __LINE__, __FILE__);
            if ($query->EOF)
            {
                $l_tdr_nowlink2 = str_replace("[tdr_src_sector_id]", $source['sector_id'], $l_tdr_nowlink2);
                $l_tdr_nowlink2 = str_replace("[tdr_dest_sector_id]", $dest['sector_id'], $l_tdr_nowlink2);
                traderoute_die ($l_tdr_nowlink2);
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
        $l_tdr_moreturnsneeded = str_replace("[tdr_dist_triptime]", $dist['triptime'], $l_tdr_moreturnsneeded);
        $l_tdr_moreturnsneeded = str_replace("[tdr_playerinfo_turns]", $playerinfo['turns'], $l_tdr_moreturnsneeded);
        traderoute_die ($l_tdr_moreturnsneeded);
    }

    // Sector Defense Check
    $hostile = 0;

    $result99 = $db->Execute("SELECT * FROM {$db->prefix}sector_defence WHERE sector_id = ? AND ship_id <> ?", array ($source['sector_id'], $playerinfo['ship_id']));
    db_op_result ($db, $result99, __LINE__, __FILE__);
    if (!$result99->EOF)
    {
        $fighters_owner = $result99->fields;
        $nsresult = $db->Execute("SELECT * FROM {$db->prefix}ships WHERE ship_id=?", array ($fighters_owner['ship_id']));
        db_op_result ($db, $nsresult, __LINE__, __FILE__);
        $nsfighters = $nsresult->fields;

        if ($nsfighters['team'] != $playerinfo['team'] || $playerinfo['team']==0)
        {
            $hostile = 1;
        }
    }

    $result98 = $db->Execute("SELECT * FROM {$db->prefix}sector_defence WHERE sector_id = ? AND ship_id <> ?", array ($dest['sector_id'], $playerinfo['ship_id']));
    db_op_result ($db, $result98, __LINE__, __FILE__);
    if (!$result98->EOF)
    {
        $fighters_owner = $result98->fields;
        $nsresult = $db->Execute("SELECT * FROM {$db->prefix}ships WHERE ship_id=?", array ($fighters_owner['ship_id']));
        db_op_result ($db, $nsresult, __LINE__, __FILE__);
        $nsfighters = $nsresult->fields;

        if ($nsfighters['team'] != $playerinfo['team'] || $playerinfo['team']==0)
        {
            $hostile = 1;
        }
    }

    if ($hostile > 0 && $playerinfo['hull'] > $mine_hullsize)
    {
        traderoute_die ($l_tdr_hostdef);
    }

    // Special Port Nothing to do
    if ($traderoute['source_type'] == 'P' && $source['port_type'] == 'special' && $playerinfo['trade_colonists'] == 'N' && $playerinfo['trade_fighters'] == 'N' && $playerinfo['trade_torps'] == 'N')
    {
        traderoute_die ($l_tdr_globalsetbuynothing);
    }

    // Check if zone allows trading  SRC
    if ($traderoute['source_type'] == 'P')
    {
        $res = $db->Execute("SELECT * FROM {$db->prefix}zones,{$db->prefix}universe WHERE {$db->prefix}universe.sector_id=? AND {$db->prefix}zones.zone_id={$db->prefix}universe.zone_id;", array ($traderoute['source_id']));
        db_op_result ($db, $res, __LINE__, __FILE__);
        $zoneinfo = $res->fields;
        if ($zoneinfo['allow_trade'] == 'N')
        {
            traderoute_die ($l_tdr_nosrcporttrade);
        }
        elseif ($zoneinfo['allow_trade'] == 'L')
        {
            if ($zoneinfo['corp_zone'] == 'N')
            {
                $res = $db->Execute("SELECT team FROM {$db->prefix}ships WHERE ship_id=?", array ($zoneinfo['owner']));
                db_op_result ($db, $res, __LINE__, __FILE__);
                $ownerinfo = $res->fields;

                if ($playerinfo['ship_id'] != $zoneinfo['owner'] && $playerinfo['team'] == 0 || $playerinfo['team'] != $ownerinfo['team'])
                {
                    traderoute_die ($l_tdr_tradesrcportoutsider);
                }
            }
            else
            {
                if ($playerinfo['team'] != $zoneinfo['owner'])
                {
                    traderoute_die ($l_tdr_tradesrcportoutsider);
                }
            }
        }
    }

    // Check if zone allows trading  DEST
    if ($traderoute['dest_type'] == 'P')
    {
        $res = $db->Execute("SELECT * FROM {$db->prefix}zones,{$db->prefix}universe WHERE {$db->prefix}universe.sector_id=? AND {$db->prefix}zones.zone_id={$db->prefix}universe.zone_id;", array ($traderoute['dest_id']));
        db_op_result ($db, $res, __LINE__, __FILE__);
        $zoneinfo = $res->fields;
        if ($zoneinfo['allow_trade'] == 'N')
        {
            traderoute_die ($l_tdr_nodestporttrade);
        }
        elseif ($zoneinfo['allow_trade'] == 'L')
        {
            if ($zoneinfo['corp_zone'] == 'N')
            {
                $res = $db->Execute("SELECT team FROM {$db->prefix}ships WHERE ship_id=?", array ($zoneinfo['owner']));
                db_op_result ($db, $res, __LINE__, __FILE__);
                $ownerinfo = $res->fields;

                if ($playerinfo['ship_id'] != $zoneinfo['owner'] && $playerinfo['team'] == 0 || $playerinfo['team'] != $ownerinfo['team'])
                {
                    traderoute_die ($l_tdr_tradedestportoutsider);
                }
            }
            else
            {
                if ($playerinfo['team'] != $zoneinfo['owner'])
                {
                    traderoute_die ($l_tdr_tradedestportoutsider);
                }
            }
        }
    }

    traderoute_results_table_top();
    // Determine if Source is Planet or Port
    if ($traderoute['source_type'] == 'P')
    {
        echo "$l_tdr_portin $source[sector_id]";
    }
    elseif (($traderoute['source_type'] == 'L') || ($traderoute['source_type'] == 'C'))
    {
        echo "$l_tdr_planet $source[name] in $sourceport[sector_id]";
    }
    traderoute_results_source();

    // Determine if Destination is Planet or Port
    if ($traderoute['dest_type'] == 'P')
    {
        echo "$l_tdr_portin $dest[sector_id]";
    }
    elseif (($traderoute['dest_type'] == 'L') || ($traderoute['dest_type'] == 'C'))
    {
        echo "$l_tdr_planet $dest[name] in $destport[sector_id]";
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
                $free_holds = \bnt\CalcLevels::Holds ($playerinfo['hull'], $level_factor) - $playerinfo['ship_ore'] - $playerinfo['ship_organics'] - $playerinfo['ship_goods'] - $playerinfo['ship_colonists'];
                $colonists_buy = $free_holds;

                if ($playerinfo['credits'] < $colonist_price * $colonists_buy)
                {
                    $colonists_buy = $playerinfo['credits'] / $colonist_price;
                }

                if ($colonists_buy != 0)
                {
                    echo "$l_tdr_bought " . NUMBER($colonists_buy) . " $l_tdr_colonists<br>";
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
                $free_fighters = \bnt\CalcLevels::Fighters ($playerinfo['computer'], $level_factor) - $playerinfo['ship_fighters'];
                $fighters_buy = $free_fighters;

                if ($total_credits < $fighters_buy * $fighter_price)
                {
                    $fighters_buy = $total_credits / $fighter_price;
                }

                if ($fighters_buy != 0)
                {
                    echo "$l_tdr_bought " . NUMBER($fighters_buy) . " $l_tdr_fighters<br>";
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
                $free_torps = \bnt\CalcLevels::Fighters ($playerinfo['torp_launchers'], $level_factor) - $playerinfo['torps'];
                $torps_buy = $free_torps;

                if ($total_credits < $torps_buy * $torpedo_price)
                {
                    $torps_buy = $total_credits / $torpedo_price;
                }

                if ($torps_buy != 0)
                {
                    echo "$l_tdr_bought " . NUMBER($torps_buy) . " $l_tdr_torps<br>";
                }

                $sourcecost-= $torps_buy * $torpedo_price;
            }
            else
            {
                $torps_buy = 0;
            }

            if ($torps_buy == 0 && $colonists_buy == 0 && $fighters_buy == 0)
            {
                echo "$l_tdr_nothingtotrade<br>";
            }

            if ($traderoute['circuit'] == '1')
            {
                $resb = $db->Execute("UPDATE {$db->prefix}ships SET ship_colonists=ship_colonists+?, ship_fighters=ship_fighters+?,torps=torps+?, ship_energy=ship_energy+? WHERE ship_id=?", array ($colonists_buy, $fighters_buy, $torps_buy, $dist['scooped1'], $playerinfo['ship_id']));
                db_op_result ($db, $resb, __LINE__, __FILE__);
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
                        echo "$l_tdr_sold " . NUMBER($ore_buy) . " $l_tdr_ore ($l_tdr_portisfull)<br>";
                    }
                    else
                    {
                        echo "$l_tdr_sold " . NUMBER($ore_buy) . " $l_tdr_ore<br>";
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
                        echo "$l_tdr_sold " . NUMBER($goods_buy) . " $l_tdr_goods ($l_tdr_portisfull)<br>";
                    }
                    else
                    {
                        echo "$l_tdr_sold " . NUMBER($goods_buy) . " $l_tdr_goods<br>";
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
                        echo "$l_tdr_sold " . NUMBER($organics_buy) . " $l_tdr_organics ($l_tdr_portisfull)<br>";
                    }
                    else
                    {
                        echo "$l_tdr_sold " . NUMBER($organics_buy) . " $l_tdr_organics<br>";
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
                        echo "$l_tdr_sold " . NUMBER($energy_buy) . " $l_tdr_energy ($l_tdr_portisfull)<br>";
                    }
                    else
                    {
                        echo "$l_tdr_sold " . NUMBER($energy_buy) . " $l_tdr_energy<br>";
                    }
                }
                $playerinfo['ship_energy'] -= $energy_buy;
            }

            $free_holds = \bnt\CalcLevels::Holds ($playerinfo['hull'], $level_factor) - $playerinfo['ship_ore'] - $playerinfo['ship_organics'] - $playerinfo['ship_goods'] - $playerinfo['ship_colonists'];

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
                        echo "$l_tdr_bought " . NUMBER($ore_buy) . " $l_tdr_ore ($l_tdr_portisempty)<br>";
                    }
                }

                if ($ore_buy != 0)
                {
                    echo "$l_tdr_bought " . NUMBER($ore_buy) . " $l_tdr_ore<br>";
                }
                $playerinfo['ship_ore'] += $ore_buy;
                $sourcecost -= $ore_buy * $ore_price1;
                $resc = $db->Execute("UPDATE {$db->prefix}universe SET port_ore=port_ore-?, port_energy=port_energy-?, port_goods=port_goods-?, port_organics=port_organics-? WHERE sector_id=?", array ($ore_buy, $energy_buy, $goods_buy, $organics_buy, $source['sector_id']));
                db_op_result ($db, $resc, __LINE__, __FILE__);
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
                        echo "$l_tdr_bought " . NUMBER($goods_buy) . " $l_tdr_goods ($l_tdr_portisempty)<br>";
                    }
                }

                if ($goods_buy != 0)
                {
                    echo "$l_tdr_bought " . NUMBER($goods_buy) . " $l_tdr_goods<br>";
                }

                $playerinfo['ship_goods'] += $goods_buy;
                $sourcecost -= $goods_buy * $goods_price1;

                $resd = $db->Execute("UPDATE {$db->prefix}universe SET port_ore=port_ore-?, port_energy=port_energy-?, port_goods=port_goods-?, port_organics=port_organics-? WHERE sector_id=?", array ($ore_buy, $energy_buy, $goods_buy, $organics_buy, $source['sector_id']));
                db_op_result ($db, $resd, __LINE__, __FILE__);
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
                        echo "$l_tdr_bought " . NUMBER($organics_buy) . " $l_tdr_organics ($l_tdr_portisempty)<br>";
                    }
                }

                if ($organics_buy != 0)
                {
                    echo "$l_tdr_bought " . NUMBER($organics_buy) . " $l_tdr_organics<br>";
                }

                $playerinfo['ship_organics'] += $organics_buy;
                $sourcecost -= $organics_buy * $organics_price1;
                $rese = $db->Execute("UPDATE {$db->prefix}universe SET port_ore=port_ore-?, port_energy=port_energy-?, port_goods=port_goods-?, port_organics=port_organics-? WHERE sector_id=?", array ($ore_buy, $energy_buy, $goods_buy, $organics_buy, $source['sector_id']));
                db_op_result ($db, $rese, __LINE__, __FILE__);
            }

            if ($source['port_type'] == 'energy')
            {
                $energy_price1 = $energy_price - $energy_delta * $source['port_energy'] / $energy_limit * $inventory_factor;
                $energy_buy = \bnt\CalcLevels::Energy ($playerinfo['power'], $level_factor) - $playerinfo['ship_energy'] - $dist['scooped1'];

                if ($playerinfo['credits'] + $sourcecost < $energy_buy * $energy_price1)
                {
                    $energy_buy = ($playerinfo['credits'] + $sourcecost) / $energy_price1;
                }

                if ($source['port_energy'] < $energy_buy)
                {
                    $energy_buy = $source['port_energy'];
                    if ($source['port_energy'] == 0)
                    {
                        echo "$l_tdr_bought " . NUMBER($energy_buy) . " $l_tdr_energy ($l_tdr_portisempty)<br>";
                    }
                }

                if ($energy_buy != 0)
                {
                    echo "$l_tdr_bought " . NUMBER($energy_buy) . " $l_tdr_energy<br>";
                }
                $playerinfo['ship_energy'] += $energy_buy;
                $sourcecost -= $energy_buy * $energy_price1;
                $resf = $db->Execute("UPDATE {$db->prefix}universe SET port_ore=port_ore-?, port_energy=port_energy-?, port_goods=port_goods-?, port_organics=port_organics-? WHERE sector_id=?", array ($ore_buy, $energy_buy, $goods_buy, $organics_buy, $source['sector_id']));
                db_op_result ($db, $resf, __LINE__, __FILE__);
            }

            if ($dist['scooped1'] > 0)
            {
                $playerinfo['ship_energy']+= $dist['scooped1'];
                if ($playerinfo['ship_energy'] > \bnt\CalcLevels::Energy ($playerinfo['power'], $level_factor))
                {
                    $playerinfo['ship_energy'] = \bnt\CalcLevels::Energy ($playerinfo['power'], $level_factor);
                }
            }

            if ($ore_buy == 0 && $goods_buy == 0 && $energy_buy == 0 && $organics_buy == 0)
            {
                echo "$l_tdr_nothingtotrade<br>";
            }

            if ($traderoute['circuit'] == '1')
            {
                $resf = $db->Execute("UPDATE {$db->prefix}ships SET ship_ore=?, ship_goods=?, ship_organics=?, ship_energy=? WHERE ship_id=?", array ($playerinfo['ship_ore'], $playerinfo['ship_goods'], $playerinfo['ship_organics'], $playerinfo['ship_energy'], $playerinfo['ship_id']));
                db_op_result ($db, $resf, __LINE__, __FILE__);
            }
        }
    }
    // Source is planet
    elseif (($traderoute['source_type'] == 'L') || ($traderoute['source_type'] == 'C'))
    {
        $free_holds = \bnt\CalcLevels::Holds ($playerinfo['hull'], $level_factor) - $playerinfo['ship_ore'] - $playerinfo['ship_organics'] - $playerinfo['ship_goods'] - $playerinfo['ship_colonists'];
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
                    echo "$l_tdr_loaded " . NUMBER($goods_buy) . " $l_tdr_goods<br>";
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
                    echo "$l_tdr_loaded " . NUMBER($ore_buy) . " $l_tdr_ore<br>";
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
                    echo "$l_tdr_loaded " . NUMBER($organics_buy) . " $l_tdr_organics<br>";
                }
                else
                {
                    $organics_buy = 0;
                }

                if ($ore_buy == 0 && $goods_buy == 0 && $organics_buy == 0)
                {
                    echo "$l_tdr_nothingtoload<br>";
                }

                if ($traderoute['circuit'] == '1')
                {
                    $resg = $db->Execute("UPDATE {$db->prefix}ships SET ship_ore=?, ship_goods=?, ship_organics=? WHERE ship_id=?", array ($playerinfo['ship_ore'], $playerinfo['ship_goods'], $playerinfo['ship_organics'], $playerinfo['ship_id']));
                    db_op_result ($db, $resg, __LINE__, __FILE__);
                }
            }
            else  // Buy from planet - not implemented yet
            {
            }

            $resh = $db->Execute("UPDATE {$db->prefix}planets SET ore=ore-?, goods=goods-?, organics=organics-? WHERE planet_id=?", array ($ore_buy, $goods_buy, $organics_buy, $source['planet_id']));
            db_op_result ($db, $resh, __LINE__, __FILE__);
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
                echo "$l_tdr_loaded " . NUMBER($colonists_buy) . " $l_tdr_colonists<br>";
            }
            else
            {
                $colonists_buy = 0;
            }

            $free_torps = \bnt\CalcLevels::Torpedoes ($playerinfo['torp_launchers'], $level_factor) - $playerinfo['torps'];
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
                echo "$l_tdr_loaded " . NUMBER($torps_buy) . " $l_tdr_torps<br>";
            }
            else
            {
                $torps_buy = 0;
            }

            $free_fighters = \bnt\CalcLevels::Fighters ($playerinfo['computer'], $level_factor) - $playerinfo['ship_fighters'];
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
                echo "$l_tdr_loaded " . NUMBER($fighters_buy) . " $l_tdr_fighters<br>";
            }
            else
            {
                $fighters_buy = 0;
            }

            if ($fighters_buy == 0 && $torps_buy == 0 && $colonists_buy == 0)
            {
                echo "$l_tdr_nothingtoload<br>";
            }

            if ($traderoute['circuit'] == '1')
            {
                $resi = $db->Execute("UPDATE {$db->prefix}ships SET torps=?, ship_fighters=?, ship_colonists=? WHERE ship_id=?", array ($playerinfo['torps'], $playerinfo['ship_fighters'], $playerinfo['ship_colonists'], $playerinfo['ship_id']));
                db_op_result ($db, $resi, __LINE__, __FILE__);
            }

            $resj = $db->Execute("UPDATE {$db->prefix}planets SET colonists=colonists-?, torps=torps-?, fighters=fighters-? WHERE planet_id=?", array ($colonists_buy, $torps_buy, $fighters_buy, $source['planet_id']));
            db_op_result ($db, $resj, __LINE__, __FILE__);
        }
    }

    if ($dist['scooped1'] != 0)
    {
        echo "$l_tdr_scooped " . NUMBER($dist['scooped1']) . " $l_tdr_energy<br>";
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
                        echo "$l_tdr_sold " . NUMBER($ore_buy) . " $l_tdr_ore ($l_tdr_portisfull)<br>";
                    }
                    else
                    {
                        echo "$l_tdr_sold " . NUMBER($ore_buy) . " $l_tdr_ore<br>";
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
                        echo "$l_tdr_sold " . NUMBER($goods_buy) . " $l_tdr_goods ($l_tdr_portisfull)<br>";
                    }
                    else
                    {
                        echo "$l_tdr_sold " . NUMBER($goods_buy) . " $l_tdr_goods<br>";
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
                        echo "$l_tdr_sold " . NUMBER($organics_buy) . " $l_tdr_organics ($l_tdr_portisfull)<br>";
                    }
                    else
                    {
                        echo "$l_tdr_sold " . NUMBER($organics_buy) . " $l_tdr_organics<br>";
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
                        echo "$l_tdr_sold " . NUMBER($energy_buy) . " $l_tdr_energy ($l_tdr_portisfull)<br>";
                    }
                    else
                    {
                        echo "$l_tdr_sold " . NUMBER($energy_buy) . " $l_tdr_energy<br>";
                    }
                }
                $playerinfo['ship_energy'] -= $energy_buy;
            }
            else
            {
                $energy_buy = 0;
            }

            $free_holds = \bnt\CalcLevels::Holds ($playerinfo['hull'], $level_factor) - $playerinfo['ship_ore'] - $playerinfo['ship_organics'] - $playerinfo['ship_goods'] - $playerinfo['ship_colonists'];

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
                            echo "$l_tdr_bought " . NUMBER($ore_buy) . " $l_tdr_ore ($l_tdr_portisempty)<br>";
                        }
                    }

                    if ($ore_buy != 0)
                    {
                        echo "$l_tdr_bought " . NUMBER($ore_buy) . " $l_tdr_ore<br>";
                    }

                    $playerinfo['ship_ore'] += $ore_buy;
                    $destcost -= $ore_buy * $ore_price1;
                }
                $resk = $db->Execute("UPDATE {$db->prefix}universe SET port_ore=port_ore-?, port_energy=port_energy-?, port_goods=port_goods-?, port_organics=port_organics-? WHERE sector_id=?", array ($ore_buy, $energy_buy, $goods_buy, $organics_buy, $dest['sector_id']));
                db_op_result ($db, $resk, __LINE__, __FILE__);
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
                            echo "$l_tdr_bought " . NUMBER($goods_buy) . " $l_tdr_goods ($l_tdr_portisempty)<br>";
                        }
                    }

                    if ($goods_buy != 0)
                    {
                        echo "$l_tdr_bought " . NUMBER($goods_buy) . " $l_tdr_goods<br>";
                    }

                    $playerinfo['ship_goods'] += $goods_buy;
                    $destcost -= $goods_buy * $goods_price1;
                }
                $resl = $db->Execute("UPDATE {$db->prefix}universe SET port_ore=port_ore-?, port_energy=port_energy-?, port_goods=port_goods-?, port_organics=port_organics-? WHERE sector_id=?", array ($ore_buy, $energy_buy, $goods_buy, $organics_buy, $dest['sector_id']));
                db_op_result ($db, $resl, __LINE__, __FILE__);
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
                            echo "$l_tdr_bought " . NUMBER($organics_buy) . " $l_tdr_organics ($l_tdr_portisempty)<br>";
                        }
                    }

                    if ($organics_buy != 0)
                    {
                        echo "$l_tdr_bought " . NUMBER($organics_buy) . " $l_tdr_organics<br>";
                    }

                    $playerinfo['ship_organics'] += $organics_buy;
                    $destcost -= $organics_buy * $organics_price1;
                }
                $resm = $db->Execute("UPDATE {$db->prefix}universe SET port_ore=port_ore-?, port_energy=port_energy-?, port_goods=port_goods-?, port_organics=port_organics-? WHERE sector_id=?", array ($ore_buy, $energy_buy, $goods_buy, $organics_buy, $dest['sector_id']));
                db_op_result ($db, $resm, __LINE__, __FILE__);
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
                    $energy_buy = \bnt\CalcLevels::Energy ($playerinfo['power'], $level_factor) - $playerinfo['ship_energy'] - $dist['scooped1'];
                    if ($playerinfo['credits'] + $destcost < $energy_buy * $energy_price1)
                    {
                        $energy_buy = ($playerinfo['credits'] + $destcost) / $energy_price1;
                    }

                    if ($dest['port_energy'] < $energy_buy)
                    {
                        $energy_buy = $dest['port_energy'];
                        if ($dest['port_energy'] == 0)
                        {
                            echo "$l_tdr_bought " . NUMBER($energy_buy) . " $l_tdr_energy ($l_tdr_portisempty)<br>";
                        }
                    }

                    if ($energy_buy != 0)
                    {
                        echo "$l_tdr_bought " . NUMBER($energy_buy) . " $l_tdr_energy<br>";
                    }

                    $playerinfo['ship_energy'] += $energy_buy;
                    $destcost -= $energy_buy * $energy_price1;
                }

                if ($ore_buy == 0 && $goods_buy == 0 && $energy_buy == 0 && $organics_buy == 0)
                {
                    echo "$l_tdr_nothingtotrade<br>";
                }

                $resn = $db->Execute("UPDATE {$db->prefix}universe SET port_ore=port_ore-?, port_energy=port_energy-?, port_goods=port_goods-?, port_organics=port_organics-? WHERE sector_id=?", array ($ore_buy, $energy_buy, $goods_buy, $organics_buy, $dest['sector_id']));
                db_op_result ($db, $resn, __LINE__, __FILE__);
            }

            if ($dist['scooped2'] > 0)
            {
                $playerinfo['ship_energy']+= $dist['scooped2'];

                if ($playerinfo['ship_energy'] > \bnt\CalcLevels::Energy ($playerinfo['power'], $level_factor))
                {
                    $playerinfo['ship_energy'] = \bnt\CalcLevels::Energy ($playerinfo['power'], $level_factor);
                }
            }
            $reso = $db->Execute("UPDATE {$db->prefix}ships SET ship_ore=?, ship_goods=?, ship_organics=?, ship_energy=? WHERE ship_id=?", array ($playerinfo['ship_ore'], $playerinfo['ship_goods'], $playerinfo['ship_organics'], $playerinfo['ship_energy'], $playerinfo['ship_id']));
            db_op_result ($db, $reso, __LINE__, __FILE__);
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
                    echo "$l_tdr_dumped " . NUMBER($colonists_buy) . " $l_tdr_colonists ($l_tdr_planetisovercrowded)<br>";
                }
                else
                {
                    echo "$l_tdr_dumped " . NUMBER($colonists_buy) . " $l_tdr_colonists<br>";
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
                echo "$l_tdr_dumped " . NUMBER($fighters_buy) . " $l_tdr_fighters<br>";
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
                echo "$l_tdr_dumped " . NUMBER($torps_buy) . " $l_tdr_torps<br>";
            }

            if ($torps_buy == 0 && $fighters_buy == 0 && $colonists_buy == 0)
            {
                echo "$l_tdr_nothingtodump<br>";
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

            $resp = $db->Execute("UPDATE {$db->prefix}planets SET colonists=colonists+?, fighters=fighters+?, torps=torps+? WHERE planet_id=?", array ($colonists_buy, $fighters_buy, $torps_buy, $traderoute['dest_id']));
            db_op_result ($db, $resp, __LINE__, __FILE__);

            if ($traderoute['source_type'] == 'L' || $traderoute['source_type'] == 'C')
            {
                $resq = $db->Execute("UPDATE {$db->prefix}ships SET ship_colonists=?, ship_fighters=?, torps=?, ship_energy=ship_energy+? WHERE ship_id=?", array ($col_dump, $fight_dump, $torps_dump, $dist['scooped'], $playerinfo['ship_id']));
                db_op_result ($db, $resq, __LINE__, __FILE__);
            }
            else
            {
                if ($setcol == 1)
                {
                    $resr = $db->Execute("UPDATE {$db->prefix}ships SET ship_colonists=?, ship_fighters=ship_fighters-?, torps=torps-?, ship_energy=ship_energy+? WHERE ship_id=?", array ($col_dump, $fight_dump, $torps_dump, $dist['scooped'], $playerinfo['ship_id']));
                    db_op_result ($db, $resr, __LINE__, __FILE__);
                }
                else
                {
                    $ress = $db->Execute("UPDATE {$db->prefix}ships SET ship_colonists=ship_colonists-?, ship_fighters=ship_fighters-?, torps=torps-?, ship_energy=ship_energy+? WHERE ship_id=?", array ($col_dump, $fight_dump, $torps_dump, $dist['scooped'], $playerinfo['ship_id']));
                    db_op_result ($db, $ress, __LINE__, __FILE__);
                }
            }
        }
        if ($dist['scooped2'] != 0)
        {
            echo "$l_tdr_scooped " . NUMBER($dist['scooped1']) . " $l_tdr_energy<br>";
        }
    }
    else
    {
        echo $l_tdr_onlyonewaytdr;
        $destcost = 0;
    }
    traderoute_results_show_cost();

    if ($sourcecost > 0)
    {
        echo "$l_tdr_profit : " . NUMBER(abs($sourcecost));
    }
    else
    {
        echo "$l_tdr_cost : " . NUMBER(abs($sourcecost));
    }
    traderoute_results_close_cost();

    if ($destcost > 0)
    {
        echo "$l_tdr_profit : " . NUMBER(abs($destcost));
    }
    else
    {
        echo "$l_tdr_cost : " . NUMBER(abs($destcost));
    }

    traderoute_results_close_table();

    $total_profit = $sourcecost + $destcost;
    traderoute_results_display_totals($total_profit);

    if ($traderoute['circuit'] == '1')
    {
        $newsec = $destport['sector_id'];
    }
    else
    {
        $newsec = $sourceport['sector_id'];
    }
    $rest = $db->Execute("UPDATE {$db->prefix}ships SET turns=turns-?, credits=credits+?, turns_used=turns_used+?, sector=? WHERE ship_id=?", array ($dist['triptime'], $total_profit, $dist['triptime'], $newsec, $playerinfo['ship_id']));
    db_op_result ($db, $rest, __LINE__, __FILE__);
    $playerinfo['credits']+= $total_profit - $sourcecost;
    $playerinfo['turns']-= $dist['triptime'];

    $tdr_display_creds =   NUMBER($playerinfo['credits']);
    traderoute_results_display_summary($tdr_display_creds);
    // echo $j." -- ";
    if ($traderoute['circuit'] == 2)
    {
        $l_tdr_engageagain = str_replace("[here]", "<a href=\"traderoute.php?engage=[tdr_engage]\">" . $l_here . "</a>", $l_tdr_engageagain);
        $l_tdr_engageagain = str_replace("[five]", "<a href=\"traderoute.php?engage=[tdr_engage]&amp;tr_repeat=5\">" . $l_tdr_five . "</a>", $l_tdr_engageagain);
        $l_tdr_engageagain = str_replace("[ten]", "<a href=\"traderoute.php?engage=[tdr_engage]&amp;tr_repeat=10\">" . $l_tdr_ten . "</a>", $l_tdr_engageagain);
        $l_tdr_engageagain = str_replace("[fifty]", "<a href=\"traderoute.php?engage=[tdr_engage]&amp;tr_repeat=50\">" . $l_tdr_fifty . "</a>", $l_tdr_engageagain);
        $l_tdr_engageagain = str_replace("[tdr_engage]", $engage, $l_tdr_engageagain);
        if ($j == 1)
        {
            echo $l_tdr_engageagain . "\n";
            traderoute_results_show_repeat ($engage);
        }
    }
    if ($j == 1)
    {
        traderoute_die ("");
    }
}
?>
