<?

include("config.php3");
include_once($gameroot . "/languages/$lang");
updatecookie();

$title="Options"; 
include("header.php3");

connectdb();

if(checklogin())
{
  die();
}

bigtitle();

//-------------------------------------------------------------------------------------------------
mysql_query("LOCK TABLES ships READ");

$res = mysql_query("SELECT * FROM ships WHERE email='$username'");
$playerinfo = mysql_fetch_array($res);
mysql_free_result($res);

mysql_query("UNLOCK TABLES");
//-------------------------------------------------------------------------------------------------

echo "<FORM ACTION=option2.php3 METHOD=POST>";
echo "<TABLE BORDER=0 CELLSPACING=0 CELLPADDING=2>";
echo "<TR BGCOLOR=\"$color_header\">";
echo "<TD COLSPAN=2><B>Change password</B></TD>";
echo "</TR>";
echo "<TR BGCOLOR=\"$color_line1\">";
echo "<TD>Current password:</TD>";
echo "<TD><INPUT TYPE=PASSWORD NAME=oldpass SIZE=16 MAXLENGTH=16 VALUE=\"\"></TD>";
echo "</TR>";
echo "<TR BGCOLOR=\"$color_line2\">";
echo "<TD>New password:</TD>";
echo "<TD><INPUT TYPE=PASSWORD NAME=newpass1 SIZE=16 MAXLENGTH=16 VALUE=\"\"></TD>";
echo "</TR>";
echo "<TR BGCOLOR=\"$color_line1\">";
echo "<TD>New password (again):</TD>";
echo "<TD><INPUT TYPE=PASSWORD NAME=newpass2 SIZE=16 MAXLENGTH=16 VALUE=\"\"></TD>";
echo "</TR>";
echo "<TR BGCOLOR=\"$color_header\">";
echo "<TD COLSPAN=2><B>User interface</B></TD>";
echo "</TR>";
$intrf = ($playerinfo['interface'] == 'N') ? "CHECKED" : "";
echo "<TR BGCOLOR=\"$color_line1\">";
echo "<TD>Use new layout?</TD><TD><INPUT TYPE=CHECKBOX NAME=intrf VALUE=N $intrf></INPUT></TD>";
echo "</TR>";
echo "<TR BGCOLOR=\"$color_header\">";
echo "<TD COLSPAN=2><B>Language</B></TD>";
echo "</TR>";
echo "<TR BGCOLOR=\"$color_line1\">";
echo "<TD>Select one :</TD><TD><select NAME=newlang>";

foreach($avail_lang as $curlang)
{
  if($curlang['file'] == $playerinfo[lang])
    $selected = "selected";
  else
    $selected = "";

  echo "<option value=$curlang[file] $selected>$curlang[name]</option>";
}

echo "</select></td>";
echo "</TR>";
echo "<TR BGCOLOR=\"$color_header\">";
echo "<TD COLSPAN=2><B>DHTML</B></TD>";
echo "</TR>";
echo "<TR BGCOLOR=\"$color_line2\">";
$dhtml = ($playerinfo['dhtml'] == 'Y') ? "CHECKED" : "";
echo "<TD>Enabled</TD><TD><INPUT TYPE=CHECKBOX NAME=dhtml VALUE=Y $dhtml></INPUT></TD>";
echo "</TABLE>";
echo "<BR>";
echo "<INPUT TYPE=SUBMIT value=Save>";
echo "</FORM>";

TEXT_GOTOMAIN();

include("footer.php3");

?>

