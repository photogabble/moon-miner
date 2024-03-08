<?php declare(strict_types=1);
/**
 * Blacknova Traders, a Free & Opensource (FOSS), web-based 4X space/strategy game.
 *
 * @copyright 2024 Simon Dann, Ron Harwood and the BNT development team
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

namespace App\Types;

enum LogType: int
{
    case LOG_LOGIN = 1; // Sent when logging in
    case LOG_LOGOUT = 2; // Sent when logging out
    case LOG_ATTACK_OUTMAN = 3; // Sent to target when better engines
    case LOG_ATTACK_OUTSCAN = 4; // Sent to target when better cloak
    case LOG_ATTACK_EWD = 5; // Sent to target when EWD engaged
    case LOG_ATTACK_EWDFAIL = 6; // Sent to target when EWD failed
    case LOG_ATTACK_LOSE = 7; // Sent to target when he lost
    case LOG_ATTACKED_WIN = 8; // Sent to target when he won
    case LOG_TOLL_PAID = 9; // Sent when paid a toll
    case LOG_HIT_MINES = 10; // Sent when hit mines
    case LOG_SHIP_DESTROYED_MINES = 11; // Sent when destroyed by mines
    case LOG_PLANET_DEFEATED_D = 12; // Sent when one of your defeated planets is destroyed instead of captured
    case LOG_PLANET_DEFEATED = 13; // Sent when a planet is defeated
    case LOG_PLANET_NOT_DEFEATED = 14; // Sent when a planet survives
    case LOG_RAW = 15; // This log is sent as-is
    case LOG_TOLL_RECV = 16; // Sent when you receive toll money
    case LOG_DEFS_DESTROYED = 17; // Sent for destroyed sector defenses
    case LOG_PLANET_EJECT = 18; // Sent when ejected from a planet due to team switch
    case LOG_BADLOGIN = 19; // Sent when bad login
    case LOG_PLANET_SCAN = 20; // Sent when a planet has been scanned
    case LOG_PLANET_SCAN_FAIL = 21; // Sent when a planet scan failed
    case LOG_PLANET_CAPTURE = 22; // Sent when a planet is captured
    case LOG_SHIP_SCAN = 23; // Sent when a ship is scanned
    case LOG_SHIP_SCAN_FAIL = 24; // Sent when a ship scan fails
    case LOG_XENOBE_ATTACK = 25; // Xenobes send this to themselves
    case LOG_STARVATION = 26; // Sent when colonists are starving... Is this actually used in the game?
    case LOG_TOW = 27; // Sent when a player is towed
    case LOG_DEFS_DESTROYED_F = 28; // Sent when a player destroys fighters
    case LOG_DEFS_KABOOM = 29; // Sent when sector fighters destroy you
    case LOG_HARAKIRI = 30; // Sent when self-destructed
    case LOG_TEAM_REJECT = 31; // Sent when player refuses invitation
    case LOG_TEAM_RENAME = 32; // Sent when renaming a team
    case LOG_TEAM_M_RENAME = 33; // Sent to members on team rename
    case LOG_TEAM_KICK = 34; // Sent to booted player
    case LOG_TEAM_CREATE = 35; // Sent when created a team
    case LOG_TEAM_LEAVE = 36; // Sent when leaving a team
    case LOG_TEAM_NEWLEAD = 37; // Sent when leaving a team, appointing a new leader
    case LOG_TEAM_LEAD = 38; // Sent to the new team leader
    case LOG_TEAM_JOIN = 39; // Sent when joining a team
    case LOG_TEAM_NEWMEMBER = 40; // Sent to leader on join
    case LOG_TEAM_INVITE = 41; // Sent to invited player
    case LOG_TEAM_NOT_LEAVE = 42; // Sent to leader on leave
    case LOG_ADMIN_HARAKIRI = 43; // Sent to admin on self-destruct
    case LOG_ADMIN_PLANETDEL = 44; // Sent to admin on planet destruction instead of capture
    case LOG_DEFENCE_DEGRADE = 45; // Sent sector fighters have no supporting planet
    case LOG_PLANET_CAPTURED = 46; // Sent to player when he captures a planet
    case LOG_BOUNTY_CLAIMED = 47; // Sent to player when they claim a bounty
    case LOG_BOUNTY_PAID = 48; // Sent to player when their bounty on someone is paid
    case LOG_BOUNTY_CANCELLED = 49; // Sent to player when their bounty is refunded
    case LOG_SPACE_PLAGUE = 50; // Sent when space plague attacks a planet
    case LOG_PLASMA_STORM = 51; // Sent when a plasma storm attacks a planet
    case LOG_BOUNTY_FEDBOUNTY = 52; // Sent when the federation places a bounty on a player
    case LOG_PLANET_BOMBED = 53; // Sent after bombing a planet
    case LOG_ADMIN_ILLEGVALUE = 54; // Sent to admin on planet destruction instead of capture
    case LOG_ADMIN_PLANETCHEAT = 55; // Sent to admin due to planet hack (hack_id, ip, planet_id, ship_id)
}
