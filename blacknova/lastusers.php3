<?

include("config.php3");
updatecookie();

$max_last = 20;

$title="Last $max_last Users";
include("header.php3");

connectdb();
bigtitle();

$res = mysql_query("SELECT character_name,last_login FROM ships WHERE email NOT LIKE '%@furangee' ORDER BY last_login DESC LIMIT $max_last");
if($res)
{
  echo "<TABLE BORDER=0 CELLSPACING=0 CELLPADDING=2>";
  echo "<TR BGCOLOR=\"$color_header\"><TD><B>Player</B></TD><TD><B>Date/Time</B></TD></TR>";
  $color = $color_line1;
  while($row = mysql_fetch_array($res))
  {
    echo "<TR BGCOLOR=\"$color\"><TD>$row[character_name]</TD><TD>$row[last_login]</TD></TR>";
    if($color == $color_line1)
    {
      $color = $color_line2;
    }
    else
    {
      $color = $color_line1;
    }
  }
  echo "</TABLE>";
}

echo "<BR>";

if(empty($username))
{
  TEXT_GOTOLOGIN();
}
else
{
  TEXT_GOTOMAIN();
}

include("footer.php3");

?>
