<?

include("config.php3");
updatecookie();

$title="Administration";
include("header.php3");

connectdb();
bigtitle();

function CHECKED($yesno)
{
  return(($yesno == "Y") ? "CHECKED" : "");
}

function YESNO($onoff)
{
  return(($onoff == "ON") ? "Y" : "N");
}

$module = $menu;

if($swordfish != $adminpass)
{
  echo "<FORM ACTION=admin.php3 METHOD=POST>";
  echo "Password: <INPUT TYPE=PASSWORD NAME=swordfish SIZE=20 MAXLENGTH=20><BR><BR>";
  echo "<INPUT TYPE=SUBMIT VALUE=Submit><INPUT TYPE=RESET VALUE=Reset>";
  echo "</FORM>";
}
else
{
  if(empty($module))
  {
    echo "Welcome to the BlackNova Traders administration module<BR><BR>";
    echo "Select a function from the list below:<BR>";
    echo "<FORM ACTION=admin.php3 METHOD=POST>";
    echo "<SELECT NAME=menu>";
    echo "<OPTION VALUE=useredit SELECTED>User editor</OPTION>";
    echo "<OPTION VALUE=univedit>Universe editor</OPTION>";
    echo "<OPTION VALUE=sectedit>Sector editor</OPTION>";
    echo "<OPTION VALUE=linkedit>Link editor</OPTION>";
    echo "<OPTION VALUE=zoneedit>Zone editor</OPTION>";
    echo "</SELECT>";
    echo "<INPUT TYPE=HIDDEN NAME=swordfish VALUE=$swordfish>";
    echo "&nbsp;<INPUT TYPE=SUBMIT VALUE=Submit>";
    echo "</FORM>";
  }
  else
  {
    $button_main = true;

    if($module == "useredit")
    {
      echo "<B>User editor</B>";
      echo "<BR>";
      echo "<FORM ACTION=admin.php3 METHOD=POST>";
      if(empty($user))
      {
        echo "<SELECT SIZE=20 NAME=user>";
        $res = mysql_query("SELECT ship_id,character_name FROM ships ORDER BY character_name");
        while($row = mysql_fetch_array($res))
        {
          echo "<OPTION VALUE=$row[ship_id]>$row[character_name]</OPTION>";
        }
        mysql_free_result($res);
        echo "</SELECT>";
        echo "&nbsp;<INPUT TYPE=SUBMIT VALUE=Edit>";
      }
      else
      {
        if(empty($operation))
        {
          $res = mysql_query("SELECT * FROM ships WHERE ship_id=$user");
          $row = mysql_fetch_array($res);
          echo "<TABLE BORDER=0 CELLSPACING=0 CELLPADDING=5>";
          echo "<TR><TD>Player name</TD><TD><INPUT TYPE=TEXT NAME=character_name VALUE=\"$row[character_name]\"></TD></TR>";
          echo "<TR><TD>Password</TD><TD><INPUT TYPE=PASSWORD NAME=password VALUE=\"$row[password]\"></TD></TR>";
          echo "<TR><TD>E-mail</TD><TD><INPUT TYPE=TEXT NAME=email VALUE=\"$row[email]\"></TD></TR>";
          echo "<TR><TD>ID</TD><TD>$user</TD></TR>";
          echo "<TR><TD>Ship</TD><TD><INPUT TYPE=TEXT NAME=ship_name VALUE=\"$row[ship_name]\"></TD></TR>";
          echo "<TR><TD>Destroyed?</TD><TD><INPUT TYPE=CHECKBOX NAME=ship_destroyed VALUE=ON " . CHECKED($row[ship_destroyed]) . "></TD></TR>";
          echo "<TR><TD>Levels</TD>";
          echo "<TD><TABLE BORDER=0 CELLSPACING=0 CELLPADDING=5>";
          echo "<TR><TD>Hull</TD><TD><INPUT TYPE=TEXT SIZE=5 NAME=hull VALUE=\"$row[hull]\"></TD>";
          echo "<TD>Engines</TD><TD><INPUT TYPE=TEXT SIZE=5 NAME=engines VALUE=\"$row[engines]\"></TD>";
          echo "<TD>Power</TD><TD><INPUT TYPE=TEXT SIZE=5 NAME=power VALUE=\"$row[power]\"></TD>";
          echo "<TD>Computer</TD><TD><INPUT TYPE=TEXT SIZE=5 NAME=computer VALUE=\"$row[computer]\"></TD></TR>";
          echo "<TR><TD>Sensors</TD><TD><INPUT TYPE=TEXT SIZE=5 NAME=sensors VALUE=\"$row[sensors]\"></TD>";
          echo "<TD>Armour</TD><TD><INPUT TYPE=TEXT SIZE=5 NAME=armour VALUE=\"$row[armour]\"></TD>";
          echo "<TD>Shields</TD><TD><INPUT TYPE=TEXT SIZE=5 NAME=shields VALUE=\"$row[shields]\"></TD>";
          echo "<TD>Beams</TD><TD><INPUT TYPE=TEXT SIZE=5 NAME=beams VALUE=\"$row[beams]\"></TD></TR>";
          echo "<TR><TD>Torpedoes</TD><TD><INPUT TYPE=TEXT SIZE=5 NAME=torp_launchers VALUE=\"$row[torp_launchers]\"></TD>";
          echo "<TD>Cloak</TD><TD><INPUT TYPE=TEXT SIZE=5 NAME=cloak VALUE=\"$row[cloak]\"></TD></TR>";
          echo "</TABLE></TD></TR>";
          echo "<TR><TD>Holds</TD>";
          echo "<TD><TABLE BORDER=0 CELLSPACING=0 CELLPADDING=5>";
          echo "<TR><TD>Ore</TD><TD><INPUT TYPE=TEXT SIZE=8 NAME=ship_ore VALUE=\"$row[ship_ore]\"></TD>";
          echo "<TD>Organics</TD><TD><INPUT TYPE=TEXT SIZE=8 NAME=ship_organics VALUE=\"$row[ship_organics]\"></TD>";
          echo "<TD>Goods</TD><TD><INPUT TYPE=TEXT SIZE=8 NAME=ship_goods VALUE=\"$row[ship_goods]\"></TD></TR>";
          echo "<TR><TD>Energy</TD><TD><INPUT TYPE=TEXT SIZE=8 NAME=ship_energy VALUE=\"$row[ship_energy]\"></TD>";
          echo "<TD>Colonists</TD><TD><INPUT TYPE=TEXT SIZE=8 NAME=ship_colonists VALUE=\"$row[ship_colonists]\"></TD></TR>";
          echo "</TABLE></TD></TR>";
          echo "<TR><TD>Devices</TD>";
          echo "<TD><TABLE BORDER=0 CELLSPACING=0 CELLPADDING=5>";
          echo "<TR><TD>Beacons</TD><TD><INPUT TYPE=TEXT SIZE=5 NAME=dev_beacon VALUE=\"$row[dev_beacon]\"></TD>";
          echo "<TD>Warp Editors</TD><TD><INPUT TYPE=TEXT SIZE=5 NAME=dev_warpedit VALUE=\"$row[dev_warpedit]\"></TD>";
          echo "<TD>Genesis Torpedoes</TD><TD><INPUT TYPE=TEXT SIZE=5 NAME=dev_genesis VALUE=\"$row[dev_genesis]\"></TD></TR>";
          echo "<TR><TD>Mine Deflectors</TD><TD><INPUT TYPE=TEXT SIZE=5 NAME=dev_minedeflector VALUE=\"$row[dev_minedeflector]\"></TD>";
          echo "<TD>Emergency Warp</TD><TD><INPUT TYPE=TEXT SIZE=5 NAME=dev_emerwarp VALUE=\"$row[dev_emerwarp]\"></TD></TR>";
          echo "<TR><TD>Escape Pod</TD><TD><INPUT TYPE=CHECKBOX NAME=dev_escapepod VALUE=ON " . CHECKED($row[dev_escapepod]) . "></TD>";
          echo "<TD>FuelScoop</TD><TD><INPUT TYPE=CHECKBOX NAME=dev_fuelscoop VALUE=ON " . CHECKED($row[dev_fuelscoop]) . "></TD></TR>";
          echo "</TABLE></TD></TR>";
          echo "<TR><TD>Credits</TD><TD><INPUT TYPE=TEXT NAME=credits VALUE=\"$row[credits]\"></TD></TR>";
          echo "<TR><TD>Turns</TD><TD><INPUT TYPE=TEXT NAME=turns VALUE=\"$row[turns]\"></TD></TR>";
          echo "<TR><TD>Current sector</TD><TD><INPUT TYPE=TEXT NAME=sector VALUE=\"$row[sector]\"></TD></TR>";
          echo "</TABLE>";
          mysql_free_result($res);
          echo "<BR>";
          echo "<INPUT TYPE=HIDDEN NAME=user VALUE=$user>";
          echo "<INPUT TYPE=HIDDEN NAME=operation VALUE=save>";
          echo "<INPUT TYPE=SUBMIT VALUE=Save>";
        }
        elseif($operation == "save")
        {
          // update database
          $_ship_destroyed = empty($ship_destroyed) ? "N" : "Y";
          $_dev_escapepod = empty($dev_escapepod) ? "N" : "Y";
          $_dev_fuelscoop = empty($dev_fuelscoop) ? "N" : "Y";
          mysql_query("UPDATE ships SET character_name='$character_name',password='$password',email='$email',ship_name='$ship_name',ship_destroyed='$_ship_destroyed',hull='$hull',engines='$engines',power='$power',computer='$computer',sensors='$sensors',armour='$armour',shields='$shields',beams='$beams',torp_launchers='$torp_launchers',cloak='$cloak',credits='$credits',turns='$turns',dev_warpedit='$dev_warpedit',dev_genesis='$dev_genesis',dev_beacon='$dev_beacon',dev_emerwarp='$dev_emerwarp',dev_escapepod='$_dev_escapepod',dev_fuelscoop='$_dev_fuelscoop',dev_minedeflector='$dev_minedeflector',sector='$sector',ship_ore='$ship_ore',ship_organics='$ship_organics',ship_goods='$ship_goods',ship_energy='$ship_energy',ship_colonists='$ship_colonists' WHERE ship_id=$user");
          echo "Changes saved<BR><BR>";
          echo "<INPUT TYPE=SUBMIT VALUE=\"Return to User editor\">";
          $button_main = false;
        }
        else
        {
          echo "Invalid operation";
        }
      }
      echo "<INPUT TYPE=HIDDEN NAME=menu VALUE=useredit>";
      echo "<INPUT TYPE=HIDDEN NAME=swordfish VALUE=$swordfish>";
      echo "</FORM>";
    }
    elseif($module == "univedit")
    {
      echo "<B>Universe editor</B>";

        $title="Expand/Contract the Universe";
        echo "<BR>Expand or Contract the Universe <BR>";

        //$result = mysql_query ("SELECT sector_id, angle1, angle2,distance FROM universe ORDER BY sector_id ASC");
        
        if (empty($action))
        {
        echo "<FORM ACTION=admin.php3 METHOD=POST>";
        echo "Universe Size: <INPUT TYPE=TEXT NAME=radius VALUE=\"$universe_size\">";
        echo "<INPUT TYPE=HIDDEN NAME=swordfish VALUE=$swordfish>";
        echo "<INPUT TYPE=HIDDEN NAME=menu VALUE=univedit>";
        echo "<INPUT TYPE=HIDDEN NAME=action VALUE=doexpand> ";
        echo "<INPUT TYPE=SUBMIT VALUE=\"Play God\">";
        echo "</FORM>";
    	}
        elseif ($action == "doexpand")
        {
        echo "<BR><FONT SIZE='+2'>Be sure to update your config.php3 file with the new universe_size value</FONT><BR>";
        srand((double)microtime()*1000000);
        $result = mysql_query ("SELECT sector_id FROM universe ORDER BY sector_id ASC");
        while ($row=mysql_fetch_array($result))
        {
                $distance=rand(1,$radius);
                mysql_query ("UPDATE universe SET distance=$distance WHERE sector_id=$row[sector_id]");
                echo "Updated sector $row[sector_id] set to $distance<BR>";
        }
        
	}
    	}
    elseif($module == "sectedit")
    {
      echo "<H2>Sector editor</H2>";
      echo "<FORM ACTION=admin.php3 METHOD=POST>";
      if(empty($sector))
      {
        echo "<H5>Note: Cannot Edit Sector 0</H5>";
        echo "<SELECT SIZE=20 NAME=sector>";
        $res = mysql_query("SELECT sector_id FROM universe ORDER BY sector_id");
        while($row = mysql_fetch_array($res))
        {
          echo "<OPTION VALUE=$row[sector_id]> $row[sector_id] </OPTION>";
        }
        mysql_free_result($res);
        echo "</SELECT>";
        echo "&nbsp;<INPUT TYPE=SUBMIT VALUE=Edit>";
      }
      else
      {
        if(empty($operation))
        {
          $res = mysql_query("SELECT * FROM universe WHERE sector_id=$sector");
          $row = mysql_fetch_array($res);

          echo "<TABLE BORDER=0 CELLSPACING=2 CELLPADDING=2>";
          echo "<TR><TD><tt>          Sector ID  </tt></TD><TD><FONT COLOR=#66FF00>$sector</FONT></TD>";
          echo "<TD ALIGN=Right><tt>  Sector Name</tt></TD><TD><INPUT TYPE=TEXT SIZE=15 NAME=sector_name VALUE=\"$row[sector_name]\"></TD>";
          echo "<TD ALIGN=Right><tt>  Zone ID    </tt></TD><TD>";
                                      echo "<SELECT SIZE=1 NAME=zone_id>";
                                      $ressubb = mysql_query("SELECT zone_id,zone_name FROM zones ORDER BY zone_name");
                                      while($rowsubb = mysql_fetch_array($ressubb))
                                      {
                                      if ($rowsubb[zone_id] == $row[zone_id])
                                        { 
                                        echo "<OPTION SELECTED=$rowsubb[zone_id] VALUE=$rowsubb[zone_id]>$rowsubb[zone_name]</OPTION>";
                                        } else { 
                                        echo "<OPTION VALUE=$rowsubb[zone_id]>$rowsubb[zone_name]</OPTION>";
                                        }
                                      }
                                      mysql_free_result($ressubb);
                                      echo "</SELECT></TD></TR>";
          echo "<TR><TD><tt>          Beacon     </tt></TD><TD COLSPAN=5><INPUT TYPE=TEXT SIZE=70 NAME=beacon VALUE=\"$row[beacon]\"></TD></TR>";
          echo "<TR><TD><tt>          Distance   </tt></TD><TD><INPUT TYPE=TEXT SIZE=9 NAME=distance VALUE=\"$row[distance]\"></TD>";
          echo "<TD ALIGN=Right><tt>  Angle1     </tt></TD><TD><INPUT TYPE=TEXT SIZE=9 NAME=angle1 VALUE=\"$row[angle1]\"></TD>";
          echo "<TD ALIGN=Right><tt>  Angle2     </tt></TD><TD><INPUT TYPE=TEXT SIZE=9 NAME=angle2 VALUE=\"$row[angle2]\"></TD></TR>";
          echo "<TR><TD COLSPAN=6>    <HR>       </TD></TR>";
          echo "<TR><TD COLSPAN=2><tt>Planet     </tt><INPUT TYPE=CHECKBOX NAME=planet VALUE=ON " . CHECKED($row[planet]) . "></TD>";
          echo "<TD ALIGN=Right><tt>  Planet Name</tt></TD><TD><INPUT TYPE=TEXT SIZE=15 NAME=planet_name VALUE=\"$row[planet_name]\"></TD>";
          echo "<TD ALIGN=Right><tt>  Base       </tt><INPUT TYPE=CHECKBOX NAME=base VALUE=ON " . CHECKED($row[base]) . "></TD>";
          echo "<TD ALIGN=Right><tt>  Set To Sell</tt><INPUT TYPE=CHECKBOX NAME=base_sells VALUE=ON " . CHECKED($row[base_sells]) . "></TD></TR>";
          echo "</TABLE>";

          echo "<TABLE BORDER=0 CELLSPACING=2 CELLPADDING=2>";
          echo "<TR><TD><tt>          Planet Owner</tt></TD><TD>";
                                      echo "<SELECT SIZE=1 NAME=planet_owner>";
                                      $ressuba = mysql_query("SELECT ship_id,character_name FROM ships ORDER BY character_name");
                                      echo "<OPTION VALUE=0>No One</OPTION>";
                                      while($rowsuba = mysql_fetch_array($ressuba))
                                      {
                                      if ($rowsuba[ship_id] == $row[planet_owner])
                                        { 
                                        echo "<OPTION SELECTED=$rowsuba[ship_id] VALUE=$rowsuba[ship_id]>$rowsuba[character_name]</OPTION>";
                                        } else {  
                                        echo "<OPTION VALUE=$rowsuba[ship_id]>$rowsuba[character_name]</OPTION>";
                                        }
                                      }
                                      mysql_free_result($ressuba);
                                      echo "</SELECT></TD>";
          echo "<TD ALIGN=Right><tt>  Organics   </tt></TD><TD><INPUT TYPE=TEXT SIZE=9 NAME=planet_organics VALUE=\"$row[planet_organics]\"></TD>";
          echo "<TD ALIGN=Right><tt>  Ore        </tt></TD><TD><INPUT TYPE=TEXT SIZE=9 NAME=planet_ore VALUE=\"$row[planet_ore]\"></TD>";
          echo "<TD ALIGN=Right><tt>  Goods      </tt></TD><TD><INPUT TYPE=TEXT SIZE=9 NAME=planet_goods VALUE=\"$row[planet_goods]\"></TD>";
          echo "<TD ALIGN=Right><tt>  Energy     </tt></TD><TD><INPUT TYPE=TEXT SIZE=9 NAME=planet_energy VALUE=\"$row[planet_energy]\"></TD></TR>";
          echo "<TR><TD><tt>          Planet Corp</tt></TD><TD><INPUT TYPE=TEXT SIZE=5 NAME=planet_corp VALUE=\"$row[planet_corp]\"></TD>";
          echo "<TD ALIGN=Right><tt>  Colonists  </tt></TD><TD><INPUT TYPE=TEXT SIZE=9 NAME=planet_colonists VALUE=\"$row[planet_colonists]\"></TD>";
          echo "<TD ALIGN=Right><tt>  Credits    </tt></TD><TD><INPUT TYPE=TEXT SIZE=9 NAME=planet_credits VALUE=\"$row[planet_credits]\"></TD>";
          echo "<TD ALIGN=Right><tt>  Fighters   </tt></TD><TD><INPUT TYPE=TEXT SIZE=9 NAME=planet_fighters VALUE=\"$row[planet_fighters]\"></TD>";
          echo "<TD ALIGN=Right><tt>  Torpedoes  </tt></TD><TD><INPUT TYPE=TEXT SIZE=9 NAME=base_torp VALUE=\"$row[base_torp]\"></TD></TR>";
          echo "<TR><TD COLSPAN=2><tt>Planet Production</tt></TD>";
          echo "<TD ALIGN=Right><tt>  Organics   </tt></TD><TD><INPUT TYPE=TEXT SIZE=9 NAME=prod_organics VALUE=\"$row[prod_organics]\"></TD>";
          echo "<TD ALIGN=Right><tt>  Ore        </tt></TD><TD><INPUT TYPE=TEXT SIZE=9 NAME=prod_ore VALUE=\"$row[prod_ore]\"></TD>";
          echo "<TD ALIGN=Right><tt>  Goods      </tt></TD><TD><INPUT TYPE=TEXT SIZE=9 NAME=prod_goods VALUE=\"$row[prod_goods]\"></TD>";
          echo "<TD ALIGN=Right><tt>  Energy     </tt></TD><TD><INPUT TYPE=TEXT SIZE=9 NAME=prod_energy VALUE=\"$row[prod_energy]\"></TD></TR>";
          echo "<TR><TD COLSPAN=6><tt>Planet Production</tt></TD>";
          echo "<TD ALIGN=Right><tt>  Fighters   </tt></TD><TD><INPUT TYPE=TEXT SIZE=9 NAME=prod_fighters VALUE=\"$row[prod_fighters]\"></TD>";
          echo "<TD ALIGN=Right><tt>  Torpedoes  </tt></TD><TD><INPUT TYPE=TEXT SIZE=9 NAME=prod_torp VALUE=\"$row[prod_torp]\"></TD></TR>";
          echo "<TR><TD COLSPAN=10>   <HR>       </TD></TR>";
          echo "<TR><TD><tt>          Port Type  </tt></TD><TD>";
                                      echo "<SELECT SIZE=1 NAME=port_type>";
                                      $oportnon = $oportorg = $oportore = $oportgoo = $oportene = "VALUE"; 
                                      if ($row[port_type] == "none") $oportnon = "SELECTED=none VALUE";
                                      if ($row[port_type] == "organics") $oportorg = "SELECTED=organics VALUE";
                                      if ($row[port_type] == "ore") $oportore = "SELECTED=ore VALUE";
                                      if ($row[port_type] == "goods") $oportgoo = "SELECTED=goods VALUE";
                                      if ($row[port_type] == "energy") $oportene = "SELECTED=energy VALUE";
                                      echo "<OPTION $oportnon=none>none</OPTION>";
                                      echo "<OPTION $oportorg=organics>organics</OPTION>";
                                      echo "<OPTION $oportore=ore>ore</OPTION>";
                                      echo "<OPTION $oportgoo=goods>goods</OPTION>";
                                      echo "<OPTION $oportene=energy>energy</OPTION>";
                                      echo "</SELECT></TD>";
          echo "<TD ALIGN=Right><tt>  Organics   </tt></TD><TD><INPUT TYPE=TEXT SIZE=9 NAME=port_organics VALUE=\"$row[port_organics]\"></TD>";
          echo "<TD ALIGN=Right><tt>  Ore        </tt></TD><TD><INPUT TYPE=TEXT SIZE=9 NAME=port_ore VALUE=\"$row[port_ore]\"></TD>";
          echo "<TD ALIGN=Right><tt>  Goods      </tt></TD><TD><INPUT TYPE=TEXT SIZE=9 NAME=port_goods VALUE=\"$row[port_goods]\"></TD>";
          echo "<TD ALIGN=Right><tt>  Energy     </tt></TD><TD><INPUT TYPE=TEXT SIZE=9 NAME=port_energy VALUE=\"$row[port_energy]\"></TD></TR>";
          echo "<TR><TD COLSPAN=10>   <HR>       </TD></TR>";
          echo "<TR><TD COLSPAN=4><tt>Sector Deployment Owner </tt>";
                                      echo "<SELECT SIZE=1 NAME=fm_owner>";
                                      $ressubc = mysql_query("SELECT ship_id,character_name FROM ships ORDER BY character_name");
                                      echo "<OPTION VALUE=0>No One</OPTION>";
                                      while($rowsubc = mysql_fetch_array($ressubc))
                                      {
                                      if ($rowsubc[ship_id] == $row[fm_owner])
                                        { 
                                        echo "<OPTION SELECTED=$rowsubc[ship_id] VALUE=$rowsubc[ship_id]>$rowsubc[character_name]</OPTION>";
                                        } else {  
                                        echo "<OPTION VALUE=$rowsubc[ship_id]>$rowsubc[character_name]</OPTION>";
                                        }
                                      }
                                      mysql_free_result($ressubc);
                                      echo "</SELECT></TD>";
          echo "<TD ALIGN=Right><tt>  Fighters   </tt></TD><TD><INPUT TYPE=TEXT SIZE=9 NAME=fighters VALUE=\"$row[fighters]\"></TD>";
          echo "<TD ALIGN=Right><tt>  Mines      </tt></TD><TD><INPUT TYPE=TEXT SIZE=9 NAME=mines VALUE=\"$row[mines]\"></TD>";
          echo "<TD ALIGN=Right><tt>  Deploy Type</tt></TD><TD>";
                                      echo "<SELECT SIZE=1 NAME=fm_setting>";
                                      $ofmsettol = $ofmsetatt = "VALUE"; 
                                      if ($row[fm_setting] == "toll") $ofmsettol = "SELECTED=toll VALUE";
                                      if ($row[fm_setting] == "attack") $ofmsetatt = "SELECTED=attack VALUE";
                                      echo "<OPTION $ofmsettol=toll>Toll</OPTION>";
                                      echo "<OPTION $ofmsetatt=attack>Attack</OPTION>";
                                      echo "</SELECT></TD></TR>";
          echo "<TR><TD COLSPAN=10>   <HR>       </TD></TR>";
          echo "</TABLE>";

          mysql_free_result($res);
          echo "<BR>";
          echo "<INPUT TYPE=HIDDEN NAME=sector VALUE=$sector>";
          echo "<INPUT TYPE=HIDDEN NAME=operation VALUE=save>";
          echo "<INPUT TYPE=SUBMIT SIZE=1 VALUE=Save>";
        }
        elseif($operation == "save")
        {
          // update database
          $_planet = empty($planet) ? "N" : "Y";
          $_base = empty($base) ? "N" : "Y";
          $_base_sells = empty($base_sells) ? "N" : "Y";
          mysql_query("UPDATE universe SET sector_name='$sector_name',zone_id='$zone_id',beacon='$beacon',planet='$_planet',planet_name='$planet_name',base='$_base',base_sells='$_base_sells',planet_owner='$planet_owner',planet_organics='$planet_organics',planet_ore='$planet_ore',planet_goods='$planet_goods',planet_energy='$planet_energy',planet_corp='$planet_corp',planet_colonists='$planet_colonists',planet_credits='$planet_credits',planet_fighters='$planet_fighters',base_torp='$base_torp',prod_organics='$prod_organics',prod_ore='$prod_ore',prod_goods='$prod_goods',prod_energy='$prod_energy',prod_fighters='$prod_fighters',prod_torp='$prod_torp',port_type='$port_type',port_organics='$port_organics',port_ore='$port_ore',port_goods='$port_goods',port_energy='$port_energy',distance='$distance',angle1='$angle1',angle2='$angle2',fighters='$fighters',mines='$mines',fm_owner='$fm_owner',fm_setting='$fm_setting' WHERE sector_id=$sector");
          echo "Changes saved<BR><BR>";
          echo "<INPUT TYPE=SUBMIT VALUE=\"Return to Sector editor\">";
          $button_main = false;
        }
        else
        {
          echo "Invalid operation";
        }
      }
      echo "<INPUT TYPE=HIDDEN NAME=menu VALUE=sectedit>";
      echo "<INPUT TYPE=HIDDEN NAME=swordfish VALUE=$swordfish>";
      echo "</FORM>";
    }
    elseif($module == "linkedit")
    {
      echo "<B>Link editor</B>";
    }
    elseif($module == "zoneedit")
    {
      echo "<B>Zone editor</B>";
      echo "<BR>";
      echo "<FORM ACTION=admin.php3 METHOD=POST>";
      if(empty($zone))
      {
        echo "<SELECT SIZE=20 NAME=zone>";
        $res = mysql_query("SELECT zone_id,zone_name FROM zones ORDER BY zone_name");
        while($row = mysql_fetch_array($res))
        {
          echo "<OPTION VALUE=$row[zone_id]>$row[zone_name]</OPTION>";
        }
        mysql_free_result($res);
        echo "</SELECT>";
        echo "<INPUT TYPE=HIDDEN NAME=operation VALUE=editzone>";
        echo "&nbsp;<INPUT TYPE=SUBMIT VALUE=Edit>";
       
      }
      else
      {
        if($operation == "editzone")
        {
          $res = mysql_query("SELECT * FROM zones WHERE zone_id=$zone");
          $row = mysql_fetch_array($res);
          echo "<TABLE BORDER=0 CELLSPACING=0 CELLPADDING=5>";
          echo "<TR><TD>Zone ID</TD><TD>$row[zone_id]</TD></TR>";
          echo "<TR><TD>Zone Name</TD><TD><INPUT TYPE=TEXT NAME=zone_name VALUE=\"$row[zone_name]\"></TD></TR>";
          echo "<TR><TD>Allow Beacon</TD><TD><INPUT TYPE=CHECKBOX NAME=zone_beacon VALUE=ON " . CHECKED($row[allow_beacon]) . "></TD>";
          echo "<TR><TD>Allow Attack</TD><TD><INPUT TYPE=CHECKBOX NAME=zone_attack VALUE=ON " . CHECKED($row[allow_attack]) . "></TD>";
          echo "<TR><TD>Allow WarpEdit</TD><TD><INPUT TYPE=CHECKBOX NAME=zone_warpedit VALUE=ON " . CHECKED($row[allow_warpedit]) . "></TD>";
          echo "<TR><TD>Allow Planet</TD><TD><INPUT TYPE=CHECKBOX NAME=zone_planet VALUE=ON " . CHECKED($row[allow_planet]) . "></TD>";
          echo "</TABLE>";
          echo "<TR><TD>Max Hull</TD><TD><INPUT TYPE=TEXT NAME=zone_hull VALUE=\"$row[max_hull]\"></TD></TR>";
          mysql_free_result($res);
          echo "<BR>";
          echo "<INPUT TYPE=HIDDEN NAME=zone VALUE=$zone>";
          echo "<INPUT TYPE=HIDDEN NAME=operation VALUE=savezone>";
          echo "<INPUT TYPE=SUBMIT VALUE=Save>";
        }
        elseif($operation == "savezone")
        {
          // update database
          $_zone_beacon = empty($zone_beacon) ? "N" : "Y";
          $_zone_attack = empty($zone_attack) ? "N" : "Y";
          $_zone_warpedit = empty($zone_warpedit) ? "N" : "Y";
          $_zone_planet = empty($zone_planet) ? "N" : "Y";
          mysql_query("UPDATE zones SET zone_name='$zone_name',allow_beacon='$_zone_beacon' ,allow_attack='$_zone_attack' ,allow_warpedit='$_zone_warpedit' ,allow_planet='$_zone_planet', max_hull='$zone_hull' WHERE zone_id=$zone");
          echo "Mudanças Salvas<BR><BR>";
          echo "<INPUT TYPE=SUBMIT VALUE=\"Return to Zone Editor \">";
          $button_main = false;
        }
        else
        {
          echo "Invalid operation";
        }
      }
      echo "<INPUT TYPE=HIDDEN NAME=menu VALUE=zoneedit>";
      echo "<INPUT TYPE=HIDDEN NAME=swordfish VALUE=$swordfish>";
      echo "</FORM>";
    }
    
    else
    {
      echo "Unknown function";
    }

    if($button_main)
    {
      echo "<BR><BR>";
      echo "<FORM ACTION=admin.php3 METHOD=POST>";
      echo "<INPUT TYPE=HIDDEN NAME=swordfish VALUE=$swordfish>";
      echo "<INPUT TYPE=SUBMIT VALUE=\"Return to main menu\">";
      echo "</FORM>";
    }
  }
}
  
include("footer.php3");

?> 
