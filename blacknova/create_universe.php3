<?

include("config.php3");
updatecookie();

$title="Create Universe";
include("header.php3");

connectdb();
bigtitle();

if($swordfish != $adminpass)
{
  echo "<form action=create_universe.php3 method=post>";
  echo "Password: <input type=password name=swordfish size=20 maxlength=20><BR><BR>";
  echo "<input type=submit value=Submit><input type=reset value=Reset>";
  echo "</form>";
}
elseif($swordfish == $adminpass && $engage == "")
{
  echo "Max sectors set to $sector_max in config.php3<BR><BR>";
  echo "<form action=create_universe.php3 method=post>";
  echo "<table>";
  echo "<tr><td><b><u>Base/Planet Setup</u></b></td><td></td></tr>";
  echo "<tr><td>Percent Special</td><td><input type=text name=special size=2 maxlength=2 value=1></td></tr>";
  echo "<tr><td>Percent Ore</td><td><input type=text name=ore size=2 maxlength=2 value=20></td></tr>";
  echo "<tr><td>Percent Organics</td><td><input type=text name=organics size=2 maxlength=2 value=20></td></tr>";
  echo "<tr><td>Percent Goods</td><td><input type=text name=goods size=2 maxlength=2 value=20></td></tr>";
  echo "<tr><td>Percent Energy</td><td><input type=text name=energy size=2 maxlength=2 value=20></td></tr>";
  echo "<tr><td>Percent Empty</td><td>Equal to 100 - total of above.</td></tr>";
  echo "<tr><td>Initial Commodities to Sell<br><td><input type=text name=initscommod size=5 maxlength=5 value=50.00> % of max</td></tr>";
  echo "<tr><td>Initial Commodities to Buy<br><td><input type=text name=initbcommod size=5 maxlength=5 value=50.00> % of max</td></tr>";
  echo "<tr><td><b><u>Sector/Link Setup</u></b></td><td></td></tr>";
  $fedsecs = intval($sector_max / 300); 
  $loops = intval($sector_max / 300);
  echo "<TR><TD>Number of Federation sectors</TD><TD><INPUT TYPE=TEXT NAME=fedsecs SIZE=4 MAXLENGTH=4 VALUE=$fedsecs></TD></TR>";
  echo "<tr><td>Number of loops</td><td><input type=text name=loops size=2 maxlength=2 value=$loops></td></tr>";
  echo "<tr><td>Percent of sectors with unowned planets</td><td><input type=text name=planets size=2 maxlength=2 value=10></td></tr>";
  echo "<tr><td></td><td><input type=hidden name=engage value=1><input type=hidden name=swordfish value=$swordfish><input type=submit value=Submit><input type=reset value=Reset></td></tr>";
  echo "</table>";
  echo "</form>";
}
elseif($swordfish == $adminpass && $engage == "1")
{
  if($fedsecs > $sector_max)
  {
    echo "The number of Federation sectors must be smaller than the size of the universe!";
    include("footer.php3");
    die();
  }
  echo "So you would like your $sector_max sector universe to have:<BR><BR>";
  $spp = round($sector_max*$special/100);
  echo "$spp special ports<BR>";
  $oep = round($sector_max*$ore/100);
  echo "$oep ore ports<BR>";
  $ogp = round($sector_max*$organics/100);
  echo "$ogp organics ports<BR>";
  $gop = round($sector_max*$goods/100);
  echo "$gop goods ports<BR>";
  $enp = round($sector_max*$energy/100);
  echo "$enp energy ports<BR>";
  echo "$initscommod% initial commodities to sell<BR>";
  echo "$initbcommod% initial commodities to buy<BR>";
  $empty = $sector_max-$spp-$oep-$ogp-$gop-$enp;
  echo "$empty empty sectors<BR>";
  echo "$fedsecs Federation sectors<BR>";
  echo "$loops loops<BR>";
  $nump = round ($sector_max*$planets/100);
  echo "$nump unowned planets<BR><BR>";
  echo "If this is correct, click confirm - otherwise go back.<BR>";
  echo "<form action=create_universe.php3 method=post>";
  echo "<input type=hidden name=spp value=$spp>";
  echo "<input type=hidden name=oep value=$oep>";
  echo "<input type=hidden name=ogp value=$ogp>";
  echo "<input type=hidden name=gop value=$gop>";
  echo "<input type=hidden name=enp value=$enp>";
  echo "<input type=hidden name=initscommod value=$initscommod>";
  echo "<input type=hidden name=initbcommod value=$initbcommod>";
  echo "<input type=hidden name=nump value=$nump>";
  echo "<INPUT TYPE=HIDDEN NAME=fedsecs VALUE=$fedsecs>";
  echo "<input type=hidden name=loops value=$loops>";
  echo "<input type=hidden name=engage value=2><input type=hidden name=swordfish value=$swordfish>";
  echo "<input type=submit value=Confirm>";
  echo "</form>";
  echo "<BR><BR><FONT COLOR=RED>WARNING: ALL TABLES WILL BE DROPPED AND THE GAME WILL BE RESET WHEN YOU CLICK 'CONFIRM' !</FONT>";
}
elseif($swordfish==$adminpass && $engage=="2")
{
  // logs should also be deleted
  // ...
  echo "Dropping all tables...<BR>";
  mysql_query("DROP TABLE IF EXISTS links,ships,universe,zones,ibank_accounts");
  echo "Creating tables...<BR>";
  mysql_query("CREATE TABLE links(" .
                 "link_id bigint(20) unsigned DEFAULT '0' NOT NULL auto_increment," .
                 "link_start bigint(20) unsigned DEFAULT '0' NOT NULL," .
                 "link_dest bigint(20) unsigned DEFAULT '0' NOT NULL," .
                 "PRIMARY KEY (link_id)," .
                 "KEY link_start (link_start)," .
                 "KEY link_dest (link_dest)" .
               ")");
  mysql_query("CREATE TABLE ships(" .
                 "ship_id bigint(20) unsigned DEFAULT '0' NOT NULL auto_increment," .
                 "ship_name char(20)," .
                 "ship_destroyed enum('Y','N') DEFAULT 'N' NOT NULL," .
                 "character_name char(20) NOT NULL," .
                 "password char(16) NOT NULL," .
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
                 "rating smallint DEFAULT '0' NOT NULL," .
                 "score bigint(20) DEFAULT '0' NOT NULL," .
                 "interface enum('N','O') DEFAULT 'N' NOT NULL," .
                 "PRIMARY KEY (ship_id)," .
                 "KEY ship_id (ship_id)," .
                 "UNIQUE ship_id_2 (ship_id)" .
               ")");
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
  mysql_query("CREATE TABLE ibank_accounts(" .
                 "id bigint(20) DEFAULT '0' NOT NULL," .
                 "ballance bigint(20) DEFAULT '0'," .
                 "loan bigint(20)  DEFAULT '0'," .
                 "ibank_shareholder int(11) DEFAULT '0' NOT NULL," .
                 "ibank_employee int(1) DEFAULT '0' NOT NULL," .
                 "ibank_owner int(1) DEFAULT '0' NOT NULL," .
                 "PRIMARY KEY(id)" .
               ")");
  mysql_query("CREATE TABLE teams(" .
                 "id bigint(20) DEFAULT '0' NOT NULL," .
                 "creator bigint(20) DEFAULT '0'," .
                 "team_name tinytext," .
                 "PRIMARY KEY(id)" .
               ")");

  echo "Creating sector 0 - Sol...<BR>";
  $initsore = $ore_limit * $initscommod / 100.0;
  $initsorganics = $organics_limit * $initscommod / 100.0;
  $initsgoods = $goods_limit * $initscommod / 100.0;
  $initsenergy = $energy_limit * $initscommod / 100.0;
  $initbore = $ore_limit * $initbcommod / 100.0;
  $initborganics = $organics_limit * $initbcommod / 100.0;
  $initbgoods = $goods_limit * $initbcommod / 100.0;
  $initbenergy = $energy_limit * $initbcommod / 100.0;
  $insert = mysql_query("INSERT INTO universe (sector_id, sector_name, zone_id, port_type, port_organics, port_ore, port_goods, port_energy, planet, planet_name, planet_organics, planet_ore, planet_goods, planet_energy, planet_colonists, planet_credits, planet_fighters, planet_owner, base, base_sells, base_torp, beacon, angle1, angle2, distance, fighters, mines, fm_owner, fm_setting, planet_defeated) VALUES ('0', 'Sol', '1', 'special', '', '', '', '', 'N', '', '', '', '', '', '', '', '', '', 'N', 'N', '', 'Sol: Hub of the Universe', '0', '0', '0', ,'0', '0', '', ,'toll','N')");
  $update = mysql_query("UPDATE universe SET sector_id=0 WHERE sector_id=1");
  echo "Creating sector 1 - Alpha Centauri...<BR>";
  $insert = mysql_query("INSERT INTO universe (sector_id, sector_name, zone_id, port_type, port_organics, port_ore, port_goods, port_energy, planet, planet_name, planet_organics, planet_ore, planet_goods, planet_energy, planet_colonists, planet_credits, planet_fighters, planet_owner, base, base_sells, base_torp, beacon, angle1, angle2, distance, fighters, mines, fm_owner, fm_setting, planet_defeated) VALUES ('1', 'Alpha Centauri', '1', 'energy', '', '', '', '', 'N', '', '', '', '', '', '', '', '', '', 'N', 'N', '', 'Aplha Centauri: Gateway to the Galaxy', '0', '0', '1', '0', '0', '', ,'toll','N')");
  $remaining = $sector_max-1;
  srand((double)microtime()*1000000);
  echo "Creating remaining $remaining sectors...";
  for($i=1; $i<=$remaining; $i++)
  {
    $distance=rand(1,$universe_size);
    $angle1=rand(0,180);
    $angle2=rand(0,90);
    $insert = mysql_query("INSERT INTO universe (sector_id,zone_id,angle1,angle2,distance) VALUES ('','1',$angle1,$angle2,$distance)");
  }
  echo "<BR>Selecting $fedsecs Federation sectors...<BR>";
  $replace = mysql_query("REPLACE INTO zones (zone_id, zone_name, allow_beacon, allow_attack, allow_warpedit, allow_planet, max_hull) VALUES ('1', 'Unchartered space', 'Y', 'Y', 'Y', 'Y', '0')");
  $replace = mysql_query("REPLACE INTO zones (zone_id, zone_name, allow_beacon, allow_attack, allow_warpedit, allow_planet, max_hull) VALUES ('2', 'Federation space', 'N', 'N', 'N', 'N', '$fed_max_hull')");
  $update = mysql_query("UPDATE universe SET zone_id='2' WHERE sector_id<$fedsecs");
  echo "Selecting $spp sectors for additional special ports...<BR>";
  for($i=2; $i<=$sector_max; $i++)
  {
    $num = rand(2, $sector_max - 1);
    $sectors[$i] = $num;
  }
//  $sectors=range(2,$sector_max);
//  shuffle($sectors);
  for($i=2; $i<$spp; $i++)
  {
    mysql_query("UPDATE universe SET port_type='special' WHERE sector_id=$sectors[$i]");
    echo "$sectors[$i] - ";
  }
  echo "done<BR>";
  echo "Selecting $oep sectors for ore ports...<BR>";
  $last = $spp;
  for($i=$last; $i<$last+$oep; $i++)
  {
    mysql_query("UPDATE universe SET port_type='ore',port_ore=$initsore,port_organics=$initborganics,port_goods=$initbgoods,port_energy=$initbenergy WHERE sector_id=$sectors[$i]");
    echo "$sectors[$i] - ";
  }
  echo "done<BR>";
  echo "Selecting $ogp sectors for organics ports...<BR>";
  $last = $last + $oep;
  for($i=$last; $i<$last+$ogp; $i++)
  {
    mysql_query("UPDATE universe SET port_type='organics',port_ore=$initbore,port_organics=$initsorganics,port_goods=$initbgoods,port_energy=$initbenergy WHERE sector_id=$sectors[$i]");
    echo "$sectors[$i] - ";
  }
  echo "done<BR>";
  echo "Selecting $gop sectors for goods ports...<BR>";
  $last = $last + $gop;
  for($i=$last; $i<$last+$gop; $i++)
  {
    mysql_query("UPDATE universe SET port_type='goods',port_ore=$initbore,port_organics=$initborganics,port_goods=$initsgoods,port_energy=$initbenergy WHERE sector_id=$sectors[$i]");
    echo "$sectors[$i] - ";
  }
  echo "done<BR>";
  echo "Selecting $enp sectors for energy ports...<BR>";
  $last = $last + $gop;
  for($i=$last; $i<$last+$enp; $i++)
  {
    mysql_query("UPDATE universe SET port_type='energy',port_ore=$initbore,port_organics=$initborganics,port_goods=$initbgoods,port_energy=$initsenergy WHERE sector_id=$sectors[$i]");
    echo "$sectors[$i] - ";
  }
  echo "done<BR>";
  echo "Selecting $nump sectors for unowned planets...<BR>";
  for($i=0; $i<=$sector_max; $i++)
  {
    $num = rand(0, $sector_max - 1);
    $sectors[$i] = $num;
  }
//  $sectors=range(0,$sector_max);
//  shuffle($sectors);
  for($i=0; $i<$nump; $i++)
  {
    $update = mysql_query("UPDATE universe SET planet='Y', planet_colonists=2, planet_owner=null WHERE sector_id=$sectors[$i]");
    echo "$sectors[$i] - ";
  }
  echo "done<BR>";
  // this is a temporary fix in order not to have planets in restricted sectors
  echo "Removing planets from restricted sectors...<BR>";
  $result = mysql_query("SELECT zone_id FROM zones WHERE allow_planet='N'");
  while($row = mysql_fetch_array($result))
  {
    $update = mysql_query("UPDATE universe SET planet='N', planet_colonists=0, planet_owner=null WHERE zone_id=$row[zone_id]");
  }
  echo "done<BR>";
  $loopsize = round($sector_max/$loops);
  $start = 0;
  $finish = $loopsize - 1;
  for($i=1; $i<=$loops; $i++)
  {
    echo "Creating loop $i of $loopsize sectors - from sector $start to $finish...<BR>";
    for($j=$start; $j<$finish; $j++)
    {
      $k = $j + 1;
      $update = mysql_query("INSERT INTO links (link_start,link_dest) VALUES ($j,$k)");
      $update = mysql_query("INSERT INTO links (link_start,link_dest) VALUES ($k,$j)");
      echo "$j<=>$k - ";
    }
    $update = mysql_query("INSERT INTO links (link_start,link_dest) VALUES ($start,$finish)");
    $update = mysql_query("INSERT INTO links (link_start,link_dest) VALUES ($finish,$start)");
    echo "$finish<=>$start";
    echo "done loop $i<BR>";
    $start=$finish+1;
    $finish=$finish+$loopsize;
  }
  echo "Randomly One-way Linking Sectors...<BR>";
  for($i=0; $i<=$sector_max; $i++)
  {
    $num = rand(0, $sector_max - 1);
    $sectors[$i] = $num;
  }
  //$sectors=range(0,$sector_max);
  //shuffle($sectors);
  for($i=0; $i<=$sector_max; $i++)
  {
    $update = mysql_query("INSERT INTO links (link_start,link_dest) VALUES ($i,$sectors[$i])");
    echo "$i=>$sectors[$i] - ";
  }
  echo "done.<BR>";
  echo "Randomly Two-way Linking Sectors...<BR>";
  for($i=0; $i<=$sector_max; $i++)
  {
    $num = rand(0, $sector_max - 1);
    $sectors[$i] = $num;
  }
  //$sectors=range(0,$sector_max);
  //shuffle($sectors);
  for($i=0; $i<=$sector_max; $i++)
  {
    $update = mysql_query("INSERT INTO links (link_start,link_dest) VALUES ($i,$sectors[$i])");
    $update = mysql_query("INSERT INTO links (link_start,link_dest) VALUES ($sectors[$i],$i)");
    echo "$i<=>$sectors[$i] - ";
  }
  echo "done.<BR>";
  
  echo "Creating iBank default account...<BR>";
  mysql_query("INSERT INTO ibank_accounts (id,ballance,loan,ibank_shareholder,ibank_employee,ibank_owner) VALUES ($ibank_owner,1000000000000000,0,100,1,1);");
  echo "done.<BR>";
}
else
{
  echo "Huh?";
}
  
include("footer.php3");

?> 
