<?

  if(!isset($swordfish) || $swordfish != $adminpass)
    die("Script has not been called properly");

  // *********************************
  // ***** FURANGEE TURN UPDATES *****
  // *********************************
  echo "<BR><B>FURANGEE TURNS</B><BR><BR>";

  // *********************************
  // ******* INCLUDE FUNCTIONS *******
  // *********************************
  include_once("furangee_funcs.php");
  global $targetlink;

  // *********************************
  // **** MAKE FURANGEE SELECTION ****
  // *********************************
  $furcount = $furcount0 = $furcount0a = $furcount1 = $furcount1a = $furcount2 = $furcount2a = $furcount3 = $furcount3a = $furcount3h = 0;
  $res = mysql_query("SELECT * FROM ships JOIN furangee WHERE email=furangee_id and active='Y' and ship_destroyed='N' ORDER BY sector");
  while($playerinfo = mysql_fetch_array($res))
  {
    // *********************************
    // ****** REGENERATE/BUY STATS *****
    // *********************************
    furangeeregen();
    // *********************************
    // ****** RUN THROUGH ORDERS *******
    // *********************************
    $furcount++;
    if (rand(1,5) > 1)                                 // ****** 20% CHANCE OF NOT MOVING AT ALL ******
    {
      // *********************************
      // ****** ORDERS = 0 SENTINEL ******
      // *********************************
      if ($playerinfo[orders] == 0)
      {
        $furcount0++;
        // ****** FIND A TARGET ******
        // ****** IN MY SECTOR, NOT MYSELF, NOT ON A PLANET ******
        $reso0 = mysql_query("SELECT * FROM ships WHERE sector=$playerinfo[sector] and email!='$playerinfo[email]' and planet_id=0");
        if ($rowo0 = mysql_fetch_array($reso0))
        {
          if ($playerinfo[aggression] == 0)            // ****** O = 0 & AGRESSION = 0 PEACEFUL ******
          {
            // This Guy Does Nothing But Sit As A Target Himself
          }
          elseif ($playerinfo[aggression] == 1)        // ****** O = 0 & AGRESSION = 1 ATTACK SOMETIMES ******
          {
            // Furangee's only compare number of fighters when determining if they have an attack advantage
            if ($playerinfo[ship_fighters] > $rowo0[ship_fighters])
            {
              $furcount0a++;
              playerlog($playerinfo[ship_id], LOG_FURANGEE_ATTACK, "$rowo0[character_name]");
              furangeetoship($rowo0[ship_id]);
            }
          }
          elseif ($playerinfo[aggression] == 2)        // ****** O = 0 & AGRESSION = 2 ATTACK ALLWAYS ******
          {
            $furcount0a++;
            playerlog($playerinfo[ship_id], LOG_FURANGEE_ATTACK, "$rowo0[character_name]");
            furangeetoship($rowo0[ship_id]);
          }
        }
      }
      // *********************************
      // ******** ORDERS = 1 ROAM ********
      // *********************************
      elseif ($playerinfo[orders] == 1)
      {
        $furcount1++;
        // ****** ROAM TO A NEW SECTOR BEFORE DOING ANYTHING ELSE ******
        $targetlink = $playerinfo[sector];
        furangeemove();
        // ****** FIND A TARGET ******
        // ****** IN MY SECTOR, NOT MYSELF, NOT ON A PLANET ******
        $reso1 = mysql_query("SELECT * FROM ships WHERE sector=$targetlink and email!='$playerinfo[email]' and planet_id=0");
        if ($rowo1 = mysql_fetch_array($reso1))
        {
          if ($playerinfo[aggression] == 0)            // ****** O = 0 & AGRESSION = 0 PEACEFUL ******
          {
            // This Guy Does Nothing But Sit As A Target Himself
          }
          elseif ($playerinfo[aggression] == 1)        // ****** O = 0 & AGRESSION = 1 ATTACK SOMETIMES ******
          {
            // Furangee's only compare number of fighters when determining if they have an attack advantage
            if ($playerinfo[ship_fighters] > $rowo1[ship_fighters])
            {
              $furcount1a++;
              playerlog($playerinfo[ship_id], LOG_FURANGEE_ATTACK, "$rowo1[character_name]");
              furangeetoship($rowo1[ship_id]);
            }
          }
          elseif ($playerinfo[aggression] == 2)        // ****** O = 0 & AGRESSION = 2 ATTACK ALLWAYS ******
          {
            $furcount1a++;
            playerlog($playerinfo[ship_id], LOG_FURANGEE_ATTACK, "$rowo1[character_name]");
            furangeetoship($rowo1[ship_id]);
          }
        }
      }
      // *********************************
      // *** ORDERS = 2 ROAM AND TRADE ***
      // *********************************
      elseif ($playerinfo[orders] == 2)
      {
        $furcount2++;
        // ****** ROAM TO A NEW SECTOR BEFORE DOING ANYTHING ELSE ******
        $targetlink = $playerinfo[sector];
        furangeemove();
        // ****** NOW TRADE BEFORE WE DO ANY AGGRESSION CHECKS ******
        furangeetrade();
        // ****** FIND A TARGET ******
        // ****** IN MY SECTOR, NOT MYSELF, NOT ON A PLANET ******
        $reso2 = mysql_query("SELECT * FROM ships WHERE sector=$targetlink and email!='$playerinfo[email]' and planet_id=0");
        if ($rowo2 = mysql_fetch_array($reso2))
        {
          if ($playerinfo[aggression] == 0)            // ****** O = 0 & AGRESSION = 0 PEACEFUL ******
          {
            // This Guy Does Nothing But Sit As A Target Himself
          }
          elseif ($playerinfo[aggression] == 1)        // ****** O = 0 & AGRESSION = 1 ATTACK SOMETIMES ******
          {
            // Furangee's only compare number of fighters when determining if they have an attack advantage
            if ($playerinfo[ship_fighters] > $rowo2[ship_fighters])
            {
              $furcount2a++;
              playerlog($playerinfo[ship_id], LOG_FURANGEE_ATTACK, "$rowo2[character_name]");
              furangeetoship($rowo2[ship_id]);
            }
          }
          elseif ($playerinfo[aggression] == 2)        // ****** O = 0 & AGRESSION = 2 ATTACK ALLWAYS ******
          {
            $furcount2a++;
            playerlog($playerinfo[ship_id], LOG_FURANGEE_ATTACK, "$rowo2[character_name]");
            furangeetoship($rowo2[ship_id]);
          }
        }
      }
      // *********************************
      // *** ORDERS = 3 ROAM AND HUNT  ***
      // *********************************
      elseif ($playerinfo[orders] == 3)
      {
        $furcount3++;
        // ****** LET SEE IF WE GO HUNTING THIS ROUND BEFORE WE DO ANYTHING ELSE ******
        $hunt=rand(0,3);                               // *** 25% CHANCE OF HUNTING ***
        if ($hunt==0)
        {
        $furcount3h++;
        furangeehunter();
        } else
        {
          // ****** ROAM TO A NEW SECTOR BEFORE DOING ANYTHING ELSE ******
          furangeemove();
          // ****** FIND A TARGET ******
          // ****** IN MY SECTOR, NOT MYSELF, NOT ON A PLANET ******
          $reso3 = mysql_query("SELECT * FROM ships WHERE sector=$playerinfo[sector] and email!='$playerinfo[email]' and planet_id=0");
          if ($rowo3 = mysql_fetch_array($reso3))
          {
            if ($playerinfo[aggression] == 0)            // ****** O = 0 & AGRESSION = 0 PEACEFUL ******
            {
              // This Guy Does Nothing But Sit As A Target Himself
            }
            elseif ($playerinfo[aggression] == 1)        // ****** O = 0 & AGRESSION = 1 ATTACK SOMETIMES ******
            {
              // Furangee's only compare number of fighters when determining if they have an attack advantage
              if ($playerinfo[ship_fighters] > $rowo3[ship_fighters])
              {
                $furcount3a++;
                playerlog($playerinfo[ship_id], LOG_FURANGEE_ATTACK, "$rowo3[character_name]");
                furangeetoship($rowo3[ship_id]);
              }
            }
            elseif ($playerinfo[aggression] == 2)        // ****** O = 0 & AGRESSION = 2 ATTACK ALLWAYS ******
            {
              $furcount3a++;
              playerlog($playerinfo[ship_id], LOG_FURANGEE_ATTACK, "$rowo3[character_name]");
              furangeetoship($rowo3[ship_id]);
            }
          }
        }
      }
    }
  }
  $furnonmove = $furcount - ($furcount0 + $furcount1 + $furcount2);
  echo "Counted $furcount Furangee players that are ACTIVE with working ships.<BR>";
  echo "$furnonmove Furangee players did not do anything this round. <BR>";
  echo "$furcount0 Furangee players had SENTINEL orders of which $furcount0a launched attacks. <BR>";
  echo "$furcount1 Furangee players had ROAM orders of which $furcount1a launched attacks. <BR>";
  echo "$furcount2 Furangee players had ROAM AND TRADE orders of which $furcount2a launched attacks. <BR>";
  echo "$furcount3 Furangee players had ROAM AND HUNT orders of which $furcount3a launched attacks and $furcount3h went hunting. <BR>";
  echo "FURANGEE TURNS COMPLETE. <BR>";
  echo "<BR>";
  // *********************************
  // ***** END OF FURANGEE TURNS *****
  // *********************************

?>
