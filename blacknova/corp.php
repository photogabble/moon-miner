<?


include("config.php3");

updatecookie();

include_once($gameroot . "/languages/$lang");
$title=$l_corpm_title;;
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
		echo ("$l_corpm_tocorp<BR>");
		$result = mysql_query("UPDATE planets SET corp='$playerinfo[team]', owner=$playerinfo[ship_id] WHERE planet_id=$planet_id");
    $ownership = calc_ownership($playerinfo[sector]);

      if(!empty($ownership))

        echo "<p>$ownership<p>";


	}
	if ($action == "planetpersonal")
	{
		echo ("$l_corpm_topersonal<BR>");
		$result = mysql_query("UPDATE planets SET corp='0', owner=$playerinfo[ship_id] WHERE planet_id=$planet_id");
    $ownership = calc_ownership($playerinfo[sector]);

      if(!empty($ownership))

        echo "<p>$ownership<p>";

	}
TEXT_GOTOMAIN();
}
else
{
echo ("<BR>$l_corpm_exploit<BR>");
TEXT_GOTOMAIN();
}


include("footer.php3");

?>
