<?
include("config.php3");
updatecookie();

$title="Move";

include("header.php3");

//Connect to the database
connectdb();

//Check to see if the user is logged in
if (checklogin())
{
    die();
}

//Retrieve the user and ship information
$result = mysql_query ("SELECT * FROM ships WHERE email='$username'");
//Put the player information into the array: "playerinfo"
$playerinfo=mysql_fetch_array($result);

//Check to see if the player has less than one turn available
//and if so return to the main menu
if ($playerinfo[turns]<1)
{
	echo "You need at least one turn to move.<BR><BR>";
	TEXT_GOTOMAIN();
	include("footer.php3");
	die();
}

//Retrieve all the sector information about the current sector
$result2 = mysql_query ("SELECT * FROM universe WHERE sector_id='$playerinfo[sector]'");
//Put the sector information into the array "sectorinfo"
$sectorinfo=mysql_fetch_array($result2);

//Retrive all the warp links out of the current sector
$result3 = mysql_query ("SELECT * FROM links WHERE link_start='$playerinfo[sector]'");
$i=0;
$flag=0;
if ($result3>0)
{
    //loop through the available warp links to make sure it's a valid move
    while ($row = mysql_fetch_array($result3))
    {
        if ($row[link_dest]==$sector && $row[link_start]==$playerinfo[sector])
        {
            $flag=1;
        }
        $i++;
    }
}

//Check if there was a valid warp link to move to
if ($flag==1)
{
    $ok=1;
$stamp = date("Y-m-d H-i-s");
	$query="UPDATE ships SET last_login='$stamp',turns=turns-1, turns_used=turns_used+1, sector=$sector where ship_id=$playerinfo[ship_id]";
    $move_result = mysql_query ("$query");
	if (!$move_result)
	{
		$error = mysql_error($move_result);
		mail ("harwoodr@cgocable.net","Move Error", "Start Sector: $sectorinfo[sector_id]\nEnd Sector: $sector\nPlayer: $playerinfo[character_name] - $playerinfo[ship_id]\n\nQuery:  $query\n\nMySQL error: $error");
	}

    /* enter code for checking dangers in new sector */
    $calledfrom = "move.php3";
    include("check_fighters.php3");
    include("check_mines.php3");
    if ($ok==1) {echo "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"0;URL=$interface\">";} else
    {
        TEXT_GOTOMAIN();
    }
}
else
{
    echo "Move failed!<BR><BR>";
    TEXT_GOTOMAIN();
}

echo "</body></html>";

?>
