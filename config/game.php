<?php

// Blacknova Traders - A web-based massively multiplayer space combat and trading game
// Copyright 2001-2024 Ron Harwood and the BNT development team
//
//  This program is free software: you can redistribute it and/or modify
//  it under the terms of the GNU Affero General Public License as
//  published by the Free Software Foundation, either version 3 of the
//  License, or at your option any later version.
//
//  This program is distributed in the hope that it will be useful,
//  but WITHOUT ANY WARRANTY; without even the implied warranty of
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//  GNU Affero General Public License for more details.
//
//  You should have received a copy of the GNU Affero General Public License
//  along with this program.  If not, see <http://www.gnu.org/licenses/>.
//
// File: config/game.php

return [

    /*
    |--------------------------------------------------------------------------
    | Optional Features
    |--------------------------------------------------------------------------
    |
    | Changing these can change how the game feels for a more challenging
    | play through.
    |
    */

    // Allow players to use full long range scan during this game?
    'allow_fullscan' => true,
    // Allow players to use the Navigation computer during this game?
    'allow_navcomp' => true,
    // Allow players to use the Intergalactic Bank IGB during this game?
    'allow_ibank' => true,
    // Allow players to use genesis torps to destroy planets?
    'allow_genesis_destroy' => false,
    // Allow the sub-orbital fighter sofa attack in this game?
    'allow_sofa' => false,
    // Allow the known space map in this game?
    'allow_ksm' => true,

    /*
    |--------------------------------------------------------------------------
    | Starter Ship Values
    |--------------------------------------------------------------------------
    |
    | When a player first joins the game these values determine their ship
    | ship status
    |
    */

    // The amount of fighters on the ship the player starts with
    'start_fighters' => 10,
    // The armor a player starts with
    'start_armor' => 10,
    // The credits a player starts the game with
    'start_credits' => 1000,
    // The amount of energy on the ship the player starts with
    'start_energy' => 100,
    // The number of turns all players are given at the start of the game
    'start_turns' => 1200,
    // Do ships start with a LSSD ?
    'start_lssd' => false,
    // Starting warp editors
    'start_editors' => 0,
    // Start mine deflectors
    'start_minedeflectors' => 0,
    // Start emergency warp units
    'start_emerwarp' => 0,
    // Start space_beacons
    'start_beacon' => 0,
    // Starting genesis torps
    'start_genesis' => 0,
    // Start game equipped with escape pod?
    'start_escape_pod' => false,
    // Start game equipped with fuel scoop?
    'start_scoop' => false,

    /*
    |--------------------------------------------------------------------------
    | iBank
    |--------------------------------------------------------------------------
    |
    | Interest rates and loan terms for the in-game bank.
    |
    */

    // Interest rate for account funds - Note that this is calculated every system update
    'ibank_interest' => 0.0003,
    // Paymentfee
    'ibank_paymentfee' => 0.05,
    // Loan interest - it is a good idea to put double what you get on a planet
    'ibank_loaninterest' => 0.0010,
    // One-time loan fee
    'ibank_loanfactor' => 0.10,
    // Maximum loan allowed, percent of net worth
    'ibank_loanlimit' => 0.25,
    // Turns a player has to play before ship transfers are allowed 0=disable
    'ibank_min_turns' => 1200,
    // Max amount of sender's value allowed for ship transfers 0=disable
    'ibank_svalue' => 0.15,
    // Time in minutes before two similar transfers are allowed for ship transfers. 0=disable
    'ibank_trate' => 1440,
    // Time in minutes players have to repay a loan
    'ibank_lrate' => 1440,
    // Cost in turns for consolidate : 1/$ibank_consolidate
    'ibank_tconsolidate' => 10,

    /*
    |--------------------------------------------------------------------------
    | Planet Production Values
    |--------------------------------------------------------------------------
    |
    | Production percentage values
    | TODO: Percentages should be between 0 and 1
    |
    */

    // Default planet ore production percentage
    'default_prod_ore' => 20.0,
    // Default planet organics production percentage
    'default_prod_organics' => 20.0,
    // Default planet goods production percentage
    'default_prod_goods' => 20.0,
    // Default planet energy production percentage
    'default_prod_energy' => 20.0,
    // Default planet fighters production percentage
    'default_prod_fighters' => 10.0,
    // Default planet torpedo production percentage
    'default_prod_torp' => 10.0,
    // The rate of production for fighters on a planet times production, times player/planet setting for fit_prate
    'fighter_prate' => 0.01,
    // The rate of production for torpedoes on a planet times production, times player/planet setting for torp_prate
    'torpedo_prate' => 0.025,
    // The rate of production for credits on a planet times production, times player/planet setting for 100% minus all prates
    'credits_prate' => 3.0,
    // The rate of production for colonists on a planet prior to consideration of organics
    'colonist_production_rate' => 0.005,
    // The rate of reproduction for colonists on a planet after consideration of starvation due to organics
    'colonist_reproduction_rate' => 0.0005,
    // The interest rate offered by the IGB
    'interest_rate' => 1.0005,

    /*
    |--------------------------------------------------------------------------
    | Port Production Values
    |--------------------------------------------------------------------------
    |
    | Production and Trade values for Port structures. The game has four port
    | types: ore, organics, goods and energy.
    |
    */

    // Default price for ore
    'ore_price' => 11,
    // The delta, or difference for ore to range + or - from the default to allow trading profitably
    'ore_delta' => 5,
    // The amount of ore that is regenerated by a port every tick times the port_regenrate
    'ore_rate' => 75000,
    // The rate of production for ore on a planet times production, times player/planet setting for ore_prate
    'ore_prate' => 0.25,
    // The maximum amount of ore a port will accept or produce up to
    'ore_limit' => 100000000,


    // Default price of organics
    'organics_price' => 5,
    // The delta, or difference for organics to range + or - from the default to allow trading profitably
    'organics_delta' => 2,
    // The amount of organics that is regenerated by a port every tick times the port_regenrate
    'organics_rate' => 5000,
    // The rate of production for organics on a planet times production, times player/planet setting for org_prate
    'organics_prate' => 0.5,
    // The maximum amount of organics a port will accept or produce up to
    'organics_limit' => 100000000,


    // Default price of goods
    'goods_price' => 15,
    // The delta, or difference for goods to range + or - from the default to allow trading profitably
    'goods_delta' => 7,
    // The amount of goods that is regenerated by a port every tick times the port_regenrate
    'goods_rate' => 75000,
    // The rate of production for goods on a planet times production, times player/planet setting for goods_prate
    'goods_prate' => 0.25,
    // The maximum amount of goods a port will accept or produce up to
    'goods_limit' => 100000000,


    // Default price of energy
    'energy_price' => 3,
    // The delta, or difference for energy to range + or - from the default to allow trading profitably
    'energy_delta' => 1,
    // The amount of energy that is regenerated by a port every tick times the port_regenrate
    'energy_rate' => 75000,
    // The rate of production for energy on a planet times production, times player/planet setting for energy_prate
    'energy_prate' => 0.5,
    // The maximum amount of energy a port will accept or produce up to
    'energy_limit' => 1000000000,

    /*
    |--------------------------------------------------------------------------
    | Ship Device Settings
    |--------------------------------------------------------------------------
    |
    | Ship device limits and pricing.
    |
    */

    // The price for a genesis device purchased at a special port
    'dev_genesis_price' => 1000000,
    // The price for a beacon purchased at a special port
    'dev_beacon_price' => 100,
    // The price for an emergency warp device purchased at a special port
    'dev_emerwarp_price' => 1000000,
    // The price for a warp editor purchased at a special port
    'dev_warpedit_price' => 100000,
    // The price for a mine deflector purchased at a special port
    'dev_minedeflector_price' => 10,
    // The price for an escape pod purchased at a special port
    'dev_escapepod_price' => 100000,
    // The price for a fuel scoop gives energy while real spacing purchased at a special port
    'dev_fuelscoop_price' => 100000,
    // The price for a last seen ship device purchased at a special port
    'dev_lssd_price' => 10000000,
    // The price for units of armor purchased at a special port
    'armor_price' => 5,
    // The price for a fighter purchased at a special port
    'fighter_price' => 50,
    // The price for a torpedo purchased at a special port
    'torpedo_price' => 25,
    // The standard price for a colonist at a special port
    'colonist_price' => 5,
    // The amount of damage a single torpedo will cause
    'torp_dmg_rate' => 10,
    // The maximum number of emergency warp devices a player can have
    'max_emerwarp' => 10,
    // Must stay at 55 due to PHP/MySQL cap limit.
    'max_upgrades_devices' => 55,
    // The maximum number of genesis devices a player can have at one time
    'max_genesis' => 10,
    // The maximum number of beacons a player can have at one time
    'max_beacons' => 10,
    // The maximum number of warpeditors a player can have at one time
    'max_warpedit' => 10,

    /*
    |--------------------------------------------------------------------------
    | Base Build Costs
    |--------------------------------------------------------------------------
    |
    | Requirements for building a planetary base.
    |
    */

    // The amount of ore required to be placed on a planet to create a base.
    'base_ore' => 10000,
    // The amount of goods required to be placed on a planet to create a base.
    'base_goods' => 10000,
    // The amount of organics required to be placed on a planet to create a base.
    'base_organics' => 10000,
    // The amount of credits required to be placed on a planet to create a base.
    'base_credits' => 10000000,

    /*
    |--------------------------------------------------------------------------
    | Colour Settings
    |--------------------------------------------------------------------------
    |
    | TODO: To be moved into page header as css variables.
    |
    */

    // GUI colors - soon to be moved into templates
    'color_header' => '#500050',
    // GUI colors - soon to be moved into templates
    'color_line1' => '#300030',
    // GUI colors - soon to be moved into templates
    'color_line2' => '#400040',

    /*
    |--------------------------------------------------------------------------
    | Newbie Nice
    |--------------------------------------------------------------------------
    |
    |
    */

    // If a ship is destroyed without a EWD, *and* is below a certain level for all items, then regen their ship
    'newbie_nice' => true,
    // If a destroyed player has a hull less than newbie hull, they will be regen'd to play more
    'newbie_hull' => 8,
    // If a destroyed player has a engines less than newbie engines, they will be regen'd to play more
    'newbie_engines' => 8,
    // If a destroyed player has a power less than newbie power, they will be regen'd to play more
    'newbie_power' => 8,
    // If a destroyed player has a computer less than newbie computer, they will be regen'd to play more
    'newbie_computer' => 8,
    // If a destroyed player has a sensors less than newbie sensors, they will be regen'd to play more
    'newbie_sensors' => 8,
    // If a destroyed player has a armor less than newbie armor, they will be regen'd to play more
    'newbie_armor' => 8,
    // If a destroyed player has a shields less than newbie shield, they will be regen'd to play more
    'newbie_shields' => 8,
    // If a destroyed player has a beams less than newbie beams, they will be regen'd to play more
    'newbie_beams' => 8,
    // If a destroyed player has a torp_launcher less than newbie torp_launcher, they will be regen'd to play more.
    'newbie_torp_launchers' => 8,
    // If a destroyed player has a cloak less than newbie cloak, they will be regen'd to play more.
    'newbie_cloak' => 8,

    /*
    |--------------------------------------------------------------------------
    | Ship Upgrades
    |--------------------------------------------------------------------------
    |
    |
    |
    */

    // Upgrade price is upgrade factor OR 2^level difference times the upgrade cost
    'upgrade_cost' => 1000,
    // Upgrade factor is the numeric base usually 2 that is raised to the power of level difference for determining cost
    'upgrade_factor' => 2,
    // How effective a level is. amount = level_factor ^ item_level possibly times another value, depending on the item
    'level_factor' => 1.45,
    // The number of units that a single hull can hold
    'inventory_factor' => 1,

    /*
    |--------------------------------------------------------------------------
    | Bounty Settings
    |--------------------------------------------------------------------------
    |
    |
    |
    */

    // Max amount a player can place as bounty - good idea to make it the same as $ibank_svalue. 0=disable
    'bounty_maxvalue' => 0.15,
    // Ratio of players networth before attacking results in a bounty. 0=disable
    'bounty_ratio' => 0.75,
    // Minimum number of turns a target must have had before attacking them may not get you a bounty. 0=disable
    'bounty_minturns' => 500,

    /*
    |--------------------------------------------------------------------------
    | Scan Settings
    |--------------------------------------------------------------------------
    |
    |
    |
    */

    // The cost in turns for doing a full scan
    'fullscan_cost' => 1,
    // The percentage added to the comparison of cloak to sensors to determine the possibility of error
    'scan_error_factor' => 20,

    /*
    |--------------------------------------------------------------------------
    | Xenobe Settings
    |--------------------------------------------------------------------------
    |
    |
    |
    */

    // What Xenobe start with
    'xen_start_credits' => 1000000,
    // Amount of credits each xenobe receive on each xenobe tick
    'xen_unemployment' => 100000,
    // Percent of xenobe that are aggressive or hostile
    'xen_aggression' => 100,
    // Percent of created xenobe that will own planets. Recommended to keep at small percentage
    'xen_planets' => 5,

    /*
    |--------------------------------------------------------------------------
    | Misc Settings
    |--------------------------------------------------------------------------
    |
    |
    |
    */

    // Minimum size hull has to be to hit mines
    'mine_hullsize' => 8,
    // Max hull size before EWD degrades
    'ewd_maxhullsize' => 15,
    // Number of sectors you'd like your universe to have
    'sector_max' => 1000,
    // Maximum number of links in a sector
    'link_max' => 10,
    // This increases the distance between sectors, which increases the cost of realspace movement
    'universe_size' => 200,
    // The maximum hull size you can have before being towed out of fed space
    'fed_max_hull' => 8,
    // The maximum number of ranks displayed on ranking.php
    'max_ranks' => 100,
    // Amount of rating gained from combat
    'rating_combat_factor' => 0.8,
    // Additional factor added to tech levels by having a base on your planet. All your base are belong to us.
    'base_defense' => 1,
    // The maximum number of colonists on a planet
    'colonist_limit' => 100000000,
    // How many units of organics does a single colonist eat require to avoid starvation
    'organics_consumption' => 0.05,
    // If there is insufficient organics, colonists die of starvation at this rate/percentage
    'starvation_death_rate' => 0.01,
    // The maximum number of planets allowed in a sector
    'max_planets_sector' => 5,
    // The maximum number of saved traderoutes a player can have
    'max_traderoutes_player' => 40,
    // The minimum number of planets with bases in a sector that a player needs to have to take ownership of the zone
    'min_bases_to_own' => 3,
    // The default language the game displays in until a player chooses a language
    'default_lang' => 'english',
    // If transferring credits to/from corp planets is allowed.
    'corp_planet_transfers' => false,
    // Percentage of planet's value a ship must be worth to be able to capture it. 0=disable
    'min_value_capture' => 0,
    // The percentage rate at which defenses fits, mines degrade during scheduler runs
    'defence_degrade_rate' => 0.05,
    // The amount of energy needed from planets in sector to maintain a fighter during scheduler runs
    'energy_per_fighter' => 0.10,
    // Percentage of colonists killed by space plague
    'space_plague_kills' => 0.20,
    // Max amount of credits allowed on a planet without a base
    'max_credits_without_base' => 10000000,
    // The amount of units regenerated by ports during a scheduler tick
    'port_regenrate' => 10,
    // Switch between old style footer and new style. Old is text, and only the time until next update. New is a table including server time.
    'footer_style' => 'old',
    // Should the footer show the memory and time to generate page?
    'footer_show_debug' => true,
    // Limit captured planets Max Credits to max_credits_without_base
    'sched_planet_valid_credits' => true,
    // Stop access on all Special Ports when you have a federation bounty on you.
    'bounty_all_special' => true,
    // Address for the forum link
    'link_forums' => 'https://forums.blacknova.net/',
    // The title for the administrator
    'admin_name' => 'Game Admin',
    // The title for the administrators ship
    'admin_ship_name' => "Game Admin's ship",
    // The title for the administrators zone
    'admin_zone_name' => "Game Admin's zone",
    // True if you'd like to enable gravatars for players
    'enable_gravatars' => false,
    // Used for players and the admin until/unless they choose another
    'default_template' => 'classic',
    // The number of presets available to players
    'preset_max' => 5,
];
