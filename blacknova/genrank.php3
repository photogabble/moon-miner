<?

include("config.php3");
updatecookie();

$title="Player Ranking";
include("header.php3");

connectdb();

bigtitle();

$res = mysql_query("SELECT ship_id FROM ships");
while($row = mysql_fetch_array($res))
{
  gen_score($row[ship_id]);
}

echo "<BR><BR>";
TEXT_GOTOMAIN();

include("footer.php3");

?>
