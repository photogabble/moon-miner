<? 
	$title="Login"; 
	include("header.php3");
        include("config.php3");
?>

<center><BR><img src=images/blacknova-traders.jpg><BR><BR><H1>Welcome to <? echo "$game_name"; ?></H1><BR>
Click <a href=login.php3>here</a> to login.
<BR><BR>
Problems? <a href=mailto:<? echo "$admin_mail"; ?>>Mail us.</a><BR><BR>There are bugs - this is very beta right now.<BR><BR>
<a href=lastusers.php3>Last Users</a> <a href=ranking.php3>Current Scores</a> <a href=settings.php>Game Settings</a> <BR>
</center>


<? 

	include("footer.php3");

?>
