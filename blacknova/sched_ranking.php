<?

  if(!isset($swordfish) || $swordfish != $adminpass)
    die("Script has not been called properly");

  echo "<B>RANKING</B><BR><BR>";
  $res = mysql_query("SELECT ship_id FROM ships WHERE ship_destroyed='N'");
  while($row = mysql_fetch_array($res))
  {
    gen_score($row[ship_id]);
  }
  echo "<BR>";

?>