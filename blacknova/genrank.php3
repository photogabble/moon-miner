<?

include("config.php3");
updatecookie();

$title="Player Ranking";
include("header.php3");

if($swordfish != $adminpass)
{
  echo "<FORM ACTION=genrank.php3 METHOD=POST>";
  echo "Password: <INPUT TYPE=PASSWORD NAME=swordfish SIZE=20 MAXLENGTH=20><BR><BR>";
  echo "<INPUT TYPE=SUBMIT VALUE=Submit><INPUT TYPE=RESET VALUE=Reset>";
  echo "</FORM>";
}
else
{
  connectdb();

  bigtitle();

  $res = mysql_query("SELECT ship_id FROM ships");
  while($row = mysql_fetch_array($res))
  {
    gen_score($row[ship_id]);
  }

  echo "<BR><BR>";
  TEXT_GOTOMAIN();
}
include("footer.php3");

?>
