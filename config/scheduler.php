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

/*
|--------------------------------------------------------------------------
| Game Tick Scheduler
|--------------------------------------------------------------------------
|
| All sched_* vars are in minutes.
| These are true minutes, no matter to what interval you're running.
| the scheduler script. The scheduler will auto-adjust, possibly running
| many of the same events in a single call.
|
*/
return [

    /*
    |--------------------------------------------------------------------------
    | Minutes between Ticks
    |--------------------------------------------------------------------------
    |
    | Set this to how often in minutes you are running the scheduler script.
    |
    */
    'sched_ticks' => 1,

    /*
    |--------------------------------------------------------------------------
    | New Turns Rate
    |--------------------------------------------------------------------------
    |
    | New turns rate also includes towing, xenobe
    |
    */
    'sched_turns' => 2,

    /*
    |--------------------------------------------------------------------------
    | Port Production Rate
    |--------------------------------------------------------------------------
    |
    | How often port production occurs
    |
    */
    'sched_ports' => 1,

    /*
    |--------------------------------------------------------------------------
    | Planet Production Rate
    |--------------------------------------------------------------------------
    |
    | How often planet production occurs
    |
    */

    'sched_planets' => 2,
    /*
    |--------------------------------------------------------------------------
    | Interest Calculation Rate
    |--------------------------------------------------------------------------
    |
    | How often IGB interests are added
    |
    */
    'sched_igb' => 2,

    /*
    |--------------------------------------------------------------------------
    | Ranking Generation Rate
    |--------------------------------------------------------------------------
    |
    | How often rankings will be generated
    |
    */
    'sched_ranking' => 30,

    /*
    |--------------------------------------------------------------------------
    | News Generation Rate
    |--------------------------------------------------------------------------
    |
    | How often news are generated
    |
    */
    'sched_news' => 15,

    /*
    |--------------------------------------------------------------------------
    | Fighter Degrade Rate
    |--------------------------------------------------------------------------
    |
    | How often sector fighters degrade when unsupported by a planet
    |
    */
    'sched_degrade' => 6,

    /*
    |--------------------------------------------------------------------------
    | Apocalypse Run Rate
    |--------------------------------------------------------------------------
    |
    | How often apocalypse events will occur
    |
    */
    'sched_apocalypse' => 15,

    /*
    |--------------------------------------------------------------------------
    | Governor Run Rate
    |--------------------------------------------------------------------------
    |
    | How often the governor will run, cleaning up out-of-bound values
    |
    */
    'sched_thegovernor' => 1,

];
