<?
include("extension.inc");
include("config.$phpext");

connectdb();
if(checklogin())
{
  die();
}
$title = "Save Options";

if($intrf == "N")
{
  $interface = "main.$phpext";
  setcookie("interface", "main.$phpext");
}
else
{
  $intrf = "O";
  $interface = "maintext.$phpext";
  setcookie("interface", "maintext.$phpext");
}

//-------------------------------------------------------------------------------------------------
mysql_query("LOCK TABLES ships WRITE");

if($newpass1 == $newpass2 && $password == $oldpass && $newpass1 != "")
{
  $userpass = $username."+".$newpass1;
  SetCookie("userpass",$userpass,time()+(3600*24)*365,$gamepath,$gamedomain);
  setcookie("id",$id);
}

include("header.$phpext");
bigtitle();

if($newpass1 == "" && $newpass2 == "")
{
  echo "Password was left unchanged.<BR><BR>";
}
elseif($password != $oldpass)
{
  echo "Original password incorrect. Password was left unchanged.<BR><BR>";
}
elseif($newpass1 != $newpass2)
{
  echo "New password fields do not match. Password was left unchanged.<BR><BR>";
}
else
{
  $res = mysql_query("SELECT ship_id,password FROM ships WHERE email='$username'");
  $playerinfo = mysql_fetch_array($res);
  mysql_free_result($res);
  if($oldpass != $playerinfo[password])
  {
    echo "Original password incorrect.  Password was left unchanged.<BR><BR>";
  }
  else
  {
    $res = mysql_query("UPDATE ships SET password='$newpass1' WHERE ship_id=$playerinfo[ship_id]");
    if($res)
    {
      echo "Password changed.<BR><BR>";
    }
    else
    {
      echo "Error changing password<BR><BR>";
    }
  }
}

$res = mysql_query("UPDATE ships SET interface='$intrf' WHERE email='$username'");
if($res)
{
  echo "User interface setting updated.<BR><BR>";
}
else
{
  echo "Failed to update user interface setting.<BR><BR>";
}

mysql_query("UNLOCK TABLES");
//-------------------------------------------------------------------------------------------------

TEXT_GOTOMAIN();

include("footer.$phpext");

?>
