<?

function QUERYOK($res)
{
  if($res)
  {
    echo " ok.<BR>";
  }
  else
  {
    die(" FAILED.");
  }
}

function get_player_name($userid)
{
  $query = mysql_query("select character_name from ships where ship_id='$userid'");
  $name = mysql_fetch_array($query);

  return $name[character_name];
}

?>