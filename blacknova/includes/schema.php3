<?

function create_schema()
{
global $maxlen_password;

// Delete all tables in the database
echo "Dropping all tables...";
mysql_query("DROP TABLE IF EXISTS ibank_accounts");
mysql_query("DROP TABLE IF EXISTS links");
mysql_query("DROP TABLE IF EXISTS news");
mysql_query("DROP TABLE IF EXISTS newstypes");
mysql_query("DROP TABLE IF EXISTS newsactions");
mysql_query("DROP TABLE IF EXISTS ships");
mysql_query("DROP TABLE IF EXISTS teams");
mysql_query("DROP TABLE IF EXISTS universe");
mysql_query("DROP TABLE IF EXISTS zones");
echo "All tables have been dropped...<BR>";

// Create database schema
echo "Creating tables...<BR>";
echo "Creating table: links...";
mysql_query("CREATE TABLE links(" .
            "link_id bigint(20) unsigned DEFAULT '0' NOT NULL auto_increment," .
            "link_start bigint(20) unsigned DEFAULT '0' NOT NULL," .
            "link_dest bigint(20) unsigned DEFAULT '0' NOT NULL," .
            "PRIMARY KEY (link_id)," .
            "KEY link_start (link_start)," .
            "KEY link_dest (link_dest)" .
            ")");
echo "created.<BR>";

echo "Creating table: ships...";
mysql_query("CREATE TABLE ships(" .
            "ship_id bigint(20) unsigned DEFAULT '0' NOT NULL auto_increment," .
            "ship_name char(20)," .
            "ship_destroyed enum('Y','N') DEFAULT 'N' NOT NULL," .
            "character_name char(20) NOT NULL," .
            "password char($maxlen_password) NOT NULL," .
            "email char(40) NOT NULL," .
            "hull tinyint(3) unsigned DEFAULT '0' NOT NULL," .
            "engines tinyint(3) unsigned DEFAULT '0' NOT NULL," .
            "power tinyint(3) unsigned DEFAULT '0' NOT NULL," .
            "computer tinyint(3) unsigned DEFAULT '0' NOT NULL," .
            "sensors tinyint(3) unsigned DEFAULT '0' NOT NULL," .
            "beams tinyint(3) unsigned DEFAULT '0' NOT NULL," .
            "torp_launchers tinyint(3) DEFAULT '0' NOT NULL," .
            "torps bigint(20) unsigned DEFAULT '0' NOT NULL," .
            "shields tinyint(3) unsigned DEFAULT '0' NOT NULL," .
            "armour tinyint(3) unsigned DEFAULT '0' NOT NULL," .
            "armour_pts bigint(20) DEFAULT '0' NOT NULL," .
            "cloak tinyint(3) unsigned DEFAULT '0' NOT NULL," .
            "credits bigint(20) DEFAULT '0' NOT NULL," .
            "sector bigint(20) unsigned," .
            "ship_ore bigint(20) DEFAULT '0' NOT NULL," .
            "ship_organics bigint(20) DEFAULT '0' NOT NULL," .
            "ship_goods bigint(20) DEFAULT '0' NOT NULL," .
            "ship_energy bigint(20) DEFAULT '0' NOT NULL," .
            "ship_colonists bigint(20) DEFAULT '0' NOT NULL," .
            "ship_fighters bigint(20) DEFAULT '0' NOT NULL," .
            "turns smallint(4) DEFAULT '0' NOT NULL," .
            "ship_damage set('engines','power','computer','sensors','torps','cloak','shields') NOT NULL," .
            "on_planet enum('Y','N') DEFAULT 'N' NOT NULL," .
            "dev_warpedit smallint(5) DEFAULT '0' NOT NULL," .
            "dev_genesis smallint(5) DEFAULT '0' NOT NULL," .
            "dev_beacon smallint(5) DEFAULT '0' NOT NULL," .
            "dev_emerwarp smallint(5) DEFAULT '0' NOT NULL," .
            "dev_escapepod enum('Y','N') DEFAULT 'N' NOT NULL," .
            "dev_fuelscoop enum('Y','N') DEFAULT 'N' NOT NULL," .
            "dev_minedeflector smallint(5) DEFAULT '0' NOT NULL," .
            "turns_used bigint(20) unsigned DEFAULT '0' NOT NULL," .
            "last_login datetime," .
            "preset1 bigint(20) DEFAULT '0' NOT NULL," .
            "preset2 bigint(20) DEFAULT '0' NOT NULL," .
            "preset3 bigint(20) DEFAULT '0' NOT NULL," .
            "rating bigint(20) DEFAULT '0' NOT NULL," .
            "score bigint(20) DEFAULT '0' NOT NULL," .
            "interface enum('N','O') DEFAULT 'N' NOT NULL," .
            "ip_address tinytext NOT NULL," .
            "PRIMARY KEY (ship_id)," .
            "KEY ship_id (ship_id)" .
            ")");
echo "created.<BR>";

echo "Creating table: universe...";
mysql_query("CREATE TABLE universe(" .
            "sector_id bigint(20) unsigned DEFAULT '0' NOT NULL auto_increment," .
            "sector_name tinytext," .
            "zone_id bigint(20) DEFAULT '0' NOT NULL," .
            "port_type enum('ore','organics','goods','energy','special','none') DEFAULT 'none' NOT NULL," .
            "port_organics bigint(20) DEFAULT '0' NOT NULL," .
            "port_ore bigint(20) DEFAULT '0' NOT NULL," .
            "port_goods bigint(20) DEFAULT '0' NOT NULL," .
            "port_energy bigint(20) DEFAULT '0' NOT NULL," .
            "planet enum('Y','N') DEFAULT 'N' NOT NULL," .
            "planet_name tinytext," .
            "planet_organics bigint(20) DEFAULT '0' NOT NULL," .
            "planet_ore bigint(20) DEFAULT '0' NOT NULL," .
            "planet_goods bigint(20) DEFAULT '0' NOT NULL," .
            "planet_energy bigint(20) DEFAULT '0' NOT NULL," .
            "planet_colonists bigint(20) DEFAULT '0' NOT NULL," .
            "planet_credits bigint(20) DEFAULT '0' NOT NULL," .
            "planet_fighters bigint(20) DEFAULT '0' NOT NULL," .
            "planet_owner bigint(20) unsigned," .
            "base enum('Y','N') DEFAULT 'N' NOT NULL," .
            "base_sells enum('Y','N') DEFAULT 'N' NOT NULL," .
            "base_torp bigint(20) DEFAULT '0' NOT NULL," .
            "prod_organics float(5,2) unsigned DEFAULT '20.0' NOT NULL," .
            "prod_ore float(5,2) unsigned DEFAULT '20.0' NOT NULL," .
            "prod_goods float(5,2) unsigned DEFAULT '20.0' NOT NULL," .
            "prod_energy float(5,2) unsigned DEFAULT '20.0' NOT NULL," .
            "prod_fighters float(5,2) unsigned DEFAULT '10.0' NOT NULL," .
            "prod_torp float(5,2) unsigned DEFAULT '10.0' NOT NULL," .
            "beacon tinytext," .
            "angle1 float(10,2) DEFAULT '0.00' NOT NULL," .
            "angle2 float(10,2) DEFAULT '0.00' NOT NULL," .
            "distance bigint(20) unsigned DEFAULT '0' NOT NULL," .
            "fighters bigint(20) DEFAULT '0' NOT NULL," .
            "mines bigint(20) DEFAULT '0' NOT NULL," .
            "fm_owner bigint(20) DEFAULT '0' NOT NULL," .
            "fm_setting enum('attack','toll') DEFAULT 'toll' NOT NULL," .
            "planet_defeated enum('Y','N') DEFAULT 'N' NOT NULL," .
            "PRIMARY KEY (sector_id)," .
            "KEY sector_id (sector_id)," .
            "UNIQUE sector_id_2 (sector_id)," .
            "UNIQUE sector_id_3 (sector_id)" .
            ")");
echo "created.<BR>";

echo "Creating table: zones...";
mysql_query("CREATE TABLE zones(" .
            "zone_id bigint(20) unsigned DEFAULT '0' NOT NULL auto_increment," .
            "zone_name tinytext," .
            "allow_beacon enum('Y','N') DEFAULT 'Y' NOT NULL," .
            "allow_attack enum('Y','N') DEFAULT 'Y' NOT NULL," .
            "allow_warpedit enum('Y','N') DEFAULT 'Y' NOT NULL," .
            "allow_planet enum('Y','N') DEFAULT 'Y' NOT NULL," .
            "max_hull bigint(20) DEFAULT '0' NOT NULL," .
            "PRIMARY KEY(zone_id)," .
            "KEY zone_id(zone_id)" .
            ")");
echo "created.<BR>";

echo "Creating table: ibank_accounts...";
mysql_query("CREATE TABLE ibank_accounts(" .
            "id bigint(20) DEFAULT '0' NOT NULL," .
            "ballance bigint(20) DEFAULT '0'," .
            "loan bigint(20)  DEFAULT '0'," .
            "ibank_shareholder int(11) DEFAULT '0' NOT NULL," .
            "ibank_employee int(1) DEFAULT '0' NOT NULL," .
            "ibank_owner int(1) DEFAULT '0' NOT NULL," .
            "PRIMARY KEY(id)" .
            ")");
echo "created.<BR>";

echo "Creating table: teams...";
mysql_query("CREATE TABLE teams(" .
            "id bigint(20) DEFAULT '0' NOT NULL," .
            "creator bigint(20) DEFAULT '0'," .
            "team_name tinytext," .
            "PRIMARY KEY(id)" .
            ")");
echo "created.<BR>";

echo "Creating table: news...";
mysql_query("CREATE TABLE news(" .
            "news_id bigint(20) unsigned NOT NULL auto_increment," .
            "newsdate timestamp(14)," .
            "newstypes_id varchar(6) NOT NULL," .
            "action_id varchar(5) NOT NULL," .
            "newsdata longtext NOT NULL," .
            "PRIMARY KEY (news_id)," .
            "KEY newstypes_id (newstypes_id)" .
            ")");
echo "created.<BR>";

echo "Creating table: newstypes...";
mysql_query("CREATE TABLE newstypes(" .
            "newstypes_id varchar(6) NOT NULL," .
            "description varchar(50) NOT NULL," .
            "PRIMARY KEY (newstypes_id)," .
            "KEY newstypes_id (newstypes_id)," .
            "UNIQUE newstypes_id_2 (newstypes_id)" .
            ")");
echo "created.<BR>";

echo "Creating table: newsactions...";
mysql_query("CREATE TABLE newsactions(" .
            "action_id varchar(6) NOT NULL," .
            "description varchar(50)," .
            "PRIMARY KEY (action_id)," .
            "KEY action_id (action_id)" .
            ")");
echo "created.<BR>";

echo "Creating default values for the reference tables...";
//Insert default values into the reference tables
mysql_query("INSERT INTO newstypes VALUES ( 'GEN', 'General News')");
mysql_query("INSERT INTO newstypes VALUES ( 'BAT', 'Battle Results')");
mysql_query("INSERT INTO newstypes VALUES ( 'SCAN', 'Unauthorized Scan')");
mysql_query("INSERT INTO newsactions VALUES( 'SSCAN', 'Ship to Ship Scan')");
mysql_query("INSERT INTO newsactions VALUES( 'PSCAN', 'Planet Scan')");
mysql_query("INSERT INTO newsactions VALUES( 'PDEF', 'Planet Defeated')");
mysql_query("INSERT INTO newsactions VALUES( 'SDEF', 'Ship Defeated')");
echo "created.<BR>";

//Finished
echo "Database schema creation complete.<BR>";
}

?>
