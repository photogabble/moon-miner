<?
include("config.php3");
updatecookie();

include_once($gameroot . "/languages/$lang");
$title=$l_ze_title;
include("header.php3");

connectdb();

if(checklogin())
  die();

bigtitle();

$res = mysql_query("SELECT * FROM zones WHERE zone_id='$zone'");
if(!mysql_num_rows($res))
  zoneedit_die($l_zi_nexist);
$curzone = mysql_fetch_array($res);

if($curzone[corp_zone] == 'N')
{
  $result = mysql_query("SELECT ship_id FROM ships WHERE email='$username'");
  $ownerinfo = mysql_fetch_array($result);
}
else
{
  $result = mysql_query("SELECT creator, id FROM teams WHERE creator=$curzone[owner]");
  $ownerinfo = mysql_fetch_array($result);
}

if(($curzone[corp_zone] == 'N' && $curzone[owner] != $ownerinfo[ship_id]) || ($curzone[corp_zone] == 'Y' && $curzone[owner] != $ownerinfo[id] && $row[owner] == $ownerinfo[creator]))
  zoneedit_die($l_ze_notowner);

if($command == change)
  zoneedit_change();

if($curzone[allow_beacon] == 'Y')
  $ybeacon = "checked";
else
  $nbeacon = "checked";

if($curzone[allow_attack] == 'Y')
  $yattack = "checked";
else
  $nattack = "checked";

if($curzone[allow_warpedit] == 'Y')
  $ywarpedit = "checked";
else
  $nwarpedit = "checked";

if($curzone[allow_planet] == 'Y')
  $yplanet = "checked";
elseif($curzone[allow_planet] == 'N')
  $nplanet = "checked";
else
  $lplanet = "checked";

if($curzone[allow_trade] == 'Y')
  $ytrade = "checked";
elseif($curzone[allow_trade] == 'N')
  $ntrade = "checked";
else
  $ltrade = "checked";

if($curzone[allow_defenses] == 'Y')
  $ydefense = "checked";
elseif($curzone[allow_defenses] == 'N')
  $ndefense = "checked";
else
  $ldefense = "checked";

echo "<form action=zoneedit.php?command=change&zone=$zone method=post>" .
     "<table border=0><tr>" .
     "<td align=right><font size=2><b>$l_ze_name : &nbsp;</b></font></td>" .
     "<td><input type=text name=name size=30 maxlength=30 value=\"$curzone[zone_name]\"></td>" .
     "</tr><tr>" .
     "<td align=right><font size=2><b>$l_ze_allow $l_beacons : &nbsp;</b></font></td>" .
     "<td><input type=radio name=beacons value=Y $ybeacon>&nbsp;$l_yes&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type=radio name=beacons value=N $nbeacon>&nbsp;$l_no</td>" .
     "</tr><tr>" .
     "<td align=right><font size=2><b>$l_ze_attacks : &nbsp;</b></font></td>" .
     "<td><input type=radio name=attacks value=Y $yattack>&nbsp;$l_yes&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type=radio name=attacks value=N $nattack>&nbsp;$l_no</td>" .
     "</tr><tr>" .
     "<td align=right><font size=2><b>$l_ze_allow $l_warpedit : &nbsp;</b></font></td>" .
     "<td><input type=radio name=warpedits value=Y $ywarpedit>&nbsp;$l_yes&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type=radio name=warpedits value=N $nwarpedit>&nbsp;$l_no</td>" .
     "</tr><tr>" .
     "<td align=right><font size=2><b>$l_allow $l_sector_def : &nbsp;</b></font></td>" .
     "<td><input type=radio name=defenses value=Y $ydefense>&nbsp;$l_yes&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type=radio name=defenses value=N $ndefense>&nbsp;$l_no&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type=radio name=defenses value=L $ldefense>&nbsp;$l_zi_limit</td>" .
     "</tr><tr>" .
     "<td align=right><font size=2><b>$l_ze_genesis : &nbsp;</b></font></td>" .
     "<td><input type=radio name=planets value=Y $yplanet>&nbsp;$l_yes&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type=radio name=planets value=N $nplanet>&nbsp;$l_no&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type=radio name=planets value=L $lplanet>&nbsp;$l_zi_limit</td>" .
     "</tr><tr>" .
     "<td align=right><font size=2><b>$l_allow $l_title_port : &nbsp;</b></font></td>" .
     "<td><input type=radio name=trades value=Y $ytrade>&nbsp;$l_yes&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type=radio name=trades value=N $ntrade>&nbsp;$l_no&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type=radio name=trades value=L $ltrade>&nbsp;$l_zi_limit</td>" .
     "</tr><tr>" .
     "<td colspan=2 align=center><br><input type=submit value=$l_submit></td></tr>" .
     "</table>" .
     "</form>";


echo "<a href=zoneinfo.php3?zone=$zone>$l_clickme</a> $l_ze_return.<p>";
TEXT_GOTOMAIN();

include("footer.php3");

//-----------------------------------------------------------------

function zoneedit_change()
{
  global $zone;
  global $name;
  global $beacons;
  global $attacks;
  global $warpedits;
  global $planets;
  global $trades;
  global $defenses;
  global $l_clickme, $l_ze_saved, $l_ze_return;

  if(!get_magic_quotes_gpc())
    $name = addslashes($name);
  mysql_query("UPDATE zones SET zone_name='$name', allow_beacon='$beacons', allow_attack='$attacks', allow_warpedit='$warpedits', allow_planet='$planets', allow_trade='$trades', allow_defenses='$defenses' WHERE zone_id=$zone");
  echo mysql_error();
  echo "$l_ze_saved<p>";
  echo "<a href=zoneinfo.php3?zone=$zone>$l_clickme</a> $l_ze_return.<p>";
  TEXT_GOTOMAIN();

  include("footer.php3");
  die();
}

function zoneedit_die($error_msg)
{
  echo "<p>$error_msg<p>";
  mysql_query("UNLOCK TABLES");

  TEXT_GOTOMAIN();
  include("footer.php3");
  die();
}

?>
