<?
	include("config.php3");
	updatecookie();

	$title="Send Message";
	include("header.php3");

	connectdb();

	if (checklogin()) {die();}

	$result = mysql_query ("SELECT * FROM ships WHERE email='$username'");
	$playerinfo=mysql_fetch_array($result);
        bigtitle();
	
	
	if (empty($content))
	{
		$result2 = mysql_query ("SELECT character_name FROM ships");
		echo "<form action=mailto2.php3 method=post>";
		echo "<table>";
		$num_players = mysql_num_rows($result2);
		echo "<tr><td>TO:</td><td><select name=to>";
		for ($i=1; $i<=$num_players ; $i++)
		{
			$row=mysql_fetch_array($result2);
			$names[$i]=$row[character_name];
		}
		sort($names);
		for ($i=0; $i<=$num_players ; $i++)
		{
			echo "<option>$names[$i]";
		}
		echo "</select></td></tr>";
		echo "<tr><td>FROM:</td><td><input disabled type=text name=dummy size=40 maxlength=40 value=\"$playerinfo[character_name] - $playerinfo[email]\"></td></tr>";
		echo "<tr><td>SUBJECT:</td><td><input type=text name=subject size=40 maxlength=40></td></tr>";
		echo "<tr><td>MESSAGE:</td><td><textarea name=content rows=5 cols=40></textarea></td></tr>";
		echo "<tr><td></td><td><input type=submit value=Send><input type=reset value=Clear></td>";
		echo "</table>";
		echo "</form>";
	} else {
		echo "Message Sent<BR><BR>";
		$result3= mysql_query ("SELECT email FROM ships WHERE character_name='$to'");
		$address=mysql_fetch_array($result3);
		mail("$address[email]", "$subject", "Message from $playerinfo[character_name] in the $game_name Game.\n\n$content","From: $playerinfo[email]\nX-Mailer: PHP/" . phpversion());
	}

	echo "Click <a href=main.php3>here</a> to return to main menu.";
	include("footer.php3");

?> 
