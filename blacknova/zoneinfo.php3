<?
include("config.php3");
updatecookie();

include_once($gameroot . "/languages/$lang");
$title=$l_zi_title;
include("header.php3");

connectdb();

if(checklogin())
  die();

bigtitle();

$res = mysql_query("SELECT * FROM ships WHERE email='$username'");
$playerinfo = mysql_fetch_array($res);
mysql_free_result($res);

$res = mysql_query("SELECT * FROM zones WHERE zone_id='$zone'");
if(!mysql_num_rows($res))
  echo $l_zi_nexist;
else
{
  $row = mysql_fetch_array($res);
  if($row[zone_id] == '2')
    $ownername = $l_zi_feds;
  elseif($row[zone_id] == '3')
    $ownername = $l_zi_traders;
  elseif($row[zone_id] == '1')
    $ownername = $l_zi_nobody;
  elseif($row[zone_id] == '4')
    $ownername = $l_zi_war;
  else
  {
    if($row[corp_zone] == 'N')
    {
      $result = mysql_query("SELECT ship_id, character_name FROM ships WHERE ship_id=$row[owner]");
      $ownerinfo = mysql_fetch_array($result);
      $ownername = $ownerinfo[character_name];
    }
    else
    {
      $result = mysql_query("SELECT team_name, creator, id FROM teams WHERE id=$row[owner]");
      $ownerinfo = mysql_fetch_array($result);
      $ownername = $ownerinfo[team_name];
    }
  }

  if($row[allow_beacon] == 'Y')
    $beacon=$l_zi_allow;
  else
    $beacon=$l_zi_notallow;

  if($row[allow_attack] == 'Y')
    $attack=$l_zi_allow;
  else
    $attack=$l_zi_notallow;

  if($row[allow_defenses] == 'Y')
    $defense = $l_zi_allow;
  elseif($row[allow_defenses] == 'N')
    $defense = $l_zi_notallow;
  else
    $defense = $l_zi_limit;

  if($row[allow_warpedit] == 'Y')
    $warpedit=$l_zi_allow;
  else
    $warpedit=$l_zi_notallow;

  if($row[allow_planet] == 'Y')
    $planet=$l_zi_allow;
  elseif($row[allow_planet] == 'L')
    $planet=$l_zi_limit;
  else
    $planet=$l_zi_notallow;

  if($row[allow_trade] == 'Y')
    $trade=$l_zi_allow;
  elseif($row[allow_trade] == 'L')
    $trade=$l_zi_limit;
  elseif($row[allow_trade] == 'N')
    $trade=$l_zi_notallow;

  if($row[max_hull] == 0)
    $hull=$l_zi_ul;
  else
    $hull=$row[max_hull];

  if(($row[corp_zone] == 'N' && $row[owner] == $playerinfo[ship_id]) || ($row[corp_zone] == 'Y' && $row[owner] == $playerinfo[team] && $playerinfo[ship_id] == $ownerinfo[creator]))
    echo "<center>$l_zi_control. <a href=zoneedit.php?zone=$zone>$l_clickme</a> $l_zi_tochange</center><p>";

  echo "<table border=1 cellspacing=1 cellpadding=0 width=\"65%\" align=center>" .
       "<tr bgcolor=$color_line2><td align=center colspan=2><b><font color=white>$row[zone_name]</font></b></td></tr>" .
       "<tr><td colspan=2>" .
       "<table border=0 cellspacing=0 cellpadding=2 width=\"100%\" align=center>" .
       "<tr bgcolor=$color_line1><td width=\"50%\"><font color=white size=3>&nbsp;$l_zi_owner</font></td><td width=\"50%\"><font color=white size=3>$ownername&nbsp;</font></td></tr>" .
       "<tr bgcolor=$color_line2><td><font color=white size=3>&nbsp;$l_beacons</font></td><td><font color=white size=3>$beacon&nbsp;</font></td></tr>" .
       "<tr bgcolor=#300030><td><font color=white size=3>&nbsp;$l_att_att</font></td><td><font color=white size=3>$attack&nbsp;</font></td></tr>" .
       "<tr bgcolor=#400040><td><font color=white size=3>&nbsp;$l_md_title</font></td><td><font color=white size=3>$defense&nbsp;</font></td></tr>" .
       "<tr bgcolor=#300030><td><font color=white size=3>&nbsp;$l_warpedit</font></td><td><font color=white size=3>$warpedit&nbsp;</font></td></tr>" .
       "<tr bgcolor=#400040><td><font color=white size=3>&nbsp;$l_planets</font></td><td><font color=white size=3>$planet&nbsp;</font></td></tr>" .
       "<tr bgcolor=#300030><td><font color=white size=3>&nbsp;$l_title_port</font></td><td><font color=white size=3>$trade&nbsp;</font></td></tr>" .
       "<tr bgcolor=#400040><td><font color=white size=3>&nbsp;$l_zi_maxhull</font></td><td><font color=white size=3>$hull&nbsp;</font></td></tr>" .
       "</table>" .
       "</td></tr>" .
       "</table>";
}
echo "<BR><BR>";

TEXT_GOTOMAIN();
include("footer.php3");
?>
