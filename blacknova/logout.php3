<?


include("config.php3");
$title = "Logout"; 

SetCookie("userpass","",0,$gamepath,$gamedomain);
SetCookie("userpass","",0); // Delete from default path as well.
setcookie("username","",0); // Legacy support, delete the old login cookies.
setcookie("password","",0); // Legacy support, delete the old login cookies.
setcookie("id","",0);
setcookie("res","",0);

include("header.php3");

connectdb();

$result = mysql_query("SELECT * FROM ships WHERE email='$username'");
$playerinfo = mysql_fetch_array($result);

$current_score = gen_score($playerinfo[ship_id]);

bigtitle();
echo "You current score is $current_score.<BR>";
echo "$username is now logged out.  Click <A HREF=index.php3>here</A> to return to game.";

include("footer.php3");

?>
