<?
include("config.php3");

connectdb();

if($intrf == "N")
{
  $interface = "main.php3";
  setcookie("interface", "main.php3");
}
else
{
  $intrf = "O";
  $interface = "maintext.php3";
  setcookie("interface", "maintext.php3");
}

$title = "Change Options";
include("header.php3");

echo "<CENTER>";
bigtitle();
echo "</CENTER>";

$result3 = mysql_query("UPDATE ships SET interface='$intrf' WHERE email='$username'");

if($result3)
{
  echo "<CENTER><H2>Interface has been updated</H2></CENTER>";
}
else
{
  echo "<CENTER><H2>Error updating interface!</H2></CENTER>";
}
if($newpass1 == "" && $newpass2 == "")
{
  TEXT_GOTOMAIN();
  die();
}

if($newpass1 == $newpass2 && $password == $oldpass && $newpass1 != "")
{
  setcookie("username", $username);
  setcookie("password", $newpass1);
  setcookie("id", $id);
}
else
{
  $title = "Password Problem";
  include("header.php3");
  echo "<CENTER><H2>PASSWORD PROBLEM</H2><BR><BR>";
  echo "$password - $oldpass - $newpass1 - $newpass2<BR><BR>";
  if($password != $oldpass)
  {
    echo "Original password incorrect!<BR><BR>";
  }
  if($newpass1 != $newpass2)
  {
    echo "New password did not match re-entered password.<BR><BR>";
  }
  if($newpass1 == "" && $newpass2 == "")
  {
    echo "Blank passwords are not allowed!<BR><BR>";
  }
  echo "Click <a href=options.php3>here</a> to go back.";
  include("footer.php3");
}

$result = mysql_query("SELECT * FROM ships WHERE email='$username'");
$playerinfo = mysql_fetch_array($result);
if($oldpass != $playerinfo[password])
{
  echo "Original password incorrect!<BR><BR>";
  die();
}
$result2 = mysql_query("UPDATE ships SET password='$newpass1' WHERE ship_id=$playerinfo[ship_id]");

if($result2)
{
  echo "Password has been changed.<BR><BR>";
  TEXT_GOTOMAIN();
}
else
{
  echo "Error changing password!";
}

include("footer.php3");

?>
