# phpMyAdmin MySQL-Dump
# http://phpwizard.net/phpMyAdmin/
#
# Host: localhost Database : blacknova

# --------------------------------------------------------
#
# Table structure for table 'links'
#

CREATE TABLE links (
   link_id bigint(20) unsigned DEFAULT '0' NOT NULL auto_increment,
   link_start bigint(20) unsigned DEFAULT '0' NOT NULL,
   link_dest bigint(20) unsigned DEFAULT '0' NOT NULL,
   PRIMARY KEY (link_id),
   KEY link_start (link_start),
   KEY link_dest (link_dest)
);


# --------------------------------------------------------
#
# Table structure for table 'ships'
#

CREATE TABLE ships (
   ship_id bigint(20) unsigned DEFAULT '0' NOT NULL auto_increment,
   ship_name char(20),
   ship_destroyed enum('Y','N') DEFAULT 'N' NOT NULL,
   character_name char(20) NOT NULL,
   password char(16) NOT NULL,
   email char(40) NOT NULL,
   hull tinyint(3) unsigned DEFAULT '0' NOT NULL,
   engines tinyint(3) unsigned DEFAULT '0' NOT NULL,
   power tinyint(3) unsigned DEFAULT '0' NOT NULL,
   computer tinyint(3) unsigned DEFAULT '0' NOT NULL,
   sensors tinyint(3) unsigned DEFAULT '0' NOT NULL,
   beams tinyint(3) unsigned DEFAULT '0' NOT NULL,
   torp_launchers tinyint(3) DEFAULT '0' NOT NULL,
   torps bigint(20) unsigned DEFAULT '0' NOT NULL,
   shields tinyint(3) unsigned DEFAULT '0' NOT NULL,
   armour tinyint(3) unsigned DEFAULT '0' NOT NULL,
   armour_pts bigint(20) DEFAULT '0' NOT NULL,
   cloak tinyint(3) unsigned DEFAULT '0' NOT NULL,
   credits bigint(20) DEFAULT '0' NOT NULL,
   sector bigint(20) unsigned,
   ship_ore bigint(20) DEFAULT '0' NOT NULL,
   ship_organics bigint(20) DEFAULT '0' NOT NULL,
   ship_goods bigint(20) DEFAULT '0' NOT NULL,
   ship_energy bigint(20) DEFAULT '0' NOT NULL,
   ship_colonists bigint(20) DEFAULT '0' NOT NULL,
   ship_fighters bigint(20) DEFAULT '0' NOT NULL,
   turns smallint(4) DEFAULT '0' NOT NULL,
   ship_damage set('engines','power','computer','sensors','torps','cloak','shields') NOT NULL,
   on_planet enum('Y','N') DEFAULT 'N' NOT NULL,
   dev_warpedit smallint(5) DEFAULT '0' NOT NULL,
   dev_genesis smallint(5) DEFAULT '0' NOT NULL,
   dev_beacon smallint(5) DEFAULT '0' NOT NULL,
   dev_emerwarp smallint(5) DEFAULT '0' NOT NULL,
   dev_escapepod enum('Y','N') DEFAULT 'N' NOT NULL,
   dev_fuelscoop enum('Y','N') DEFAULT 'N' NOT NULL,
   dev_minedeflector smallint(5) DEFAULT '0' NOT NULL,
   turns_used bigint(20) unsigned DEFAULT '0' NOT NULL,
   last_login datetime,
   preset1 bigint(20) DEFAULT '0' NOT NULL,
   preset2 bigint(20) DEFAULT '0' NOT NULL,
   preset3 bigint(20) DEFAULT '0' NOT NULL,
   PRIMARY KEY (ship_id),
   KEY ship_id (ship_id),
   UNIQUE ship_id_2 (ship_id)
);


# --------------------------------------------------------
#
# Table structure for table 'universe'
#

CREATE TABLE universe (
   sector_id bigint(20) unsigned DEFAULT '0' NOT NULL auto_increment,
   sector_name tinytext,
   zone_id bigint(20) DEFAULT '0' NOT NULL,
   port_type enum('ore','organics','goods','energy','special','none') DEFAULT 'none' NOT NULL,
   port_organics bigint(20) DEFAULT '0' NOT NULL,
   port_ore bigint(20) DEFAULT '0' NOT NULL,
   port_goods bigint(20) DEFAULT '0' NOT NULL,
   port_energy bigint(20) DEFAULT '0' NOT NULL,
   planet enum('Y','N') DEFAULT 'N' NOT NULL,
   planet_name tinytext,
   planet_organics bigint(20) DEFAULT '0' NOT NULL,
   planet_ore bigint(20) DEFAULT '0' NOT NULL,
   planet_goods bigint(20) DEFAULT '0' NOT NULL,
   planet_energy bigint(20) DEFAULT '0' NOT NULL,
   planet_colonists bigint(20) DEFAULT '0' NOT NULL,
   planet_credits bigint(20) DEFAULT '0' NOT NULL,
   planet_fighters bigint(20) DEFAULT '0' NOT NULL,
   planet_owner bigint(20) unsigned,
   base enum('Y','N') DEFAULT 'N' NOT NULL,
   base_sells enum('Y','N') DEFAULT 'N' NOT NULL,
   base_torp bigint(20) DEFAULT '0' NOT NULL,
   beacon tinytext,
   angle1 float(10,2) DEFAULT '0.00' NOT NULL,
   angle2 float(10,2) DEFAULT '0.00' NOT NULL,
   distance bigint(20) unsigned DEFAULT '0' NOT NULL,
   mines bigint(20) DEFAULT '0' NOT NULL,
   planet_defeated enum('Y','N') DEFAULT 'N' NOT NULL,
   PRIMARY KEY (sector_id),
   KEY sector_id (sector_id),
   UNIQUE sector_id_2 (sector_id),
   UNIQUE sector_id_3 (sector_id)
);

# --------------------------------------------------------
#
# Table structure for table 'zones'
#

CREATE TABLE zones (
   zone_id bigint(20) unsigned DEFAULT '0' NOT NULL auto_increment,
   zone_name tinytext,
   allow_beacon enum('Y','N') DEFAULT 'Y' NOT NULL,
   allow_attack enum('Y','N') DEFAULT 'Y' NOT NULL,
   allow_warpedit enum('Y','N') DEFAULT 'Y' NOT NULL,
   allow_planet enum('Y','N') DEFAULT 'Y' NOT NULL,
   max_hull bigint(20) DEFAULT '0' NOT NULL,
   PRIMARY KEY(zone_id),
   KEY zone_id(zone_id)
);
