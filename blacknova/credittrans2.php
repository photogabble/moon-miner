<?
include("config.php");
updatecookie();

$title="Credit Sweeper";
include("languages/$lang");

include("header.php");

connectdb();

if(checklogin())
{
  die();
}

//-------------------------------------------------------------------------------------------------


bigtitle();

// Get the Player Array Data into $playerinfo	
	$result = mysql_query("SELECT * FROM $dbtables[ships] WHERE email='$username'");
	$playerinfo = mysql_fetch_array($result);
	mysql_free_result($result);

// Get the Player's Planets that meet the criteria into an array called $planets
	$result = mysql_query("SELECT * FROM $dbtables[planets] WHERE owner=$playerinfo[ship_id] AND credits<$maxamount");
//---
	$i=0;
	 while ($row = mysql_fetch_array($result))
  {
    $planets[$i] = $row;
    if($planets[$i][name] == "")
      $planets[$i][name] = "Unnamed";
    $i++;
  }
//---
	$num_planets = mysql_num_rows($result);
	mysql_free_result($result);
	$totalcredits=0;
	$i=0;
	while ($i < $num_planets)
	{
		$totalcredits=$totalcredits + $planets[$i][credits];
		$i++;
	}
	$fee=.005*$totalcredits;  //the rate I set in is .5%, it can be changed
	settype($fee, "integer");
	$transfercredits=$totalcredits-$fee;
	echo "
		<br><br>You have a total of $num_planets planets that have less than
		$maxamount credits.  <br>They have a total of $totalcredits on them.
		Your fee will be $fee credits, leaving you with a total to be transfered 
		into your ship of $transfercredits.<br><br>
		Do you wish to complete the transfer? <a href=\"credittrans3.php?maxamount=$maxamount\">YES!</a><br><br>";
	
	
//---------------------------------------------------------------------------------
  TEXT_GOTOMAIN();
  include("footer.php");
  die();
  ?>