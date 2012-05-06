<?php

  if (preg_match("/sched_turns.php/i", $_SERVER['PHP_SELF']))
  {
      echo "You can not access this file directly!";
      die();
  }

  echo "<B>TURNS</B><BR><BR>";
  echo "Adding turns...";
  QUERYOK($db->Execute("UPDATE $dbtables[ships] SET turns = turns + ($turns_per_tick * $multiplier) WHERE turns < $max_turns;"));
  echo "Ensuring maximum turns are $max_turns...";
  QUERYOK($db->Execute("UPDATE $dbtables[ships] SET turns = $max_turns WHERE turns > $max_turns;"));
  echo "<BR>";
  $multiplier = 0;

?>
