<?
	include("config.php");
	updatecookie();

  include_once($gameroot . "/languages/$lang");
	$title=$l_ewd_title;
	include("header.php");

	connectdb();

	if (checklogin()) {die();}

	$result = mysql_query ("SELECT * FROM ships WHERE email='$username'");
	$playerinfo=mysql_fetch_array($result);
	srand((double)microtime()*1000000);
        bigtitle();
	if ($playerinfo[dev_emerwarp]>0)
	{
		$dest_sector=rand(0,$sector_max);
		$result_warp = mysql_query ("UPDATE ships SET sector=$dest_sector, dev_emerwarp=dev_emerwarp-1 WHERE ship_id=$playerinfo[ship_id]");
		$l_ewd_used=str_replace("[sector]",$dest_sector,$l_ewd_used);
		echo "$l_ewd_used<BR><BR>";
	} else {
		echo "$l_ewd_none<BR><BR>";
	}

    TEXT_GOTOMAIN();

	include("footer.php");

?>
