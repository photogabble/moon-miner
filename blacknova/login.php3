<? 

$title="Login"; 

include("header.php3");

include("config.php3");

echo "<CENTER>";
bigtitle();
echo "</CENTER>";

echo "<CENTER>";

echo "<FORM ACTION=\"login2.php3\" method=POST>";
echo "<BR><BR>";
echo "If you get a \"Can't connect to local MySQL\" error, it's because my hosting service's DB is down - again - sorry!.";

echo "<BR><BR>";

echo "<TABLE BORDER=0 CELLSPACING=0 CELLPADDING=4>";
echo "<TR>";
echo "<TD>E-mail Address</TD>";
echo "<TD><INPUT TYPE=TEXT NAME=\"email\" SIZE=20 MAXLENGTH=40 VALUE=\"$username\"></TD>";
echo "</TR>";
echo "<TR>";
echo "<TD>Password</TD>";
echo "<TD><INPUT TYPE=PASSWORD NAME=\"pass\" SIZE=20 MAXLENGTH=20 VALUE=\"$password\"></TD>";
echo "</TR>";
echo "</TABLE>";

echo "<BR>";
echo "<INPUT TYPE=SUBMIT VALUE=\"Submit\"><INPUT TYPE=RESET VALUE=\"Reset\">";
echo "<BR><BR>";

echo "If you are a new player, click <A HREF=new.php3>here</A>.<BR><BR>";
echo "Problems? <A HREF=mailto:$admin_mail>E-mail us</A>";

echo "</FORM>";

echo "<A HREF=http://blacknova.community.everyone.net/commun_v3/scripts/directory.pl TARGET=_blank>Forums</A> - <A HREF=ranking.php3>Rankings</A>";
echo "<BR><BR>";

echo "</CENTER>";

include("footer.php3");

?>
