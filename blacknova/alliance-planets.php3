<?

include("config.php3");
updatecookie();

$title="Alliance Planet Report";
include("header.php3");

connectdb();

if(checklogin())
{
  die();
}


$res = mysql_query("SELECT * FROM ships WHERE email='$username'");
$playerinfo = mysql_fetch_array($res);

if ($playerinfo[team]==0) 
{
 echo "<BR>You aren't in an alliance.";
 echo "<BR><BR>";
 TEXT_GOTOMAIN();

 include("footer.php3");

 return; 
}


mysql_free_result($res);


$query = "SELECT * FROM planets WHERE corp=$playerinfo[team]";
if(!empty($sort))
{
  $query .= " ORDER BY";
  if($sort == "name")
  {
    $query .= " $sort ASC";
  }
  elseif($sort == "organics" || $sort == "ore" || $sort == "goods" || $sort == "energy" || 
    $sort == "colonists" || $sort == "credits" || $sort == "fighters")
  {
    $query .= " $sort DESC";
  }
  elseif($sort == "torp")
  {
    $query .= " torps DESC";
  }
  else
  {
    $query .= " sector_id ASC";
  }
}

$res = mysql_query($query);

bigtitle(); 


echo "<BR>";
echo "<B><A HREF=planet-report.php3>Show personal planets</A></B>";
echo "<BR>";
echo "<BR>";


$i = 0;
if($res)
{
  while($row = mysql_fetch_array($res))
  {
    $planet[$i] = $row;
    $i++;
  }
}
mysql_free_result($res);

$num_planets = $i;
if($num_planets < 1)
{
  echo "<BR>Your alliance has no planets";
}
else
{
  echo "Click on column header to sort.<BR><BR>";
  echo "<TABLE WIDTH=\"100%\" BORDER=0 CELLSPACING=0 CELLPADDING=2>";
  echo "<TR BGCOLOR=\"$color_header\">";
  echo "<TD><B><A HREF=alliance-planets.php3>Sector</A></B></TD>";
  echo "<TD><B><A HREF=alliance-planets.php3?sort=name>Name</A></B></TD>";
  echo "<TD><B><A HREF=alliance-planets.php3?sort=ore>Ore</A></B></TD>";
  echo "<TD><B><A HREF=alliance-planets.php3?sort=organics>Organics</A></B></TD>";
  echo "<TD><B><A HREF=alliance-planets.php3?sort=goods>Goods</A></B></TD>";
  echo "<TD><B><A HREF=alliance-planets.php3?sort=energy>Energy</A></B></TD>";
  echo "<TD><B><A HREF=alliance-planets.php3?sort=colonists>Colonists</A></B></TD>";
  echo "<TD><B><A HREF=alliance-planets.php3?sort=credits>Credits</A></B></TD>";
  echo "<TD><B><A HREF=alliance-planets.php3?sort=fighters>Fighters</A></B></TD>";
  echo "<TD><B><A HREF=alliance-planets.php3?sort=torp>Torpedoes</A></B></TD>";
  echo "<TD><B>Base?</B></TD><TD><B>Selling?</B></TD>";
  echo "</TR>";
  $total_organics = 0;
  $total_ore = 0;
  $total_goods = 0;
  $total_energy = 0;
  $total_colonists = 0;
  $total_credits = 0;
  $total_fighters = 0;
  $total_torp = 0;
  $total_base = 0;
  $total_selling = 0;
  $color = $color_line1;
  for($i=0; $i<$num_planets; $i++)
  {
    $total_organics += $planet[$i][organics];
    $total_ore += $planet[$i][ore];
    $total_goods += $planet[$i][goods];
    $total_energy += $planet[$i][energy];
    $total_colonists += $planet[$i][colonists];
    $total_credits += $planet[$i][credits];
    $total_fighters += $planet[$i][fighters];
    $total_torp += $planet[$i][torps];
    if($planet[$i][base] == "Y")
    {
      $total_base += 1;
    }
    if($planet[$i][sells] == "Y")
    {
      $total_selling += 1;
    }
    if(empty($planet[$i][name]))
    {
      $planet[$i][name] = "Unnamed";
    }
    echo "<TR BGCOLOR=\"$color\">";
    echo "<TD><A HREF=rsmove.php3?engage=1&destination=". $planet[$i][sector_id] . ">". $planet[$i][sector_id] ."</A></TD>";
    echo "<TD>" . $planet[$i][name] . "</TD>";
    echo "<TD>" . NUMBER($planet[$i][ore]) . "</TD>";
    echo "<TD>" . NUMBER($planet[$i][organics]) . "</TD>";
    echo "<TD>" . NUMBER($planet[$i][goods]) . "</TD>";
    echo "<TD>" . NUMBER($planet[$i][energy]) . "</TD>";
    echo "<TD>" . NUMBER($planet[$i][colonists]) . "</TD>";
    echo "<TD>" . NUMBER($planet[$i][credits]) . "</TD>";
    echo "<TD>" . NUMBER($planet[$i][fighters]) . "</TD>";
    echo "<TD>" . NUMBER($planet[$i][torps]) . "</TD>";
    echo "<TD>" . ($planet[$i][base] == 'Y' ? "Yes" : "No") . "</TD>";
    echo "<TD>" . ($planet[$i][sells] == 'Y' ? "Yes" : "No") . "</TD>";
    echo "</TR>";

    if($color == $color_line1)
    {
      $color = $color_line2;
    }
    else
    {
      $color = $color_line1;
    }
  }
  echo "<TR BGCOLOR=\"$color\">";
  echo "<TD></TD>";
  echo "<TD>Totals</TD>";
  echo "<TD>" . NUMBER($total_ore) . "</TD>";
  echo "<TD>" . NUMBER($total_organics) . "</TD>";
  echo "<TD>" . NUMBER($total_goods) . "</TD>";
  echo "<TD>" . NUMBER($total_energy) . "</TD>";
  echo "<TD>" . NUMBER($total_colonists) . "</TD>";
  echo "<TD>" . NUMBER($total_credits) . "</TD>";
  echo "<TD>" . NUMBER($total_fighters) . "</TD>";
  echo "<TD>" . NUMBER($total_torp) . "</TD>";
  echo "<TD>" . NUMBER($total_base) . "</TD>";
  echo "<TD>" . NUMBER($total_selling) . "</TD>";
  echo "</TR>";
  echo "</TABLE>";
}

echo "<BR><BR>";

TEXT_GOTOMAIN();

include("footer.php3");

?> 
