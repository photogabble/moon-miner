<?
	include("config.php3");
	include($gameroot . $default_lang);

	$title=$l_mail_title;
	include("header.php3");


	bigtitle();
	mysql_connect($dbhost, $dbuname, $dbpass);
	@mysql_select_db("$dbname") or die ("Unable to select database");

	$result = mysql_query ("select email, password from ships where email='$mail'");

	if(mysql_num_rows($result)==1) {
	$playerinfo=mysql_fetch_row($result);
	$l_mail_message=str_replace("[pass]",$playerinfo[1],$l_mail_message);
	mail("$mail", "$l_mail_topic", "$l_mail_message\n\nhttp://$SERVER_NAME","From: webmaster@$SERVER_NAME\nReply-To: webmaster@$SERVER_NAME\nX-Mailer: PHP/" . phpversion());
	echo "$l_mail_sent $mail.";
        } else {
                echo "<b>$l_mail_noplayer</b><br>";
        }

	include("footer.php3");
?>

