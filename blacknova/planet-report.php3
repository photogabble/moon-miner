<?

include("config.php3");
updatecookie();

$title="Planet Report";
include("header.php3");

connectdb();

if(checklogin())
{
  die();
}

$res = mysql_query("SELECT * FROM ships WHERE email='$username'");
$playerinfo = mysql_fetch_array($res);
mysql_free_result($res);

$query = "SELECT * FROM universe WHERE planet_owner=$playerinfo[ship_id]";
if(!empty($sort))
{
  $query .= " ORDER BY";
  if($sort == "name")
  {
    $query .= " planet_$sort ASC";
  }
  elseif($sort == "organics" || $sort == "ore" || $sort == "goods" || $sort == "energy" || 
    $sort == "colonists" || $sort == "credits" || $sort == "fighters")
  {
    $query .= " planet_$sort DESC";
  }
  elseif($sort == "torp")
  {
    $query .= " base_torp DESC";
  }
  else
  {
    $query .= " sector_id ASC";
  }
}

$res = mysql_query($query);

bigtitle(); 

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
  echo "<BR>You have no planets<BR><BR>";
}
else
{
  echo "Click on column header to sort by that value.<BR>";
  echo "<table>";
  echo "<tr><td><a href=planet-report.php3>Sector</a></td>".
    "<td><a href=planet-report.php3?sort=name>Planet Name</a></td>".
    "<td><a href=planet-report.php3?sort=organics>Organics</td>".
    "<td><a href=planet-report.php3?sort=ore>Ore</td>".
    "<td><a href=planet-report.php3?sort=goods>Goods</td>".
    "<td><a href=planet-report.php3?sort=energy>Energy</td>".
    "<td><a href=planet-report.php3?sort=colonists>Colonists</td>".
    "<td><a href=planet-report.php3?sort=credits>Credits</td>".
    "<td><a href=planet-report.php3?sort=fighters>Fighters</td>".
    "<td><a href=planet-report.php3?sort=torp>Torpedoes</td>".
    "<td>Base</td><td>Selling</td><td>Defeated</td></tr>";
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
  $total_defeated = 0;
  for($i=0; $i<$num_planets; $i++)
  {
    $total_organics += $planet[$i][planet_organics];
    $total_ore += $planet[$i][planet_ore];
    $total_goods += $planet[$i][planet_goods];
    $total_energy += $planet[$i][planet_energy];
    $total_colonists += $planet[$i][planet_colonists];
    $total_credits += $planet[$i][planet_credits];
    $total_fighters += $planet[$i][planet_fighters];
    $total_torp += $planet[$i][base_torp];
    if($planet[$i][base] == "Y")
    {
      $total_base += 1;
    }
    if($planet[$i][base_sells] == "Y")
    {
      $total_selling += 1;
    }
    if($planet[$i][planet_defeated] == "Y")
    {
      $total_defeated += 1;
    }
    if(empty($planet[$i][planet_name]))
    {
      $planet[$i][planet_name] = "Unnamed";
    }
    echo "<tr><td><a href=rsmove.php3?engage=1&destination=". $planet[$i][sector_id] . ">". 
      $planet[$i][sector_id] ."</a>" . "</td><td>". $planet[$i][planet_name] ."</td><td>". 
      $planet[$i][planet_organics] ."</td><td>". $planet[$i][planet_ore] ."</td><td>". 
      $planet[$i][planet_goods] ."</td><td>". $planet[$i][planet_energy] ."</td><td>". 
      $planet[$i][planet_colonists] ."</td><td>". $planet[$i][planet_credits] ."</td><td>". 
      $planet[$i][planet_fighters] ."</td><td>". $planet[$i][base_torp] ."</td><td>". 
      $planet[$i][base] ."</td><td>". $planet[$i][base_sells] ."</td><td>". 
      $planet[$i][planet_defeated] ."</td></tr>";
  }
  echo "<tr><td></td><td>Totals</td><td>". $total_organics ."</td><td>". $total_ore ."</td><td>". 
    $total_goods ."</td><td>". $total_energy ."</td><td>". $total_colonists ."</td><td>". 
    $total_credits ."</td><td>". $total_fighters ."</td><td>". $total_torp ."</td><td>". 
    $total_base ."</td><td>". $total_selling ."</td><td>". $total_defeated ."</td></tr>";
  echo "</table><BR><BR>";
}

echo "Click <a href=main.php3>here</a> to return to main menu.";

include("footer.php3");

?> 
