<?
include("extension.inc");
	include("config.php3");
	updatecookie();

	$title="Send a message to the Co-ordinator of an Alliance";
	include("header.php3");

	connectdb();

	if (checklogin()) {die();}

	$result = mysql_query ("SELECT * FROM ships WHERE email='$username'");
	$playerinfo=mysql_fetch_array($result);
	if (!$recipient)
		echo "Huh?<BR>";
	else {
		$result = mysql_query ("SELECT * FROM ships WHERE email='$username'");
		$playerinfo=mysql_fetch_array($result);
		$result = mysql_query ("SELECT * FROM ships WHERE ship_id='$recipient'");
		$recipientinfo=mysql_fetch_array($result);
	     bigtitle();	
		if (empty($content))
		{
			echo "<form action=$PHP_SELF method=post><input type=hidden name=recipient value=$recipient>";
			echo "<table>";
			echo "<tr><td>TO:</td><td><input disabled type=text name=to size=40 maxlength=40 value='$recipientinfo[character_name]'></td></tr>";
			echo "<tr><td>FROM:</td><td><input disabled type=text name=from size=40 maxlength=40 value=\"$playerinfo[character_name] - $playerinfo[email]\"></td></tr>";
			echo "<tr><td>SUBJECT:</td><td><input disabled type=text name=dummy size=40 maxlength=40 value='Alliance post'></td></tr>";
			echo "<tr><td>MESSAGE:</td><td><textarea name=content rows=5 cols=40></textarea></td></tr>";
			echo "<tr><td></td><td><input type=submit value=Send><input type=reset value=Reset></td>";
			echo "</table>";
			echo "</form>";
		} else {
			$res = mysql_query("SELECT email FROM ships WHERE ship_id='$recipient'");
			$address = mysql_fetch_array($res);
			echo "Message Sent<BR><BR>";
			mail("$address[email]", "Alliance post", "Message from $playerinfo[character_name] in the game $game_name.\n\n$content","From: $playerinfo[email]\nX-Mailer: PHP/" . phpversion());
		}
	}

    TEXT_GOTOMAIN();
	include("footer.php3");

?> 
