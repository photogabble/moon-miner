<?

include("config.php3");
updatecookie();

$title="Zone Information";
include("header.php3");

connectdb();

if(checklogin())
  die();

bigtitle();

// Get User Info
$res = mysql_query("SELECT * FROM zones WHERE zone_id='$zone'");
if(!mysql_num_rows($res))
  echo "This section of space does not exist!";
else
{
  $row = mysql_fetch_array($res);
  if($row[zone_name] == 'Federation space')
    $ownername = "Federation";
  elseif($row[zone_name] == 'Free-Trade space')
    $ownername = "The Free-Trade Coalition";
  elseif($row[zone_name] == 'Unchartered space')
    $ownername = "Nobody";
  else
  {
    if($row[corp_zone] == 'N')
    {
      $result = mysql_query("SELECT character_name FROM ships WHERE ship_id=$row[owner]");
      $ownerinfo = mysql_fetch_array($result);
      $ownername = $ownerinfo[character_name];
    }
    else
    {
      $result = mysql_query("SELECT team_name FROM teams WHERE id=$row[owner]");
      $ownerinfo = mysql_fetch_array($result);
      $ownername = $ownerinfo[team_name];
    }
  }

  if($row[allow_beacon] == 'Y')
    $beacon="Allowed";
  else
    $beacon="Not allowed";

  if($row[allow_attack] == 'Y')
    $attack="Allowed";
  else
    $attack="Not allowed";

  if($row[allow_defenses] == 'Y')
    $defense = "Allowed";
  else
    $defense = "Not allowed";

  if($row[allow_warpedit] == 'Y')
    $warpedit="Allowed";
  else
    $warpedit="Not allowed";

  if($row[allow_planet] == 'Y')
    $planet="Allowed";
  else
    $planet="Not allowed";

  if($row[allow_trade] == 'Y')
    $trade="Allowed";
  elseif($row[allow_trade] == 'L')
    $trade="Limited to owner and allies";
  elseif($row[allow_trade] == 'N')
    $trade="Not allowed";

  if($row[max_hull] == 0)
    $hull="Unlimited";
  else
    $hull=$row[max_hull];


  echo "<table border=1 cellspacing=1 cellpadding=0 width=\"65%\" align=center>" .
       "<tr bgcolor=$color_line2><td align=center colspan=2><b><font color=white>$row[zone_name]</font></b></td></tr>" .
       "<tr><td colspan=2>" .
       "<table border=0 cellspacing=0 cellpadding=2 width=\"100%\" align=center>" .
       "<tr bgcolor=$color_line1><td><font color=white size=3>&nbsp;Zone owner</font></td><td align=center><font color=white size=3>$ownername&nbsp;</font></td></tr>" .
       "<tr bgcolor=$color_line2><td><font color=white size=3>&nbsp;Beacons</font></td><td align=center><font color=white size=3>$beacon&nbsp;</font></td></tr>" .
       "<tr bgcolor=#300030><td><font color=white size=3>&nbsp;Attacking</font></td><td align=center><font color=white size=3>$attack&nbsp;</font></td></tr>" .
       "<tr bgcolor=#400040><td><font color=white size=3>&nbsp;Sector defenses</font></td><td align=center><font color=white size=3>$defense&nbsp;</font></td></tr>" .
       "<tr bgcolor=#300030><td><font color=white size=3>&nbsp;Warp Editors</font></td><td align=center><font color=white size=3>$warpedit&nbsp;</font></td></tr>" .
       "<tr bgcolor=#400040><td><font color=white size=3>&nbsp;Planets</font></td><td align=center><font color=white size=3>$planet&nbsp;</font></td></tr>" .
       "<tr bgcolor=#300030><td><font color=white size=3>&nbsp;Trading at port</font></td><td align=center><font color=white size=3>$trade&nbsp;</font></td></tr>" .
       "<tr bgcolor=#400040><td><font color=white size=3>&nbsp;Maximum hull size allowed</font></td><td align=center><font color=white size=3>$hull&nbsp;</font></td></tr>" .
       "</table>" .
       "</td></tr>" .
       "</table>";
}

echo "<BR><BR>";
TEXT_GOTOMAIN();

include("footer.php3");

?>