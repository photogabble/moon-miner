<?
include("extension.inc");
	$title="Mail Password"; 
	include("header.$phpext");

	include("config.$phpext");
	bigtitle();
	mysql_connect($dbhost, $dbuname, $dbpass);
	@mysql_select_db("$dbname") or die ("Unable to select database");
	
	$result = mysql_query ("select email, password from ships where email='$mail'");
	
	if(mysql_num_rows($result)==1) {
	$playerinfo=mysql_fetch_row($result);
	mail("$mail", "$game_name Password", "Greetings,\n\nSomeone from the IP address $ip requested that your password for $game_name be sent to you.\n\nYour password is: $playerinfo[1]\n\nThank you\n\nThe $game_name web team.\n\nhttp://$SERVER_NAME","From: webmaster@$SERVER_NAME\nReply-To: webmaster@$SERVER_NAME\nX-Mailer: PHP/" . phpversion());
	echo "Password has been sent to $mail.";
        } else {
                echo "<b>No Such Player! - Create a new player <a href=new.$phpext>here</a>.</b><br>";
        }

	include("footer.$phpext");
?>

