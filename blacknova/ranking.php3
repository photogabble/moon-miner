<?

include("config.php3");
updatecookie();

$title="Top $max_rank Players";
include("header.php3");

connectdb();
bigtitle();

//-------------------------------------------------------------------------------------------------
mysql_query("LOCK TABLES ships READ");

$res = mysql_query("SELECT COUNT(*) AS num_players FROM ships WHERE ship_destroyed='N' and email NOT LIKE '%@furangee'");
$row = mysql_fetch_array($res);
$num_players = $row['num_players'];
mysql_free_result($res);

if($sort=="turns")
{
  $res = mysql_query("SELECT score,character_name,turns_used,last_login,rating FROM ships WHERE ship_destroyed='N' and email NOT LIKE '%@furangee' ORDER BY turns_used DESC,character_name ASC LIMIT $max_rank");
}
elseif($sort=="login")
{
  $res = mysql_query("SELECT score,character_name,turns_used,last_login,rating FROM ships WHERE ship_destroyed='N' and email NOT LIKE '%@furangee' ORDER BY last_login DESC,character_name ASC LIMIT $max_rank");
}
elseif($sort=="good")
{
  $res = mysql_query("SELECT score,character_name,turns_used,last_login,rating FROM ships WHERE ship_destroyed='N' and email NOT LIKE '%@furangee' ORDER BY rating DESC,character_name ASC LIMIT $max_rank");
}
elseif($sort=="bad")
{
  $res = mysql_query("SELECT score,character_name,turns_used,last_login,rating FROM ships WHERE ship_destroyed='N' and email NOT LIKE '%@furangee' ORDER BY rating ASC,character_name ASC LIMIT $max_rank");
}
else
{
  $res = mysql_query("SELECT score,character_name,turns_used,last_login,rating FROM ships WHERE ship_destroyed='N' and email NOT LIKE '%@furangee' ORDER BY score DESC,character_name ASC LIMIT $max_rank");
}
mysql_query("UNLOCK TABLES");
//-------------------------------------------------------------------------------------------------

if(!mysql_num_rows($res))
{
  echo "No Results to show.<BR>";
}
else
{
  echo "<BR>Total number of players: " . NUMBER($num_players);
  echo "<BR>Players with destroyed ships are not counted.<BR><BR>";
  echo "<TABLE BORDER=0 CELLSPACING=0 CELLPADDING=2>";
  echo "<TR BGCOLOR=\"$color_header\"><TD><B>Rank</B></TD><TD><B><A HREF=ranking.php3>Score</A></B></TD><TD><B>Player</B></TD><TD><B><A HREF=ranking.php3?sort=turns>Turns used</A></B></TD><TD><B><A HREF=ranking.php3?sort=login>Last login</A></B></TD><TD><B><A HREF=ranking.php3?sort=good>Good</A>/<A HREF=ranking.php3?sort=bad>Evil</A></B></TD></TR>";
  $color = $color_line1;
  while($row = mysql_fetch_array($res))
  {
    $i++;
    $rating=round(pow( abs($row[rating]) , .5));
    if(abs($row[rating])!=$row[rating])
    {
      $rating=-1*$rating;
    }
    echo "<TR BGCOLOR=\"$color\"><TD>" . NUMBER($i) . "</TD><TD>" . NUMBER($row[score]) . "</TD><TD>$row[character_name]</TD><TD>" . NUMBER($row[turns_used]) . "</TD><TD>$row[last_login]</TD><TD>" . NUMBER($rating) . "</TD></TR>";
    if($color == $color_line1)
    {
      $color = $color_line2;
    }
    else
    {
      $color = $color_line1;
    }
  }
  mysql_free_result($res);
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
