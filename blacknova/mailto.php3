<?

include("config.php3");
updatecookie();

include_once($gameroot . "/languages/$lang");

$title="Send Message";
include("header.php3");

connectdb();

if(checklogin())
{
  die();
}

$res = mysql_query("SELECT * FROM ships WHERE email='$username'");
$playerinfo = mysql_fetch_array($res);
mysql_free_result($res);

bigtitle();

if(empty($content))
{
  $res = mysql_query("SELECT character_name FROM ships ORDER BY character_name ASC");
  echo "<FORM ACTION=mailto2.php3 METHOD=POST>";
  echo "<TABLE>";
  echo "<TR><TD>To:</TD><TD><SELECT NAME=to>";
  while($row = mysql_fetch_array($res))
  {
    echo "<OPTION>$row[character_name]";
  }
  mysql_free_result($res);
  echo "</SELECT></TD></TR>";
  echo "<TR><TD>From:</TD><TD><INPUT DISABLED TYPE=TEXT NAME=dummy SIZE=40 MAXLENGTH=40 VALUE=\"$playerinfo[character_name]\"></TD></TR>";
  echo "<TR><TD>Subject:</TD><TD><INPUT TYPE=TEXT NAME=subject SIZE=40 MAXLENGTH=40></TD></TR>";
  echo "<TR><TD>Message:</TD><TD><TEXTAREA NAME=content ROWS=5 COLS=40></TEXTAREA></TD></TR>";
  echo "<TR><TD></TD><TD><INPUT TYPE=SUBMIT VALUE=Send><INPUT TYPE=RESET VALUE=Clear></TD>";
  echo "</TABLE>";
  echo "</FORM>";
}
else
{
  echo "Message Sent<BR><BR>";
#  $res = mysql_query("SELECT email FROM ships WHERE character_name='$to'");
#  $address = mysql_fetch_array($res);
#  mysql_free_result($res);
#  mail($address[email], $subject, "Message from ".$playerinfo[character_name]." in the ".$game_name." Game.\n\n".$content,"From: ".$playerinfo[email]."\nX-Mailer: PHP/" . phpversion());

  $res = mysql_query("SELECT * FROM ships WHERE character_name='$to'");
  $target_info = mysql_fetch_array($res);
  mysql_query("INSERT INTO messages (sender_id, recp_id, subject, message) VALUES ('".$playerinfo[ship_id]."', '".$target_info[ship_id]."', '".$subject."', '".$content."')");
  #using this three lines to get recipients ship_id and sending the message -- blindcoder

}

TEXT_GOTOMAIN();

include("footer.php3");

?> 

