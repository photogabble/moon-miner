<?

include("extension.inc");
include("config.php3");

updatecookie();

$title=("Corporation Menu");
include("header.php3");

connectdb();
if (checklogin())
{
	die();
}

//------------------------------------
mysql_query("LOCK TABLES ships WRITE, planets WRITE, zones WRITE, teams READ, universe WRITE");
$result = mysql_query("SELECT * FROM ships WHERE email='$username'");
$playerinfo=mysql_fetch_array($result);

$result2 = mysql_query("SELECT * FROM planets WHERE planet_id=$planet_id");
if($result2)

  $planetinfo=mysql_fetch_array($result2);

if ($planetinfo[owner] == $playerinfo[ship_id] || ($planetinfo[corp] == $playerinfo[team] && $playerinfo[team] >> 0))

{

bigtitle();

	if ($action == "planetcorp")
	{
		echo ("Planet is now a Corporate Planet!<BR>");
		$result = mysql_query("UPDATE planets SET corp='$playerinfo[team]', owner=$playerinfo[ship_id] WHERE planet_id=$planet_id");
    $ownership = calc_ownership($playerinfo[sector]);

      if(!empty($ownership))

        echo "<p>$ownership<p>";

		
	}
	if ($action == "planetpersonal")
	{
		echo ("Planet is now a Personal Planet!<BR>");
		$result = mysql_query("UPDATE planets SET corp='0', owner=$playerinfo[ship_id] WHERE planet_id=$planet_id");
    $ownership = calc_ownership($playerinfo[sector]);

      if(!empty($ownership))

        echo "<p>$ownership<p>";

	}
TEXT_GOTOMAIN();

}

else

{

adminlog($playerinfo[ship_id], "$playerinfo[ship_name]'s attempted to use an exploit to gain ownership of planet in sector $sectorinfo[sector_id]");

echo ("<BR>You intercept a garbled borg message on your scanner... you should probably start worrying...<BR>");

TEXT_GOTOMAIN();

}


include("footer.php3");

?>
