<?
        include("config.php3");
        updatecookie();

        $title="Main Menu";
        include("header.php3");
        connectdb();
        if (checklogin()) {die();}

	$result = mysql_query ("SELECT * FROM ships WHERE email='$username'");
	$playerinfo=mysql_fetch_array($result);

	$result2 = mysql_query ("SELECT * FROM universe WHERE sector_id='$playerinfo[sector]'");
	$sectorinfo=mysql_fetch_array($result2);

	$result3 = mysql_query ("SELECT * FROM links WHERE link_start='$playerinfo[sector]'");
	bigtitle();
	if ($playerinfo[on_planet]=="Y")
	{
		if ($sectorinfo[planet]=="Y")
		{
			echo "Click <a href=planet.php3>here</a> to go to the planet menu.<BR>"; 
			echo "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"0;URL=planet.php3?id=".$playerinfo[ship_id]."\">";
			die();
		} else {
			$update = mysql_query("UPDATE ships SET on_planet='N' WHERE ship_id=$playerinfo[ship_id]");
			echo "<BR>On a non-existent planet???<BR><BR>";
		}
	}
	if (!empty($sectorinfo[beacon])){echo "$sectorinfo[beacon]<BR><BR>";}

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

                echo "$playerinfo[character_name] (player #$playerinfo[ship_id]), captaining the $playerinfo[ship_name], presently in #$playerinfo[sector]";
                if ($sectorinfo[sector_name]!="") { echo " ($sectorinfo[sector_name]).<BR><BR>";} else {echo ".<BR><BR>";}
		echo "You have $playerinfo[turns] turns, and have used $playerinfo[turns_used] turns so far.<BR><BR>";
		echo "Ore=$playerinfo[ship_ore] - Organics=$playerinfo[ship_organics] - Goods=$playerinfo[ship_goods] - Energy=$playerinfo[ship_energy] - Credits=$playerinfo[credits]<BR><BR>";
                if ($num_links==0) { echo "There are no links out of this sector.<BR><BR>";} else
                {
                        echo "Links lead to the following sectors (click to move): ";
                        for  ($i=0; $i<$num_links;$i++)
                        {
                                echo "<a href=move.php3?sector=$links[$i]>$links[$i]</a>";
                                if ($i+1!=$num_links) { echo ", ";}
                        }
                        echo "<BR><BR>";
                        echo "Long-range scan: ";
                        if($allow_fullscan)
                        {
                          echo "<A HREF=lrscan.php3?sector=*>full scan</A> - By sector: ";
                        }
                        for($i=0; $i<$num_links;$i++)
                        {
                                echo "<a href=lrscan.php3?sector=$links[$i]>$links[$i]</a>";
                                if ($i+1!=$num_links) { echo ", ";}
                        }
                        echo "<BR><BR>";                }
		/* Get a list of the ships in this sector */
		if ($playerinfo[sector]!=0)
		{
                $result4 = mysql_query("SELECT * FROM ships WHERE sector=$playerinfo[sector] AND on_planet='N'");
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
                if ($num_ships<2) { echo "There are no other ships in this sector.<BR><BR>";} else
                {
                        echo "The are other ships in this sector (click to scan - if blank, there may be cloaked ships): ";
                        for  ($i=0; $i<$num_ships;$i++)
                        {
				/* display other ships in sector - unless they are successfully cloaked, or they are this player. */
				$success=(10 - $targetinfo[cloak] + $playerinfo[sensors])*5;
				if ($success<5){$success=5;}
				if ($success>95){$success=95;}
				$roll=rand(1,100);

				if ($ships[$i]!=$playerinfo[ship_name] && $roll<$success){
                                        echo "<a href=scan.php3?ship_id=$ship_id[$i]>$ships[$i]</a> (<a href=attack.php3?ship_id=$ship_id[$i]>attack</a>/<a href=mailto.php3?to=$ship_id[$i]>mail</a>)";
                                        if ($i+1!=$num_ships) { echo "  ";}

                                }
                        }
                        echo "<BR><BR>";
                }
            } else { echo "There is so much traffic in Sol (Sector 0) that you cannot even isolate other ships!<BR><BR>"; } 
		if ($sectorinfo[port_type]!="none") {echo "There is a <a href=port.php3>$sectorinfo[port_type] port</a> here.<BR><BR>";}
			if ($sectorinfo[planet]=="Y" && $sectorinfo[sector_id]!=0)
			{
				echo "There is a <a href=planet.php3>planet</a> here ";
				if (empty($sectorinfo[planet_name]))
				{
					echo "with no name ";
				} else {
					echo "named $sectorinfo[planet_name] ";
				}

				if ($sectorinfo[planet_owner]=="")
				{
					echo "and it is unowned.<BR><BR>";
				} else {
					$result5 = mysql_query ("SELECT character_name FROM ships WHERE ship_id=$sectorinfo[planet_owner]");
					$planet_owner_name=mysql_fetch_array($result5);
					echo "owned by <a href=mailto.php3?to=$sectorinfo[planet_owner]>$planet_owner_name[character_name]</a> (#$sectorinfo[planet_owner])<BR><BR>";
				}
			}
			echo "Real Space Presets:  <a href=rsmove.php3?engage=1&destination=$playerinfo[preset1]>$playerinfo[preset1]</a> & <a href=rsmove.php3?engage=1&destination=$playerinfo[preset2]>$playerinfo[preset2]</a> & <a href=rsmove.php3?engage=1&destination=$playerinfo[preset3]>$playerinfo[preset3]</a> - <a href=preset.php3>Change Presets</a><BR><BR>";
			echo "Trade Route Presets:  <a href=traderoute.php3?phase=2&destination=$playerinfo[preset1]>$playerinfo[preset1]</a> & <a href=traderoute.php3?phase=2&destination=$playerinfo[preset2]>$playerinfo[preset2]</a> & <a href=traderoute.php3?phase=2&destination=$playerinfo[preset3]>$playerinfo[preset3]</a><BR><BR>";

			echo "<a href=device.php3>Use Device</a> - <a href=report.php3>Report</a> - <a href=log.php3>View Log</a> - <a href=rsmove.php3>Realspace Move</a> - <a href=traderoute.php3>Trade Route</a> - <a href=mailto2.php3>Send Message</a> - <a href=planet-report.php3>Planet Report</a> -"; 
			echo "<a href=logout.php3>Logout</a> - <a href=options.php3>Options</a> - <a href=ranking.php3>Rankings</a> - <a href=feedback.php3>Feedback</a> - <a href=help.php3>Help!</a> - <a href=http://blacknova.community.everyone.net/commun_v3/scripts/directory.pl target=\"_blank\"> Forums</a><BR><BR>";
	echo "System Time:  ";
	print (date("l dS of F Y h:i:s A")) ;
	echo "<BR>Last System Update:  ";
	$lastupdate=filemtime("/home2/blacknova/cron.txt");
	print (date("l dS of F Y h:i:s A",$lastupdate)) ; 
	echo "<BR>Updates happen every 6 minutes.";
        include("footer.php3");

?> 
