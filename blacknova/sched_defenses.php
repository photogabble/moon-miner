<?
  if(!isset($swordfish) || $swordfish != $adminpass)
    die("Script has not been called properly");

  $db->Execute("DELETE from $dbtables[sector_defence] where quantity <= 0");

?>