<?
include("config.php");
updatecookie();

include("languages/$lang");
$title=$l_ranks_title;
include("header.php");

connectdb();
bigtitle();

//-------------------------------------------------------------------------------------------------

$res = $db->Execute("SELECT COUNT(*) AS num_players FROM $dbtables[ships] WHERE ship_destroyed='N' and email NOT LIKE '%@furangee'");
$row = $res->fields;
$num_players = $row['num_players'];

if($sort=="turns")
{
  $by="turns_used DESC,character_name ASC";
}
elseif($sort=="login")
{
  $by="last_login DESC,character_name ASC";
}
elseif($sort=="good")
{
  $by="rating DESC,character_name ASC";
}
elseif($sort=="bad")
{
  $by="rating ASC,character_name ASC";
}
elseif($sort=="alliance")
{
  $by="$dbtables[teams].team_name DESC, character_name ASC";
}
else
{
  $by="score DESC,character_name ASC";
}

$res = $db->Execute("SELECT $dbtables[ships].score,$dbtables[ships].character_name,$dbtables[ships].turns_used,$dbtables[ships].last_login,$dbtables[ships].rating, $dbtables[teams].team_name FROM $dbtables[ships] LEFT JOIN $dbtables[teams] ON $dbtables[ships].team = $dbtables[teams].id  WHERE ship_destroyed='N' and email NOT LIKE '%@furangee' ORDER BY $by LIMIT $max_rank");

//-------------------------------------------------------------------------------------------------

if(!$res)
{
  echo "$l_ranks_none<BR>";
}
else
{
  echo "<BR>$l_ranks_pnum: " . NUMBER($num_players);
  echo "<BR>$l_ranks_dships<BR><BR>";
  echo "<TABLE BORDER=0 CELLSPACING=0 CELLPADDING=2>";
  echo "<TR BGCOLOR=\"$color_header\"><TD><B>$l_ranks_rank</B></TD><TD><B><A HREF=ranking.php>$l_score</A></B></TD><TD><B>$l_player</B></TD><TD><B><A HREF=ranking.php?sort=turns>$l_turns_used</A></B></TD><TD><B><A HREF=ranking.php?sort=login>$l_ranks_lastlog</A></B></TD><TD><B><A HREF=ranking.php?sort=good>$l_ranks_good</A>/<A HREF=ranking.php?sort=bad>$l_ranks_evil</A></B></TD><TD><B><A HREF=ranking.php?sort=alliance>$l_team_alliance</A></B></TD></TR>\n";
  $color = $color_line1;
  while(!$res->EOF)
  {
    $row = $res->fields;
    $i++;
    $rating=round(sqrt( abs($row[rating]) ));
    if(abs($row[rating])!=$row[rating])
    {
      $rating=-1*$rating;
    }
    echo "<TR BGCOLOR=\"$color\"><TD>" . NUMBER($i) . "</TD><TD>" . NUMBER($row[score]) . "</TD><TD>$row[character_name]</TD><TD>" . NUMBER($row[turns_used]) . "</TD><TD>$row[last_login]</TD><TD>&nbsp;&nbsp;" . NUMBER($rating) . "</TD><TD>$row[team_name]&nbsp;</TD></TR>\n";
    if($color == $color_line1)
    {
      $color = $color_line2;
    }
    else
    {
      $color = $color_line1;
    }
    $res->MoveNext();
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

include("footer.php");

?>
