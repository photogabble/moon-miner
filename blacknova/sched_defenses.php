<?
  if(!isset($swordfish) || $swordfish != $adminpass)
    die("Script has not been called properly");

  mysql_query("DELETE from sector_defence where quantity <= 0");

?>