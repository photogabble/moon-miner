<?

  if(!isset($swordfish) || $swordfish != $adminpass)
    die("Script has not been called properly");

  echo "<B>TURNS</B><BR><BR>";
  echo "Adding turns...";
  QUERYOK(mysql_query("UPDATE ships SET turns=turns+1 WHERE turns<$max_turns"));
  //Someone explain why we are doing this???  If they get neg turns then they can stay.
  //echo "Ensuring minimum turns are 0...";
  //QUERYOK(mysql_query("UPDATE ships SET turns=0 WHERE turns<0"));
  echo "Ensuring maximum turns are $max_turns...";
  QUERYOK(mysql_query("UPDATE ships SET turns=$max_turns WHERE turns>$max_turns"));
  echo "<BR>";

?>