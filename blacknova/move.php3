<?
        include("config.php3");
        updatecookie();

        $title="Move";

	echo "<!doctype html public \"-//w3c//dtd html 3.2//en\"><html><head><title>$title</title></head>";


        connectdb();

        if (checklogin()) {die();}


                $result = mysql_query ("SELECT * FROM ships WHERE email='$username'");
                $playerinfo=mysql_fetch_array($result);

	if ($playerinfo[turns]<1)
	{
		echo "You need at least one turn to move.<BR><BR>";
	    TEXT_GOTOMAIN();
		include("footer.php3");		
		die();
	}

                $result2 = mysql_query ("SELECT * FROM universe WHERE sector_id='$playerinfo[sector]'");
                $sectorinfo=mysql_fetch_array($result2);

                $result3 = mysql_query ("SELECT * FROM links WHERE link_start='$playerinfo[sector]'");
                $i=0;
                $flag=0;
                if ($result3>0)
                {
                        while ($row = mysql_fetch_array($result3))
                        {
                                if ($row[link_dest]==$sector && $row[link_start]==$playerinfo[sector]) {$flag=1;}
                                $i++;

                        }
                }
                if ($flag==1)
                {
                        $ok=1;
			$query="UPDATE ships SET turns=turns-1, turns_used=turns_used+1, sector=$sector where ship_id=$playerinfo[ship_id]";
                        $move_result = mysql_query ("$query");
			if (!$move_result)
			{
				$error = mysql_error($move_result);
				mail ("harwoodr@cgocable.net","Move Error", "Start Sector: $sectorinfo[sector_id]\nEnd Sector: $sector\nPlayer: $playerinfo[character_name] - $playerinfo[ship_id]\n\nQuery:  $query\n\nMySQL error: $error");
			}
                        /* enter code for checking dangers in new sector */
                        if ($ok=1) {echo "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"0;URL=$interface\">";} else
                        {
                                echo "report bad stuff here!";
                        }
                } else {
                        echo "Move failed!<BR><BR>";
					    TEXT_GOTOMAIN();
                }

	echo "</body></html>";

?>
