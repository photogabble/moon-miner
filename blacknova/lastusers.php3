<?

include("config.php3");
updatecookie();

$max_last = 20;

$title="Last $max_last Users";
include("header.php3");

connectdb();
bigtitle();

$res = mysql_query("SELECT character_name,last_login FROM ships ORDER BY last_login DESC LIMIT $max_last");
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

echo "<BR>Click <A HREF=main.php3>here</A> to return to main menu.";

include("footer.php3");

?>
