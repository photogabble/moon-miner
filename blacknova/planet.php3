<?

include("extension.inc");
include("config.$phpext");
include("combat.$phpext");

updatecookie();

$title="Planet Menu";
include("header.$phpext");

connectdb();
if(checklogin())
{
  die();
}
//-------------------------------------------------------------------------------------------------

$result = mysql_query("SELECT * FROM ships WHERE email='$username'");
$playerinfo=mysql_fetch_array($result);

$result2 = mysql_query("SELECT * FROM universe WHERE sector_id=$playerinfo[sector]");
$sectorinfo=mysql_fetch_array($result2);

$result3 = mysql_query("SELECT * FROM planets WHERE planet_id=$planet_id");
if($result3)
  $planetinfo=mysql_fetch_array($result3);

bigtitle();

srand((double)microtime()*1000000);

if(!empty($planetinfo))
/* if there is a planet in the sector show appropriate menu */
{ 
  if($playerinfo[sector] != $planetinfo[sector_id])
  {
    if($playerinfo[on_planet] == 'Y')
      mysql_query("UPDATE ships SET on_planet='N' WHERE ship_id=$playerinfo[ship_id]");
    echo "You are not in the same sector as this planet, so you can't interact with it.<BR>";
    TEXT_GOTOMAIN();
    include("footer.$phpext");
    die();
  }
  if($planetinfo[owner] == 0 && $command != "capture")
  {
    echo "This planet is unowned.<BR><BR>";
    $update = mysql_query("UPDATE planets SET fighters=0, defeated='Y' WHERE planet_id=$planet_id]");
    echo "You may <a href=planet.$phpext?planet_id=$planet_id&command=capture>capture</a> the planet or just leave it undefended.<BR><BR>";
    echo "<BR>";
    TEXT_GOTOMAIN();
    include("footer.$phpext");
    die();
  }
  if($planetinfo[owner] != 0)
  {
    $result3 = mysql_query("SELECT * FROM ships WHERE ship_id=$planetinfo[owner]");
    $ownerinfo = mysql_fetch_array($result3);
  }
  if($planetinfo[defeated] && $planetinfo[fighters] > 0) 
  { 
    $update = mysql_query("UPDATE planets SET defeated='N' WHERE planet_id=$planet_id");
    $planetinfo[defeated] = "N";
  }
  if(empty($command))
  {
    /* ...if there is no planet command already */
    if(empty($planetinfo[name]))
    {
      echo "Welcome to $ownerinfo[character_name]'s un-named planet.<BR><BR>";
    }
    else
    {
      echo "Welcome to $planetinfo[name], owned by $ownerinfo[character_name].<BR><BR>";
    }
    if($planetinfo[owner] == $playerinfo[ship_id] || ($planetinfo[corp] == $playerinfo[team] && $playerinfo[team] > 0))
    {
      /* owner menu */
      echo "Turns availables: $playerinfo[turns]<p>";
      echo "<a href=planet.$phpext?planet_id=$planet_id&command=name>Name</a> Planet<BR>";
      if($playerinfo[on_planet] == 'Y' && $playerinfo[planet_id] == $planet_id)
      {
        echo "You are presently on the surface of the planet.<BR>";
        echo "<a href=planet.$phpext?planet_id=$planet_id&command=leave>Leave</a> Planet<BR>";
        echo "You can also <a href=logout.$phpext>log-out</a> in the safety of your planet.<BR>";
      }
      else
      {
        echo "You are presently in orbit of the planet.<BR>";
        echo "<a href=planet.$phpext?planet_id=$planet_id&command=land>Land</a> on Planet<BR>";
      }
      echo "<a href=planet.$phpext?planet_id=$planet_id&command=transfer>Transfer</a> commodities/resources/colonists to/from Planet<BR>";
      if($planetinfo[sells] == "Y")
      {
        echo "Planet is presently selling commodities.  ";
      }
      else
      {
        echo "Planet is not presently selling commodities.  ";
      }
      echo "Toggle planet <a href=planet.$phpext?planet_id=$planet_id&command=sell>selling</a> commodities<BR>";
      if($planetinfo[base] == "N")
      {
        echo "With enough commodites and credits, you can <a href=planet.$phpext?planet_id=$planet_id&command=base>build a base</a> to help defend the planet.<BR>";
      }
      else
      { 
        echo "You have a base on this planet.<BR>";
      }
     
	if ($planetinfo[corp] == 0)
	{
		echo "You can also make this planet a <a href=corp.php?planet_id=$planet_id&action=planetcorp>Corporate Planet</a>.<BR>";
	}
	else
	{
		echo "You can also make this planet a <a href=corp.php?planet_id=$planet_id&action=planetpersonal>Personal Planet</a>.<BR>";
	}
 
      /* change production rates */
      echo "<FORM ACTION=planet.$phpext?planet_id=$planet_id METHOD=POST>";
      echo "<INPUT TYPE=HIDDEN NAME=command VALUE=productions><BR>";
      echo "<TABLE BORDER=0 CELLSPACING=0 CELLPADDING=2>";
      echo "<TR BGCOLOR=\"$color_header\"><TD></TD><TD><B>Ore</B></TD><TD><B>Organics</B></TD><TD><B>Goods</B></TD><TD><B>Energy</B></TD><TD><B>Colonists</B></TD><TD><B>Credits</B></TD><TD><B>Fighters</B></TD><TD><B>Torpedoes</TD></TR>";
      echo "<TR BGCOLOR=\"$color_line1\">";
      echo "<TD>Current Quantities</TD>";
      echo "<TD>" . NUMBER($planetinfo[ore]) . "</TD>";
      echo "<TD>" . NUMBER($planetinfo[organics]) . "</TD>";
      echo "<TD>" . NUMBER($planetinfo[goods]) . "</TD>";
      echo "<TD>" . NUMBER($planetinfo[energy]) . "</TD>";
      echo "<TD>" . NUMBER($planetinfo[colonists]) . "</TD>";
      echo "<TD>" . NUMBER($planetinfo[credits]) . "</TD>";
      echo "<TD>" . NUMBER($planetinfo[fighters]) . "</TD>";
      echo "<TD>" . NUMBER($planetinfo[torps]) . "</TD>";
      echo "</TR>";
      echo "<TR BGCOLOR=\"$color_line2\"><TD>Production Percentages</TD>";
      echo "<TD><INPUT TYPE=TEXT NAME=pore VALUE=\"$planetinfo[prod_ore]\" SIZE=6 MAXLENGTH=6></TD>";
      echo "<TD><INPUT TYPE=TEXT NAME=porganics VALUE=\"$planetinfo[prod_organics]\" SIZE=6 MAXLENGTH=6></TD>";
      echo "<TD><INPUT TYPE=TEXT NAME=pgoods VALUE=\"$planetinfo[prod_goods]\" SIZE=6 MAXLENGTH=6></TD>";
      echo "<TD><INPUT TYPE=TEXT NAME=penergy VALUE=\"$planetinfo[prod_energy]\" SIZE=6 MAXLENGTH=6></TD>";
      echo "<TD>n/a</TD><TD>*</TD>";
      echo "<TD><INPUT TYPE=TEXT NAME=pfighters VALUE=\"$planetinfo[prod_fighters]\" SIZE=6 MAXLENGTH=6></TD>";
      echo "<TD><INPUT TYPE=TEXT NAME=ptorp VALUE=\"$planetinfo[prod_torp]\" SIZE=6 MAXLENGTH=6></TD>";
      echo "</TABLE>* Production of credits beyond banking interest is 100 - other percentages<BR><BR>";
      echo "<INPUT TYPE=SUBMIT VALUE=Update>";
      echo "</FORM><BR>";
    }
    else
    {
      /* visitor menu */
      if($planetinfo[sells] == "Y")
      {
        echo "<a href=planet.$phpext?planet_id=$planet_id&command=buy>Buy</a> commodities from Planet<BR>";
      }
      else
      {
        echo "Planet is not selling commodities.<BR>";
      }
      echo "<a href=planet.$phpext?planet_id=$planet_id&command=attac>Attack</a> on Planet<BR>";
      echo "<a href=planet.$phpext?planet_id=$planet_id&command=scan>Scan</a> Planet<BR>";
    }
  }
  elseif($planetinfo[owner] == $playerinfo[ship_id] || ($planetinfo[corp] == $playerinfo[team] && $playerinfo[team] > 0))
  {
    /* player owns planet and there is a command */
    if($command == "sell")
    {
      if($planetinfo[sells] == "Y")
      {
        /* set planet to not sell */
        echo "Planet now set not to sell.<BR>";
        $result4 = mysql_query("UPDATE planets SET sells='N' WHERE planet_id=$planet_id");
      }
      else
      {
        echo "Planet now set to sell.<BR>";
        $result4b = mysql_query ("UPDATE planets SET sells='Y' WHERE planet_id=$planet_id");
      }
    }
    elseif($command == "name")
    {
      /* name menu */
      echo "<form action=\"planet.$phpext?planet_id=$planet_id&command=cname\" method=\"post\">";       
      echo "Enter new planet name:  ";
      echo "<input type=\"text\" name=\"new_name\" size=\"20\" maxlength=\"20\" value=\"$planetinfo[name]\"><BR><BR>";
      echo "<input type=\"submit\" value=\"Submit\"><input type=\"reset\" value=\"Reset\"><BR><BR>";
      echo "</form>";
    }
    elseif($command == "cname")
    {
      /* name2 menu */
      $new_name = trim(strip_tags($new_name));
      $result5 = mysql_query("UPDATE planets SET name='$new_name' WHERE planet_id=$planet_id");
      $new_name = stripslashes($new_name);
      echo "Planet name changed to $new_name.";
    }
    elseif($command == "land")
    {
      /* land menu */
      echo "You have landed on the planet's surface.<BR><BR>";
      $update = mysql_query("UPDATE ships SET on_planet='Y', planet_id=$planet_id WHERE ship_id=$playerinfo[ship_id]");
    }
    elseif($command == "leave")
    {
      /* leave menu */
      echo "You are no longer on the planet's surface.<BR><BR>";
      $update = mysql_query("UPDATE ships SET on_planet='N' WHERE ship_id=$playerinfo[ship_id]"); 
    }
    elseif($command == "transfer")
    {
      /* transfer menu */
      $free_holds = NUM_HOLDS($playerinfo[hull]) - $playerinfo[ship_ore] - $playerinfo[ship_organics] - $playerinfo[ship_goods] - $playerinfo[ship_colonists];
      $free_power = NUM_ENERGY($playerinfo[power]) - $playerinfo[ship_energy];
      echo "You have room for " . NUMBER($free_holds) . " units of additional cargo.  You have capacity for " . NUMBER($free_power) . " units of addtional power.<BR><BR>";
      echo "<FORM ACTION=planet2.$phpext?planet_id=$planet_id METHOD=POST>";
      echo "<TABLE WIDTH=\"100%\" BORDER=0 CELLSPACING=0 CELLPADDING=0>";
      echo"<TR BGCOLOR=\"$color_header\"><TD><B>Commodity</B></TD><TD><B>Planet</B></TD><TD><B>Ship</B></TD><TD><B>Transfer</B></TD><TD><B>To Planet?</B></TD><TD><B>All?</B></TD></TR>";
      echo"<TR BGCOLOR=\"$color_line1\"><TD>Ore</TD><TD>" . NUMBER($planetinfo[ore]) . "</TD><TD>" . NUMBER($playerinfo[ship_ore]) . "</TD><TD><INPUT TYPE=TEXT NAME=transfer_ore SIZE=10 MAXLENGTH=20></TD><TD><INPUT TYPE=CHECKBOX NAME=tpore VALUE=-1></TD><TD><INPUT TYPE=CHECKBOX NAME=allore VALUE=-1></TD></TR>";
      echo"<TR BGCOLOR=\"$color_line2\"><TD>Organics</TD><TD>" . NUMBER($planetinfo[organics]) . "</TD><TD>" . NUMBER($playerinfo[ship_organics]) . "</TD><TD><INPUT TYPE=TEXT NAME=transfer_organics SIZE=10 MAXLENGTH=20></TD><TD><INPUT TYPE=CHECKBOX NAME=tporganics VALUE=-1></TD><TD><INPUT TYPE=CHECKBOX NAME=allorganics VALUE=-1></TD></TR>";
      echo"<TR BGCOLOR=\"$color_line1\"><TD>Goods</TD><TD>" . NUMBER($planetinfo[goods]) . "</TD><TD>" . NUMBER($playerinfo[ship_goods]) . "</TD><TD><INPUT TYPE=TEXT NAME=transfer_goods SIZE=10 MAXLENGTH=20></TD><TD><INPUT TYPE=CHECKBOX NAME=tpgoods VALUE=-1></TD><TD><INPUT TYPE=CHECKBOX NAME=allgoods VALUE=-1></TD></TR>";
      echo"<TR BGCOLOR=\"$color_line2\"><TD>Energy</TD><TD>" . NUMBER($planetinfo[energy]) . "</TD><TD>" . NUMBER($playerinfo[ship_energy]) . "</TD><TD><INPUT TYPE=TEXT NAME=transfer_energy SIZE=10 MAXLENGTH=20></TD><TD><INPUT TYPE=CHECKBOX NAME=tpenergy VALUE=-1></TD><TD><INPUT TYPE=CHECKBOX NAME=allenergy VALUE=-1></TD></TR>";
      echo"<TR BGCOLOR=\"$color_line1\"><TD>Colonists</TD><TD>" . NUMBER($planetinfo[colonists]) . "</TD><TD>" . NUMBER($playerinfo[ship_colonists]) . "</TD><TD><INPUT TYPE=TEXT NAME=transfer_colonists SIZE=10 MAXLENGTH=20></TD><TD><INPUT TYPE=CHECKBOX NAME=tpcolonists VALUE=-1></TD><TD><INPUT TYPE=CHECKBOX NAME=allcolonists VALUE=-1></TD></TR>";
      echo"<TR BGCOLOR=\"$color_line2\"><TD>Fighters</TD><TD>" . NUMBER($planetinfo[fighters]) . "</TD><TD>" . NUMBER($playerinfo[ship_fighters]) . "</TD><TD><INPUT TYPE=TEXT NAME=transfer_fighters SIZE=10 MAXLENGTH=20></TD><TD><INPUT TYPE=CHECKBOX NAME=tpfighters VALUE=-1></TD><TD><INPUT TYPE=CHECKBOX NAME=allfighters VALUE=-1></TD></TR>";
      echo"<TR BGCOLOR=\"$color_line1\"><TD>Torpedoes</TD><TD>" . NUMBER($planetinfo[torps]) . "</TD><TD>" . NUMBER($playerinfo[torps]) . "</TD><TD><INPUT TYPE=TEXT NAME=transfer_torps SIZE=10 MAXLENGTH=20></TD><TD><INPUT TYPE=CHECKBOX NAME=tptorps VALUE=-1></TD><TD><INPUT TYPE=CHECKBOX NAME=alltorps VALUE=-1></TD></TR>";
      echo"<TR BGCOLOR=\"$color_line2\"><TD>Credits</TD><TD>" . NUMBER($planetinfo[credits]) . "</TD><TD>" . NUMBER($playerinfo[credits]) . "</TD><TD><INPUT TYPE=TEXT NAME=transfer_credits SIZE=10 MAXLENGTH=20></TD><TD><INPUT TYPE=CHECKBOX NAME=tpcredits VALUE=-1></TD><TD><INPUT TYPE=CHECKBOX NAME=allcredits VALUE=-1></TD></TR>";
      echo "</TABLE><BR>";
      echo "<INPUT TYPE=SUBMIT VALUE=Transfer>&nbsp;<INPUT TYPE=RESET VALUE=Reset>";
      echo "</FORM>";
    }
    elseif($command == "base")
    {
      /* build a base */
      if($planetinfo[ore] >= $base_ore && $planetinfo[organics] >= $base_organics &&
        $planetinfo[goods] >= $base_goods && $planetinfo[credits] >= $base_credits)
      {
        $update1 = mysql_query("UPDATE planets SET base='Y', ore=ore-$base_ore, organics=organics-$base_organics, goods=goods-$base_goods, credits=credits-$base_credits WHERE planet_id=$planet_id");
        $update1b = mysql_query("UPDATE ships SET turns=turns-1, turns_used=turns_used-1 where ship_ip=$playerinfo[ship_id]");
        echo "Base constructed.<BR><BR>";
        $ownership = calc_ownership($playerinfo[sector]);

        if(!empty($ownership))

          echo "$ownership<p>";

      }
      else
      {
        echo "To build a base there must be at least $base_credits credits, $base_ore units of ore, $base_organics units of organics, and $base_goods units of goods on the planet .<BR><BR>";
      }
    }
    elseif($command == "productions")
    {
      /* change production percentages */
      if($porganics < 0.0 || $pore < 0.0 || $pgoods < 0.0 || $penergy < 0.0 || $pfighters < 0.0 || $ptorp < 0.0)
      {
        echo "You may not change production percentages to a negative number.<BR><BR>";
      }
      elseif(($porganics + $pore + $pgoods + $penergy + $pfighters + $ptorp) > 100.0)
      {
        echo "You may not change production percentages to higher than a total of 100%.<BR><BR>";
      }
      else
      {
        mysql_query("UPDATE planets SET prod_ore=$pore,prod_organics=$porganics,prod_goods=$pgoods,prod_energy=$penergy,prod_fighters=$pfighters,prod_torp=$ptorp WHERE planet_id=$planet_id");
        echo "Production percentages changed.<BR><BR>";
      }
    }
    else
    {
      echo "Command not available.<BR>";            
    }   
  }
  else
  {
    /* player doesn't own planet and there is a command */
    if($command == "buy")
    {
      if($planetinfo[sells] == "Y")
      {
        $ore_price = ($ore_price + $ore_delta / 4);
        $organics_price = ($organics_price + $organics_delta / 4);
        $goods_price = ($goods_price + $goods_delta / 4);
        $energy_price = ($energy_price + $energy_delta / 4);
        echo "<form action=planet3.$phpext?planet_id=$planet_id method=post>";
        echo "<table>";
        echo "<tr><td>Commodity</td><td>Available</td><td>Price</td><td>Buy</td><td>Cargo</td></tr>";
        echo "<tr><td>Ore</td><td>$planetinfo[ore]</td><td>$ore_price</td><td><input type=text name=trade_ore size=10 maxlength=20 value=0></td><td>$playerinfo[ship_ore]</td></tr>";
        echo "<tr><td>Organics</td><td>$planetinfo[organics]</td><td>$organics_price</td><td><input type=text name=trade_organics size=10 maxlength=20 value=0></td><td>$playerinfo[ship_organics]</td></tr>";
        echo "<tr><td>Goods</td><td>$planetinfo[goods]</td><td>$goods_price</td><td><input type=text name=trade_goods size=10 maxlength=20 value=0></td><td>$playerinfo[ship_goods]</td></tr>";
        echo "<tr><td>Energy</td><td>$planetinfo[energy]</td><td>$energy_price</td><td><input type=text name=trade_energy size=10 maxlength=20 value=0></td><td>$playerinfo[ship_energy]</td></tr>";
        echo "</table>";
        echo "<input type=submit value=Submit><input type=reset value=Reset><BR></form>";
      }
      else
      {
        echo "Planet is not selling commodities.<BR>";
      }
    }
    elseif($command == "attac")
    {
//check to see if sure...
    if($planetinfo[sells] == "Y")
      {
        echo "<a href=planet.$phpext?planet_id=$planet_id&command=buy>Buy</a> commodities from Planet<BR>";
      }
      else
      {
        echo "Planet is not selling commodities.<BR>";
      }
      echo "<a href=planet.$phpext?planet_id=$planet_id&command=attack>Attack</a> on Planet <B> Are You SURE...</B><BR>";
      echo "<a href=planet.$phpext?planet_id=$planet_id&command=scan>Scan</a> Planet<BR>";
    }
    elseif($command == "attack")
    {
    	planetcombat();
    } 
    elseif($command == "scan")
    {
      /* scan menu */
      if($playerinfo[turns] < 1)
      {
        echo "You need at least one turn to scan a planet.<BR><BR>";
	    TEXT_GOTOMAIN();
        include("footer.$phpext");   
        die();
      }
      /* determine per cent chance of success in scanning target ship - based on player's sensors and opponent's cloak */
      $success = (10 - $ownerinfo[cloak] / 2 + $playerinfo[sensors]) * 5;
      if($success < 5)
      {
        $success = 5;
      }
      if($success > 95)
      {
        $success = 95;
      }
      $roll = rand(1, 100);
      if($roll > $success)
      {
        /* if scan fails - inform both player and target. */
        echo "Sensors cannot get a fix on target!<BR><BR>";
        TEXT_GOTOMAIN();
        playerlog($ownerinfo[ship_id], "$playerinfo[character_name] attempted to scan one of your planets in sector $playerinfo[sector], but failed.");
        include("footer.$phpext");
        die();
      }
      else
      {
        playerlog($ownerinfo[ship_id], "One of your planets in sector $playerinfo[sector] was scanned by $playerinfo[character_name].");
        /* scramble results by scan error factor. */
        $sc_error= SCAN_ERROR($playerinfo[sensors], $targetinfo[cloak]);
        echo "Scan results on $planetinfo[name], owned by:  $ownerinfo[character_name]<BR><BR>";
        echo "<table>";
        echo "<tr><td>Commodities:</td><td></td>";
        echo "<tr><td>Organics:</td>";
        $roll = rand(1, 100);
        if($roll < $success)
        {
          $sc_planet_organics=round($planetinfo[organics] * $sc_error / 100);
          echo "<td>$sc_planet_organics</td></tr>";
        }
        else
        {
          echo "<td>???</td></tr>";
        }
        echo "<tr><td>Ore:</td>";
        $roll = rand(1, 100);
        if($roll < $success)
        {
          $sc_planet_ore=round($planetinfo[ore] * $sc_error / 100);
          echo "<td>$sc_planet_ore</td></tr>";
        }
        else
        {
          echo "<td>???</td></tr>";
        }
        echo "<tr><td>Goods:</td>";
        $roll = rand(1, 100);
        if($roll < $success)
        {
          $sc_planet_goods=round($planetinfo[goods] * $sc_error / 100);
          echo "<td>$sc_planet_goods</td></tr>";
        }
        else
        {
          echo "<td>???</td></tr>";
        }
        echo "<tr><td>Energy:</td>";
        $roll = rand(1, 100);
        if($roll < $success)
        {
          $sc_planet_energy=round($planetinfo[energy] * $sc_error / 100);
          echo "<td>$sc_planet_energy</td></tr>";
        }
        else
        {
          echo "<td>???</td></tr>";
        }
        echo "<tr><td>Colonists:</td>";
        $roll = rand(1, 100);
        if($roll < $success)
        {
          $sc_planet_colonists=round($planetinfo[colonists] * $sc_error / 100);
          echo "<td>$sc_planet_colonists</td></tr>";
        }
        else
        {
          echo "<td>???</td></tr>";
        }
        echo "<tr><td>Credits:</td>";
        $roll = rand(1, 100);
        if($roll < $success)
        {
          $sc_planet_credits=round($planetinfo[credits] * $sc_error / 100);
          echo "<td>$sc_planet_credits</td></tr>";
        }
        else
        {
          echo "<td>???</td></tr>";
        }
        echo "<tr><td>Defenses:</td><td></td>";
        echo "<tr><td>Base:</td>";
        $roll = rand(1, 100);
        if($roll < $success)
        {
          echo "<td>$planetinfo[base]</td></tr>";
        }
        else
        {
          echo "<td>???</td></tr>";
        }
        echo "<tr><td>Base Torpedoes:</td>";
        $roll = rand(1, 100);
        if($roll < $success)
        {
          $sc_base_torp=round($planetinfo[torps] * $sc_error / 100);
          echo "<td>$sc_base_torp</td></tr>";
        }
        else
        {
          echo "<td>???</td></tr>";
        }
        echo "<tr><td>Fighters:</td>";
        $roll = rand(1, 100);
        if($roll < $success)
        {
          $sc_planet_fighters=round($planetinfo[fighters] * $sc_error / 100);
          echo "<td>$sc_planet_fighters</td></tr>";
        }
        else
        {
          echo "<td>???</td></tr>";
        }
        echo "<tr><td>Beams:</td>";
        $roll = rand(1, 100);
        if($roll < $success)
        {
          $sc_beams=round($ownerinfo[beams] * $sc_error / 100);
          echo "<td>$sc_beams</td></tr>";
        }
        else
        {
          echo "<td>???</td></tr>";
        }
        echo "<tr><td>Torpedo Launchers:</td>";
        $roll = rand(1, 100);
        if($roll < $success)
        {
          $sc_torp_launchers=round($ownerinfo[torp_launchers] * $sc_error / 100);
          echo "<td>$sc_torp_launchers</td></tr>";
        }
        else
        {
          echo "<td>???</td></tr>";
        }
        echo "<tr><td>Shields</td>";
        $roll=rand(1, 100);
        if($roll < $success)
        {
          $sc_shields=round($ownerinfo[shields] * $sc_error / 100);
          echo "<td>$sc_shields</td></tr>";
        }
        else
        {
          echo "<td>???</td></tr>";
        }
        echo "</table><BR>";
        $roll=rand(1, 100);
        if($ownerinfo[sector] == $playerinfo[sector] && $ownerinfo[on_planet] == 'Y' && $roll < $success)
        {
          echo "$ownerinfo[character_name] is on the planet.<BR><BR>";
        }
      }
      $update = mysql_query("UPDATE ships SET turns=turns-1, turns_used=turns_used+1 WHERE ship_id=$playerinfo[ship_id]");
    }
    elseif($command == "capture" && $planetinfo[defeated] && $planetinfo[fighters] == 0)
    {
      echo "Planet captured.<BR>";
      $update = mysql_query("UPDATE planets SET corp=null, owner=$playerinfo[ship_id], base='N', defeated='N' WHERE planet_id=$planet_id");
      if($planetinfo[owner] != 0)
      {       
        playerlog($ownerinfo[ship_id], "Your planet '$planetinfo[name]' in sector $playerinfo[sector] was captured by $playerinfo[character_name].");
        gen_score($ownerinfo[ship_id]);
      }
    }
    elseif($command == "capture")
    {
      echo "Planet not defeated!<BR>";
    }
    else
    {
      echo "Command not available.<BR>";            
    }
  }
}
else
{
  echo "That planet is no longer there.";
}
if($command != "")
{
  echo "<BR>Click <a href=planet.$phpext?planet_id=$planet_id>here</a> to return to planet menu.<BR><BR>";
}
if($allow_ibank)
{
  echo "<BR>Access the planet's <A HREF=\"ibank.$phpext?planet_id=$planet_id\">IGB Banking Terminal</A>.<BR><BR>";
}

//-------------------------------------------------------------------------------------------------
TEXT_GOTOMAIN();

include("footer.$phpext");


?> 
