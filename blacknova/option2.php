<?

include("config.php");
include_once($gameroot . "/languages/$lang");
connectdb();
if(checklogin())
{
  die();
}
$title = "$l_opt2_title";

if($intrf == "N")
{
  $interface = "main.php";
  setcookie("interface", "main.php");
}
else
{
  $intrf = "O";
  $interface = "maintext.php";
  setcookie("interface", "maintext.php");
}

//-------------------------------------------------------------------------------------------------
mysql_query("LOCK TABLES ships WRITE");

if($newpass1 == $newpass2 && $password == $oldpass && $newpass1 != "")
{
  $userpass = $username."+".$newpass1;
  SetCookie("userpass",$userpass,time()+(3600*24)*365,$gamepath,$gamedomain);
  setcookie("id",$id);
}

$lang=$newlang;
SetCookie("lang",$lang,time()+(3600*24)*365,$gamepath,$gamedomain);
include_once($gameroot . "/languages/$lang");

include("header.php");
bigtitle();

if($newpass1 == "" && $newpass2 == "")
{
  echo $l_opt2_passunchanged;
}
elseif($password != $oldpass)
{
  echo $l_opt2_srcpassfalse;
}
elseif($newpass1 != $newpass2)
{
  echo $l_opt2_newpassnomatch;
}
else
{
  $res = mysql_query("SELECT ship_id,password FROM ships WHERE email='$username'");
  $playerinfo = mysql_fetch_array($res);
  mysql_free_result($res);
  if($oldpass != $playerinfo[password])
  {
    echo $l_opt2_srcpassfalse;
  }
  else
  {
    $res = mysql_query("UPDATE ships SET password='$newpass1' WHERE ship_id=$playerinfo[ship_id]");
    if($res)
    {
      echo $l_opt2_passchanged;
    }
    else
    {
      echo $l_opt2_passchangeerr;
    }
  }
}

$res = mysql_query("UPDATE ships SET interface='$intrf' WHERE email='$username'");
if($res)
{
  echo $l_opt2_userintup;
}
else
{
  echo $l_opt2_userintfail;
}

$res = mysql_query("UPDATE ships SET lang='$lang' WHERE email='$username'");
foreach($avail_lang as $curlang)
{
  if($lang == $curlang[file])
  {
    $l_opt2_chlang = str_replace("[lang]", "$curlang[name]", $l_opt2_chlang);
    
    echo $l_opt2_chlang;
    break;
  }
}

if($dhtml != 'Y')
  $dhtml = 'N';

$res = mysql_query("UPDATE ships SET dhtml='$dhtml' WHERE email='$username'");
if($res)
{
  echo $l_opt2_dhtmlup;
}
else
{
  echo $l_opt2_dhtmlfail;
}

mysql_query("UNLOCK TABLES");
//-------------------------------------------------------------------------------------------------

TEXT_GOTOMAIN();

include("footer.php");

?>
