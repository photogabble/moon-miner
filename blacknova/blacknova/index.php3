<? 
	$title="Login"; 
	include("header.php3");
        include("config.php3");
?>

<center><BR><img src=images/blacknova-traders.jpg><BR><BR><H1>Welcome to <? echo "$game_name"; ?></H1><BR>
Click <a href=login.php3>here</a> to login.
<BR><BR>
Problems? <a href=mailto:<? echo "$admin_mail"; ?>>Mail us.</a><BR><BR>There are bugs - this is very beta right now.<BR><BR</center>


<? 

	include("footer.php3");

?>
