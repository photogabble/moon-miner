<?

include("config.php3");
updatecookie();

$title="Edit Zone";
include("header.php3");

connectdb();

if(checklogin())
  die();

bigtitle();

$res = mysql_query("SELECT * FROM zones WHERE zone_id='$zone'");
if(!mysql_num_rows($res))
  zoneedit_die("This section of space does not exist!");
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
  zoneedit_die("You are not owner of this zone so you can't edit it.");

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
     "<td align=right><font size=2><b>Zone name : &nbsp;</b></font></td>" .
     "<td><input type=text name=name size=30 maxlength=30 value=\"$curzone[zone_name]\"></td>" .
     "</tr><tr>" .
     "<td align=right><font size=2><b>Allow beacons : &nbsp;</b></font></td>" .
     "<td><input type=radio name=beacons value=Y $ybeacon>&nbsp;Yes&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type=radio name=beacons value=N $nbeacon>&nbsp;No</td>" .
     "</tr><tr>" .
     "<td align=right><font size=2><b>Allow attacking other ships : &nbsp;</b></font></td>" .
     "<td><input type=radio name=attacks value=Y $yattack>&nbsp;Yes&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type=radio name=attacks value=N $nattack>&nbsp;No</td>" .
     "</tr><tr>" .
     "<td align=right><font size=2><b>Allow warp editors : &nbsp;</b></font></td>" .
     "<td><input type=radio name=warpedits value=Y $ywarpedit>&nbsp;Yes&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type=radio name=warpedits value=N $nwarpedit>&nbsp;No</td>" .
     "</tr><tr>" .
     "<td align=right><font size=2><b>Allow sector defenses : &nbsp;</b></font></td>" .
     "<td><input type=radio name=defenses value=Y $ydefense>&nbsp;Yes&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type=radio name=defenses value=N $ndefense>&nbsp;No&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type=radio name=defenses value=L $ldefense>&nbsp;Limited to you and allies</td>" .
     "</tr><tr>" .
     "<td align=right><font size=2><b>Allow creating new planets : &nbsp;</b></font></td>" .
     "<td><input type=radio name=planets value=Y $yplanet>&nbsp;Yes&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type=radio name=planets value=N $nplanet>&nbsp;No&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type=radio name=planets value=L $lplanet>&nbsp;Limited to you and allies</td>" .
     "</tr><tr>" .
     "<td align=right><font size=2><b>Allow trading at port : &nbsp;</b></font></td>" .
     "<td><input type=radio name=trades value=Y $ytrade>&nbsp;Yes&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type=radio name=trades value=N $ntrade>&nbsp;No&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type=radio name=trades value=L $ltrade>&nbsp;Limited to you and allies</td>" .
     "</tr><tr>" .
     "<td colspan=2 align=center><br><input type=submit value=Save></td></tr>" .
     "</table>" .
     "</form>";


echo "Click <a href=zoneinfo.php3?zone=$zone>here</a> to return to the zone information page.<p>";
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


  mysql_query("UPDATE zones SET zone_name='$name', allow_beacon='$beacons', allow_attack='$attacks', allow_warpedit='$warpedits', allow_planet='$planets', allow_trade='$trades', allow_defenses='$defenses' WHERE zone_id=$zone");
  echo mysql_error();
  echo "You changes have been saved.<p>";
  echo "Click <a href=zoneinfo.php3?zone=$zone>here</a> to return to the zone information page.<p>";
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