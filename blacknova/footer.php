<?
global $db;
connectdb();
$res = $db->Execute("SELECT COUNT(*) as loggedin from $dbtables[ships] WHERE (UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP($dbtables[ships].last_login)) / 60 <= 5");
$row = $res->fields;
$online = $row[loggedin];
?>
<BR><CENTER>
<?
if($online == 1)
{
   echo "There is 1 player online.";
}
else
{
echo "There are $online players online.";
}
?>
</CENTER>
<BR>
<TABLE WIDTH=100% BORDER=0 CELLSPACING=0 CELLPADDING=0>
<TR>
<TD><FONT COLOR=SILVER SIZE=-4><A HREF="http://www.sourceforge.net/projects/blacknova">BlackNova Traders</A></FONT></TD>
<TD ALIGN=RIGHT><FONT COLOR=SILVER SIZE=-4>© 2000-2002 <a href=mailto:webmaster@blacknova.net>Ron Harwood</a></FONT></TD>
</TR>
<TR><TD><FONT COLOR=SILVER SIZE=-4><A HREF="news.php">Local BlackNova News</A></TD>
</TR>
</TABLE>
</BODY>
</HTML>
