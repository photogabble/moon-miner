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
mysql_query("LOCK TABLES ships WRITE, universe WRITE, links READ, traderoutes WRITE, planets WRITE");

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



if($command == 'new')
  traderoute_new('');
elseif($command == 'create')
  traderoute_create();
elseif($command == 'edit')
  traderoute_new($traderoute_id);
elseif($command == 'delete')
  traderoute_delete();


//-----------------------------------------------------------------
if($command != 'delete')
  echo '<p>Click <a href="traderoute.php?command=new">here</a> to create a new trade route<p>';
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
        echo "&nbsp;Planet <b>$planet1[name]</b> in <b>$planet1[sector_id]</b></font></td>";
      }
      else
        echo "&nbsp;Non-existant planet!</font></td>";
    }

    echo "<td align=center><font size=2 color=white>";
    if($traderoutes[$i][source_type] == 'P')
    {
      $result = mysql_query("SELECT port_type FROM universe WHERE sector_id=" . $traderoutes[$i][source_id]);
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
      echo "&nbsp;Port in <b>" . $traderoutes[$i][dest_id] . "</b></font></td>";
    else
    {
      $result = mysql_query("SELECT name, sector_id FROM planets WHERE planet_id=" . $traderoutes[$i][dest_id]);
      if($result)
      {
        $planet2 = mysql_fetch_array($result);
        echo "&nbsp;Planet <b>$planet2[name]</b> in <b>$planet2[sector_id]</b></font></td>";
      }
      else
        echo "&nbsp;Non-existant planet!</font></td>";
    }

    echo "<td align=center><font size=2 color=white>";
    if($traderoutes[$i][dest_type] == 'P')
    {
      $result = mysql_query("SELECT port_type FROM universe WHERE sector_id=" . $traderoutes[$i][dest_id]);
      $port2 = mysql_fetch_array($result);
      echo "&nbsp;$port2[port_type]</font></td>";
    }
    else
    {
      if(empty($planet2))
        echo "&nbsp;N/A</font></td>";
      else
        echo "&nbsp;Colonists</font></td>";
    }
    
    echo "<td align=center><font size=2 color=white>";
    if($traderoutes[$i][move_type] == 'R')
      echo "&nbsp;RS</font></td>";
    else
      echo "&nbsp;Warp</font></td>";

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
mysql_query("UNLOCK TABLES");
//-------------------------------------------------------------------------------------------------

TEXT_GOTOMAIN();
include("footer.php3");

?> 

<?

function traderoute_die($error_msg)
{
  echo "<p>$error_msg<p>";
  mysql_query("UNLOCK TABLES");
  
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
  mysql_query("UNLOCK TABLES");
  
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


