<?
	include("config.php3");
	updatecookie();

	$title="Send Message";
	include("header.php3");
	connectdb();

	if (checklogin()) {die();}

	$to=intval(abs($to));

	$result = mysql_query ("SELECT * FROM ships WHERE email='$username'");
	$playerinfo=mysql_fetch_array($result);

	$result2 = mysql_query ("SELECT * FROM ships WHERE ship_id=$to");
	$targetinfo=mysql_fetch_array($result2);
        bigtitle();
	if (empty($content))
	{
		echo "<form action=mailto.php3?to=$to method=post>";
		echo "<table>";
		echo "<tr><td>TO:</td><td>$targetinfo[character_name]</td></tr>";
		echo "<tr><td>FROM:</td><td>$playerinfo[character_name] in Traders ($playerinfo[email])</td></tr>";
		echo "<tr><td>SUBJECT:</td><td><input type=text name=subject size=40 maxlength=40></td></tr>";
		echo "<tr><td>CONTENT:</td><td><textarea name=content rows=5 cols=40></textarea></td></tr>";
		echo "<input type=hidden name=to value=$to>";	
		echo "<tr><td></td><td><input type=submit value=Send><input type=reset value=Reset></td></tr>";
		echo "</table>";
		echo "</form>";
	} else {
		echo "<table>";
		echo "<tr><td>TO:</td><td>$targetinfo[character_name]</td></tr>";
		echo "<tr><td>FROM:</td><td>$playerinfo[character_name] in Traders ($playerinfo[email])</td></tr>";
		echo "<tr><td>SUBJECT:</td><td>$subject</td></tr>";
		echo "<tr><td></td><td>$content</td></tr>";
		echo "</table>";
		mail("$targetinfo[email]", "$subject", "Message from $playerinfo[character_name] in the $game_name Game.\n\n$content","From: $playerinfo[email]\nX-Mailer: PHP/" . phpversion());

		echo "<BR><BR>Message has been sent.";
	}	
	echo "Click <a href=main.php3>here</a> to return to main menu.";

	include("footer.php3");
?>

