<?

include("config.php3");
updatecookie();

$title="Ferengi Control";
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
  echo "<FORM ACTION=ferengi_control.php METHOD=POST>";
  echo "Password: <INPUT TYPE=PASSWORD NAME=swordfish SIZE=20 MAXLENGTH=20><BR><BR>";
  echo "<INPUT TYPE=SUBMIT VALUE=Submit><INPUT TYPE=RESET VALUE=Reset>";
  echo "</FORM>";
}
else
{
  // ******************************
  // ******** MAIN MENU ***********
  // ******************************
  if(empty($module))
  {
    echo "Welcome to the BlackNova Traders Ferengi Control module<BR><BR>";
    echo "Select a function from the list below:<BR>";
    echo "<FORM ACTION=ferengi_control.php METHOD=POST>";
    echo "<SELECT NAME=menu>";
    echo "<OPTION VALUE=instruct SELECTED>Ferengi Instructions</OPTION>";
    echo "<OPTION VALUE=ferengiedit>Ferengi Character Editor</OPTION>";
    echo "<OPTION VALUE=createnew>Create A New Feregi Character</OPTION>";
    echo "<OPTION VALUE=dropferengi>Drop and Re-Install Ferengi Database</OPTION>";
    echo "</SELECT>";
    echo "<INPUT TYPE=HIDDEN NAME=swordfish VALUE=$swordfish>";
    echo "&nbsp;<INPUT TYPE=SUBMIT VALUE=Submit>";
    echo "</FORM>";
  }
  else
  {
    $button_main = true;
    // ***********************************************
    // ********* START OF INSTRUCTIONS SUB ***********
    // ***********************************************
    if($module == "instruct")
    {
      echo "<H2>Ferengi Instructions</H2>";
      echo "<P>&nbsp;&nbsp;&nbsp; Welcome to the Ferengi Control module.  This is the module that will control the Ferengi players in the game. ";
      echo "It is very simple right now, but will be expanded in future versions. ";
      echo "The ultimate goal of the Ferengi players is to create some interactivity for those games without a large user base. ";
      echo "I need not say that the Ferengi will also make good cannon fodder for those games with a large user base. ";
      echo "<P>&nbsp;&nbsp;&nbsp; The first step to creating some Ferengi characters is to choose the <B>\"Drop and Re-Install ";
      echo "Ferengi Database\"</B> option from the main menu of this module.  This will prime the Ferengi Database for use. ";
      echo "Then you can choose the <B>\"Create A Ferengi Character\"</B> option. ";
      echo "<P>&nbsp;&nbsp;&nbsp; When creating a new Ferengi character the name and shipname are automatically generated. ";
      echo "You can change these default values before submitting the character for creation. ";
      echo "There are also some other fields that could be modified.  The Active checkbox indicates weather or not the Ferengi ";
      echo "will be controlled by the ferengi-update routine.  If this is not checked (or if the Ferengi's ship is destroyed) ";
      echo "then the ferengi-update routine will not touch this character.  The Orders selection list indicates what the Ferengi ";
      echo "is ordered to do.  The current Orders available are: Sentinel - This Ferengi will not move from his current sector; Roam - ";
      echo "This Ferengi will roam from sector to sector; Roam and Trade - This Ferengi will roam from sector to sector and trade at ";
      echo "any ports or planets it comes accross.  The Aggression selection list indicates what the Ferengi will do when it meets ";
      echo "other players.  The current Aggession settings available are: Peaceful - This Ferengi will show no aggression; Attack Sometimes - ";
      echo "This Ferengi will attack players only when the odds are in it's favor; Attack Always - This Ferengi is just downright mean. ";
    }
    // ***********************************************
    // ********* START OF FERENGI EDIT SUB ***********
    // ***********************************************
    elseif($module == "ferengiedit")
    {
        echo "<span style=\"font-family : courier, monospace; font-size: 12pt;\">";
      echo "Ferengi editor";
        echo "</span>";
      echo "<BR>";
      echo "<FORM ACTION=ferengi_control.php METHOD=POST>";
      if(empty($user))
      {
        echo "<SELECT SIZE=20 NAME=user>";
        $res = mysql_query("SELECT email,character_name,ship_destroyed,active,sector FROM ships JOIN ferengi WHERE email=ferengi_id ORDER BY sector");
        while($row = mysql_fetch_array($res))
        {
          $charnamelist = sprintf("%-20s", $row[character_name]);
          $charnamelist = str_replace("  ", "&nbsp;&nbsp;",$charnamelist);
          $sectorlist = sprintf("Sector %'04d&nbsp;&nbsp;", $row[sector]);
          if ($row[active] == "Y") { $activelist = "Active &Oslash;&nbsp;&nbsp;"; } else { $activelist = "Active O&nbsp;&nbsp;"; }
          if ($row[ship_destroyed] == "Y") { $destroylist = "Destroyed &Oslash;&nbsp;&nbsp;"; } else { $destroylist = "Destroyed O&nbsp;&nbsp;"; }
          printf ("<OPTION VALUE=%s>%s %s %s %s</OPTION>", $row[email], $activelist, $destroylist, $sectorlist, $charnamelist);
        }
        mysql_free_result($res);
        echo "</SELECT>";
        echo "&nbsp;<INPUT TYPE=SUBMIT VALUE=Edit>";
      }
      else
      {
        if(empty($operation))
        {
          $res = mysql_query("SELECT * FROM ships JOIN ferengi WHERE email=ferengi_id AND email='$user'");
          $row = mysql_fetch_array($res);
          echo "<TABLE BORDER=0 CELLSPACING=0 CELLPADDING=5>";
          echo "<TR><TD>Ferengi name</TD><TD><INPUT TYPE=TEXT NAME=character_name VALUE=\"$row[character_name]\"></TD></TR>";
          echo "<TR><TD>Active?</TD><TD><INPUT TYPE=CHECKBOX NAME=active VALUE=ON " . CHECKED($row[active]) . "></TD></TR>";
          echo "<TR><TD>E-mail</TD><TD>$row[email]</TD></TR>";
          echo "<TR><TD>ID</TD><TD>$row[ship_id]</TD></TR>";
          echo "<TR><TD>Ship</TD><TD><INPUT TYPE=TEXT NAME=ship_name VALUE=\"$row[ship_name]\"></TD></TR>";
          echo "<TR><TD>Destroyed?</TD><TD><INPUT TYPE=CHECKBOX NAME=ship_destroyed VALUE=ON " . CHECKED($row[ship_destroyed]) . "></TD></TR>";
          echo "<TR><TD>Orders</TD><TD>";
            echo "<SELECT SIZE=1 NAME=orders>";
            $oorder0 = $oorder1 = $oorder2 = "VALUE";
            if ($row[orders] == 0) $oorder0 = "SELECTED=0 VALUE";
            if ($row[orders] == 1) $oorder1 = "SELECTED=1 VALUE";
            if ($row[orders] == 2) $oorder2 = "SELECTED=2 VALUE";
            echo "<OPTION $oorder0=0>Sentinel</OPTION>";
            echo "<OPTION $oorder1=1>Roam</OPTION>";
            echo "<OPTION $oorder2=2>Roam and Trade</OPTION>";
            echo "</SELECT></TD></TR>";
          echo "<TR><TD>Aggression</TD><TD>";
            $oaggr0 = $oaggr1 = $oaggr2 = "VALUE";
            if ($row[aggression] == 0) $oaggr0 = "SELECTED=0 VALUE";
            if ($row[aggression] == 1) $oaggr1 = "SELECTED=1 VALUE";
            if ($row[aggression] == 2) $oaggr2 = "SELECTED=2 VALUE";
            echo "<SELECT SIZE=1 NAME=aggression>";
            echo "<OPTION $oaggr0=0>Peaceful</OPTION>";
            echo "<OPTION $oaggr1=1>Attack Sometimes</OPTION>";
            echo "<OPTION $oaggr2=2>Attack Always</OPTION>";
            echo "</SELECT></TD></TR>";
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
          echo "<TR><TD>Combat</TD>";
          echo "<TD><TABLE BORDER=0 CELLSPACING=0 CELLPADDING=5>";
          echo "<TR><TD>Fighters</TD><TD><INPUT TYPE=TEXT SIZE=8 NAME=ship_fighters VALUE=\"$row[ship_fighters]\"></TD>";
          echo "<TD>Torpedoes</TD><TD><INPUT TYPE=TEXT SIZE=8 NAME=torps VALUE=\"$row[torps]\"></TD></TR>";
          echo "<TR><TD>Armour Pts</TD><TD><INPUT TYPE=TEXT SIZE=8 NAME=armour_pts VALUE=\"$row[armour_pts]\"></TD></TR>";
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
          $_active = empty($active) ? "N" : "Y";
          $result = mysql_query("UPDATE ships SET character_name='$character_name',ship_name='$ship_name',ship_destroyed='$_ship_destroyed',hull='$hull',engines='$engines',power='$power',computer='$computer',sensors='$sensors',armour='$armour',shields='$shields',beams='$beams',torp_launchers='$torp_launchers',cloak='$cloak',credits='$credits',turns='$turns',dev_warpedit='$dev_warpedit',dev_genesis='$dev_genesis',dev_beacon='$dev_beacon',dev_emerwarp='$dev_emerwarp',dev_escapepod='$_dev_escapepod',dev_fuelscoop='$_dev_fuelscoop',dev_minedeflector='$dev_minedeflector',sector='$sector',ship_ore='$ship_ore',ship_organics='$ship_organics',ship_goods='$ship_goods',ship_energy='$ship_energy',ship_colonists='$ship_colonists',ship_fighters='$ship_fighters',torps='$torps',armour_pts='$armour_pts' WHERE email='$user'");
          if(!$result) {
            echo "Changes to Ferengi ship record have FAILED Due to the following Error:<BR><BR>";
            echo mysql_errno(). ": ".mysql_error(). "<br>";
          } else {
            echo "Changes to Ferengi ship record have been saved.<BR><BR>";
            $result2 = mysql_query("UPDATE ferengi SET active='$_active',orders='$orders',aggression='$aggression' WHERE ferengi_id='$user'");
            if(!$result2) {
              echo "Changes to Ferengi activity record have FAILED Due to the following Error:<BR><BR>";
              echo mysql_errno(). ": ".mysql_error(). "<br>";
            } else {
              echo "Changes to Ferengi activity record have been saved.<BR><BR>";
            }
          }
          echo "<INPUT TYPE=SUBMIT VALUE=\"Return to Ferengi editor\">";
          $button_main = false;
        }
        else
        {
          echo "Invalid operation";
        }
      }
      echo "<INPUT TYPE=HIDDEN NAME=menu VALUE=ferengiedit>";
      echo "<INPUT TYPE=HIDDEN NAME=swordfish VALUE=$swordfish>";
      echo "</FORM>";
    }
    // ***********************************************
    // ********* START OF DROP FERENGI SUB ***********
    // ***********************************************
    elseif($module == "dropferengi")
    {
      echo "<H1>Drop and Re-Install Ferengi Database</H1>";
      echo "<H3>This will DELETE All Ferengi records from the <i>ships</i> TABLE then DROP and reset the <i>ferengi</i> TABLE</H3>";
      echo "<FORM ACTION=ferengi_control.php METHOD=POST>";
      if(empty($operation))
      {
        echo "<BR>";
        echo "<H2><FONT COLOR=Red>Are You Sure?</FONT></H2><BR>";
        echo "<INPUT TYPE=HIDDEN NAME=operation VALUE=dropfer>";
        echo "<INPUT TYPE=SUBMIT VALUE=Drop>";
      }
      elseif($operation == "dropfer")
      {
        // Delete all ferengi in the ships table
        echo "Deleting ferengi records in the ships table...<BR>";
        mysql_query("DELETE FROM ships WHERE email LIKE '%@ferengi'");
        echo "deleted.<BR>";
        // Drop ferengi table
        echo "Dropping ferengi table...<BR>";
        mysql_query("DROP TABLE IF EXISTS ferengi");
        echo "dropped.<BR>";
        // Create ferengi table
        echo "Re-Creating table: ferengi...<BR>";
        mysql_query("CREATE TABLE ferengi(" .
            "ferengi_id char(40) NOT NULL," .
            "active enum('Y','N') DEFAULT 'Y' NOT NULL," .
            "aggression smallint(5) DEFAULT '0' NOT NULL," .
            "orders smallint(5) DEFAULT '0' NOT NULL," .
            "PRIMARY KEY (ferengi_id)," .
            "KEY ferengi_id (ferengi_id)" .
            ")");
        echo "created.<BR>";
      }
      else
      {
        echo "Invalid operation";
      }
      echo "<INPUT TYPE=HIDDEN NAME=menu VALUE=dropferengi>";
      echo "<INPUT TYPE=HIDDEN NAME=swordfish VALUE=$swordfish>";
      echo "</FORM>";
    }
    // ***********************************************
    // ******** START OF CREATE FERENGI SUB **********
    // ***********************************************
    elseif($module == "createnew")
    {
      echo "<B>Create A New Ferengi</B>";
      echo "<BR>";
      echo "<FORM ACTION=ferengi_control.php METHOD=POST>";
      if(empty($operation))
      {
        // Create Ferengi Name
        $Sylable1 = array("Ak","Al","Ar","B","Br","D","F","Fr","G","Gr","K","Kr","N","Ol","Om","P","Qu","R","S","Z");
        $Sylable2 = array("a","ar","aka","aza","e","el","i","in","int","ili","ish","ido","ir","o","oi","or","os","ov","u","un");
        $Sylable3 = array("ag","al","ak","ba","dar","g","ga","k","ka","kar","kil","l","n","nt","ol","r","s","ta","til","x");
        $sy1roll = rand(0,19);
        $sy2roll = rand(0,19);
        $sy3roll = rand(0,19);
        $character = $Sylable1[$sy1roll] . $Sylable2[$sy2roll] . $Sylable3[$sy3roll];
        $resultnm = mysql_query ("select character_name from ships where character_name='$character'");
        $namecheck = mysql_fetch_row ($resultnm);
        $nametry = 1;
        // If Name Exists Try Again - Up To Nine Times
        while (($namecheck[0]) and ($nametry <= 9)) {
          $sy1roll = rand(0,19);
          $sy2roll = rand(0,19);
          $sy3roll = rand(0,19);
          $character = $Sylable1[$sy1roll] . $Sylable2[$sy2roll] . $Sylable3[$sy3roll];
          $resultnm = mysql_query ("select character_name from ships where character_name='$character'");
          $namecheck = mysql_fetch_row ($resultnm);
          $nametry++;
        }
        // Create Ship Name
        $shipname = "Ferengi-" . $character; 
        // Select Random Sector
        $sector = rand(1,$sector_max); 
        // Display Confirmation Form
        echo "<TD><TABLE BORDER=0 CELLSPACING=0 CELLPADDING=5>";
        echo "<TR><TD>Ferengi Name</TD><TD><INPUT TYPE=TEXT SIZE=20 NAME=character VALUE=$character></TD>";
        echo "<TD ALIGN=Right>Ship Name</TD><TD><INPUT TYPE=TEXT SIZE=20 NAME=shipname VALUE=$shipname></TD>";
        echo "<TR><TD>Active?<INPUT TYPE=CHECKBOX NAME=active VALUE=ON CHECKED ></TD>";
        echo "<TD>Orders ";
          echo "<SELECT SIZE=1 NAME=orders>";
          echo "<OPTION SELECTED=0 VALUE=0>Sentinel</OPTION>";
          echo "<OPTION VALUE=1>Roam</OPTION>";
          echo "<OPTION VALUE=2>Roam and Trade</OPTION>";
          echo "</SELECT></TD>";
        echo "<TD>Sector <INPUT TYPE=TEXT SIZE=5 NAME=sector VALUE=$sector></TD>";
        echo "<TD>Aggression ";
          echo "<SELECT SIZE=1 NAME=aggression>";
          echo "<OPTION SELECTED=0 VALUE=0>Peaceful</OPTION>";
          echo "<OPTION VALUE=1>Attack Sometimes</OPTION>";
          echo "<OPTION VALUE=2>Attack Always</OPTION>";
          echo "</SELECT></TD></TR>";
        echo "</TABLE>";
        echo "<HR>";
        echo "<INPUT TYPE=HIDDEN NAME=operation VALUE=createferengi>";
        echo "<INPUT TYPE=SUBMIT VALUE=Create>";
      }
      elseif($operation == "createferengi")
      {
        // update database
        $_active = empty($active) ? "N" : "Y";
        $errflag=0;
        if ( $character=='' || $shipname=='' ) { echo "Ship name, and character name may not be blank.<BR>"; $errflag=1;}
        // Change Spaces to Underscores in shipname
        $shipname = str_replace(" ","_",$shipname);
        // Create emailname from character
        $emailname = str_replace(" ","_",$character) . "@ferengi";
        $result = mysql_query ("select email, character_name, ship_name from ships where email='$emailname' OR character_name='$character' OR ship_name='$shipname'");
        if ($result>0)
        {
          while ($row = mysql_fetch_row ($result))
          {
            if ($row[0]==$emailname) { echo "ERROR: E-mail address $emailname, is already in use.  "; $errflag=1;}
            if ($row[1]==$character) { echo "ERROR: Character name $character, is already in use.<BR>"; $errflag=1;}
            if ($row[2]==$shipname) { echo "ERROR: Ship name $shipname, is already in use.<BR>"; $errflag=1;}
          }
        }
        if ($errflag==0)
        {
          $makepass="";
          $syllables="er,in,tia,wol,fe,pre,vet,jo,nes,al,len,son,cha,ir,ler,bo,ok,tio,nar,sim,ple,bla,ten,toe,cho,co,lat,spe,ak,er,po,co,lor,pen,cil,li,ght,wh,at,the,he,ck,is,mam,bo,no,fi,ve,any,way,pol,iti,cs,ra,dio,sou,rce,sea,rch,pa,per,com,bo,sp,eak,st,fi,rst,gr,oup,boy,ea,gle,tr,ail,bi,ble,brb,pri,dee,kay,en,be,se";
          $syllable_array=explode(",", $syllables);
          srand((double)microtime()*1000000);
          for ($count=1;$count<=4;$count++) {
            if (rand()%10 == 1) {
              $makepass .= sprintf("%0.0f",(rand()%50)+1);
            } else {
              $makepass .= sprintf("%s",$syllable_array[rand()%62]);
            }
          }
          $stamp=date("Y-m-d H:i:s");
          $result2 = mysql_query("INSERT INTO ships VALUES('','$shipname','N','$character','$makepass','$emailname',0,0,0,0,0,0,0,0,0,0,$start_armour,0,$start_credits,$sector,0,0,0,$start_energy,0,$start_fighters,$start_turns,'','N',0,0,0,0,'N','N',0,0, '$stamp',0,0,0,0,'N','127.0.0.1',0,0,0,0,'Y','N','N','Y')");
          if(!$result2) {
            echo mysql_errno(). ": ".mysql_error(). "<br>";
          } else {
            echo "Ferengi has been created.<BR><BR>";
            echo "Password has been set.<BR><BR>";
            echo "Ship Records have been updated.<BR><BR>";
          }
          $result3 = mysql_query("INSERT INTO ferengi (ferengi_id,active,aggression,orders) VALUES('$emailname','$_active','$aggression','$orders')");
          if(!$result3) {
            echo mysql_errno(). ": ".mysql_error(). "<br>";
          } else {
            echo "Ferengi Records have been updated.<BR><BR>";
          }
        }
        echo "<INPUT TYPE=SUBMIT VALUE=\"Return to Ferengi Creator \">";
        $button_main = false;
      }
      else
      {
        echo "Invalid operation";
      }
      echo "<INPUT TYPE=HIDDEN NAME=menu VALUE=createnew>";
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
      echo "<FORM ACTION=ferengi_control.php METHOD=POST>";
      echo "<INPUT TYPE=HIDDEN NAME=swordfish VALUE=$swordfish>";
      echo "<INPUT TYPE=SUBMIT VALUE=\"Return to main menu\">";
      echo "</FORM>";
    }
  }
}
  
include("footer.php3");

?> 
