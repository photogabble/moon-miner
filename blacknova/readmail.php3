<?
include("config.php3");
updatecookie();

include($gameroot . $default_lang);
$title=$l_readm_title;
include("header.php3");

bigtitle();

connectdb();

if(checklogin())
{
  die();
}

$res = mysql_query("SELECT * FROM ships WHERE email='$username'");
$playerinfo = mysql_fetch_array($res);
mysql_free_result($res);

if ($action=="delete")
{
 mysql_query("DELETE FROM messages WHERE ID='".$ID."' AND recp_id='".$playerinfo[ship_id]."'");
?>
<FONT COLOR="#FF0000" Size="7"><B><Blink><Center><? echo $l_readm_delete; ?></Center></Blink></B></FONT><BR>
<?
}

$res = mysql_query("SELECT * FROM messages WHERE recp_id='".$playerinfo[ship_id]."'");
 if (mysql_num_rows($res)==0)
 {
  echo "$l_readm_nomessage";
 }
 else
 {
?>
<Table width="75%">
<TR>
<TD colspan="2" BGCOLOR="<? echo $color_header; ?>">$l_readm_center</TD>
</TR>
<TR BGCOLOR="<? echo $color_line1; ?>">
<TD>
<? echo $l_readm_sender; ?>
</TD>
<TD>
Message
</TD>
</TR>
<?
  $line_counter = true;
  while($msg = mysql_fetch_array($res))
  {
   $result = mysql_query("SELECT * FROM ships WHERE ship_id='".$msg[sender_id]."'");
   $sender = mysql_fetch_array($result);
?>
<TR BGCOLOR="<?
if ($line_counter)
{
 echo $color_line2;
 $line_counter = false;
}
else
{
 echo $color_line1;
 $line_counter = true;
}
?>">
<TD>
<? echo $sender[character_name]; ?><HR>$l_readm_captn<BR><? echo $sender[ship_name]; ?>
</TD>
<TD>
<UL><B><? echo $msg[subject]; ?></B></UL><HR><? echo nl2br($msg[message]); ?>
</TD>
<TD>
<A HREF="readmail.php3?action=delete&ID=<? echo $msg[ID]; ?>">$l_readm_del</A><BR>
<A HREF="mailto2.php3?name=<? echo $sender[character_name]; ?>&subject=<? echo $msg[subject] ?>">$l_readm_del</A><BR>
</TD>
</TR>
<?
  }
?>
</TABLE>
<?
 }

TEXT_GOTOMAIN();

include("footer.php3");
?>
