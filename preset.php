<?php


include("config.php");
updatecookie();

include("languages/$lang");
$title = "$l_pre_title";

include("header.php");

connectdb();

if(checklogin())
{
  die();
}

$result = $db->Execute("SELECT * FROM $dbtables[ships] WHERE email='$username'");
$playerinfo = $result->fields;

bigtitle();

if(!isset($change))
{
  echo "<form action='preset.php' method='post'>";
  echo "<div style='padding:2px;'>Preset 1: <input type='text' name='preset1' size='6' maxlength='6' value='{$playerinfo['preset1']}'></div>";
  echo "<div style='padding:2px;'>Preset 2: <input type='text' name='preset2' size='6' maxlength='6' value='{$playerinfo['preset2']}'></div>";
  echo "<div style='padding:2px;'>Preset 3: <input type='text' name='preset3' size='6' maxlength='6' value='{$playerinfo['preset3']}'></div>";
  echo "<input type='hidden' name='change' value='1'>";
  echo "<div style='padding:2px;'><input type='submit' value={$l_pre_save}></div>";
  echo "</FORM>";
}
else
{
  $preset1 = round(abs($preset1));
  $preset2 = round(abs($preset2));
  $preset3 = round(abs($preset3));
  if($preset1 >= $sector_max)
  {
    $l_pre_exceed = str_replace("[preset]", "1", $l_pre_exceed);
    $l_pre_exceed = str_replace("[sector_max]", ($sector_max-1), $l_pre_exceed);
    echo $l_pre_exceed;
  }
  elseif($preset2 >= $sector_max)
  {
    $l_pre_exceed = str_replace("[preset]", "2", $l_pre_exceed);
    $l_pre_exceed = str_replace("[sector_max]", ($sector_max-1), $l_pre_exceed);
    echo $l_pre_exceed;
  }
  elseif($preset3 >= $sector_max)
  {
    $l_pre_exceed = str_replace("[preset]", "3", $l_pre_exceed);
    $l_pre_exceed = str_replace("[sector_max]", ($sector_max-1), $l_pre_exceed);
    echo $l_pre_exceed;
  }
  else
  {
    $update = $db->Execute("UPDATE $dbtables[ships] SET preset1=$preset1,preset2=$preset2,preset3=$preset3 WHERE ship_id=$playerinfo[ship_id]");
    $l_pre_set = str_replace("[preset1]", "<a href=rsmove.php?engage=1&destination=$preset1>$preset1</a>", $l_pre_set);
    $l_pre_set = str_replace("[preset2]", "<a href=rsmove.php?engage=1&destination=$preset2>$preset2</a>", $l_pre_set);
    $l_pre_set = str_replace("[preset3]", "<a href=rsmove.php?engage=1&destination=$preset3>$preset3</a>", $l_pre_set);
    echo $l_pre_set;
  }
}

TEXT_GOTOMAIN();

include("footer.php");

?>


