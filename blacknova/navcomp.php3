<?
include("extension.inc");
	include("config.$phpext");
	updatecookie();

	$title="Navigation Computer";
	include("header.$phpext");
	connectdb();
	
	if(checklogin())
  {
    die();
  }

  bigtitle();

  if(!$allow_navcomp)
  {
    echo "Navigation computer is not available<BR><BR>";
    TEXT_GOTOMAIN();
	  include("footer.$phpext");
    die();
  }

	$result = mysql_query ("SELECT * FROM ships WHERE email='$username'");
	$playerinfo=mysql_fetch_array($result);
	$current_sector = $playerinfo['sector'];
	$computer_tech  = $playerinfo['computer'];

	$result2 = mysql_query ("SELECT * FROM universe WHERE sector_id='$current_sector'");
	$sectorinfo=mysql_fetch_array($result2);

	if ($state == 0)
	{
		echo "<FORM ACTION=\"navcomp.$phpext\" METHOD=POST>";
		echo "Enter sector to find path to: <INPUT NAME=\"stop_sector\"><INPUT TYPE=SUBMIT><BR>\n";
		echo "<INPUT NAME=\"state\" VALUE=1 TYPE=HIDDEN>";
		echo "</FORM>\n";
	}
	elseif ($state == 1)
	{
		if ($computer_tech < 5)
		{
			$max_search_depth = 2;
		}
		elseif ($computer_tech < 10)
		{
			$max_search_depth = 3;
		}
		elseif ($computer_tech < 15)
		{
			$max_search_depth = 4;
		}
		elseif ($computer_tech < 20)
		{
			$max_search_depth = 5;
		}
		else
		{
			$max_search_depth = 6;
		}
		#echo "Max Search Depth: $max_search_depth<BR>\n";
		for ($search_depth = 1; $search_depth <= $max_search_depth; $search_depth++)
		{
			#echo "Search Depth: $search_depth\n";
			$search_query = "SELECT	distinct\n	a1.link_start\n	,a1.link_dest \n";
			for ($i = 2; $i<=$search_depth;$i++)
			{
				$search_query = $search_query . "	,a". $i . ".link_dest \n";
			}
			$search_query = $search_query . "FROM\n	 links AS a1 \n";
	
			for ($i = 2; $i<=$search_depth;$i++)
			{
				$search_query = $search_query . "	,links AS a". $i . " \n";
			}
			$search_query = $search_query . "WHERE \n	    a1.link_start = $current_sector \n";
	
			for ($i = 2; $i<=$search_depth; $i++)
			{
				$k = $i-1;
				$search_query = $search_query . "	AND a" . $k . ".link_dest = a" . $i . ".link_start \n";
			}
			$search_query = $search_query . "	AND a" . $search_depth . ".link_dest = $stop_sector \n";
			$search_query = $search_query . "	AND a1.link_dest != a1.link_start \n";
			for ($i=2; $i<=$search_depth;$i++)
			{
				$search_query = $search_query . "	AND a" . $i . ".link_dest not in (a1.link_dest, a1.link_start ";
		
				for ($j=2; $j<$i;$j++)
				{
					$search_query = $search_query . ",a".$j.".link_dest ";
				}
				$search_query = $search_query . ")\n";
			}
			$search_query = $search_query . "ORDER BY a1.link_start, a1.link_dest ";
			for ($i=2;$i<=$search_depth;$i++)
			{
				$search_query = $search_query . ", a" . $i . ".link_dest";
			}
			$search_query = $search_query . " \nLIMIT 1";
			#echo "$search_query\n\n";
			$search_result = mysql_query ($search_query) or die ("Invalid Query");
			$found = mysql_num_rows($search_result);
			if ($found > 0)
			{
				break;
			}

			
		}
		if ($found > 0)
		{
			echo "<H3>Path Found</H3>\n";
			$links=mysql_fetch_array($search_result);
			echo $links[0];
			for ($i=1;$i<$search_depth+1;$i++)
			{
				echo " >> " . $links[$i];
			}
			echo "<BR><BR>";
			echo "It will take you $search_depth turns to get to this sector.<BR><BR>";
		}
		else
		{
			echo "Your computer technology is too primitive to compute a warp route to that distance.<BR><BR>";
		}
	}

    TEXT_GOTOMAIN();
	include("footer.$phpext");

?>
