<? 

$title="Login"; 

include("header.php3");

include("config.php3");

echo "<CENTER>";
bigtitle();
echo "</CENTER>";

echo "<CENTER>";

echo "<FORM ACTION=login2.php3 method=POST>";
echo "<BR><BR>";
echo "If you get a \"Can't connect to local MySQL\" error, it's because my hosting service's DB is down - again - sorry!.";

echo "<BR><BR>";

echo "<TABLE BORDER=0 CELLSPACING=0 CELLPADDING=4>";
echo "<TR>";
echo "<TD>E-mail Address</TD>";
echo "<TD><INPUT TYPE=TEXT NAME=email SIZE=20 MAXLENGTH=40 VALUE=\"$username\"></TD>";
echo "</TR>";
echo "<TR>";
echo "<TD>Password</TD>";
echo "<TD><INPUT TYPE=PASSWORD NAME=pass SIZE=20 MAXLENGTH=20 VALUE=\"$password\"></TD>";
echo "</TR>";
?>

<SCRIPT LANGUAGE="JavaScript">
// <!--
var swidth = 0;
if(self.screen)
{
  swidth = screen.width;
  document.write("<INPUT TYPE=HIDDEN NAME=res VALUE=\"" + swidth + "\"></INPUT>");
}
if(swidth != 640 && swidth != 800 && swidth != 1024)
{
  document.write("<TR><TD COLSPAN=2>");
  document.write("Unable to determine your screen resolution. Please choose the best fit:<BR>");
  document.write("<CENTER><INPUT TYPE=RADIO NAME=res VALUE=\"640\">640x480</INPUT>");
  document.write("<INPUT TYPE=RADIO NAME=res CHECKED VALUE=\"800\">800x600</INPUT>");
  document.write("<INPUT TYPE=RADIO NAME=res VALUE=\"1024\">1024x768</INPUT></CENTER>");
  docuemnt.write("</TD></TR>");
}
// -->
</SCRIPT>
<NOSCRIPT>
<TR><TD COLSPAN=2>
Unable to determine your screen resolution. Please choose the best fit:<BR>
<CENTER><INPUT TYPE=RADIO NAME=res VALUE="640">640x480</INPUT>
<INPUT TYPE=RADIO NAME=res CHECKED VALUE="800">800x600</INPUT>
<INPUT TYPE=RADIO NAME=res VALUE="1024">1024x768</INPUT></CENTER>
</TD></TR>
</NOSCRIPT>

<?
echo "</TABLE>";

echo "<BR>";
echo "<INPUT TYPE=SUBMIT VALUE=Login>";
echo "<BR><BR>";

echo "If you are a new player, click <A HREF=new.php3>here</A>.<BR><BR>";
echo "Problems? <A HREF=mailto:$admin_mail>E-mail us</A>";

echo "</FORM>";

if(!empty($link_forums))
{
  echo "<A HREF=$link_forums TARGET=_blank>Forums</A> - ";
}
echo "<A HREF=ranking.php3>Rankings</A>";
echo "<BR><BR>";

echo "</CENTER>";

include("footer.php3");

?>
