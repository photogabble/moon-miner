<?

  if(!isset($swordfish) || $swordfish != $adminpass)
    die("Script has not been called properly");

  echo "<B>RANKING</B><BR><BR>";
  $res = $db->Execute("SELECT ship_id FROM $dbtables[ships] WHERE ship_destroyed='N'");
  while(!$res->EOF)
  {
    gen_score($res->fields[ship_id]);
    $res->MoveNext();
  }
  echo "<BR>";

?>