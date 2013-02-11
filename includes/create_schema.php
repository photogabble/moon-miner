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
// File: includes/create_schema.php

if (strpos ($_SERVER['PHP_SELF'], 'create_schema.php')) // Prevent direct access to this file
{
    $error_file = $_SERVER['SCRIPT_NAME'];
    include 'error.php';
}

function create_schema ($db, $ADODB_SESSION_DB)
{
    // Delete all tables in the database
    table_header("Dropping Tables --- Stage 3");

    // Have SQL prepare a query for dropping tables that contains the list of all tables according to SQL.
    $drop_tables_stmt = $db->Execute('SELECT CONCAT( "DROP TABLE ", GROUP_CONCAT(TABLE_NAME)) AS stmt FROM information_schema.TABLES WHERE TABLE_SCHEMA = "' . $ADODB_SESSION_DB. '" AND TABLE_NAME LIKE "' . $db->prefix. '%"');

    // Use the query to now drop all tables as reported by SQL.
    $drop_tables = $db->Execute($drop_tables_stmt->fields['stmt']);

    $err = true_or_false (0, $db->ErrorMsg(), "No errors found", $db->ErrorNo() . ": " . $db->ErrorMsg());
    table_row ($db, "Dropping all tables","Failed","Passed");
    table_footer("Hover over the failed line to see the error.");
    echo "<strong>Dropping stage complete.</strong><p>";

// Create database schema
table_header("Creating Tables");

$db->Execute("CREATE TABLE IF NOT EXISTS {$db->prefix}adodb_logsql (" .
             "created datetime NOT NULL," .
             "sql0 varchar(250) NOT NULL," .
             "sql1 text NOT NULL," .
             "params text NOT NULL," .
             "tracer text NOT NULL," .
             "timer decimal(16,6) NOT NULL" .
             ")");
$err = true_or_false (0, $db->ErrorMsg(),"No errors found", $db->ErrorNo() . ": " . $db->ErrorMsg());

table_row ($db, "Creating adodb_logsql Table","Failed","Passed");

$db->Execute("CREATE TABLE IF NOT EXISTS {$db->prefix}bans (" .
             "ban_id int(10) unsigned NOT NULL AUTO_INCREMENT," .
             "ban_type tinyint(3) unsigned NOT NULL DEFAULT '0'," .
             "ban_mask varchar(16) DEFAULT NULL," .
             "ban_ship int(10) unsigned DEFAULT NULL," .
             "ban_date datetime DEFAULT NULL," .
             "public_info text," .
             "admin_info text," .
             "PRIMARY KEY (`ban_id`)" .
             ")");
$err = true_or_false (0, $db->ErrorMsg(),"No errors found", $db->ErrorNo() . ": " . $db->ErrorMsg());

table_row ($db, "Creating bans Table","Failed","Passed");

$db->Execute("CREATE TABLE IF NOT EXISTS {$db->prefix}bounty (" .
             "bounty_id int unsigned NOT NULL auto_increment," .
             "amount bigint(20) unsigned DEFAULT '0' NOT NULL," .
             "bounty_on int unsigned DEFAULT '0' NOT NULL," .
             "placed_by int unsigned DEFAULT '0' NOT NULL," .
             "PRIMARY KEY (bounty_id)," .
             "KEY bounty_on (bounty_on)," .
             "KEY placed_by (placed_by)" .
             ")");
$err = true_or_false (0, $db->ErrorMsg(),"No errors found", $db->ErrorNo() . ": " . $db->ErrorMsg());

table_row ($db, "Creating bounty Table","Failed","Passed");

$db->Execute("CREATE TABLE IF NOT EXISTS {$db->prefix}gameconfig (" .
             "config_id smallint(5) NOT NULL AUTO_INCREMENT," .
             "section varchar(30) NOT NULL DEFAULT 'game'," .
             "name varchar(75) NOT NULL," .
             "category char(30) NOT NULL," .
             "value varchar(128) NOT NULL," .
             "description varchar(255) NOT NULL," .
             "PRIMARY KEY (config_id)" .
             ")");
$err = true_or_false (0, $db->ErrorMsg(),"No errors found", $db->ErrorNo() . ": " . $db->ErrorMsg());

table_row ($db, "Creating gameconfig Table","Failed","Passed");

$db->Execute("CREATE TABLE IF NOT EXISTS {$db->prefix}ibank_accounts (" .
             "ship_id int DEFAULT '0' NOT NULL," .
             "balance bigint(20) DEFAULT '0'," .
             "loan bigint(20)  DEFAULT '0'," .
             "loantime datetime," .
             "PRIMARY KEY(ship_id)" .
             ")");
$err = true_or_false (0, $db->ErrorMsg(),"No errors found", $db->ErrorNo() . ": " . $db->ErrorMsg());

table_row ($db, "Creating ibank_accounts Table","Failed","Passed");

$db->Execute("CREATE TABLE IF NOT EXISTS {$db->prefix}ibank_transfers (" .
             "transfer_id int NOT NULL auto_increment," .
             "source_id int DEFAULT '0' NOT NULL," .
             "dest_id int DEFAULT '0' NOT NULL," .
             "time datetime," .
             "amount bigint(20) DEFAULT '0' NOT NULL," .
             "PRIMARY KEY(transfer_id)," .
             "KEY amount (amount)" .
             ")");
$err = true_or_false (0, $db->ErrorMsg(),"No errors found", $db->ErrorNo() . ": " . $db->ErrorMsg());

table_row ($db, "Creating ibank_transfers Table","Failed","Passed");

$db->Execute("CREATE TABLE IF NOT EXISTS {$db->prefix}ip_bans (" .
             "ban_id int unsigned NOT NULL auto_increment," .
             "ban_mask varchar(16) NOT NULL," .
             "PRIMARY KEY (ban_id)" .
             ")");
$err = true_or_false (0, $db->ErrorMsg(),"No errors found", $db->ErrorNo() . ": " . $db->ErrorMsg());

table_row ($db, "Creating ip_bans Table","Failed","Passed");

$db->Execute("CREATE TABLE IF NOT EXISTS {$db->prefix}languages (" .
             "lang_id smallint(5) NOT NULL AUTO_INCREMENT," .
             "section varchar(30) NOT NULL DEFAULT 'english'," .
             "name varchar(75) NOT NULL," .
             "value varchar(2000) NOT NULL," .
             "category char(30) NOT NULL," .
             "PRIMARY KEY (lang_id)" .
             ")");
$err = true_or_false (0, $db->ErrorMsg(),"No errors found", $db->ErrorNo() . ": " . $db->ErrorMsg());

table_row ($db, "Creating languages Table","Failed","Passed");

$db->Execute("CREATE TABLE IF NOT EXISTS {$db->prefix}links (" .
             "link_id int unsigned NOT NULL auto_increment," .
             "link_start int unsigned DEFAULT '0' NOT NULL," .
             "link_dest int unsigned DEFAULT '0' NOT NULL," .
             "PRIMARY KEY (link_id)," .
             "KEY link_start (link_start)," .
             "KEY link_dest (link_dest)" .
             ")");
$err = true_or_false (0, $db->ErrorMsg(),"No errors found", $db->ErrorNo() . ": " . $db->ErrorMsg());

table_row ($db, "Creating links Table","Failed","Passed");

$db->Execute("CREATE TABLE IF NOT EXISTS {$db->prefix}logs (" .
             "log_id int unsigned NOT NULL auto_increment," .
             "ship_id int DEFAULT '0' NOT NULL," .
             "type mediumint(5) DEFAULT '0' NOT NULL," .
             "time datetime," .
             "data varchar(255)," .
             "PRIMARY KEY (log_id)," .
             "KEY idate (ship_id,time)" .
             ")");
$err = true_or_false (0, $db->ErrorMsg(),"No errors found", $db->ErrorNo() . ": " . $db->ErrorMsg());

table_row ($db, "Creating logs Table","Failed","Passed");

$db->Execute("CREATE TABLE IF NOT EXISTS {$db->prefix}messages (" .
             "ID int NOT NULL auto_increment," .
             "sender_id int NOT NULL default '0'," .
             "recp_id int NOT NULL default '0'," .
             "subject varchar(250) default ''," .
             "sent varchar(19) NULL," .
             "message longtext NOT NULL," .
             "notified enum('Y','N') NOT NULL default 'N'," .
             "PRIMARY KEY  (ID) " .
             ")");
$err = true_or_false (0, $db->ErrorMsg(),"No errors found", $db->ErrorNo() . ": " . $db->ErrorMsg());

table_row ($db, "Creating messages Table","Failed","Passed");

$db->Execute("CREATE TABLE IF NOT EXISTS {$db->prefix}movement_log (" .
             "event_id int unsigned NOT NULL auto_increment," .
             "ship_id int DEFAULT '0' NOT NULL," .
             "sector_id int DEFAULT '0'," .
             "time datetime ," .
             "PRIMARY KEY (event_id)," .
             "KEY ship_id(ship_id)," .
             "KEY sector_id (sector_id)" .
             ")");
$err = true_or_false (0, $db->ErrorMsg(),"No errors found", $db->ErrorNo() . ": " . $db->ErrorMsg());

table_row ($db, "Creating movement_log Table","Failed","Passed");

$db->Execute("CREATE TABLE IF NOT EXISTS {$db->prefix}news (" .
             "news_id int(11) NOT NULL auto_increment," .
             "headline varchar(100) NOT NULL," .
             "newstext text NOT NULL," .
             "user_id int(11)," .
             "date datetime," .
             "news_type varchar(10)," .
             "PRIMARY KEY (news_id)" .
             ")");
$err = true_or_false (0, $db->ErrorMsg(),"No errors found", $db->ErrorNo() . ": " . $db->ErrorMsg());

table_row ($db, "Creating news Table","Failed","Passed");

$db->Execute("CREATE TABLE IF NOT EXISTS {$db->prefix}planets (" .
             "planet_id int unsigned NOT NULL auto_increment," .
             "sector_id int unsigned DEFAULT '0' NOT NULL," .
             "name tinytext," .
             "organics bigint(20) DEFAULT '0' NOT NULL," .
             "ore bigint(20) DEFAULT '0' NOT NULL," .
             "goods bigint(20) DEFAULT '0' NOT NULL," .
             "energy bigint(20) DEFAULT '0' NOT NULL," .
             "colonists bigint(20) DEFAULT '0' NOT NULL," .
             "credits bigint(20) DEFAULT '0' NOT NULL," .
             "fighters bigint(20) DEFAULT '0' NOT NULL," .
             "torps bigint(20) DEFAULT '0' NOT NULL," .
             "owner int unsigned DEFAULT '0' NOT NULL," .
             "corp int unsigned DEFAULT '0' NOT NULL," .
             "base enum('Y','N') DEFAULT 'N' NOT NULL," .
             "sells enum('Y','N') DEFAULT 'N' NOT NULL," .
             "prod_organics int DEFAULT '20.0' NOT NULL," .
             "prod_ore int DEFAULT '0' NOT NULL," .
             "prod_goods int DEFAULT '0' NOT NULL," .
             "prod_energy int DEFAULT '0' NOT NULL," .
             "prod_fighters int DEFAULT '0' NOT NULL," .
             "prod_torp int DEFAULT '0' NOT NULL," .
             "defeated enum('Y','N') DEFAULT 'N' NOT NULL," .
             "PRIMARY KEY (planet_id)," .
             "KEY owner (owner)," .
             "KEY corp (corp)" .
             ")");
$err = true_or_false (0, $db->ErrorMsg(),"No errors found", $db->ErrorNo() . ": " . $db->ErrorMsg());

table_row ($db, "Creating planets Table","Failed","Passed");

$db->Execute("CREATE TABLE IF NOT EXISTS {$db->prefix}scheduler (" .
             "sched_id int unsigned NOT NULL auto_increment," .
             "repeate enum('Y','N') DEFAULT 'N' NOT NULL," .
             "ticks_left int unsigned DEFAULT '0' NOT NULL," .
             "ticks_full int unsigned DEFAULT '0' NOT NULL," .
             "spawn int unsigned DEFAULT '0' NOT NULL," .
             "sched_file varchar(30) NOT NULL," .
             "extra_info varchar(50)," .
             "last_run BIGINT(20)," .
             "PRIMARY KEY (sched_id)" .
             ")");
$err = true_or_false (0, $db->ErrorMsg(),"No errors found", $db->ErrorNo() . ": " . $db->ErrorMsg());

table_row ($db, "Creating scheduler Table","Failed","Passed");

$db->Execute("CREATE TABLE IF NOT EXISTS {$db->prefix}sector_defence (" .
             "defence_id int unsigned NOT NULL auto_increment," .
             "ship_id int DEFAULT '0' NOT NULL," .
             "sector_id int unsigned DEFAULT '0' NOT NULL," .
             "defence_type enum('M','F') DEFAULT 'M' NOT NULL," .
             "quantity bigint(20) DEFAULT '0' NOT NULL," .
             "fm_setting enum('attack','toll') DEFAULT 'toll' NOT NULL," .
             "PRIMARY KEY (defence_id)," .
             "KEY sector_id (sector_id)," .
             "KEY ship_id (ship_id)" .
             ")");
$err = true_or_false (0, $db->ErrorMsg(),"No errors found", $db->ErrorNo() . ": " . $db->ErrorMsg());

table_row ($db, "Creating sector_defence Table","Failed","Passed");

$db->Execute("CREATE TABLE IF NOT EXISTS {$db->prefix}sessions (" .
             "sesskey VARCHAR(64) NOT NULL DEFAULT ''," .
             "expiry DATETIME NOT NULL," .
             "expireref VARCHAR(250) DEFAULT ''," .
             "created DATETIME NOT NULL," .
             "modified DATETIME NOT NULL," .
             "sessdata LONGTEXT," .
             "PRIMARY KEY (sesskey)," .
             "INDEX sess2_expiry( expiry )," .
             "INDEX sess2_expireref( expireref )" .
             ")");
$err = true_or_false (0, $db->ErrorMsg(),"No errors found", $db->ErrorNo() . ": " . $db->ErrorMsg());

table_row ($db, "Creating sessions Table","Failed","Passed");

$db->Execute("CREATE TABLE IF NOT EXISTS {$db->prefix}ships (" .
             "ship_id int unsigned NOT NULL auto_increment," .
             "ship_name char(20)," .
             "ship_destroyed enum('Y','N') DEFAULT 'N' NOT NULL," .
             "character_name char(20) NOT NULL," .
             "password char(60) NOT NULL," .
             "email char(60) NOT NULL," .
             "hull tinyint(3) unsigned DEFAULT '0' NOT NULL," .
             "engines tinyint(3) unsigned DEFAULT '0' NOT NULL," .
             "power tinyint(3) unsigned DEFAULT '0' NOT NULL," .
             "computer tinyint(3) unsigned DEFAULT '0' NOT NULL," .
             "sensors tinyint(3) unsigned DEFAULT '0' NOT NULL," .
             "beams tinyint(3) unsigned DEFAULT '0' NOT NULL," .
             "torp_launchers tinyint(3) DEFAULT '0' NOT NULL," .
             "torps bigint(20) DEFAULT '0' NOT NULL," .
             "shields tinyint(3) unsigned DEFAULT '0' NOT NULL," .
             "armor tinyint(3) unsigned DEFAULT '0' NOT NULL," .
             "armor_pts bigint(20) DEFAULT '0' NOT NULL," .
             "cloak tinyint(3) unsigned DEFAULT '0' NOT NULL," .
             "credits bigint(20) DEFAULT '0' NOT NULL," .
             "sector int unsigned DEFAULT '0' NOT NULL," .
             "ship_ore bigint(20) DEFAULT '0' NOT NULL," .
             "ship_organics bigint(20) DEFAULT '0' NOT NULL," .
             "ship_goods bigint(20) DEFAULT '0' NOT NULL," .
             "ship_energy bigint(20) DEFAULT '0' NOT NULL," .
             "ship_colonists bigint(20) DEFAULT '0' NOT NULL," .
             "ship_fighters bigint(20) DEFAULT '0' NOT NULL," .
             "ship_damage smallint(5) DEFAULT '0' NOT NULL," .
             "turns smallint(4) DEFAULT '0' NOT NULL," .
             "on_planet enum('Y','N') DEFAULT 'N' NOT NULL," .
             "dev_warpedit smallint(5) DEFAULT '0' NOT NULL," .
             "dev_genesis smallint(5) DEFAULT '0' NOT NULL," .
             "dev_beacon smallint(5) DEFAULT '0' NOT NULL," .
             "dev_emerwarp smallint(5) DEFAULT '0' NOT NULL," .
             "dev_escapepod enum('Y','N') DEFAULT 'N' NOT NULL," .
             "dev_fuelscoop enum('Y','N') DEFAULT 'N' NOT NULL," .
             "dev_minedeflector bigint(20) DEFAULT '0' NOT NULL," .
             "turns_used int unsigned DEFAULT '0' NOT NULL," .
             "last_login datetime," .
             "rating int DEFAULT '0' NOT NULL," .
             "score int DEFAULT '0' NOT NULL," .
             "team int DEFAULT '0' NOT NULL," .
             "team_invite int DEFAULT '0' NOT NULL," .
             "ip_address tinytext NOT NULL," .
             "planet_id int unsigned DEFAULT '0' NOT NULL," .
             "preset1 int DEFAULT '0' NOT NULL," .
             "preset2 int DEFAULT '0' NOT NULL," .
             "preset3 int DEFAULT '0' NOT NULL," .
             "trade_colonists enum('Y', 'N') DEFAULT 'Y' NOT NULL," .
             "trade_fighters enum('Y', 'N') DEFAULT 'N' NOT NULL," .
             "trade_torps enum('Y', 'N') DEFAULT 'N' NOT NULL," .
             "trade_energy enum('Y', 'N') DEFAULT 'Y' NOT NULL," .
             "cleared_defences tinytext," .
             "lang varchar(30) DEFAULT 'english.inc' NOT NULL," .
             "dev_lssd enum('Y','N') DEFAULT 'Y' NOT NULL," .
             "PRIMARY KEY (email)," .
             "KEY email (email)," .
             "KEY sector (sector)," .
             "KEY ship_destroyed (ship_destroyed)," .
             "KEY on_planet (on_planet)," .
             "KEY team (team)," .
             "KEY ship_id (ship_id)" .
             ")");
$err = true_or_false (0, $db->ErrorMsg(),"No errors found", $db->ErrorNo() . ": " . $db->ErrorMsg());

table_row ($db, "Creating ships Table","Failed","Passed");

$db->Execute("CREATE TABLE IF NOT EXISTS {$db->prefix}teams (" .
             "id int DEFAULT '0' NOT NULL," .
             "creator int DEFAULT '0'," .
             "team_name tinytext," .
             "description tinytext," .
             "number_of_members tinyint(3) DEFAULT '0' NOT NULL," .
             "admin enum('Y','N') NOT NULL default 'N'," .
             "PRIMARY KEY(id)," .
             "KEY admin (admin)" .
             ")");
$err = true_or_false (0, $db->ErrorMsg(),"No errors found", $db->ErrorNo() . ": " . $db->ErrorMsg());

table_row ($db, "Creating teams Table","Failed","Passed");

$db->Execute("CREATE TABLE IF NOT EXISTS {$db->prefix}traderoutes (" .
             "traderoute_id int unsigned NOT NULL auto_increment," .
             "source_id int unsigned DEFAULT '0' NOT NULL," .
             "dest_id int unsigned DEFAULT '0' NOT NULL," .
             "source_type enum('P','L','C','D') DEFAULT 'P' NOT NULL," .
             "dest_type enum('P','L','C','D') DEFAULT 'P' NOT NULL," .
             "move_type enum('R','W') DEFAULT 'W' NOT NULL," .
             "owner int unsigned DEFAULT '0' NOT NULL," .
             "circuit enum('1','2') DEFAULT '2' NOT NULL," .
             "PRIMARY KEY (traderoute_id)," .
             "KEY owner (owner)" .
             ")");
$err = true_or_false (0, $db->ErrorMsg(),"No errors found", $db->ErrorNo() . ": " . $db->ErrorMsg());

table_row ($db, "Creating traderoutes Table","Failed","Passed");

$db->Execute("CREATE TABLE IF NOT EXISTS {$db->prefix}universe (" .
             "sector_id int unsigned NOT NULL auto_increment," .
             "sector_name tinytext," .
             "zone_id int DEFAULT '0' NOT NULL," .
             "port_type enum('ore','organics','goods','energy','special','none') DEFAULT 'none' NOT NULL," .
             "port_organics bigint(20) DEFAULT '0' NOT NULL," .
             "port_ore bigint(20) DEFAULT '0' NOT NULL," .
             "port_goods bigint(20) DEFAULT '0' NOT NULL," .
             "port_energy bigint(20) DEFAULT '0' NOT NULL," .
             "KEY zone_id (zone_id)," .
             "KEY port_type (port_type)," .
             "beacon tinytext," .
             "angle1 float(10,2) DEFAULT '0.00' NOT NULL," .
             "angle2 float(10,2) DEFAULT '0.00' NOT NULL," .
             "distance bigint(20) unsigned DEFAULT '0' NOT NULL," .
             "fighters bigint(20) DEFAULT '0' NOT NULL," .
             "PRIMARY KEY (sector_id)" .
             ")");
$err = true_or_false (0, $db->ErrorMsg(),"No errors found", $db->ErrorNo() . ": " . $db->ErrorMsg());

table_row ($db, "Creating universe Table","Failed","Passed");

$db->Execute("CREATE TABLE IF NOT EXISTS {$db->prefix}xenobe (" .
             "xenobe_id char(40) NOT NULL," .
             "active enum('Y','N') DEFAULT 'Y' NOT NULL," .
             "aggression smallint(5) DEFAULT '0' NOT NULL," .
             "orders smallint(5) DEFAULT '0' NOT NULL," .
             "PRIMARY KEY (xenobe_id)," .
             "KEY xenobe_id (xenobe_id)" .
             ")");
$err = true_or_false (0, $db->ErrorMsg(),"No errors found", $db->ErrorNo() . ": " . $db->ErrorMsg());

table_row ($db, "Creating xenobe Table","Failed","Passed");

$db->execute("CREATE TABLE IF NOT EXISTS {$db->prefix}zones (" .
             "zone_id int unsigned NOT NULL auto_increment," .
             "zone_name tinytext," .
             "owner int unsigned DEFAULT '0' NOT NULL," .
             "corp_zone enum('Y', 'N') DEFAULT 'N' NOT NULL," .
             "allow_beacon enum('Y','N','L') DEFAULT 'Y' NOT NULL," .
             "allow_attack enum('Y','N') DEFAULT 'Y' NOT NULL," .
             "allow_planetattack enum('Y','N') DEFAULT 'Y' NOT NULL," .
             "allow_warpedit enum('Y','N','L') DEFAULT 'Y' NOT NULL," .
             "allow_planet enum('Y','L','N') DEFAULT 'Y' NOT NULL," .
             "allow_trade enum('Y','L','N') DEFAULT 'Y' NOT NULL," .
             "allow_defenses enum('Y','L','N') DEFAULT 'Y' NOT NULL," .
             "max_hull int DEFAULT '0' NOT NULL," .
             "PRIMARY KEY(zone_id)," .
             "KEY zone_id(zone_id)" .
             ")");
$err = true_or_false (0, $db->ErrorMsg(),"No errors found", $db->ErrorNo() . ": " . $db->ErrorMsg());

table_row ($db, "Creating zones Table","Failed","Passed");

// This adds a news item into the newly created news table
$db->Execute("INSERT INTO {$db->prefix}news (headline, newstext, date, news_type) " .
             "VALUES ('Big Bang!','Scientists have just discovered the Universe exists!',NOW(), 'col25')");

$err = true_or_false (0, $db->ErrorMsg(),"No errors found", $db->ErrorNo() . ": " . $db->ErrorMsg());

table_row ($db, "Inserting first news item","Failed","Inserted");

table_footer("Hover over the failed row to see the error.");

// Finished
echo "<strong>Database schema creation completed successfully.</strong><BR>";
}

?>
