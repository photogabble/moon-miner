<?php declare(strict_types=1);
/**
 * Moon Miner, a Free & Opensource (FOSS), web-based 4X space/strategy game forked
 * and based upon Black Nova Traders.
 *
 * @copyright 2024 Simon Dann
 * @copyright 2001-2014 Ron Harwood and the BNT development team
 *
 * @license GNU AGPL version 3.0 or (at your option) any later version.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

namespace App\Helpers;

class CalcLevels
{
    /**
     * Used for calculating max levels for things such as energy, armor, holds, etc.
     * In the classic BNT game code the multiplier was 100 for everything but energy
     * which has a multiplier of 500. Certain ship hulls might provide a bonus to the
     * multiplier such as granting more energy storage per level.
     *
     * @param int $level
     * @param int $multiplier
     * @return float
     */
    public static function maxLevels(int $level, int $multiplier = 100): float
    {
        return round(pow(config('game.level_factor'), $level) * $multiplier);
    }

    public static function energy(int $level): float
    {
        return round(pow(config('game.level_factor'), $level) * 500);
    }

    public static function planetBeams($db, $ownerinfo, $base_defense, $planetinfo)
    {
        $base_factor = ($planetinfo['base'] == 'Y') ? $base_defense : 0;

        $planetbeams = self::beams($ownerinfo['beams'] + $base_factor, $level_factor);
        $energy_available = $planetinfo['energy'];

        $res = $db->Execute("SELECT beams FROM {$db->prefix}ships WHERE planet_id = ? AND on_planet = 'Y';", array($planetinfo['planet_id']));
        Db::logDbErrors($db, $res, __LINE__, __FILE__);
        if ($res instanceof ADORecordSet)
        {
            while (!$res->EOF)
            {
                $planetbeams = $planetbeams + self::beams($res->fields['beams'], $level_factor);
                $res->MoveNext();
            }
        }

        if ($planetbeams > $energy_available)
        {
            $planetbeams = $energy_available;
        }
        $planetinfo['energy'] -= $planetbeams;

        return $planetbeams;
    }

    public static function planetShields($db, $ownerinfo, $base_defense, $planetinfo)
    {
        $base_factor = ($planetinfo['base'] == 'Y') ? $base_defense : 0;
        $planetshields = self::shields($ownerinfo['shields'] + $base_factor, $level_factor);
        $energy_available = $planetinfo['energy'];

        $res = $db->Execute("SELECT shields FROM {$db->prefix}ships WHERE planet_id = ? AND on_planet = 'Y';", array($planetinfo['planet_id']));
        Db::logDbErrors($db, $res, __LINE__, __FILE__);

        if ($res instanceof ADORecordSet)
        {
            while (!$res->EOF)
            {
                $planetshields += self::shields($res->fields['shields'], $level_factor);
                $res->MoveNext();
            }
        }

        if ($planetshields > $energy_available)
        {
            $planetshields = $energy_available;
        }
        $planetinfo['energy'] -= $planetshields;

        return $planetshields;
    }

    public static function planetTorps($db, $ownerinfo, $planetinfo, $base_defense, $level_factor)
    {
        $base_factor = ($planetinfo['base'] == 'Y') ? $base_defense : 0;
        $torp_launchers = round(pow($level_factor, ($ownerinfo['torp_launchers']) + $base_factor)) * 10;
        $torps = $planetinfo['torps'];

        $res = $db->Execute("SELECT torp_launchers FROM {$db->prefix}ships WHERE planet_id = ? AND on_planet = 'Y';", array($planetinfo['planet_id']));
        Db::logDbErrors($db, $res, __LINE__, __FILE__);
        if ($res instanceof ADORecordSet)
        {
            while (!$res->EOF)
            {
                $ship_torps =  round(pow($level_factor, $res->fields['torp_launchers'])) * 10;
                $torp_launchers = $torp_launchers + $ship_torps;
                $res->MoveNext();
            }
        }

        if ($torp_launchers > $torps)
        {
            $planettorps = $torps;
        }
        else
        {
            $planettorps = $torp_launchers;
        }

        $planetinfo['torps'] -= $planettorps;

        return $planettorps;
    }

    public static function avgTech($ship_info = null, $type = 'ship')
    {
        // Used to define what devices are used to calculate the average tech level.
        $calc_tech         = array('hull', 'engines', 'computer', 'armor', 'shields', 'beams', 'torp_launchers');
        $calc_ship_tech    = array('hull', 'engines', 'computer', 'armor', 'shields', 'beams', 'torp_launchers');
        $calc_planet_tech  = array('hull', 'engines', 'computer', 'armor', 'shields', 'beams', 'torp_launchers');

        if ($type == 'ship')
        {
            $calc_tech = $calc_ship_tech;
        }
        else
        {
            $calc_tech = $calc_planet_tech;
        }

        $count = count($calc_tech);

        $shipavg  = 0;
        for ($i = 0; $i < $count; $i++)
        {
            $shipavg += $ship_info[$calc_tech[$i]];
        }
        $shipavg /= $count;

        return $shipavg;
    }
}
