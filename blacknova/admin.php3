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
