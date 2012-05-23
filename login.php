<?php

include("config.php");

if(empty($lang))
  $lang=$default_lang;

$found = 0;
if(!empty($newlang))
{
  if(!preg_match("/^[\w]+$/", $lang))
  {
     $lang = $default_lang;

  }
  foreach($avail_lang as $key => $value)
  {
    if($newlang == $value[file])
    {
      $lang=$newlang;
      SetCookie("lang",$lang,time()+(3600*24)*365,$gamepath,$gamedomain);
      $found = 1;
      break;
    }
  }

  if($found == 0)
    $lang = $default_lang;

  $lang = $lang . ".inc";
}

include("languages/$lang");

$title=$l_login_title;

include("header.php");

echo "<center>\n";

bigtitle();

echo "<form action='login2.php' method='post'>\n";
echo "  <br>\n";
echo "  <br>\n";
echo "  <table cellpadding='4' border='0'>\n";
echo "    <tr>\n";
echo "      <td align='right'>{$l_login_email}</td>\n";
echo "      <td align='left'><input type='text' name='email' size='20' maxlength='40' value='{$username}' style='width:200px;'></td>\n";
echo "    </tr>\n";
echo "    <tr>\n";
echo "      <td align='right'>{$l_login_pw}</td>\n";
echo "      <td align='left'><input type='password' name='pass' size='20' maxlength='20' value='{$password}' style='width:200px;'></td>\n";
echo "    </tr>\n";
echo "    <tr>\n";
echo "      <td colspan='2'><center>Forgot your password?  Enter it blank and press login.</center></td>\n";
echo "    </tr>\n";
echo "  </table>\n";

echo "  <br>\n";
echo "  <input type='submit' value='{$l_login_title}'>\n";
echo "  <br>\n";
echo "  <br>\n";
echo "  {$l_login_newp}\n";
echo "  <br>\n";
echo "  <br>\n";
echo "  {$l_login_prbs}<a href='mailto:{$admin_mail}'>{$l_login_emailus}</a>\n";

echo "</form>\n";


if(!empty($link_forums))
{
    echo "<a href='$link_forums' target='_blank'>$l_forums</a> - ";
}
echo "<a href='ranking.php'>{$l_rankings}</a> - <a href='settings.php'>{$l_login_settings}</a>\n";
echo "<br>\n";
echo "<br>\n";
echo "<form action='login.php' method='post'>\n";

echo "  $l_login_lang&nbsp;&nbsp;<select name='newlang'>\n";

foreach($avail_lang as $curlang)
{
  if($curlang['file'].".inc" == $lang)
    $selected = "selected='selected'";
  else
    $selected = "";

  echo "  <option value='{$curlang['file']}' {$selected} style='width:100px;'>{$curlang['name']}</option>\n";
}

echo "  </select>\n&nbsp;&nbsp;<input type='submit' value='{$l_login_change}'>";

echo "</form>\n";
echo "</center>\n";

include("footer.php");
?>
