<?

include("config.php3");

updatecookie();

$title="Planet Menu";
include("header.php3");

connectdb();
checklogin();

$result = mysql_query("SELECT * FROM ships WHERE email='$username'");
$playerinfo=mysql_fetch_array($result);

$result2 = mysql_query("SELECT * FROM universe WHERE sector_id=$playerinfo[sector]");
$sectorinfo=mysql_fetch_array($result2);

bigtitle();

srand((double)microtime()*1000000);

if($sectorinfo[planet] == 'Y')
/* if there is a planet in the sector show appropriate menu */
{ 
  if($sectorinfo[planet_owner] == "" && $command != "capture")
  {
    echo "This planet is unowned.<BR><BR>";
    $update = mysql_query("UPDATE universe SET planet_fighters=0, planet_defeated='Y' WHERE sector_id=$sectorinfo[sector_id]");
    echo "You may <a href=planet.php3?command=capture>capture</a> the planet or just leave it undefended.<BR><BR>";
    echo "<BR>";
    TEXT_GOTOMAIN();
    include("footer.php3");
    die();
  }
  if($sectorinfo[planet_owner] != "")
  {
    $result3 = mysql_query("SELECT * FROM ships WHERE ship_id=$sectorinfo[planet_owner]");
    $ownerinfo = mysql_fetch_array($result3);
  }
  if($sectorinfo[planet_defeated] && $sectorinfo[planet_fighters] > 0) 
  { 
    $update = mysql_query("UPDATE universe SET planet_defeated='N' WHERE sector_id=$sectorinfo[sector_id]");
    $sectorinfo[planet_defeated] = "N";
  }
  if(empty($command))
  {
    /* ...if there is no planet command already */
    if(empty($sectorinfo[planet_name]))
    {
      echo "Welcome to $ownerinfo[character_name]'s un-named planet.<BR><BR>";
    }
    else
    {
      echo "Welcome to $sectorinfo[planet_name], owned by $ownerinfo[character_name].<BR><BR>";
    }
    if($sectorinfo[planet_owner] == $playerinfo[ship_id])
    {
      /* owner menu */
      echo "<a href=planet.php3?command=name>Name</a> Planet<BR>";
      if($playerinfo[on_planet] == 'Y')
      {
        echo "You are presently on the surface of the planet.<BR>";
        echo "<a href=planet.php3?command=leave>Leave</a> Planet<BR>";
        echo "You can also <a href=logout.php3>log-out</a> in the safety of your planet.<BR>";
      }
      else
      {
        echo "You are presently in orbit of the planet.<BR>";
        echo "<a href=planet.php3?command=land>Land</a> on Planet<BR>";
      }
      echo "<a href=planet.php3?command=transfer>Transfer</a> commodities/resources/colonists to/from Planet<BR>";
      if($sectorinfo[base_sells] == "Y")
      {
        echo "Planet is presently selling commodities.  ";
      }
      else
      {
        echo "Planet is not presently selling commodities.  ";
      }
      echo "Toggle planet <a href=planet.php3?command=sell>selling</a> commodities<BR>";
      if($sectorinfo[base] == "N")
      {
        echo "With enough commodites and credits, you can <a href=planet.php3?command=base>build a base</a> to help defend the planet.<BR>";
      }
      else
      { 
        echo "You have a base on this planet.<BR>";
      }
      
      /* change production rates */
      echo "<FORM ACTION=planet.php3 METHOD=POST>";
      echo "<INPUT TYPE=HIDDEN NAME=command VALUE=productions><BR>";
      echo "<TABLE BORDER=0 CELLSPACING=0 CELLPADDING=2>";
      echo "<TR BGCOLOR=\"$color_header\"><TD></TD><TD><B>Ore</B></TD><TD><B>Organics</B></TD><TD><B>Goods</B></TD><TD><B>Energy</B></TD><TD><B>Colonists</B></TD><TD><B>Credits</B></TD><TD><B>Fighters</B></TD><TD><B>Torpedoes</TD></TR>";
      echo "<TR BGCOLOR=\"$color_line1\">";
      echo "<TD>Current Quantities</TD>";
      echo "<TD>" . NUMBER($sectorinfo[planet_ore]) . "</TD>";
      echo "<TD>" . NUMBER($sectorinfo[planet_organics]) . "</TD>";
      echo "<TD>" . NUMBER($sectorinfo[planet_goods]) . "</TD>";
      echo "<TD>" . NUMBER($sectorinfo[planet_energy]) . "</TD>";
      echo "<TD>" . NUMBER($sectorinfo[planet_colonists]) . "</TD>";
      echo "<TD>" . NUMBER($sectorinfo[planet_credits]) . "</TD>";
      echo "<TD>" . NUMBER($sectorinfo[planet_fighters]) . "</TD>";
      echo "<TD>" . NUMBER($sectorinfo[base_torp]) . "</TD>";
      echo "</TR>";
      echo "<TR BGCOLOR=\"$color_line2\"><TD>Production Percentages</TD>";
      echo "<TD><INPUT TYPE=TEXT NAME=pore VALUE=\"$sectorinfo[prod_ore]\" SIZE=6 MAXLENGTH=6></TD>";
      echo "<TD><INPUT TYPE=TEXT NAME=porganics VALUE=\"$sectorinfo[prod_organics]\" SIZE=6 MAXLENGTH=6></TD>";
      echo "<TD><INPUT TYPE=TEXT NAME=pgoods VALUE=\"$sectorinfo[prod_goods]\" SIZE=6 MAXLENGTH=6></TD>";
      echo "<TD><INPUT TYPE=TEXT NAME=penergy VALUE=\"$sectorinfo[prod_energy]\" SIZE=6 MAXLENGTH=6></TD>";
      echo "<TD>n/a</TD><TD>*</TD>";
      echo "<TD><INPUT TYPE=TEXT NAME=pfighters VALUE=\"$sectorinfo[prod_fighters]\" SIZE=6 MAXLENGTH=6></TD>";
      echo "<TD><INPUT TYPE=TEXT NAME=ptorp VALUE=\"$sectorinfo[prod_torp]\" SIZE=6 MAXLENGTH=6></TD>";
      echo "</TABLE>* Production of credits beyond banking interest is 100 - other percentages<BR><BR>";
      echo "<INPUT TYPE=SUBMIT VALUE=Update>";
      echo "</FORM><BR>";
    }
    else
    {
      /* visitor menu */
      if($sectorinfo[base_sells] == "Y")
      {
        echo "<a href=planet.php3?command=buy>Buy</a> commodities from Planet<BR>";
      }
      else
      {
        echo "Planet is not selling commodities.<BR>";
      }
      echo "<a href=planet.php3?command=attack>Attack</a> on Planet<BR>";
      echo "<a href=planet.php3?command=scan>Scan</a> Planet<BR>";
    }
  }
  elseif($sectorinfo[planet_owner] == $playerinfo[ship_id])
  {
    /* player owns planet and there is a command */
    if($command == "sell")
    {
      if($sectorinfo[base_sells] == "Y")
      {
        /* set planet to not sell */
        echo "Planet now set not to sell.<BR>";
        $result4 = mysql_query("UPDATE universe SET base_sells='N' WHERE sector_id=$sectorinfo[sector_id]");
      }
      else
      {
        echo "Planet now set to sell.<BR>";
        $result4b = mysql_query ("UPDATE universe SET base_sells='Y' WHERE sector_id=$sectorinfo[sector_id]");
      }
    }
    elseif($command == "name")
    {
      /* name menu */
      echo "<form action=\"planet.php3?command=cname\" method=\"post\">";       
      echo "Enter new planet name:  ";
      echo "<input type=\"text\" name=\"new_name\" size=\"20\" maxlength=\"20\" value=\"$sectorinfo[planet_name]\"><BR><BR>";
      echo "<input type=\"submit\" value=\"Submit\"><input type=\"reset\" value=\"Reset\"><BR><BR>";
      echo "</form>";
    }
    elseif($command == "cname")
    {
      /* name2 menu */
      $new_name = trim(strip_tags($new_name));
      $result5 = mysql_query("UPDATE universe SET planet_name='$new_name' WHERE sector_id=$sectorinfo[sector_id]");
      $new_name = stripslashes($new_name);
      echo "Planet name changed to $new_name.";
    }
    elseif($command == "land")
    {
      /* land menu */
      echo "You have landed on the planet's surface.<BR><BR>";
      $update = mysql_query("UPDATE ships SET on_planet='Y' WHERE ship_id=$playerinfo[ship_id]");
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
      echo "<FORM ACTION=planet2.php3 METHOD=POST>";
      echo "<TABLE WIDTH=\"100%\" BORDER=0 CELLSPACING=0 CELLPADDING=0>";
      echo"<TR BGCOLOR=\"$color_header\"><TD><B>Commodity</B></TD><TD><B>Planet</B></TD><TD><B>Ship</B></TD><TD><B>Transfer</B></TD><TD><B>To Planet?</B></TD></TR>";
      echo"<TR BGCOLOR=\"$color_line1\"><TD>Ore</TD><TD>" . NUMBER($sectorinfo[planet_ore]) . "</TD><TD>" . NUMBER($playerinfo[ship_ore]) . "</TD><TD><INPUT TYPE=TEXT NAME=transfer_ore SIZE=10 MAXLENGTH=20></TD><TD><INPUT TYPE=CHECKBOX NAME=tpore VALUE=-1></TD></TR>";
      echo"<TR BGCOLOR=\"$color_line2\"><TD>Organics</TD><TD>" . NUMBER($sectorinfo[planet_organics]) . "</TD><TD>" . NUMBER($playerinfo[ship_organics]) . "</TD><TD><INPUT TYPE=TEXT NAME=transfer_organics SIZE=10 MAXLENGTH=20></TD><TD><INPUT TYPE=CHECKBOX NAME=tporganics VALUE=-1></TD></TR>";
      echo"<TR BGCOLOR=\"$color_line1\"><TD>Goods</TD><TD>" . NUMBER($sectorinfo[planet_goods]) . "</TD><TD>" . NUMBER($playerinfo[ship_goods]) . "</TD><TD><INPUT TYPE=TEXT NAME=transfer_goods SIZE=10 MAXLENGTH=20></TD><TD><INPUT TYPE=CHECKBOX NAME=tpgoods VALUE=-1></TD></TR>";
      echo"<TR BGCOLOR=\"$color_line2\"><TD>Energy</TD><TD>" . NUMBER($sectorinfo[planet_energy]) . "</TD><TD>" . NUMBER($playerinfo[ship_energy]) . "</TD><TD><INPUT TYPE=TEXT NAME=transfer_energy SIZE=10 MAXLENGTH=20></TD><TD><INPUT TYPE=CHECKBOX NAME=tpenergy VALUE=-1></TD></TR>";
      echo"<TR BGCOLOR=\"$color_line1\"><TD>Colonists</TD><TD>" . NUMBER($sectorinfo[planet_colonists]) . "</TD><TD>" . NUMBER($playerinfo[ship_colonists]) . "</TD><TD><INPUT TYPE=TEXT NAME=transfer_colonists SIZE=10 MAXLENGTH=20></TD><TD><INPUT TYPE=CHECKBOX NAME=tpcolonists VALUE=-1></TD></TR>";
      echo"<TR BGCOLOR=\"$color_line2\"><TD>Fighters</TD><TD>" . NUMBER($sectorinfo[planet_fighters]) . "</TD><TD>" . NUMBER($playerinfo[ship_fighters]) . "</TD><TD><INPUT TYPE=TEXT NAME=transfer_fighters SIZE=10 MAXLENGTH=20></TD><TD><INPUT TYPE=CHECKBOX NAME=tpfighters VALUE=-1></TD></TR>";
      echo"<TR BGCOLOR=\"$color_line1\"><TD>Torpedoes</TD><TD>" . NUMBER($sectorinfo[base_torp]) . "</TD><TD>" . NUMBER($playerinfo[torps]) . "</TD><TD><INPUT TYPE=TEXT NAME=transfer_torps SIZE=10 MAXLENGTH=20></TD><TD><INPUT TYPE=CHECKBOX NAME=tptorps VALUE=-1></TD></TR>";
      echo"<TR BGCOLOR=\"$color_line2\"><TD>Credits</TD><TD>" . NUMBER($sectorinfo[planet_credits]) . "</TD><TD>" . NUMBER($playerinfo[credits]) . "</TD><TD><INPUT TYPE=TEXT NAME=transfer_credits SIZE=10 MAXLENGTH=20></TD><TD><INPUT TYPE=CHECKBOX NAME=tpcredits VALUE=-1></TD></TR>";
      echo "</TABLE><BR>";
      echo "<INPUT TYPE=SUBMIT VALUE=Transfer>&nbsp;<INPUT TYPE=RESET VALUE=Reset>";
      echo "</FORM>";
    }
    elseif($command == "base")
    {
      /* build a base */
      if($sectorinfo[planet_ore] >= $base_ore && $sectorinfo[planet_organics] >= $base_organics &&
        $sectorinfo[planet_goods] >= $base_goods && $sectorinfo[planet_credits] >= $base_credits)
      {
        $update1 = mysql_query("UPDATE universe SET base='Y', planet_ore=planet_ore-$base_ore, planet_organics=planet_organics-$base_organics, planet_goods=planet_goods-$base_goods, planet_credits=planet_credits-$base_credits WHERE sector_id=$sectorinfo[sector_id]");
        $update1b = mysql_query("UPDATE ships SET turns=turns-1, turns_used=turns_used-1 where ship_ip=$playerinfo[ship_id]");
        echo "Base constructed.<BR><BR>";
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
        mysql_query("UPDATE universe SET prod_ore=$pore,prod_organics=$porganics,prod_goods=$pgoods,prod_energy=$penergy,prod_fighters=$pfighters,prod_torp=$ptorp WHERE sector_id=$sectorinfo[sector_id]");
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
      if($sectorinfo[base_sells] == "Y")
      {
        $ore_price = ($ore_price + $ore_delta / 4);
        $organics_price = ($organics_price + $organics_delta / 4);
        $goods_price = ($goods_price + $goods_delta / 4);
        $energy_price = ($energy_price + $energy_delta / 4);
        echo "<form action=planet3.php3 method=post>";
        echo "<table>";
        echo "<tr><td>Commodity</td><td>Available</td><td>Price</td><td>Buy</td><td>Cargo</td></tr>";
        echo "<tr><td>Ore</td><td>$sectorinfo[planet_ore]</td><td>$ore_price</td><td><input type=text name=trade_ore size=10 maxlength=20 value=0></td><td>$playerinfo[ship_ore]</td></tr>";
        echo "<tr><td>Organics</td><td>$sectorinfo[planet_organics]</td><td>$organics_price</td><td><input type=text name=trade_organics size=10 maxlength=20 value=0></td><td>$playerinfo[ship_organics]</td></tr>";
        echo "<tr><td>Goods</td><td>$sectorinfo[planet_goods]</td><td>$goods_price</td><td><input type=text name=trade_goods size=10 maxlength=20 value=0></td><td>$playerinfo[ship_goods]</td></tr>";
        echo "<tr><td>Energy</td><td>$sectorinfo[planet_energy]</td><td>$energy_price</td><td><input type=text name=trade_energy size=10 maxlength=20 value=0></td><td>$playerinfo[ship_energy]</td></tr>";
        echo "</table>";
        echo "<input type=submit value=Submit><input type=reset value=Reset><BR></form>";
      }
      else
      {
        echo "Planet is not selling commodities.<BR>";
      }
    }
    elseif($command == "attack")
    {
      /* attack menu */
      if($playerinfo[turns] < 1)
      {
        echo "You need at least one turn to attack a planet.<BR><BR>";
        TEXT_GOTOMAIN();
        include("footer.php3");   
        die();
      }
      $result4 = mysql_query("SELECT * FROM ships WHERE sector=$playerinfo[sector] AND on_planet='Y'");
      $onplanet = mysql_fetch_array($result4);
      echo "-$onplanet[on_planet]-<BR><BR>";
      if($onplanet[on_planet] == 'N' or empty($onplanet[on_planet]))
      {
        echo "Attacking planet.<BR><BR>";
        if($sectorinfo[base] == "Y")
        {
          $ownerinfo[beams] = $ownerinfo[beams] + $base_modifier;
        }
        $planetbeams = NUM_BEAMS($ownerinfo[beams]);
        $playerbeams = NUM_BEAMS($playerinfo[beams]);
        echo "Player Beams - $playerbeams<BR><BR>";
        if($playerbeams<$planetbeams)
        {
          echo "Planet has more beams than you.<BR><BR>";
        }
        else
        {
          echo "Planet has less beams than you.<BR><BR>";
        }
        $playershields = NUM_SHIELDS($playerinfo[shields]);
        echo "Player Shields - $playershields<BR><BR>";
        if($sectorinfo[base] == "Y")
        {
          $ownerinfo[shields] = $ownerinfo[shields] + $base_modifier;
        }
        $planetshields = NUM_SHIELDS($ownerinfo[shields]);
        if($playershields<$planetshields)
        {
          echo "Planet has more shields than you.<BR><BR>";
        }
        else
        {
          echo "Planet has less shields than you.<BR><BR>";
        }
        $playertorpnum = round(pow($level_factor, $playerinfo[torp_launchers])) * 2;
        if($playertorpnum > $playerinfo[torps])
        {
          $playertorpnum = $playerinfo[torps];
        }
        if($sectorinfo[base] == "Y")
        {
          $ownerinfo[torp_launchers] = $ownerinfo[torp_launchers] + $base_modifier;
        }
        $planettorpnum = round(pow($level_factor, $ownerinfo[torp_launchers])) * 2;
        if($planettorpnum > $sectorinfo[base_torp])
        {
          $planettorpnum = $sectorinfo[base_torp];
        }
        $playertorpdmg = $torp_dmg_rate * $playertorpnum;
        echo "Player torp damage - $playertorpdmg<BR><BR>";
        $planettorpdmg = $torp_dmg_rate * $planettorpnum;
        if($playertorpdmg<$planettorpdmg)
        {
          echo "Planet has more torps than you.<BR><BR>";
        }
        else
        {
          echo "Planet has less torps than you.<BR><BR>";
        }
        $playerarmour = $playerinfo[armour_pts];
        echo "Player Armour - $playerarmour<BR><BR>";       
        $playerfighters = $playerinfo[ship_fighters];
        echo "Player Fighters - $playerfighters<BR><BR>";       
        $planetfighters = $sectorinfo[planet_fighters];
        if($playerfighters<$planetfighters)
        {
          echo "Planet has more fighters than you.<BR><BR>";
        }
        else
        {
          echo "Planet has less fighters than you.<BR><BR>";
        }
        $planetdestroyed = 0;
        $playerdestroyed = 0;
        echo "Attacking $ownerinfo[character_name]'s planet in sector $playerinfo[sector]:<BR><BR>";
        echo "Beams hit:<BR>";
        if($planetfighters > 0 && $playerbeams > 0)
        {
          if($playerbeams > $planetfighters)
          {
            echo "$ownerinfo[character_name]'s planet lost $planetfighters fighters<BR>";
            $planetfighters = 0;
            $playerbeams = $playerbeams - $planetfighters;
          }
          else
          {
            $planetfighters = $planetfighters - $playerbeams;
            echo "$ownerinfo[character_name]'s planet lost $playerbeams fighters<BR>";
            $playerbeams = 0;
          }
        }
        if($playerfighters > 0 && $planetbeams > 0)
        {
          if($planetbeams > round($playerfighters / 2))
          {
            $temp = round($playerfighters / 2);
            $lost = $playerfighters - $temp;
            echo "You lost fighters<BR>";
            $playerfighters = $temp;
            $planetbeams = $planetbeams - $lost;
          }
          else
          {
            $playerfighters = $playerfighters - $planetbeams;
            echo "You lost fighters<BR>";
            $planetbeams = 0;
          }
        }
        if($playerbeams > 0)
        {
          if($playerbeams > $planetshields)
          {
            $playerbeams = $playerbeams - $planetshields;
            $planetshields = 0;
            echo "$ownerinfo[character_name]'s planetary shields are down!<BR>";
          }
          else
          {
            echo "$ownerinfo[character_name]'s planetary shields are hit for $playerbeams damage.<BR>";
            $planetshields = $planetshields - $playerbeams;
            $playerbeams = 0;
          }
        }
        if($planetbeams > 0)
        {
          if($planetbeams > $playershields)
          {
            $planetbeams = $planetbeams - $playershields;
            $playershields = 0;
            echo "Your shields are down!<BR>";
          }
          else
          {
            echo "Your shields are hit.<BR>";
            $playershields = $playershields - $planetbeams;
            $planetbeams = 0;
          }
        }
        if($planetbeams > 0)
        {
          if($planetbeams > $playerarmour)
          {
            $playerarmour = 0;
            echo "Your armour has been breached!<BR>";
          }
          else
          {
            $playerarmour = $playerarmour - $planetbeams;
            echo "Your armour is hit.<BR>";
          } 
        } 
        echo "<BR>Torpedoes hit:<BR>";
        if($planetfighters > 0 && $playertorpdmg > 0)
        {
          if($playertorpdmg > $planetfighters)
          {
            echo "$ownerinfo[character_name]'s planet lost $planetfighters fighters<BR>";
            $planetfighters = 0;
            $playertorpdmg = $playertorpdmg - $planetfighters;
          }
          else
          {
            $planetfighters = $planetfighters - $playertorpdmg;
            echo "$ownerinfo[character_name]'s planet lost $playertorpdmg fighters<BR>";
            $playertorpdmg = 0;
          }
        }
        if($playerfighters > 0 && $planettorpdmg > 0)
        {
          if($planettorpdmg > round($playerfighters / 2))
          {
            $temp = round($playerfighters / 2);
            $lost = $playerfighters - $temp;
            echo "You lost fighters<BR>";
            $playerfighters = $temp;
            $planettorpdmg = $planettorpdmg - $lost;
          }
          else
          {
            $playerfighters = $playerfighters - $planettorpdmg;
            echo "You lost fighters<BR>";
            $planettorpdmg = 0;
          }
        }
        if($planettorpdmg > 0)
        {
          if($planettorpdmg > $playerarmour)
          {
            $playerarmour = 0;
            echo "Your armour has been breached!<BR>";
          }
          else
          {
            $playerarmour = $playerarmour - $planettorpdmg;
            echo "Your armour is hit.<BR>";
          } 
        }
        if($playertorpdmg > 0 && $planetfighters > 0)
        {
          if($playertorpdmg > $planetfighters)
          {
            $playertorpdmg = $planetfighters;
          }
          $planetfighters = $planetfighters - $playertorpdmg;
          echo "Planet loses $playertorpdmg fighters.<BR><BR>";
        }
        echo "<BR>Fighters Attack:<BR>";
        if($playerfighters > 0 && $planetfighters > 0)
        {
          if($playerfighters > $planetfighters)
          {
            echo "$ownerinfo[character_name]'s planet lost all fighters.<BR>";
            $tempplanetfighters = 0;
          }
          else
          {
            echo "$ownerinfo[character_name]'s planet lost $playerfighters fighters.<BR>";
            $tempplanetfighters = $planetfighters - $playerfighters;
          }
          if($planetfighters > $playerfighters)
          {
            echo "You lost all fighters.<BR>";
            $tempplayfighters = 0;
          }
          else
          {
            echo "You lost fighters.<BR>";
            $tempplayfighters = $playerfighters - $planetfighters;
          }     
          $playerfighters = $tempplayfighters;
          $planetfighters = $tempplanetfighters;
        }
        if($playerfighters > 0 && $planetshields > 0)
        {
          if($playerfighters > $planetshields)
          {
            echo "$ownerinfo[character_name]'s planet's shields are down.<BR>";
            $planetshields = 0;
          }
          else
          {
            echo "$ownerinfo[character_name]'s planet lost $playerfighters shield points.<BR>";
            $planetshields = $planetshields - $playerfighters;
          }
        }
        if($planetfighters > 0)
        {
          if($planetfighters > $playerarmour)
          {
            $playerarmour = 0;
            echo "Your armour is breached!<BR>";
          }
          else
          {
            $playerarmour = $playerarmour - $planetfighters;
            echo "Your armour is hit.<BR>";
          }
        }
        if($playerarmour < 1)
        {
          $free_ore = round($playerinfo[ship_ore]/2);
          $free_organics = round($playerinfo[ship_organics]/2);
          $free_goods = round($playerinfo[ship_goods]/2);
          $ship_value=$upgrade_cost*(round(pow($upgrade_factor, $playerinfo[hull]))+round(pow($upgrade_factor, $playerinfo[engines]))+round(pow($upgrade_factor, $playerinfo[power]))+round(pow($upgrade_factor, $playerinfo[computer]))+round(pow($upgrade_factor, $playerinfo[sensors]))+round(pow($upgrade_factor, $playerinfo[beams]))+round(pow($upgrade_factor, $playerinfo[torp_launchers]))+round(pow($upgrade_factor, $playerinfo[shields]))+round(pow($upgrade_factor, $playerinfo[armour]))+round(pow($upgrade_factor, $playerinfo[cloak])));
          $ship_salvage_rate=rand(0,10);
          $ship_salvage=$ship_value*$ship_salvage_rate/100;
          echo "Your ship has been destroyed!<BR><BR>";
          if($playerinfo[dev_escapepod] == "Y")
          {
            echo "Luckily you have an escape pod!<BR><BR>";
            mysql_query("UPDATE ships SET hull=0,engines=0,power=0,sensors=0,computer=0,beams=0,torp_launchers=0,torps=0,armour=0,armour_pts=100,cloak=0,shields=0,sector=0,ship_organics=0,ship_ore=0,ship_goods=0,ship_energy=$start_energy,ship_colonists=0,ship_fighters=100,dev_warpedit=0,dev_genesis=1,dev_beacon=0,dev_emerwarp=0,dev_escapepod='N',dev_fuelscoop='N',dev_minedeflector=0,on_planet='N' WHERE ship_id=$playerinfo[ship_id]"); 
          }
          else
          {
            db_kill_player($playerinfo['ship_id']);
          }
        }
        else
        { 
          $free_ore=0;
          $free_goods=0;
          $free_organics=0;
          $ship_salvage_rate=0;
          $ship_salvage=0;
          $planetrating = $ownerinfo[hull] + $ownerinfo[engines] + $ownerinfo[computer] + $ownerinfo[beams] + $ownerinfo[torp_launchers] + $ownerinfo[shields] + $ownerinfo[armour];
          $rating_change=($ownerinfo[rating]/abs($ownerinfo[rating]))*$planetrating*10;
          $fighters_lost = $playerinfo[ship_fighters] - $playerfighters;
          $armour_lost = $playerinfo[armour_pts] - $playerarmour;
          mysql_query("UPDATE ships SET ship_fighters=ship_fighters-$fighters_lost, torps=torps-$playertorpnum,armour_pts=armour_pts-$armour_lost, rating=rating-$rating_change WHERE ship_id=$playerinfo[ship_id]");
        } 
        if($planetshields < 1 && $planetfighters < 1 && $playerarmour > 0)
        {
          echo "<BR>Planet defeated.<BR><BR>";
          echo "You may <a href=planet.php3?command=capture>capture</a> the planet or just leave it undefended.<BR><BR>";
          playerlog($ownerinfo[ship_id], "Your planet in sector $playerinfo[sector] was defeated in battle by $playerinfo[character_name].");
          gen_score($ownerinfo[ship_id]);
          $update7a = mysql_query("UPDATE universe SET planet_fighters=0, base_torp=base_torp-$planettorpnum, planet_defeated='Y' WHERE sector_id=$sectorinfo[sector_id]");
        }
        else
        {
          echo "<BR>Planet not defeated.<BR><BR>";
          $fighters_lost = $sectorinfo[planet_fighters] - $planetfighters;
          playerlog($ownerinfo[ship_id], "Your planet in sector $playerinfo[sector] was attacked by $playerinfo[character_name], but was not defeated.  You salvaged $free_ore units of ore, $free_organics units of organics, $free_goods units of goods, and salvaged $ship_salvage_rate% of the ship for $ship_salvage credits.");
          gen_score($ownerinfo[ship_id]);
          $update7b = mysql_query("UPDATE universe SET planet_fighters=planet_fighters-$fighters_lost, base_torp=base_torp-$planettorpnum, planet_ore=planet_ore+$free_ore, planet_goods=planet_goods+$free_goods, planet_organics=planet_organics+$free_organics, planet_credits=planet_credits+$ship_salvage WHERE sector_id=$sectorinfo[sector_id]");
        }
        $update = mysql_query("UPDATE ships SET turns=turns-1, turns_used=turns_used+1 WHERE ship_id=$playerinfo[ship_id]");
      }
      else
      {
        $ownerbeams = NUM_BEAMS($ownerinfo[beams]);
        if($sectorinfo[base] == "Y")
        {
          $ownerinfo[beams] = $ownerinfo[beams] + $base_modifier;
        }
        $planetbeams = NUM_BEAMS($ownerinfo[beams]);
        $playerbeams = NUM_BEAMS($playerinfo[beams]);
        echo "Player Beams - $playerbeams<BR><BR>";
        if($playerbeams<$planetbeams)
        {
          echo "Planet has more beams than you.<BR><BR>";
        }
        else
        {
          echo "Planet has less beams than you.<BR><BR>";
        }
        $playershields = NUM_SHIELDS($playerinfo[shields]);
        echo "Player Shields - $playershields<BR><BR>";
        $ownershields = NUM_SHIELDS($ownerinfo[shields]);
        if($sectorinfo[base] == "Y")
        {
          $ownerinfo[shields] = $ownerinfo[shields] + $base_modifier;
        }
        $planetshields = NUM_SHIELDS($ownerinfo[shields]);
        if($playershields<$planetshields)
        {
          echo "Planet has more shields than you.<BR><BR>"; 
        }
        else
        {
          echo "Planet has less shields than you.<BR><BR>";
        }
        $playertorpnum = round(pow($level_factor, $playerinfo[torp_launchers])) * 2;
        if($playertorpnum > $playerinfo[torps])
        {
          $playertorpnum = $playerinfo[torps];
        }
        $ownertorpnum = round(pow($level_factor, $ownerinfo[torp_launchers])) * 2;
        if($ownertorpnum > $ownerinfo[torps])
        {
          $ownertorpnum = $ownerinfo[torps];
        }
        $ownertorpdmg = $torp_dmg_rate * $ownertorpnum;
        if($sectorinfo[base] == "Y")
        {
          $ownerinfo[torp_launchers] = $ownerinfo[torp_launchers] + $base_modifier;
        }
        $planettorpnum = round(pow($level_factor,$ownerinfo[torp_launchers])) * 2;
        if($planettorpnum > $sectorinfo[base_torp])
        {
          $planettorpnum = $sectorinfo[base_torp];
        }
        $playertorpdmg = $torp_dmg_rate * $playertorpnum;
        $planettorpdmg = $torp_dmg_rate * $planettorpnum;
        if($playertorpdmg<$planettorpdmg)
        {
          echo "Planet has more torps than you.<BR><BR>";
        }
        else
        {
          echo "Planet has less torps than you.<BR><BR>";
        }
        $playerarmour = $playerinfo[armour_pts];
        $ownerarmour = $ownerinfo[armour_pts];  
        $playerfighters = $playerinfo[ship_fighters];
        $ownerfighters = $ownerinfo[ship_fighters];
        $planetfighters = $sectorinfo[planet_fighters];
        if($playerfighters<$planetfighters)
        {
          echo "Planet has more fighters than you.<BR><BR>";
        }
        else
        {
          echo "Planet has less fighters than you.<BR><BR>";
        }
        $planetdestroyed = 0;
        $playerdestroyed = 0;
        $ownerdestroyed = 0;
        echo "Attacking $ownerinfo[character_name] on his planet in sector $playerinfo[sector]:<BR><BR>";
        echo "Beams hit:<BR>";
        if($planetfighters > 0 && $playerbeams > 0)
        {
          if($playerbeams > round($planetfighters / 2))
          {
            $temp = round($planetfighters / 2);
            $lost = $planetfighters - $temp;
            echo "$ownerinfo[character_name]'s planet lost $lost fighters<BR>";
            $planetfighters = $temp;
            $playerbeams = $playerbeams - $lost;
          }
          else
          {
            $planetfighters = $planetfighters - $playerbeams;
            echo "$ownerinfo[character_name]'s planet lost $playerbeams fighters<BR>";
            $playerbeams = 0;
          }
        }
        if($playerfighters > 0 && $planetbeams > 0)
        {
          if($planetbeams > round($playerfighters / 2))
          {
            $temp = round($playerfighters / 2);
            $lost = $playerfighters - $temp;
            echo "You lost fighters<BR>";
            $playerfighters = $temp;
            $planetbeams = $planetbeams - $lost;
          }
          else
          {
            $playerfighters = $playerfighters - $planetbeams;
            echo "You lost fighters<BR>";
            $planetbeams = 0;
          }
        }
        if($playerfighters > 0 && $ownerbeams > 0)
        {
          if($ownerbeams > round($playerfighters / 2))
          {
            $temp = round($playerfighters / 2);
            $lost = $playerfighters - $temp;
            echo "You lost fighters<BR>";
            $playerfighters = $temp;
            $ownerbeams = $ownerbeams - $lost;
          }
          else
          {
            $playerfighters = $playerfighters - $ownerbeams;
            echo "You lost fighters<BR>";
            $ownerbeams = 0;
          }
        }
        if($playerbeams > 0)
        {
          if($playerbeams > $planetshields)
          {
            $playerbeams = $playerbeams - $planetshields;
            $planetshields = 0;
            echo "$ownerinfo[character_name]'s planetary shields are down!<BR>";
          }
          else
          {
            echo "$ownerinfo[character_name]'s planetary shields are hit for $playerbeams damage.<BR>";
            $planetshields = $planetshields - $playerbeams;
            $playerbeams = 0;
          }
        }
        if($planetbeams > 0)
        {
          if($planetbeams > $playershields)
          {
            $planetbeams = $planetbeams - $playershields;
            $playershields = 0;
            echo "Your shields are down!<BR>";
          }
          else
          {
            echo "Your shields are hit.<BR>";
            $playershields = $playershields - $planetbeams;
            $planetbeams = 0;
          }
        }
        if($ownerbeams > 0)
        {
          if($ownerbeams > $playershields)
          {
            $ownerbeams = $ownerbeams - $playershields;
            $playershields = 0;
            echo "Your shields are down!<BR>";
          }
          else
          {
            echo "Your shields are hit.<BR>";
            $playershields = $playershields - $ownerbeams;
            $ownerbeams = 0;
          }
        }
        if($playerbeams > 0)
        {
          if($playerbeams > $ownerarmour)
          {
            $ownerarmour = 0;
            echo "$ownerinfo[character_name]'s armour has been breached!<BR>";
          }
          else
          {
            $ownerarmour = $ownerarmour - $playerbeams;
            echo "$ownerinfo[character_name]'s armour is hit for $playerbeams damage.<BR>";
          }
        }
        if($planetbeams > 0)
        {
          if($planetbeams > $playerarmour)
          {
            $playerarmour = 0;
            echo "Your armour has been breached!<BR>";
          }
          else
          {
            $playerarmour = $playerarmour - $planetbeams;
            echo "Your armour is hit.<BR>";
          } 
        }
        if($ownerbeams > 0)
        {
          if($ownerbeams > $playerarmour)
          {
            $playerarmour = 0;
            echo "Your armour has been breached!<BR>";
          }
          else
          {
            $playerarmour = $playerarmour - $ownerbeams;
            echo "Your armour is hit.<BR>";
          }
        } 
        echo "<BR>Torpedoes hit:<BR>";
        if($planetfighters > 0 && $playertorpdmg > 0)
        {
          if($playertorpdmg > $planetfighters)
          {
            echo "$ownerinfo[character_name]'s planet lost $planetfighters fighters<BR>";
            $planetfighters = 0;
            $playertorpdmg = $playertorpdmg - $planetfighters;
          }
          else
          {
            $planetfighters = $planetfighters - $playertorpdmg;
            echo "$ownerinfo[character_name]'s planet lost $playertorpdmg fighters<BR>";
            $playertorpdmg = 0;
          }
        }
        if($ownerfighters > 0 && $playertorpdmg > 0)
        {
          if($playertorpdmg > round($ownerfighters / 2))
          {
            $temp = round($ownerfighters / 2);
            $lost = $ownerfighters - $temp;
            echo "$ownerinfo[character_name] lost $lost fighters<BR>";
            $ownerfighters = $temp;
            $playertorpdmg = $playertorpdmg - $lost;
          }
          else
          {
            $ownerfighters = $ownerfighters - $playertorpdmg;
            echo "$ownerinfo[character_name] lost $playertorpdmg fighters<BR>";
            $playertorpdmg = 0;
          }
        }
        if($playerfighters > 0 && $planettorpdmg > 0)
        {
          if($planettorpdmg > round($playerfighters / 2))
          {
            $temp = round($playerfighters / 2);
            $lost = $playerfighters - $temp;
            echo "You lost fighters<BR>";
            echo "$temp - $playerfighters - $targettorpdmg";
            $playerfighters = $temp;
            $planettorpdmg = $planettorpdmg - $lost;
          }
          else
          {
            $playerfighters = $playerfighters - $planettorpdmg;
            echo "You lost fighters<BR>";
            $planettorpdmg = 0;
          }
        }
        if($playerfighters > 0 && $ownertorpdmg > 0)
        {
          if($ownertorpdmg > round($playerfighters / 2))
          {
            $temp = round($playerfighters / 2);
            $lost = $playerfighters - $temp;
            echo "You lost fighters<BR>";
            echo "$temp - $playerfighters - $ownertorpdmg";
            $playerfighters = $temp;
            $ownertorpdmg = $ownertorpdmg - $lost;
          }
          else
          {
            $playerfighters = $playerfighters - $ownertorpdmg;
            echo "You lost fighters<BR>";
            $ownertorpdmg = 0;
          }
        }
        if($playertorpdmg > 0)
        {
          if($playertorpdmg > $ownerarmour)
          {
            $ownerarmour = 0;
            echo "$ownerinfo[character_name]'s armour has been breached!<BR>";
          }
          else
          {
            $ownerarmour = $ownerarmour - $playertorpdmg;
            echo "$ownerinfo[character_name]'s  armour is hit for $playertorpdmg damage.<BR>";
          }
        }
        if($planettorpdmg > 0)
        {
          if($planettorpdmg > $playerarmour)
          {
            $playerarmour = 0;
            echo "Your armour has been breached!<BR>";
          }
          else
          {
            $playerarmour = $playerarmour - $planettorpdmg;
            echo "Your armour is hit.<BR>";
          }
        }
        if($ownertorpdmg > 0)
        {
          if($ownertorpdmg > $playerarmour)
          {
            $playerarmour = 0;
            echo "Your armour has been breached!<BR>";
          }
          else
          {
            $playerarmour = $playerarmour-$ownertorpdmg;
            echo "Your armour is hit.<BR>";
          }
        }
        echo "<BR>Fighters Attack:<BR>";
        if($playerfighters > 0 && $planetfighters > 0)
        {
          if($playerfighters > $planetfighters)
          {
            echo "$ownerinfo[character_name]'s planet lost all fighters.<BR>";
            $tempplanetfighters = 0;
          }
          else
          {
            echo "$ownerinfo[character_name]'s planet lost $playerfighters fighters.<BR>";
            $tempplanetfighters = $planetfighters - $playerfighters;
          }
          if($planetfighters > $playerfighters)
          {
            echo "You lost all fighters.<BR>";
            $tempplayfighters = 0;
          }
          else
          {
            echo "You lost fighters.<BR>";
            $tempplayfighters = $playerfighters - $planetfighters;
          }
          if($ownerfighters > $playerfighters)
          {
            echo "You lost all fighters.<BR>";
            $tempplayfighters = 0;
          }
          else
          {
            echo "You lost fighters.<BR>";
            $tempplayfighters = $tempplayfighters - $ownerfighters;
          }
          $playerfighters = $tempplayfighters;
          $planetfighters = $tempplanetfighters;
        }
        if($playerfighters > 0 && $planetshields > 0)  
        {
          if($playerfighters > $planetshields)
          {
            echo "$ownerinfo[character_name]'s planet's shields are down.<BR>";
            $planetshields = 0;
          }
          else
          {
            echo "$ownerinfo[character_name]'s planet lost $playerfighters shield points.<BR>";
            $planetshields=$planetshields-$playerfighters;
          }
        }
        if($playerfighters > 0)
        {
          if($playerfighters > $ownerarmour)
          {
            $ownerarmour = 0;
            echo "$ownerinfo[character_name]'s armour is breached!<BR>";
          }
          else
          {
            $ownerarmour = $ownerarmour - $playerfighters;
            echo "$ownerinfo[character_name]'s armour is hit for $playerfighters damage.<BR>";
          }
        }
        if($planetfighters > 0)
        {
          if($planetfighters > $playerarmour)
          {
            $playerarmour = 0;
            echo "Your armour is breached!<BR>";
          }
          else
          {
            $playerarmour = $playerarmour - $planetfighters;
            echo "Your armour is hit.<BR>";
          }
        }
        if($ownerfighters > 0)
        {
          if($ownerfighters > $playerarmour)
          {
            $playerarmour = 0;
            echo "Your armour is breached!<BR>";
          }
          else
          {
            $playerarmour = $playerarmour - $ownerfighters;
            echo "Your armour is hit.<BR>";
          }
        }
        if($playerarmour < 1)
        {
          $free_ore = round($playerinfo[ship_ore]/2);
          $free_organics = round($playerinfo[ship_organics]/2);
          $free_goods = round($playerinfo[ship_goods]/2);
          $ship_value=$upgrade_cost*(round(pow($upgrade_factor, $playerinfo[hull]))+round(pow($upgrade_factor, $playerinfo[engines]))+round(pow($upgrade_factor,$playerinfo[power]))+round(pow($upgrade_factor, $playerinfo[computer]))+round(pow($upgrade_factor, $playerinfo[sensors]))+round(pow($upgrade_factor, $playerinfo[beams]))+round(pow($upgrade_factor, $playerinfo[torp_launchers]))+round(pow($upgrade_factor, $playerinfo[shields]))+round(pow($upgrade_factor, $playerinfo[armour]))+round(pow($upgrade_factor, $playerinfo[cloak])));
          $ship_salvage_rate=rand(0,10);
          $ship_salvage=$ship_value*$ship_salvage_rate/100;
          echo "Your ship has been destroyed!<BR><BR>";
          if($playerinfo[dev_escapepod] == "Y")
          {
            echo "Luckily you have an escape pod!<BR><BR>";
            mysql_query("UPDATE ships SET hull=0,engines=0,power=0,sensors=0,computer=0,beams=0,torp_launchers=0,torps=0,armour=0,armour_pts=100,cloak=0,shields=0,sector=0,ship_organics=0,ship_ore=0,ship_goods=0,ship_energy=$start_energy,ship_colonists=0,ship_fighters=100,dev_warpedit=0,dev_genesis=1,dev_beacon=0,dev_emerwarp=0,dev_escapepod='N',dev_fuelscoop='N',dev_minedeflector=0,on_planet='N' WHERE ship_id=$playerinfo[ship_id]"); 
          }
          else
          {
            db_kill_player($playerinfo['ship_id']);
          }
        }
        else
        {
          $free_ore=0;
          $free_goods=0;
          $free_organics=0;
          $ship_salvage_rate=0;
          $ship_salvage=0;
          $planetrating = $ownerinfo[hull] + $ownerinfo[engines] + $ownerinfo[computer] + $ownerinfo[beams] + $ownerinfo[torp_launchers] + $ownerinfo[shields] + $ownerinfo[armour];
          $rating_change=($ownerinfo[rating]/abs($ownerinfo[rating]))*$planetrating*10;
          $fighters_lost = $playerinfo[ship_fighters] - $playerfighters;
          $armour_lost = $playerinfo[armour_pts] - $playerarmour;
          $update6b = mysql_query("UPDATE ships SET ship_fighters=ship_fighters-$fighters_lost, torps=torps-$playertorpnum, armour_pts=armour_pts-$playerarmour, rating=rating-$rating_change WHERE ship_id=$playerinfo[ship_id]");
        } 
        if($ownerarmour < 1)
        {
          echo "$ownerinfo[character_name]'s ship has been destroyed!<BR><BR>";
          if($ownerinfo[dev_escapepod] == "Y")
          {
            echo "An escape pod was launched!<BR><BR>";
            mysql_query("UPDATE ships SET hull=0,engines=0,power=0,sensors=0,computer=0,beams=0,torp_launchers=0,torps=0,armour=0,armour_pts=100,cloak=0,shields=0,sector=0,ship_organics=0,ship_ore=0,ship_goods=0,ship_energy=1000,ship_colonists=0,ship_fighters=100,dev_warpedit=0,dev_genesis=1,dev_beacon=0,dev_emerwarp=0,dev_escapepod='N',dev_fuelscoop='N',dev_minedeflector=0 WHERE ship_id=$ownerinfo[ship_id]"); 
            playerlog($ownerinfo[ship_id], "$playerinfo[character_name] attacked the planet you were on and destroyed your ship!  Luckily you had an escape pod!<BR><BR>");
          }
          else
          {
            db_kill_player($ownerinfo['ship_id']);
            playerlog($ownerinfo[ship_id], "$playerinfo[character_name] attacked the planet you were on and destroyed your ship!<BR><BR>");
          }
        }
        else
        {
          $planetrating = $ownerinfo[hull] + $ownerinfo[engines] + $ownerinfo[computer] + $ownerinfo[beams] + $ownerinfo[torp_launchers] + $ownerinfo[shields] + $ownerinfo[armour];
          $rating_change=($ownerinfo[rating]/abs($ownerinfo[rating]))*$planetrating*10;
          $fighters_lost = $ownerinfo[ship_fighters] - $ownerfighters;
          $armour_lost = $ownerinfo[armour_pts] - $ownerarmour;
          $update6b = mysql_query("UPDATE ships SET ship_fighters=ship_fighters-$fighters_lost, torps=torps-$playertorpnum, armour_pts=armour_pts-$armour_lost, rating=rating-$rating_change WHERE ship_id=$playerinfo[ship_id]");
        } 
        if($planetshields < 1 && $planetfighters < 1 && $playerarmour > 0 && $ownerarmour < 1)
        {
          echo "<BR>Planet defeated.<BR><BR>";
          echo "You may <a href=planet.php3?command=capture>capture</a> the planet or just leave it undefended.<BR><BR>";
          playerlog($ownerinfo[ship_id], "Your planet in sector $playerinfo[sector] was defeated in battle by $playerinfo[character_name].");
          gen_score($ownerinfo[ship_id]);
          $update7a = mysql_query("UPDATE universe SET planet_fighters=0, base_torp=base_torp-$planettorpnum, planet_defeated='Y' WHERE sector_id=$sectorinfo[sector_id]");
        }
        else
        {
          echo "<BR>Planet not defeated.<BR><BR>";
          $fighters_lost = $sectorinfo[planet_fighters] - $planetfighters;
           playerlog($ownerinfo[ship_id], "Your planet in sector $playerinfo[sector] was attacked by $playerinfo[character_name], but was not defeated.  You salvaged $free_ore units of ore, $free_organics units of organics, $free_goods unitsof goods, and salvaged $ship_salvage_rate% of the ship for $ship_salvage credits.");
          gen_score($ownerinfo[ship_id]);
          $update7b = mysql_query("UPDATE universe SET planet_fighters=planet_fighters-$fighters_lost, base_torp=base_torp-$planettorpnum, planet_ore=planet_ore+$free_ore, planet_goods=planet_goods+$free_goods, planet_organics=planet_organics+$free_organics, planet_credits=planet_credits+$ship_salvage WHERE sector_id=$sectorinfo[sector_id]");
        }
        $update = mysql_query("UPDATE ships SET turns=turns-1, turns_used=turns_used+1 WHERE ship_id=$playerinfo[ship_id]");
      }
    }
    elseif($command == "scan")
    {
      /* scan menu */
      if($playerinfo[turns] < 1)
      {
        echo "You need at least one turn to scan a planet.<BR><BR>";
	    TEXT_GOTOMAIN();
        include("footer.php3");   
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
        playerlog($ownerinfo[ship_id], "$playerinfo[character_name] attempted to scan your planet in sector $playerinfo[sector], but failed.");
        include("footer.php3");
        die();
      }
      else
      {
        playerlog($ownerinfo[ship_id], "Your planet in sector $playerinfo[sector] was scanned by $playerinfo[character_name].");
        /* scramble results by scan error factor. */
        $sc_error= SCAN_ERROR($playerinfo[sensors], $targetinfo[cloak]);
        echo "Scan results on $sectorinfo[planet_name], owned by:  $ownerinfo[character_name]<BR><BR>";
        echo "<table>";
        echo "<tr><td>Commodities:</td><td></td>";
        echo "<tr><td>Organics:</td>";
        $roll = rand(1, 100);
        if($roll < $success)
        {
          $sc_planet_organics=round($sectorinfo[planet_organics] * $sc_error / 100);
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
          $sc_planet_ore=round($sectorinfo[planet_ore] * $sc_error / 100);
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
          $sc_planet_goods=round($sectorinfo[planet_goods] * $sc_error / 100);
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
          $sc_planet_energy=round($sectorinfo[planet_energy] * $sc_error / 100);
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
          $sc_planet_colonists=round($sectorinfo[planet_colonists] * $sc_error / 100);
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
          $sc_planet_credits=round($sectorinfo[planet_credits] * $sc_error / 100);
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
          echo "<td>$sectorinfo[base]</td></tr>";
        }
        else
        {
          echo "<td>???</td></tr>";
        }
        echo "<tr><td>Base Torpedoes:</td>";
        $roll = rand(1, 100);
        if($roll < $success)
        {
          $sc_base_torp=round($sectorinfo[base_torp] * $sc_error / 100);
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
          $sc_planet_fighters=round($sectorinfo[planet_fighters] * $sc_error / 100);
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
    elseif($command == "capture" && $sectorinfo[planet_defeated] && $sectorinfo[planet_fighters] == 0)
    {
      echo "Planet captured.<BR>";
      $update = mysql_query("UPDATE universe SET planet_owner=$playerinfo[ship_id], base='N', planet_defeated='N' WHERE sector_id=$sectorinfo[sector_id]");
      if($sectorinfo[planet_owner] != "")
      {       
        playerlog($ownerinfo[ship_id], "Your planet in sector $playerinfo[sector] was captured by $playerinfo[character_name].");
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
  echo "There is no planet in this sector.  ";
}
if($command != "")
{
  echo "<BR>Click <a href=planet.php3>here</a> to return to planet menu.<BR><BR>";
}
if($allow_ibank)
{
  echo "<BR>Access the planet's <A HREF=\"ibank.php3\">IGB Banking Terminal</A>.<BR><BR>";
}

TEXT_GOTOMAIN();

include("footer.php3");

?> 
