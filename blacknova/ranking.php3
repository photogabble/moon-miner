<?

include("config.php3");
updatecookie();

$max_rank = 100;

$title="Top $max_rank Players";
include("header.php3");

connectdb();
bigtitle();

$res = mysql_query("SELECT score,character_name,turns_used,last_login FROM ships WHERE ship_destroyed='N' ORDER BY score DESC,character_name ASC LIMIT $max_rank");
if(!mysql_num_rows($res))
{
  echo "No Results to show.<BR>";
}
else
{
  echo "<BR>Players with destroyed ships are not counted.<BR><BR>";
  echo "<TABLE BORDER=0 CELLSPACING=0 CELLPADDING=2>";
  echo "<TR BGCOLOR=\"$color_header\"><TD><B>Rank</B></TD><TD><B>Score</B></TD><TD><B>Player</B></TD><TD><B>Turns used</B></TD><TD><B>Last login</B></TD></TR>";
  $color = $color_line1;
  while($row = mysql_fetch_array($res))
  {
    $i++;
    echo "<TR BGCOLOR=\"$color\"><TD>" . NUMBER($i) . "</TD><TD>" . NUMBER($row[score]) . "</TD><TD>$row[character_name]</TD><TD>" . NUMBER($row[turns_used]) . "</TD><TD>$row[last_login]</TD></TR>";
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
