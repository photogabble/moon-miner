<?
        include("config.php3");
        updatecookie();

        $title="Long Range Scan";
        include("header.php3");

	connectdb();
        if (checklogin()) {die();}

                $result = mysql_query ("SELECT * FROM ships WHERE email='$username'");
                $playerinfo=mysql_fetch_array($result);

                $result2 = mysql_query ("SELECT * FROM universe WHERE sector_id='$sector'");
                $sectorinfo=mysql_fetch_array($result2);

        bigtitle();

                $result3 = mysql_query ("SELECT * FROM links WHERE link_start='$sector'");
                $i=0;
                if ($result3>0)
                {
                        while ($row = mysql_fetch_array($result3))
                        {
                                $links[$i]=$row[link_dest];
                                $i++;
                        }
                }
                $num_links=$i;

                $result3a = mysql_query ("SELECT * FROM links WHERE link_start='$playerinfo[sector]'");
                $i=0;
                $flag=0;
                if ($result3a>0)
                {
                        while ($row = mysql_fetch_array($result3a))
                        {
                                if ($row[link_dest]==$sector) {$flag=1;}
                                $i++;
                        }
                }

                if ($flag==0) {echo "Can't scan sector from current sector! Click <a href=main.php3>here</a> to go back."; die();}

                echo "Long Range Scan of Sector #$sector";
                if ($sectorinfo[sector_name]!="") { echo " ($sectorinfo[sector_name]).<BR><BR>";} else {echo ".<BR><BR>";}
                if ($num_links==0) { echo "There are no links out of this sector.<BR><BR>";} else
                {
                        echo "Links lead to the following sectors: ";
                        for  ($i=0; $i<$num_links;$i++)
                        {
                                echo "$links[$i]";
                                if ($i+1!=$num_links) { echo ", ";}
                        }
                        echo "<BR><BR>";
                }
		if ($sector!=0)
		{
	                $result4 = mysql_query("SELECT * FROM ships WHERE sector='$sector'");
        	        $i=0;
                	if ($result4>0)
	                {
        	                while ($row = mysql_fetch_array($result4))
                	        {
	                                $ships[$i]=$row[ship_name];
        	                        $ship_id[$i]=$row[ship_id];
                	                $i++;
	                        }
        	        }
                	$num_ships=$i;
	                if ($num_ships<1) { echo "There are no ships in this sector.<BR><BR>";} else
        	        {
                	        echo "The following other ships are here: ";
                       		for  ($i=0; $i<$num_ships;$i++)
                 		{
                                	if ($ships[$i]!=$playerinfo[ship_name])
					{
        	                                echo "$ships[$i]";
                	                        if ($i+1!=$num_ships) { echo " ";}
                        	        }
	                        }
                        echo "<BR><BR>";
			}
		} else { echo "Sector 0 is too congested to scan for ships!<BR><BR>"; }
                if ($sectorinfo[port_type]!="none") {echo "There is a $sectorinfo[port_type] port here.<BR><BR>";}
		if ($sectorinfo[planet]=="Y" && $sectorinfo[sector_id]!=0)			
		{
			echo "There is a planet here ";
				if (empty($sectorinfo[planet_name]))				{					echo "with no name ";
				} else {					echo "named $sectorinfo[planet_name] ";				}
				if ($sectorinfo[planet_owner]=="")				{
					echo "and it is unowned.<BR><BR>";				} else {
					$result5 = mysql_query ("SELECT character_name FROM ships WHERE ship_id=$sectorinfo[planet_owner]");
					$planet_owner_name=mysql_fetch_array($result5);
					echo "owned by <a href=mailto.php3?to=$sectorinfo[planet_owner]>$planet_owner_name[character_name]</a> (#$sectorinfo[planet_owner])<BR><BR>";
				}	
		}	
		echo "Click <a href=move.php3?sector=$sector>here</a> to move to sector $sector.<BR><BR>";
                echo "Click <a href=main.php3>here</a> to return to main menu.";

        include("footer.php3");

?>
