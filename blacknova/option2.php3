<?
include("config.php3");

connectdb();

if($intrf=="N")
{
	$interface="main.php3";
	setcookie("interface", "main.php3");
}
else
{
	$intrf="O";
	$interface="maintext.php3";
	setcookie("interface", "maintext.php3");
}

$title="Change Options";
include("header.php3");

echo "<center>";
bigtitle();
echo "</center>";

$result3 = mysql_query ("update ships set interface='$intrf' where email='$username'");

if ($result3) {echo "<center><H2>Interface has been updated</H2></center>";}
else {echo "<center><H2>Error updating interface!</H2></center>";}
if($newpass1=="" && $newpass2=="")
{
	echo "Click <a href=$interface>here</a> to continue.";
	die();
}

if ($newpass1==$newpass2 && $password==$oldpass && $newpass1!="")
{
	setcookie("username", $username);
	setcookie("password", $newpass1);
	setcookie("id", $id);
	} else {
	$title="Password Problem";
	include("header.php3");
	echo "<center><H2>PASSWORD PROBLEM</H2><BR><BR>";
	echo "$password - $oldpass - $newpass1 - $newpass2<BR><BR>";
	if ($password!=$oldpass) {echo "Original password incorrect!<BR><BR>";}
	if ($newpass1!=$newpass2) {echo "New password did not match re-entered password.<BR><BR>";}
	if ($newpass1=="" && $newpass2=="") {echo "Blank passwords are not allowed!<BR><BR>";}
	echo "Click <a href=options.php3>here</a> to go back.";
	include("footer.php3");
}



$result= mysql_query ("select * from ships where email='$username'");
$playerinfo=mysql_fetch_array($result);
If ($oldpass!=$playerinfo[password]) {echo "Original password incorrect!<BR><BR>"; die();}
$result2 = mysql_query ("update ships set password='$newpass1' where ship_id=$playerinfo[ship_id]");

if ($result2) {echo "Password has been changed.  Click <a href=$interface>here</a> to continue.";}
else {echo "Error changing password!";}

include("footer.php3");

?>

