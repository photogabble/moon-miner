<?


include("config.php3");
updatecookie();

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
  $res2 = mysql_query("SELECT team_name FROM teams ORDER BY team_name ASC");
  echo "<FORM ACTION=mailto2.php3 METHOD=POST>";
  echo "<TABLE>";
  echo "<TR><TD>To:</TD><TD><SELECT NAME=to>";
  while($row = mysql_fetch_array($res))
  {
    echo "<OPTION>$row[character_name]</OPTION>";
  }
  while($row2 = mysql_fetch_array($res2))
  {
    echo "<OPTION>Alliance: $row2[team_name]</OPTION>";
  }

  mysql_free_result($res);
  mysql_free_result($res2);
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

if (strpos($to, "Alliance:")===false) {
  $res = mysql_query("SELECT * FROM ships WHERE character_name='$to'");
  $target_info = mysql_fetch_array($res);
  mysql_query("INSERT INTO messages (sender_id, recp_id, subject, message) VALUES ('".$playerinfo[ship_id]."', '".$target_info[ship_id]."', '".$subject."', '".$content."')");
     } else {
     $to = str_replace ("Alliance:", "", $to);
     $to = trim($to);
     $to = addslashes($to);
     $res = mysql_query("SELECT id FROM teams WHERE team_name='$to'");
     $row = mysql_fetch_array($res);

     $res2 = mysql_query("SELECT * FROM ships where team='$row[id]'");

     while ($row2 = mysql_fetch_array($res2)) {
           mysql_query("INSERT INTO messages (sender_id, recp_id, subject, message) VALUES ('".$playerinfo[ship_id]."', '".$row2[ship_id]."', '".$subject."', '".$content."')");

       }

     }

}

TEXT_GOTOMAIN();

include("footer.php3");

?>
