<?
include("config.php3");
updatecookie();

$title="Trade Routes";
include("header.php3");

connectdb();

if(checklogin())
{
  die();
}

//-------------------------------------------------------------------------------------------------


bigtitle();

$result = mysql_query("SELECT * FROM ships WHERE email='$username'");
$playerinfo = mysql_fetch_array($result);

$result = mysql_query("SELECT * FROM traderoutes WHERE owner=$playerinfo[ship_id]");
$num_traderoutes=mysql_num_rows($result);
$i=0;
while($row = mysql_fetch_array($result))
{
  $traderoutes[$i] = $row;
  $i++;
}
mysql_free_result($result);

$freeholds = NUM_HOLDS($playerinfo[hull]) - $playerinfo[ship_ore] - $playerinfo[ship_organics] - $playerinfo[ship_goods] - $playerinfo[ship_colonists];
$maxholds = NUM_HOLDS($playerinfo[hull]);
$maxenergy = NUM_ENERGY($playerinfo[power]);
if ($playerinfo[ship_colonists] < 0 || $playerinfo[ship_ore] < 0 || $playerinfo[ship_organics] < 0 || $playerinfo[ship_goods] < 0 || $playerinfo[ship_energy] < 0 || $freeholds < 0)
{
	if ($playerinfo[ship_colonists] < 0 || $playerinfo[ship_colonists] > $maxholds) 
	{
		adminlog($playerinfo[ship_id], "$playerinfo[ship_name]'s ship had $playerinfo[ship_colonists] colonists, Max Holds: $maxholds.");
		$playerinfo[ship_colonists] = 0;
		adminlog($playerinfo[ship_id], "$playerinfo[ship_name]'s ship set to $playerinfo[ship_colonists] colonists.");
	}
	if ($playerinfo[ship_ore] < 0 || $playerinfo[ship_ore] > $maxholds) 
	{
		adminlog($playerinfo[ship_id], "$playerinfo[ship_name]'s ship had $playerinfo[ship_ore] ore, Max Holds: $maxholds.");
		$playerinfo[ship_ore] = 0;
		adminlog($playerinfo[ship_id], "$playerinfo[ship_name]'s set to $playerinfo[ship_ore] ore.");
	}
	if ($playerinfo[ship_organics] < 0 || $playerinfo[ship_organics] > $maxholds) 
	{
		adminlog($playerinfo[ship_id], "$playerinfo[ship_name]'s ship had $playerinfo[ship_organics] organics, Max Holds: $maxholds.");
		$playerinfo[ship_organics] = 0;
		adminlog($playerinfo[ship_id], "$playerinfo[ship_name]'s set to $playerinfo[ship_organics] organics.");
	}
	if ($playerinfo[ship_goods] < 0 || $playerinfo[ship_goods] > $maxholds) 
	{
		adminlog($playerinfo[ship_id], "$playerinfo[ship_name]'s ship had $playerinfo[ship_goods] goods, Max Holds: $maxholds.");	
		$playerinfo[ship_goods] = 0;
		adminlog($playerinfo[ship_id], "$playerinfo[ship_name]'s set to $playerinfo[ship_goods] goods");
	}
	if ($playerinfo[ship_energy] < 0 || $playerinfo[ship_energy] > $maxenergy)
	{
		adminlog($playerinfo[ship_id], "$playerinfo[ship_name]'s ship had $playerinfo[ship_energy] energy, Max Energy: $maxenergy.");
		$playerinfo[ship_energy] = 0;
		adminlog($playerinfo[ship_id], "$playerinfo[ship_name]'s set to $playerinfo[ship_energy] energy");
	}
	if ($freeholds < 0)
	{
		adminlog($playerinfo[ship_id], "$playerinfo[ship_name]'s ship had $playerinfo[ship_freeholds] holds");
		$freeholds = 0;
		adminlog($playerinfo[ship_id], "$playerinfo[ship_name]'s set to $playerinfo[ship_freeholds] holds");
	}
$update1 = mysql_query("UPDATE ships SET ship_ore=$playerinfo[ship_ore], ship_organics=$playerinfo[ship_organics], ship_goods=$playerinfo[ship_goods], ship_energy=$playerinfo[ship_energy], ship_colonists=$playerinfo[ship_colonists] WHERE ship_id=$playerinfo[ship_id]"); 
}



if($command == 'new')   //displays new trade route form
  traderoute_new('');
elseif($command == 'create')    //enters new route in db
  traderoute_create();
elseif($command == 'edit')    //displays new trade route form, edit
  traderoute_new($traderoute_id);
elseif($command == 'delete')  //displays delete info
  traderoute_delete();
elseif($command == 'settings')  //global traderoute settings form
  traderoute_settings();
elseif($command == 'setsettings') //enters settings in db
  traderoute_setsettings();
elseif(isset($engage)) //performs trade route
  traderoute_engage();


//-----------------------------------------------------------------
if($command != 'delete')
{
  echo '<p>Click <a href="traderoute.php?command=new">here</a> to create a new trade route<p>';
  echo '<p>To modify your global trade settings, click <a href=traderoute.php?command=settings>here</a><p>';
}
else
  echo "<p>Click <a href=\"traderoute.php?command=delete&confirm=yes&traderoute_id=$traderoute_id\">here</a> to confirm deletion of the following trade route :<p>";

if($num_traderoutes == 0)
  echo "You do not have any active trade routes to display.<p>";
else
{
  echo '<table border=1 cellspacing=1 cellpadding=2 width="100%" align=center>' .
       '<tr bgcolor=' . $color_line2 . '><td align="center" colspan=7><b><font color=white>
       ';

  if($command != 'delete')
    echo "Current Trade Routes";
  else
    echo "Delete Trade Route";

  echo "</font></b>" .
       "</td></tr>" .
       "<tr align=center bgcolor=$color_line2>" .
       "<td><font size=2 color=white><b>Source</b></font></td>" .
       "<td><font size=2 color=white><b>Src Type</b></font></td>" .
       "<td><font size=2 color=white><b>Destination</b></font></td>" .
       "<td><font size=2 color=white><b>Dest Type</b></font></td>" .
       "<td><font size=2 color=white><b>Move</b></font></td>" .
       "<td><font size=2 color=white><b>Circuit</b></font></td>" .
       "<td><font size=2 color=white><b>Change</b></font></td>" .
       "</tr>";
  $i=0;
  $curcolor=$color_line1;
  while($i < $num_traderoutes)
  {
    echo "<tr bgcolor=$curcolor>";
    if($curcolor == $color_line1)
      $curcolor = $color_line2; 
    else
      $curcolor = $color_line1;
    
    echo "<td><font size=2 color=white>";
    if($traderoutes[$i][source_type] == 'P')
      echo "&nbsp;Port in <b>" . $traderoutes[$i][source_id] . "</b></font></td>";
    else
    {
      $result = mysql_query("SELECT name, sector_id FROM planets WHERE planet_id=" . $traderoutes[$i][source_id]);
      if($result)
      {
        $planet1 = mysql_fetch_array($result);
        echo "&nbsp;Planet <b>$planet1[name]</b> in <a href=\"rsmove.php3?engage=1&destination=$planet1[sector_id]\">$planet1[sector_id]</a></font></td>";
      }
      else
        echo "&nbsp;Non-existant planet!</font></td>";
    }

    echo "<td align=center><font size=2 color=white>";
    if($traderoutes[$i][source_type] == 'P')
    {
      $result = mysql_query("SELECT * FROM universe WHERE sector_id=" . $traderoutes[$i][source_id]);
      $port1 = mysql_fetch_array($result);
      echo "&nbsp;$port1[port_type]</font></td>";
    }
    else
    {
      if(empty($planet1))
        echo "&nbsp;N/A</font></td>";
      else
        echo "&nbsp;Cargo</font></td>";
    }

    echo "<td><font size=2 color=white>";

    if($traderoutes[$i][dest_type] == 'P')
    	echo "&nbsp;Port in <a href=\"rsmove.php3?engage=1&destination=" . $traderoutes[$i][dest_id] . "\">" . $traderoutes[$i][dest_id] . "</a></font></td>";
    else
    {
      $result = mysql_query("SELECT name, sector_id FROM planets WHERE planet_id=" . $traderoutes[$i][dest_id]);
      if($result)
      {
        $planet2 = mysql_fetch_array($result);
        echo "&nbsp;Planet <b>$planet2[name]</b> in <a href=\"rsmove.php3?engage=1&destination=$planet2[sector_id]\">$planet2[sector_id]</a></font></td>";
      }
      else
        echo "&nbsp;Non-existant planet!</font></td>";
    }

    echo "<td align=center><font size=2 color=white>";
    if($traderoutes[$i][dest_type] == 'P')
    {
      $result = mysql_query("SELECT * FROM universe WHERE sector_id=" . $traderoutes[$i][dest_id]);
      $port2 = mysql_fetch_array($result);
      echo "&nbsp;$port2[port_type]</font></td>";
    }
    else
    {
      if(empty($planet2))
        echo "&nbsp;N/A</font></td>";
      else
      {
        echo "&nbsp;";
        if($playerinfo[trade_colonists] == 'N' && $playerinfo[trade_fighters] == 'N' && $playerinfo[trade_torps] == 'N')
          echo "None";
        else
        {
          if($playerinfo[trade_colonists] == 'Y')
            echo "Colonists";
          if($playerinfo[trade_fighters] == 'Y')
          {
            if($playerinfo[trade_colonists] == 'Y')
              echo ", ";
            echo "Fighters";
          }
          if($playerinfo[trade_torps] == 'Y')
            echo "<br>Torpedoes";
        }
        echo "</font></td>";
      }
    }
    echo "<td align=center><font size=2 color=white>";
    if($traderoutes[$i][move_type] == 'R')
    {
      echo "&nbsp;RS, ";
      
      if($traderoutes[$i][source_type] == 'P')
        $src=$port1;
      else 
        $src = $planet1[sector_id];

      if($traderoutes[$i][dest_type] == 'P')
        $dst=$port2;
      else 
        $dst = $planet2[sector_id];
            
      $dist = traderoute_distance($traderoutes[$i][source_type], $traderoutes[$i][dest_type], $src, $dst, $traderoutes[$i][circuit]);
      
      echo "$dist[triptime] turns<br>$dist[scooped] energy scooped";
            
      echo "</font></td>";
    
    }
    else
    {
      echo "&nbsp;Warp";
      if($traderoutes[$i][circuit] == '1')
        echo ", 2 turns";
      else
        echo ", 4 turns";
      echo "</font></td>";
    }

    echo "<td align=center><font size=2 color=white>";
    if($traderoutes[$i][circuit] == '1')
      echo "&nbsp;1 way</font></td>";
    else
      echo "&nbsp;2 ways</font></td>";

    echo "<td align=center><font size=2 color=white>";
    echo "<a href=\"traderoute.php?command=edit&traderoute_id=" . $traderoutes[$i][traderoute_id] . "\">";
    echo "Edit</a><br><a href=\"traderoute.php?command=delete&traderoute_id=" . $traderoutes[$i][traderoute_id] . "\">";
    echo "Delete</a></font></td></tr>";

    $i++;
  }

  echo "</table><p>";
}
?>

<?

//-------------------------------------------------------------------------------------------------

TEXT_GOTOMAIN();
include("footer.php3");

?> 

<?

function traderoute_die($error_msg)
{
  echo "<p>$error_msg<p>";

  
  TEXT_GOTOMAIN();
  include("footer.php3");
  die();
}

function traderoute_check_compatible($type1, $type2, $move, $circuit, $src, $dest)
{
  global $playerinfo;

  //check warp links compatibility
  if($move == 'warp')
  {
    $query = mysql_query("SELECT link_id FROM links WHERE link_start=$src[sector_id] AND link_dest=$dest[sector_id]");
    if(mysql_num_rows($query) == 0)
      traderoute_die("There is no warp link from sector $src[sector_id] to sector $dest[sector_id]");
    if($circuit == '2')
    {
      $query = mysql_query("SELECT link_id FROM links WHERE link_start=$dest[sector_id] AND link_dest=$src[sector_id]");
      if(mysql_num_rows($query) == 0)
        traderoute_die("There is no warp link from sector $dest[sector_id] to sector $src[sector_id]");
    }
  }
  
  //check ports compatibility
  if($type1 == 'port')
  {
    if($src[port_type] == 'special')
    {
      if($type2 != 'planet')
        traderoute_die("If a special port is source, a planet much be the destination for colonization.");
      if($dest[owner] != $playerinfo[ship_id] && ($dest[corp] == 0 || ($dest[corp] != $playerinfo[team])))
        traderoute_die("You can't colonize a planet that you or your team don't own.");
    }    
    else
    {
      if($type2 == 'planet')
        traderoute_die("A planet can be a destination only when source is a special port.");
      if($src[port_type] == $dest[port_type])
        traderoute_die("You can't make a traderoute for ports selling the same commodities.");
    }
  }
  else
  {
    if($type2 == 'planet')
      traderoute_die("You can't have both the source and destination be a planet.");
    if($dest[port_type] == 'special')
      traderoute_die("You can't sell commodities from a planet in a special port.");
  }
}


function traderoute_distance($type1, $type2, $start, $dest, $circuit, $sells = 'N')
{
  global $playerinfo;
  global $level_factor;

  $retvalue[triptime] = 0;
  $retvalue[scooped1] = 0;
  $retvalue[scooped2] = 0;
  $retvalue[scooped] = 0;

  if($type1 == 'L')
  {
    $query = mysql_query("SELECT * FROM universe WHERE sector_id=$start");
    $start = mysql_fetch_array($query);
  }
  
  if($type2 == 'L')
  {
    $query = mysql_query("SELECT * FROM universe WHERE sector_id=$dest");
    $dest = mysql_fetch_array($query);
  }

  if($start[sector_id] == $dest[sector_id])
  {
    if($circuit == '1')
      $retvalue[triptime] = '1';
    else
      $retvalue[triptime] = '2';
    return $retvalue;
  }

  $deg = pi() / 180;

  $sa1 = $start[angle1] * $deg;
  $sa2 = $start[angle2] * $deg;
  $fa1 = $dest[angle1] * $deg;
  $fa2 = $dest[angle2] * $deg;
  $x = $start[distance] * sin($sa1) * cos($sa2) - $dest[distance] * sin($fa1) * cos($fa2);
  $y = $start[distance] * sin($sa1) * sin($sa2) - $dest[distance] * sin($fa1) * sin($fa2);
  $z = $start[distance] * cos($sa1) - $dest[distance] * cos($fa1);
  $distance = round(sqrt(pow($x, 2) + pow($y, 2) + pow($z, 2)));
  $shipspeed = pow($level_factor, $playerinfo[engines]);
  $triptime = round($distance / $shipspeed);

  if(!$triptime && $destination != $playerinfo[sector])
    $triptime = 1;

  if($playerinfo[dev_fuelscoop] == "Y")
      $energyscooped = $distance * 100;
  else
    $energyscooped = 0;

  if($playerinfo[dev_fuelscoop] == "Y" && !$energyscooped && $triptime == 1)
    $energyscooped = 100;

  $free_power = NUM_ENERGY($playerinfo[power]) - $playerinfo[ship_energy];

  if($free_power < $energyscooped)
    $energyscooped = $free_power;

  if($energyscooped < 1)
    $energyscooped = 0;

  $retvalue[scooped1] = $energyscooped;

  if($circuit == '2')
  {
    if($sells == 'Y' && $playerinfo[dev_fuelscoop] == 'Y' && $type2 == 'P' && $dest[port_type] != 'energy')
    {
      $energyscooped = $distance * 100;
      $free_power = NUM_ENERGY($playerinfo[power]);
      if($free_power < $energyscooped)
        $energyscooped = $free_power;
      $retvalue[scooped2] = $energyscooped;
    }    
    elseif($playerinfo[dev_fuelscoop] == 'Y')
    {
      $energyscooped = $distance * 100;
      $free_power = NUM_ENERGY($playerinfo[power]) - $retvalue[scooped1] - $playerinfo[ship_energy];
      if($free_power < $energyscooped)
        $energyscooped = $free_power;
      $retvalue[scooped2] = $energyscooped;
    }
  }

  if($circuit == '2')
  {
    $triptime*=2;
    $triptime+=2;
  }
  else
    $triptime+=1;

  $retvalue[triptime] = $triptime;
  $retvalue[scooped] = $retvalue[scooped1] + $retvalue[scooped2];

  return $retvalue;
}

function traderoute_new($traderoute_id)
{
  global $playerinfo;
  global $num_traderoutes;
  global $max_traderoutes_player;

  if(!empty($traderoute_id))
  {
    $result = mysql_query("SELECT * FROM traderoutes WHERE traderoute_id=$traderoute_id");
    if(!result || mysql_num_rows($result) == 0)
      traderoute_die("Edit error : trade route not found in DB");
    $editroute = mysql_fetch_array($result);
  }

  if($num_traderoutes >= $max_traderoutes_player && empty($editroute))
    traderoute_die("<p>Sorry you have reached the maximum number of trade routes allowed.<p>");
  
  echo "<p><font size=3 color=blue><b>";
  if(empty($editroute))
    echo "Creating a new";
  else
    echo "Editing a ";
  echo "trade route</b></font><p>";

  $result = mysql_query("SELECT * FROM planets WHERE owner=$playerinfo[ship_id]");

  $num_planets = mysql_num_rows($result);
  $i=0;
  while ($row = mysql_fetch_array($result))
  {
    $planets[$i] = $row;
    if($planets[$i][name] == "")
      $planets[$i][name] = "Unnamed";
    $i++;
  }
  mysql_free_result($result);

  echo "You are currently in sector $playerinfo[sector]<br>";

  echo '
    <form action=traderoute.php?command=create method=post>
    <table border=0><tr>
    <td align=right><font size=2><b>Please select starting point : <br>&nbsp;</b></font></td>
    <tr>
    <td align=right><font size=2>Port : </font></td>
    <td><input type=radio name="ptype1" value="port"
    ';
  
  if(empty($editroute) || (!empty($editroute) && $editroute[source_type] == 'P'))
    echo " checked";
  
    echo '
    ></td>
    <td>&nbsp;&nbsp;<input type=text name=port_id1 size=20 align=center
    ';

  if(!empty($editroute) && $editroute[source_type] == 'P')
    echo " value=\"$editroute[source_id]\"";

    echo '
    ></td>
    </tr><tr>
    <td align=right><font size=2>Planet : </font></td>
    <td><input type=radio name="ptype1" value="planet"
    ';
    
  if(!empty($editroute) && $editroute[source_type] == 'L')
    echo " checked";

    echo '
    ></td>
    <td>&nbsp;&nbsp;<select name=planet_id1>
    ';
  
  if($num_planets == 0)
    echo "<option value=none>None</option>";
  else
  {
    $i=0;
    while($i < $num_planets)
    {
      echo "<option ";
      if($planets[$i][planet_id] == $editroute[source_id])
        echo "selected ";
      echo "value=" . $planets[$i][planet_id] . ">" . $planets[$i][name] . " in sector " . $planets[$i][sector_id] . "</option>";
      $i++;
    }
  }
  
  echo '
    </select>
    </tr><tr>
    <td>&nbsp;
    </tr><tr>
    <td align=right><font size=2><b>Please select ending point : <br>&nbsp;</b></font></td>
    <tr>
    <td align=right><font size=2>Port : </font></td>
    <td><input type=radio name="ptype2" value="port"
    ';

  if(empty($editroute) || (!empty($editroute) && $editroute[dest_type] == 'P'))
    echo " checked";
    
    echo '
    ></td>
    <td>&nbsp;&nbsp;<input type=text name=port_id2 size=20 align=center
    ';

  if(!empty($editroute) && $editroute[dest_type] == 'P')
    echo " value=\"$editroute[dest_id]\"";

    echo '
    ></td>
    </tr><tr>
    <td align=right><font size=2>Planet : </font></td>
    <td><input type=radio name="ptype2" value="planet"
    ';

  if(!empty($editroute) && $editroute[dest_type] == 'L')
    echo " checked";

  echo '
    ></td>
    <td>&nbsp;&nbsp;<select name=planet_id2>
    ';
  
  if($num_planets == 0)
    echo "<option value=none>None</option>";
  else
  {
    $i=0;
    while($i < $num_planets)
    {
      echo "<option ";
      if($planets[$i][planet_id] == $editroute[dest_id])
        echo "selected ";
      echo "value=" . $planets[$i][planet_id] . ">" . $planets[$i][name] . " in sector " . $planets[$i][sector_id] . "</option>";
      $i++;
    }
  }

  echo '
    </select>
    </tr><tr>
    <td>&nbsp;
    </tr><tr>
    <td align=right><font size=2><b>Please select move type : </b></font></td>
    <td colspan=2 valign=top><font size=2><input type=radio name="move_type" value="realspace"
    ';

  if(empty($editroute) || (!empty($editroute) && $editroute[move_type] == 'R'))
    echo " checked";

  echo '
    >&nbsp;Real Space&nbsp;&nbsp<font size=2><input type=radio name="move_type" value="warp"
    ';

  if(!empty($editroute) && $editroute[move_type] == 'W')
    echo " checked";
    
  echo '
    >&nbsp;Warp</font></td>
    </tr><tr>
    <td align=right><font size=2><b>Please select circuit : </b></font></td>
    <td colspan=2 valign=top><font size=2><input type=radio name="circuit_type" value="1"
    ';

  if(empty($editroute) || (!empty($editroute) && $editroute[circuit] == '1'))
    echo " checked";

  echo '
    >&nbsp;One way&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type=radio name="circuit_type" value="2"
    ';

  if(!empty($editroute) && $editroute[circuit] == '2')
    echo " checked";

  echo '
    >&nbsp;Both ways</font></td>
    </tr><tr>
    <td>&nbsp;
    </tr><tr>
    <td><td><td align=center>
    ';

  if(empty($editroute))
    echo '<input type=submit value="Create">';
  else
  {
    echo "<input type=hidden name=editing value=$editroute[traderoute_id]>";
    echo '<input type=submit value="Modify">';
  }

  echo '
    </table>
    Click <a href=traderoute.php>here</a> to return to the trade routes menu.<br>
    </form>
    ';

  
  TEXT_GOTOMAIN();
  include("footer.php3");
  die();
}

function traderoute_create()
{
  global $playerinfo;
  global $num_traderoutes;
  global $max_traderoutes_player;
  global $ptype1;
  global $ptype2;
  global $port_id1;
  global $port_id2;
  global $planet_id1;
  global $planet_id2;
  global $move_type;
  global $circuit_type;
  global $editing;

  if($num_traderoutes >= $max_traderoutes_player && empty($editing))
    traderoute_die("Sorry you have reached the maximum number of trade routes allowed.");
  
  //dbase sanity check for source
  if($ptype1 == 'port')
  {
    $query = mysql_query("SELECT * FROM universe WHERE sector_id=$port_id1");
    if(!$query || mysql_num_rows($query) == 0)
      traderoute_die("Error : the source port $port_id1 is not a valid port.");

    $source=mysql_fetch_array($query);
    if($source[port_type] == 'none')
      traderoute_die("Error : there is no port at sector $port_id1.");
  }    
  else
  {
    $query = mysql_query("SELECT * FROM planets WHERE planet_id=$planet_id1");
    if(!$query || mysql_num_rows($query) == 0)
      traderoute_die("Error : specified source planet doesn't exist.");
    $source=mysql_fetch_array($query);
    
    //hum, that thing was tricky
    if($source[owner] != $playerinfo[ship_id])
    {
      if(($playerinfo[team] == 0 || $playerinfo[team] != $source[corp]) && $source[sells] == 'N')
        traderoute_die("Error : you do not own planet $source[name] in sector $source[sector_id] and planet is not selling.");
    }
  }

  //dbase sanity check for dest
  if($ptype2 == 'port')
  {
    $query = mysql_query("SELECT * FROM universe WHERE sector_id=$port_id2");
    if(!$query || mysql_num_rows($query) == 0)
      traderoute_die("Error : the destination port $port_id2 is not a valid port.");

    $destination=mysql_fetch_array($query);
    if($destination[port_type] == 'none')
      traderoute_die("Error : there is no port at sector $port_id2.");
  }    
  else
  {
    $query = mysql_query("SELECT * FROM planets WHERE planet_id=$planet_id2");
    if(!$query || mysql_num_rows($query) == 0)
      traderoute_die("Error : specified destination planet doesn't exist.");
    $destination=mysql_fetch_array($query);

    if($destination[owner] != $playerinfo[ship_id] && $destination[sells] == 'N')
      traderoute_die("Error : you do not own planet $destination[name] in sector $destination[sector_id] and planet is not selling.");
  }

  //check traderoute for src => dest
  traderoute_check_compatible($ptype1, $ptype2, $move_type, $circuit_type, $source , $destination);

  if($ptype1 == 'port')
    $src_id = $port_id1;
  else
    $src_id = $planet_id1;

  if($ptype2 == 'port')
    $dest_id = $port_id2;
  else
    $dest_id = $planet_id2;

  if($ptype1 == 'port')
    $src_type = 'P';
  else
    $src_type = 'L';

  if($ptype2 == 'port')
    $dest_type = 'P';
  else
    $dest_type = 'L';
  
  if($move_type == 'realspace')
    $mtype = 'R';
  else
    $mtype = 'W';

  if(empty($editing))
  {
    $query = mysql_query("INSERT INTO traderoutes VALUES('', $src_id, $dest_id, '$src_type', '$dest_type', '$mtype', $playerinfo[ship_id], '$circuit_type')");
    echo "<p>New trade route created!";
  }
  else
  {
    $query = mysql_query("UPDATE traderoutes SET source_id=$src_id, dest_id=$dest_id, source_type='$src_type', dest_type='$dest_type', move_type='$mtype', owner=$playerinfo[ship_id], circuit='$circuit_type' WHERE traderoute_id=$editing");
    echo "<p>Trade route modified!";
    echo mysql_error();
  }
  
  echo " Click <a href=traderoute.php>here</a> to return to the trade route menu.";
  traderoute_die("");

}

function traderoute_delete()
{
  global $playerinfo;
  global $confirm;
  global $num_traderoutes;
  global $traderoute_id;
  global $traderoutes;

  $query = mysql_query("SELECT * FROM traderoutes WHERE traderoute_id=$traderoute_id");
  if(!$query || mysql_num_rows($query) == 0)
    traderoute_die("That trade route doesn't exist!");

  $delroute = mysql_fetch_array($query);

  if($delroute[owner] != $playerinfo[ship_id])
    traderoute_die("You do not own that traderoute!");
  
  if(empty($confirm))
  {
    $num_traderoutes = 1;
    $traderoutes[0] = $delroute;
    // here it continues to the main file area to print the route
  }
  else
  {
    $query = mysql_query("DELETE FROM traderoutes WHERE traderoute_id=$traderoute_id");
    echo "Trade route deleted. Click <a href=traderoute.php>here</a> to return to the trade route menu.";
    traderoute_die("");
  }
}

function traderoute_settings()
{
  global $playerinfo;
  echo "<p><font size=3 color=blue><b>Global trade route settings</b></font><p>";

  echo '
    <font color=white size=2><b>For trade routes having a special port as a source, trade :</b></font><p>
    <form action=traderoute.php?command=setsettings method=post>
    <table border=0><tr>
    <td><font size=2 color=white> - Colonists :</font></td>
    <td><input type=checkbox name=colonists
    ';

  if($playerinfo[trade_colonists] == 'Y')
    echo " checked";

  echo '
    ></tr><tr>
    <td><font size=2 color=white> - Fighters :</font></td>
    <td><input type=checkbox name=fighters
    ';

  if($playerinfo[trade_fighters] == 'Y')
    echo " checked";

  echo '
    ></tr><tr>
    <td><font size=2 color=white> - Torpedoes :</font></td>
    <td><input type=checkbox name=torps
    ';
        
  if($playerinfo[trade_torps] == 'Y')
    echo " checked";

  echo '
    ></tr>
    </table>
    <p>
    <font color=white size=2><b>For real space trade routes, what to do with the energy scooped? :</b></font><p>
    <table border=0><tr>
    <td><font size=2 color=white>&nbsp;&nbsp;&nbsp;Trade</font></td>
    <td><input type=radio name=energy value="Y"
    ';

  if($playerinfo[trade_energy] == 'Y')
    echo " checked";

  echo '
    ></td></tr><tr>
    <td><font size=2 color=white>&nbsp;&nbsp;&nbsp;Keep</font></td>
    <td><input type=radio name=energy value="N"
    ';

  if($playerinfo[trade_energy] == 'N')
    echo " checked";

  echo '></td></tr><tr><td>&nbsp;</td></tr><tr><td>
    <td><input type=submit value="Save"></td>
    </tr></table>
    </form>
    ';
  
  echo "Click <a href=traderoute.php>here</a> to return to the trade route menu.";
  traderoute_die("");

}

function traderoute_setsettings()
{
  global $playerinfo;
  global $colonists;
  global $fighters;
  global $torps;
  global $energy;

  empty($colonists) ? $colonists = 'N' : $colonists = 'Y';
  empty($fighters) ? $fighters = 'N' : $fighters = 'Y';
  empty($torps) ? $torps = 'N' : $torps = 'Y';

  mysql_query("UPDATE ships SET trade_colonists='$colonists', trade_fighters='$fighters', trade_torps='$torps', trade_energy='$energy' WHERE ship_id=$playerinfo[ship_id]");

  echo "Global trade route settings saved. Click <a href=traderoute.php>here</a> to return to the trade route menu.";
  traderoute_die("");
}

function traderoute_engage()
{
  global $playerinfo;
  global $engage;
  global $traderoutes;
  global $fighter_price;
  global $torpedo_price;
  global $colonist_price;
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
  
  //10 pages of sanity checks! yeah!
  
  foreach($traderoutes as $testroute)
  {
    if($testroute[traderoute_id] == $engage)
      $traderoute = $testroute;
  }

  if(!isset($traderoute))
    traderoute_die("Tried to engage a non-existing trade route!");

  if($traderoute[owner] != $playerinfo[ship_id])
    traderoute_die("You do not own that trade route!");

  if($traderoute[source_type] == 'P')
  {
    //retrieve port info here, we'll need it later anyway
    $result = mysql_query("SELECT * FROM universe WHERE sector_id=$traderoute[source_id]");
    if(!$result || mysql_num_rows($result) == 0)
      traderoute_die("Starting port does not seem to be valid!");

    $source = mysql_fetch_array($result);

    if($traderoute[source_id] != $playerinfo[sector])
      traderoute_die("You are not in sector $traderoute[source_id]! You must be in starting sector before you initiate a trade route!");
  }
  else
  {
    $result = mysql_query("SELECT * FROM planets WHERE planet_id=$traderoute[source_id]");
    if(!$result || mysql_num_rows($result) == 0)
      traderoute_die("Source planet doesn't seem to be a valid planet!");

    $source = mysql_fetch_array($result);

    if($source[sector_id] != $playerinfo[sector])
      traderoute_die("You are not in sector $source[sector_id]! You must be in starting sector before you initiate a trade route!");

    if($source[owner] != $playerinfo[ship_id])
      traderoute_die("You do not own planet $source[name] in sector $source[sector_id]!");
  
    //store starting port info, we'll need it later
    $result = mysql_query("SELECT * FROM universe WHERE sector_id=$source[sector_id]");
    if(!$result || mysql_num_rows($result) == 0)
      traderoute_die("Starting sector does not seem to be valid!");

    $sourceport = mysql_fetch_array($result);
  }

  if($traderoute[dest_type] == 'P')
  {
    $result = mysql_query("SELECT * FROM universe WHERE sector_id=$traderoute[dest_id]");
    if(!$result || mysql_num_rows($result) == 0)
      traderoute_die("Destination port does not seem to be valid!");

    $dest = mysql_fetch_array($result);
  }
  else
  {
    $result = mysql_query("SELECT * FROM planets WHERE planet_id=$traderoute[dest_id]");
    if(!$result || mysql_num_rows($result) == 0)
      traderoute_die("Destination planet doesn't seem to be a valid planet!");

    $dest = mysql_fetch_array($result);

    $result = mysql_query("SELECT * FROM universe WHERE sector_id=$dest[sector_id]");
    if(!$result || mysql_num_rows($result) == 0)
      traderoute_die("Destination sector does not seem to be valid!");

    $destport = mysql_fetch_array($result);
  }

  if(!isset($sourceport))
    $sourceport=$source;
  if(!isset($destport))
    $destport=$dest;

  if($traderoute[move_type] == 'W')
  {
    $query = mysql_query("SELECT link_id FROM links WHERE link_start=$source[sector_id] AND link_dest=$dest[sector_id]");
    if(mysql_num_rows($query) == 0)
      traderoute_die("There is no warp link from sector $source[sector_id] to sector $dest[sector_id]");
    if($traderoute[circuit] == '2')
    {
      $query = mysql_query("SELECT link_id FROM links WHERE link_start=$dest[sector_id] AND link_dest=$source[sector_id]");
      if(mysql_num_rows($query) == 0)
        traderoute_die("There is no warp link from sector $dest[sector_id] to sector $source[sector_id]");
      $dist[triptime] = 4;
    }
    else
      $dist[triptime] = 2;
    
    $dist[scooped] = 0;
  }
  else
    $dist = traderoute_distance('P', 'P', $sourceport, $destport, $traderoute[circuit]);

  if($playerinfo[turns] < $dist[triptime])
    traderoute_die("This trade route requires $dist[triptime] turns to complete. You only have $playerinfo[turns] left.");

  $hostile = 0;
  if (($sourceport[fighters] > 0 || $sourceport[mines] > 0) && $sourceport[fm_owner] != $playerinfo[ship_id])
  {
        $result99 = mysql_query("SELECT * FROM ships WHERE ship_id=$sourceport[fm_owner]");
        $fighters_owner = mysql_fetch_array($result99);
        if ($fighter_owner[team] != $playerinfo[team] || $playerinfo[team]==0)
            $hostile = 1;
  }
  if (($destport[fighters] > 0 || $destport[mines] > 0) && $destport[fm_owner] != $playerinfo[ship_id])
  {
        $result99 = mysql_query("SELECT * FROM ships WHERE ship_id=$destport[fm_owner]");
        $fighters_owner = mysql_fetch_array($result99);
        if ($fighter_owner[team] != $playerinfo[team] || $playerinfo[team]==0)
            $hostile = 1;
  }
  if($hostile > 0 && $playerinfo[hull] > $mine_hullsize) 
     traderoute_die("You can not use trade routes between sectors with hostile defences. You must defeat the defences first.");

  if($traderoute[source_type] == 'P' && $source[port_type] == 'special' && $playerinfo[trade_colonists] == 'N' && $playerinfo[trade_fighters] == 'N' && $playerinfo[trade_torps] == 'N')
    traderoute_die("Your global settings are set to buy nothing! You would only waste turns doing this route!");

  //We're done with checks! All that's left is to make it happen
  
  echo '
    <table border=1 cellspacing=1 cellpadding=2 width="65%" align=center>
    <tr bgcolor=#400040><td align="center" colspan=7><b><font color=white>Trade Route Results</font></b></td></tr>
    <tr align=center bgcolor=#400040>
    <td width=50%><font size=2 color=white><b>
    ';
  
  if($traderoute[source_type] == 'P')
    echo "Port in $source[sector_id]";
  else
    echo "Planet $source[name] in $sourceport[sector_id]";

  echo '
    </b></font></td>
    <td width=50%><font size=2 color=white><b>
    ';

  if($traderoute[dest_type] == 'P')
    echo "Port in $dest[sector_id]";
  else
    echo "Planet $dest[name] in $destport[sector_id]";

  echo '
    </b></font></td>
    </tr><tr bgcolor=#300030>
    <td align=center><font size=2 color=white>
    ';

  $sourcecost=0;

  if($traderoute[source_type] == 'P')
  {
    if($source[port_type] == 'special')
    {
      $total_credits = $playerinfo[credits];
      
      if($playerinfo[trade_colonists] == 'Y')
      {
        $free_holds = NUM_HOLDS($playerinfo[hull]) - $playerinfo[ship_ore] - $playerinfo[ship_organics] - $playerinfo[ship_goods] - $playerinfo[ship_colonists];
        $colonists_buy = $free_holds;
      
        if($playerinfo[credits] < $colonist_price * $colonists_buy)
          $colonists_buy = $playerinfo[credits] / $colonist_price;

        echo "Bought " . NUMBER($colonists_buy) . " Colonists<br>";
      
        $sourcecost-=$colonists_buy * $colonist_price;
        $total_credits-=$colonists_buy * $colonist_price;
      }
      else
        $colonists_buy = 0;

      if($playerinfo[trade_fighters] == 'Y')
      {
        $free_fighters = NUM_FIGHTERS($playerinfo[computer]) - $playerinfo[ship_fighters];
        $fighters_buy = $free_fighters;

        if($total_credits < $fighters_buy * $fighter_price)
          $fighters_buy = $total_credits / $fighter_price;

        echo "Bought " . NUMBER($fighters_buy) . " Fighters<br>";

        $sourcecost-=$fighters_buy * $fighter_price;
        $total_credits-=$fighters_buy * $fighter_price;
      }
      else
        $fighters_buy = 0;

      if($playerinfo[trade_torps] == 'Y')
      {
        $free_torps = NUM_FIGHTERS($playerinfo[torp_launchers]) - $playerinfo[torps];
        $torps_buy = $free_torps;

        if($total_credits < $torps_buy * $torpedo_price)
          $torps_buy = $total_credits / $torpedo_price;

        echo "Bought " . NUMBER($torps_buy) . " Torpedoes<br>";

        $sourcecost-=$torps_buy * $torpedo_price;
      }
      else
        $torps_buy = 0;
      
      if($traderoute[circuit] == '1')
        mysql_query("UPDATE ships SET ship_colonists=ship_colonists+$colonists_buy, ship_fighters=ship_fighters+$fighters_buy,torps=torps+$torps_buy, ship_energy=ship_energy+$dist[scooped1] WHERE ship_id=$playerinfo[ship_id]");
    }
    else
    {
      //sells commodities
      if($source[port_type] != 'ore')
      {
        $ore_price1 = $ore_price + $ore_delta * $source[port_ore] / $ore_limit * $iventory_factor;
        $sourcecost += $playerinfo[ship_ore] * $ore_price1;
        $ore_buy = $playerinfo[ship_ore];
        if($playerinfo[ship_ore] != 0)
          echo "Sold " . NUMBER($playerinfo[ship_ore]) . " Ore<br>";
        $playerinfo[ship_ore] = 0;
      }

      if($source[port_type] != 'goods')
      {
        $goods_price1 = $goods_price + $goods_delta * $source[port_goods] / $goods_limit * $inventory_factor;
        $sourcecost += $playerinfo[ship_goods] * $goods_price1;
        $goods_buy = $playerinfo[ship_goods];
        if($playerinfo[ship_goods] != 0)
          echo "Sold " . NUMBER($playerinfo[ship_goods]) . " Goods<br>";
        $playerinfo[ship_goods] = 0;
      }

      if($source[port_type] != 'organics')
      {
        $organics_price1 = $organics_price + $organics_delta * $source[port_organics] / $organics_limit * $inventory_factor;
        $sourcecost += $playerinfo[ship_organics] * $organics_price1;
        $organics_buy = $playerinfo[ship_organics];
        if($playerinfo[ship_organics] != 0)
          echo "Sold " . NUMBER($playerinfo[ship_organics]) . " Organics<br>";
        $playerinfo[ship_organics] = 0;
      }

      if($source[port_type] != 'energy' && $playerinfo[trade_energy] == 'Y')
      {
        $energy_price1 = $energy_price + $energy_delta * $source[port_energy] / $energy_limit * $inventory_factor;
        $sourcecost += $playerinfo[ship_energy] * $energy_price1;
        $energy_buy = $playerinfo[ship_energy];
        if($playerinfo[ship_energy] != 0)
          echo "Sold " . NUMBER($playerinfo[ship_energy]) . " Energy<br>";
        $playerinfo[ship_energy] = 0;
      }

      $free_holds = NUM_HOLDS($playerinfo[hull]) - $playerinfo[ship_ore] - $playerinfo[ship_organics] - $playerinfo[ship_goods] - $playerinfo[ship_colonists];
      
      //time to buy
      if($source[port_type] == 'ore')
      {
        $ore_price1 = $ore_price - $ore_delta * $source[port_ore] / $ore_limit * $iventory_factor;
        $ore_buy = $free_holds;
        if($playerinfo[credits] + $sourcecost < $ore_buy * $ore_price1)
          $ore_buy = ($playerinfo[credits] + $sourcecost) / $ore_price1;
        if($source[port_ore] < $ore_buy)
        {
          $ore_buy = $source[port_ore];
          if($source[port_ore] == 0)
            echo "Bought " . NUMBER($ore_buy) . " Ore (Port is empty)<br>";
        }
        if($ore_buy != 0)
          echo "Bought " . NUMBER($ore_buy) . " Ore<br>";
        $playerinfo[ship_ore] += $ore_buy; 
        $sourcecost -= $ore_buy * $ore_price1;
        mysql_query("UPDATE universe SET port_ore=port_ore-$ore_buy, port_energy=port_energy+$energy_buy, port_goods=port_goods+$goods_buy, port_organics=port_organics+$organics_buy WHERE sector_id=$source[sector_id]");
      }

      if($source[port_type] == 'goods')
      {
        $goods_price1 = $goods_price - $goods_delta * $source[port_goods] / $goods_limit * $inventory_factor;
        $goods_buy = $free_holds;
        if($playerinfo[credits] + $sourcecost < $goods_buy * $goods_price1)
          $goods_buy = ($playerinfo[credits] + $sourcecost) / $goods_price1;
        if($source[port_goods] < $goods_buy)
        {
          $goods_buy = $source[port_goods];
          if($source[port_goods] == 0)
            echo "Bought " . NUMBER($goods_buy) . " Goods (Port is empty)<br>";
        }
        if($goods_buy != 0)
          echo "Bought " . NUMBER($goods_buy) . " Goods<br>";
        $playerinfo[ship_goods] += $goods_buy; 
        $sourcecost -= $goods_buy * $goods_price1;
        mysql_query("UPDATE universe SET port_ore=port_ore+$ore_buy, port_energy=port_energy+$energy_buy, port_goods=port_goods-$goods_buy, port_organics=port_organics+$organics_buy WHERE sector_id=$source[sector_id]");
      }

      if($source[port_type] == 'organics')
      {
        $organics_price1 = $organics_price - $organics_delta * $source[port_organics] / $organics_limit * $inventory_factor;
        $organics_buy = $free_holds;
        if($playerinfo[credits] + $sourcecost < $organics_buy * $organics_price1)
          $organics_buy = ($playerinfo[credits] + $sourcecost) / $organics_price1;
        if($source[port_organics] < $organics_buy)
        {
          $organics_buy = $source[port_organics];
          if($source[port_organics] == 0)
            echo "Bought " . NUMBER($organics_buy) . " Organics (Port is empty)<br>";
        }
        if($organics_buy != 0)
          echo "Bought " . NUMBER($organics_buy) . " Organics<br>";
        $playerinfo[ship_organics] += $organics_buy; 
        $sourcecost -= $organics_buy * $organics_price1;
        mysql_query("UPDATE universe SET port_ore=port_ore+$ore_buy, port_energy=port_energy+$energy_buy, port_goods=port_goods+$goods_buy, port_organics=port_organics-$organics_buy WHERE sector_id=$source[sector_id]");
      }

      if($source[port_type] == 'energy')
      {
        $energy_price1 = $energy_price - $energy_delta * $source[port_energy] / $energy_limit * $inventory_factor;
        $energy_buy = NUM_ENERGY($playerinfo[power]) - $playerinfo[ship_energy] - $dist[scooped1];
        if($playerinfo[credits] + $sourcecost < $enerby_buy * $energy_price1)
          $energy_buy = ($playerinfo[credits] + $sourcecost) / $energy_price1;
        if($source[port_energy] < $energy_buy)
        {
          $energy_buy = $source[port_energy];
          if($source[port_energy] == 0)
            echo "Bought " . NUMBER($energy_buy) . " Energy (Port is empty)<br>";
        }
        if($energy_buy != 0)
          echo "Bought " . NUMBER($energy_buy) . " Energy<br>";
        $playerinfo[ship_energy] += $energy_buy; 
        $sourcecost -= $energy_buy * $energy_price1;
        mysql_query("UPDATE universe SET port_ore=port_ore+$ore_buy, port_energy=port_energy-$energy_buy, port_goods=port_goods+$goods_buy, port_organics=port_organics+$organics_buy WHERE sector_id=$source[sector_id]");
      }
      if($dist[scooped1] > 0)
      {
        $playerinfo[ship_energy]+= $dist[scooped1];
        if($playerinfo[ship_energy] > NUM_ENERGY($playerinfo[power]))
          $playerinfo[ship_energy] = NUM_ENERGY($playerinfo[power]);
      }
      if($ore_buy == 0 && $goods_buy == 0 && $energy_buy == 0 && $organics_buy == 0)
        echo "Nothing to trade<br>";

      if($traderoute[circuit] == '1')
        mysql_query("UPDATE ships SET ship_ore=$playerinfo[ship_ore], ship_goods=$playerinfo[ship_goods], ship_organics=$playerinfo[ship_organics], ship_energy=$playerinfo[ship_energy] WHERE ship_id=$playerinfo[ship_id]");
    }
  }
  else //source is planet
  {
    $free_holds = NUM_HOLDS($playerinfo[hull]) - $playerinfo[ship_ore] - $playerinfo[ship_organics] - $playerinfo[ship_goods] - $playerinfo[ship_colonists];

    //pick stuff up
    if($playerinfo[ship_id] == $source[owner])
    {
      if($source[goods] > 0 && $free_holds > 0 && $dest[port_type] != 'goods')
      {
        if($source[goods] > $free_holds)
          $goods_buy = $free_holds;
        else
          $goods_buy = $source[goods];
        $free_holds -= $goods_buy;
        $playerinfo[ship_goods] += $goods_buy;
        echo "Loaded " . NUMBER($goods_buy) . " Goods<br>";
      }
      else
        $goods_buy = 0;

      if($source[ore] > 0 && $free_holds > 0 && $dest[port_type] != 'ore')
      {
        if($source[ore] > $free_holds)
          $ore_buy = $free_holds;
        else
          $ore_buy = $source[ore];
        $free_holds -= $ore_buy;
        $playerinfo[ship_ore] += $ore_buy;
        echo "Loaded " . NUMBER($ore_buy) . " Ore<br>";
      }
      else
        $ore_buy = 0;

      if($source[organics] > 0 && $free_holds > 0 && $dest[port_type] != 'organics')
      {
        if($source[organics] > $free_holds)
          $organics_buy = $free_holds;
        else
          $organics_buy = $source[organics];
        $free_holds -= $organics_buy;
        $playerinfo[ship_organics] += $organics_buy;
        echo "Loaded " . NUMBER($organics_buy) . " Organics<br>";
      }
      else
        $organics_buy = 0;
    }
    else  //buy from planet
    {
    }

    mysql_query("UPDATE planets SET ore=ore-$ore_buy, goods=goods-$goods_buy, organics=organics-$organics_buy WHERE planet_id=$source[planet_id]");
  }

  if($dist[scooped1] != 0)
    echo "Scooped " . NUMBER($dist[scooped1]) . " energy<br>";

  echo '
    </font></td>
    <td align=center><font size=2 color=white>
  ';
 
  if($traderoute[circuit] == '2')
  {
    $destcost = 0;
    if($traderoute[dest_type] == 'P')
    {
      //sells commodities
      if($dest[port_type] != 'ore')
      {
        $ore_price1 = $ore_price + $ore_delta * $dest[port_ore] / $ore_limit * $iventory_factor;
        $destcost += $playerinfo[ship_ore] * $ore_price1;
        $ore_buy = $playerinfo[ship_ore];
        if($playerinfo[ship_ore] != 0)
          echo "Sold " . NUMBER($playerinfo[ship_ore]) . " Ore<br>";
        $playerinfo[ship_ore] = 0;
      }

      if($dest[port_type] != 'goods')
      {
        $goods_price1 = $goods_price + $goods_delta * $dest[port_goods] / $goods_limit * $inventory_factor;
        $destcost += $playerinfo[ship_goods] * $goods_price1;
        $goods_buy = $playerinfo[ship_goods];
        if($playerinfo[ship_goods] != 0)
          echo "Sold " . NUMBER($playerinfo[ship_goods]) . " Goods<br>";
        $playerinfo[ship_goods] = 0;
      }

      if($dest[port_type] != 'organics')
      {
        $organics_price1 = $organics_price + $organics_delta * $dest[port_organics] / $organics_limit * $inventory_factor;
        $destcost += $playerinfo[ship_organics] * $organics_price1;
        $organics_buy = $playerinfo[ship_organics];
        if($playerinfo[ship_organics] != 0)
          echo "Sold " . NUMBER($playerinfo[ship_organics]) . " Organics<br>";
        $playerinfo[ship_organics] = 0;
      }

      if($dest[port_type] != 'energy' && $playerinfo[trade_energy] == 'Y')
      {
        $energy_price1 = $energy_price + $energy_delta * $dest[port_energy] / $energy_limit * $inventory_factor;
        $destcost += $playerinfo[ship_energy] * $energy_price1;
        $energy_buy = $playerinfo[ship_energy];
        if($playerinfo[ship_energy] != 0)
          echo "Sold " . NUMBER($playerinfo[ship_energy]) . " Energy<br>";
        $playerinfo[ship_energy] = 0;
      }
      else
        $energy_buy = 0;

      $free_holds = NUM_HOLDS($playerinfo[hull]) - $playerinfo[ship_ore] - $playerinfo[ship_organics] - $playerinfo[ship_goods] - $playerinfo[ship_colonists];
      
      //time to buy
      if($dest[port_type] == 'ore')
      {
        $ore_price1 = $ore_price - $ore_delta * $dest[port_ore] / $ore_limit * $iventory_factor;
        if($traderoute[source_type] == 'L')
          $ore_buy = 0;
        else
        {
          $ore_buy = $free_holds;
          if($playerinfo[credits] + $destcost < $ore_buy * $ore_price1)
          $ore_buy = ($playerinfo[credits] + $destcost) / $ore_price1;
          if($dest[port_ore] < $ore_buy)
          {
            $ore_buy = $dest[port_ore];
            if($dest[port_ore] == 0)
              echo "Bought " . NUMBER($ore_buy) . " Ore (Port is empty)<br>";
          }
          if($ore_buy != 0)
            echo "Bought " . NUMBER($ore_buy) . " Ore<br>";
          $playerinfo[ship_ore] += $ore_buy; 
          $destcost -= $ore_buy * $ore_price1;
        }
        mysql_query("UPDATE universe SET port_ore=port_ore-$ore_buy, port_energy=port_energy+$energy_buy, port_goods=port_goods+$goods_buy, port_organics=port_organics+$organics_buy WHERE sector_id=$dest[sector_id]");
      }

      if($dest[port_type] == 'goods')
      {
        $goods_price1 = $goods_price - $goods_delta * $dest[port_goods] / $goods_limit * $inventory_factor;
        if($traderoute[source_type] == 'L')
          $goods_buy = 0;
        else
        {
          $goods_buy = $free_holds;
          if($playerinfo[credits] + $destcost < $goods_buy * $goods_price1)
            $goods_buy = ($playerinfo[credits] + $destcost) / $goods_price1;
          if($dest[port_goods] < $goods_buy)
          {
            $goods_buy = $dest[port_goods];
            if($dest[port_goods] == 0)
              echo "Bought " . NUMBER($goods_buy) . " Goods (Port is empty)<br>";
          }
          if($goods_buy != 0)
            echo "Bought " . NUMBER($goods_buy) . " Goods<br>";
          $playerinfo[ship_goods] += $goods_buy; 
          $destcost -= $goods_buy * $goods_price1;
        }
        mysql_query("UPDATE universe SET port_ore=port_ore+$ore_buy, port_energy=port_energy+$energy_buy, port_goods=port_goods-$goods_buy, port_organics=port_organics+$organics_buy WHERE sector_id=$dest[sector_id]");
      }

      if($dest[port_type] == 'organics')
      {
        $organics_price1 = $organics_price - $organics_delta * $dest[port_organics] / $organics_limit * $inventory_factor;
        if($traderoute[source_type] == 'L')
          $organics_buy = 0;
        else
        {
          $organics_buy = $free_holds;
          if($playerinfo[credits] + $destcost < $organics_buy * $organics_price1)
            $organics_buy = ($playerinfo[credits] + $destcost) / $organics_price1;
          if($dest[port_organics] < $organics_buy)
          {
            $organics_buy = $dest[port_organics];
            if($dest[port_organics] == 0)
              echo "Bought " . NUMBER($organics_buy) . " Organics (Port is empty)<br>";
          }
          if($organics_buy != 0)
            echo "Bought " . NUMBER($organics_buy) . " Organics<br>";
          $playerinfo[ship_organics] += $organics_buy; 
          $destcost -= $organics_buy * $organics_price1;
        }
        mysql_query("UPDATE universe SET port_ore=port_ore+$ore_buy, port_energy=port_energy+$energy_buy, port_goods=port_goods+$goods_buy, port_organics=port_organics-$organics_buy WHERE sector_id=$dest[sector_id]");
      }

      if($dest[port_type] == 'energy')
      {
        $energy_price1 = $energy_price - $energy_delta * $dest[port_energy] / $energy_limit * $inventory_factor;
        if($traderoute[source_type] == 'L')
          $energy_buy = 0;
        else
        {
          $energy_buy = NUM_ENERGY($playerinfo[power]) - $playerinfo[ship_energy] - $dist[scooped1];
          if($playerinfo[credits] + $destcost < $enerby_buy * $energy_price1)
            $energy_buy = ($playerinfo[credits] + $destcost) / $energy_price1;
          if($dest[port_energy] < $energy_buy)
          {
            $energy_buy = $dest[port_energy];
            if($dest[port_energy] == 0)
              echo "Bought " . NUMBER($energy_buy) . " Energy (Port is empty)<br>";
          }
          if($energy_buy != 0)
            echo "Bought " . NUMBER($energy_buy) . " Energy<br>";
          $playerinfo[ship_energy] += $energy_buy; 
          $destcost -= $energy_buy * $energy_price1;
        }
        mysql_query("UPDATE universe SET port_ore=port_ore+$ore_buy, port_energy=port_energy-$energy_buy, port_goods=port_goods+$goods_buy, port_organics=port_organics+$organics_buy WHERE sector_id=$dest[sector_id]");
      }
      if($dist[scooped2] > 0)
      {
        $playerinfo[ship_energy]+= $dist[scooped2];
        if($playerinfo[ship_energy] > NUM_ENERGY($playerinfo[power]))
          $playerinfo[ship_energy] = NUM_ENERGY($playerinfo[power]);
      }
      mysql_query("UPDATE ships SET ship_ore=$playerinfo[ship_ore], ship_goods=$playerinfo[ship_goods], ship_organics=$playerinfo[ship_organics], ship_energy=$playerinfo[ship_energy] WHERE ship_id=$playerinfo[ship_id]");
    }
    else //dest is planet
    {
      if($playerinfo[trade_colonists] == 'Y')
      {
        $colonists_buy += $playerinfo[ship_colonists];
        $col_dump = $playerinfo[ship_colonists];
      }
      else
        $col_dump = 0;

      if($colonists_buy != 0)
        echo "Dumped " . NUMBER($colonists_buy) . " Colonists<br>";
    
      if($playerinfo[trade_fighters] == 'Y')
      {
        $fighters_buy += $playerinfo[ship_fighters];
        $fight_dump = $playerinfo[ship_fighters];
      }
      else
        $fight_dump = 0;

      if($fighters_buy != 0)
        echo "Dumped " . NUMBER($fighters_buy) . " Fighters<br>";
    
      if($playerinfo[trade_torps] == 'Y')
      {
        $torps_buy += $playerinfo[torps];
        $torps_dump = $playerinfo[torps];
      }
      else
        $torps_dump = 0;

      if($torps_buy != 0)
        echo "Dumped " . NUMBER($torps_buy) . " Torpedoes<br>";
      mysql_query("UPDATE planets SET colonists=colonists+$colonists_buy, fighters=fighters+$fighters_buy, torps=torps+$torps_buy WHERE planet_id=$traderoute[dest_id]");
      mysql_query("UPDATE ships SET ship_colonists=ship_colonists-$col_dump, ship_fighters=ship_fighters-$fight_dump, torps=torps-$torps_dump, ship_energy=ship_energy+$dist[scooped] WHERE ship_id=$playerinfo[ship_id]");
    }
    if($dist[scooped2] != 0)
    {
      echo "Scooped " . NUMBER($dist[scooped1]) . " energy<br>";
    }

  }
  else
  {
    echo "Did nothing, traderoute was one way only";
    $destcost = 0;
  }

  echo "</font></td></tr><tr bgcolor=#400040><td align=center><font size=2 color=white>";
  
  if($sourcecost > 0)
    echo "Profit : " . NUMBER(abs($sourcecost));
  else
    echo "Cost : " . NUMBER(abs($sourcecost));

  echo "</font></td><td align=center><font size=2 color=white>";

  if($destcost > 0)
    echo "Profit : " . NUMBER(abs($destcost));
  else
    echo "Cost : " . NUMBER(abs($destcost));

  echo '
    </font></td></tr>
    </table>
    <p>
    <center>
    <font size=3 color=white><b>
    ';

  $total_profit = $sourcecost + $destcost;
  if($total_profit > 0)
    echo "Total profit : <font color=#00ff00>" . NUMBER(abs($total_profit)) . "</font></b><p>";
  else
    echo "Total cost : <font color=red>" . NUMBER(abs($total_profit)) . "</font></b><br>";

  if($traderoute[circuit] == '1')
    $newsec = $destport[sector_id];
  else
    $newsec = $sourceport[sector_id];

  mysql_query("UPDATE ships SET turns=turns-$dist[triptime], credits=credits+$total_profit, turns_used=turns_used+$dist[triptime], sector=$newsec WHERE ship_id=$playerinfo[ship_id]");
  $playerinfo[credits]+=$total_profit;
  $playerinfo[turns]-=$dist[triptime];

  echo "<font size=3 color=white><b>Turns used : <font color=red>$dist[triptime]</font></b><br>";
  echo "<font size=3 color=white><b>Turns left : <font color=#00ff00>$playerinfo[turns]</font></b><br><p>";
  
  echo "<font size=3 color=white><b>Credits : <font color=#00ff00>" . NUMBER($playerinfo[credits]) . "</font></b><br></center><p><font size=2>";

  if($traderoute[circuit] == 2)
    echo "Click <A HREF=traderoute.php?engage=$engage>here</A> to do this trade route again.<p>";
  traderoute_die("");
}
