<?

include("config.php");
include("languages/$lang");

$title="Game Settings";
include("header.php");


bigtitle();

//-------------------------------------------------------------------------------------------------

  
  echo "<TABLE BORDER=0 CELLSPACING=0 CELLPADDING=2>";
  echo "<TR BGCOLOR=\"$color_line1\"><TD>Minimum Hullsize needed to hit mines</TD><TD>$mine_hullsize</TD></TR>";
  echo "<TR BGCOLOR=\"$color_line2\"><TD>Hullsize When Emergency Warp Degrades</TD><TD>$ewd_maxhullsize</TD></TR>";
  echo "<TR BGCOLOR=\"$color_line1\"><TD>Number of Sectors</TD><TD>$sector_max</TD></TR>";
  echo "<TR BGCOLOR=\"$color_line2\"><TD>Maximum Links per sector</TD><TD>$link_max</TD></TR>";
  echo "<TR BGCOLOR=\"$color_line1\"><TD>Maximum Hull Size for Federation Sectors</TD><TD>$fed_max_hull</TD></TR>";
  $bank_enabled = $allow_ibank ? "Yes" : "No";
  echo "<TR BGCOLOR=\"$color_line2\"><TD>Intergalactic Bank Enabled</TD><TD>$bank_enabled</TD></TR>";
  $rate = $ibank_interest * 100;
  echo "<TR BGCOLOR=\"$color_line1\"><TD>IGB Interest rate per update</TD><TD>$rate</TD></TR>";
  $rate = $ibank_loaninterest * 100;
  echo "<TR BGCOLOR=\"$color_line2\"><TD>IGB Loan rate per update</TD><TD>$rate</TD></TR>";
  echo "<TR BGCOLOR=\"$color_line1\"><TD>Tech Level upgrade for Bases</TD><TD>$basedefense</TD></TR>";
  echo "<TR BGCOLOR=\"$color_line2\"><TD>Colonists Limit</TD><TD>$colonist_limit</TD></TR>";
  echo "<TR BGCOLOR=\"$color_line1\"><TD>Maximum number of accumulated turns</TD><TD>$max_turns</TD></TR>";
  echo "<TR BGCOLOR=\"$color_line2\"><TD>Maximum number of planets per sector</TD><TD>$max_planets_sector</TD></TR>";
  echo "<TR BGCOLOR=\"$color_line1\"><TD>Maximum number of traderoutes per player</TD><TD>$max_traderoutes_player</TD></TR>";
  echo "<TR BGCOLOR=\"$color_line2\"><TD>Colonist Production Rate</TD><TD>$colonist_production_rate</TD></TR>";
  echo "<TR BGCOLOR=\"$color_line1\"><TD>Unit of Energy used per sector fighter</TD><TD>$energy_per_fighter</TD></TR>";
  $rate = $defence_degrade_rate*100;
  echo "<TR BGCOLOR=\"$color_line2\"><TD>Sector fighter degredation percentage rate</TD><TD>$rate</TD></TR>";
  echo "<TR BGCOLOR=\"$color_line1\"><TD>Number of planets with bases need for sector ownership</TD><TD>$min_bases_to_own</TD></TR>";


  echo "</TABLE>";


echo "<BR>";

if(empty($username))
{
  TEXT_GOTOLOGIN();
}
else
{
  TEXT_GOTOMAIN();
}

include("footer.php");

?>
