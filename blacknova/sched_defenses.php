<?
  if (preg_match("/sched_defenses.php/i", $PHP_SELF)) {
      echo "You can not access this file directly!";
      die();
  }

  if(!isset($swordfish) || $swordfish != $adminpass)
    die("Script has not been called properly");

  $db->Execute("DELETE from $dbtables[sector_defence] where quantity <= 0");

?>