<?

  if(!isset($swordfish) || $swordfish != $adminpass)
    die("Script has not been called properly");

  echo "<B>TURNS</B><BR><BR>";
  echo "Adding turns...";
  QUERYOK($db->Execute("UPDATE $dbtables[ships] SET turns=turns+1 WHERE turns<$max_turns"));
  echo "Ensuring maximum turns are $max_turns...";
  QUERYOK($db->Execute("UPDATE $dbtables[ships] SET turns=$max_turns WHERE turns>$max_turns"));
  echo "<BR>";

?>