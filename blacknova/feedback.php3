<?
include("extension.inc");
	include("config.$phpext");
	updatecookie();

	$title="Send Feedback";
	include("header.$phpext");

	connectdb();

	if (checklogin()) {die();}

	$result = mysql_query ("SELECT * FROM ships WHERE email='$username'");
	$playerinfo=mysql_fetch_array($result);
        bigtitle();	
	if (empty($content))
	{
		echo "<form action=feedback.$phpext method=post>";
		echo "<table>";
		echo "<tr><td>TO:</td><td><input disabled type=text name=dummy size=40 maxlength=40 value=GameAdmin></td></tr>";
		echo "<tr><td>FROM:</td><td><input disabled type=text name=dummy size=40 maxlength=40 value=\"$playerinfo[character_name] - $playerinfo[email]\"></td></tr>";
		echo "<tr><td>SUBJECT:</td><td><input disabled type=text name=dummy size=40 maxlength=40 value=Feedback></td></tr>";
		echo "<tr><td>MESSAGE:</td><td><textarea name=content rows=5 cols=40></textarea></td></tr>";
		echo "<tr><td></td><td><input type=submit value=Send><input type=reset value=Clear></td>";
		echo "</table>";
		echo "</form>";
	} else {
		echo "Message Sent<BR><BR>";
		mail("$admin_mail", "WTW Feedback", "IP address - $ip\nGame Name - $playerinfo[character_name]\n\n$content","From: $playerinfo[email]\nX-Mailer: PHP/" . phpversion());
	}

    TEXT_GOTOMAIN();
	include("footer.$phpext");

?> 
