<? 
$title="Login"; 

include("header.php3");
include("config.php3");
?>

<CENTER>

<?php
bigtitle();
?>

<form action="login2.php3" method="post">";
<BR><BR>
If you get a "Can't connect to local MySQL" error, it's because my hosting service's DB is down - again - sorry!
<BR><BR>

<TABLE CELLPADDING="4">
<TR>
	<TD align="right">E-mail Address:</TD>
	<TD align="left"><INPUT TYPE="TEXT" NAME="email" SIZE="20" MAXLENGTH="40" VALUE="<?php echo "$username" ?>"></TD>
</TR>
<TR>
	<TD align="right">Password:</TD>
	<TD align="left"><INPUT TYPE="PASSWORD" NAME="pass" SIZE="20" MAXLENGTH="20" VALUE="<?php echo "$password" ?>"></TD>
</TR>

<SCRIPT LANGUAGE="JavaScript">
// <!--
var swidth = 0;
if(self.screen)
{
  swidth = screen.width;
  document.write("<INPUT TYPE=\"HIDDEN\" NAME=\"res\" VALUE=\"" + swidth + "\"></INPUT>");
}
if(swidth != 640 && swidth != 800 && swidth != 1024)
{
  document.write("<TR><TD COLSPAN=2>");
  document.write("Unable to determine your screen resolution. Please choose the best fit:<BR>");
  document.write("<CENTER><INPUT TYPE=\"RADIO\" NAME=\"res\" VALUE=\"640\">640x480</INPUT>");
  document.write("<INPUT TYPE=\"RADIO\" NAME=\"res\" CHECKED VALUE=\"800\">800x600</INPUT>");
  document.write("<INPUT TYPE=\"RADIO\" NAME=\"res\" VALUE=\"1024\">1024x768</INPUT></CENTER>");
  document.write("</TD></TR>");
}
// -->
</SCRIPT>
<NOSCRIPT>
<TR>
	<TD COLSPAN="2">
	Unable to determine your screen resolution. Please choose the best fit:<BR>
	<INPUT TYPE="RADIO" NAME="res" VALUE="640">640x480</INPUT>
	<INPUT TYPE="RADIO" NAME="res" CHECKED VALUE="800">800x600</INPUT>
	<INPUT TYPE="RADIO" NAME="res" VALUE="1024">1024x768</INPUT></CENTER>
	</TD>
</TR>
</NOSCRIPT>
</TABLE>
<BR>
<INPUT TYPE="SUBMIT" VALUE="Login">
<BR><BR>
If you are a new player, click <A HREF="new.php3">here</A>.
<BR><BR>
Problems? <A HREF="mailto:<?php echo "$admin_mail"?>">E-mail us</A>
</FORM>

<?php
if(!empty($link_forums))
  echo "<A HREF=\"$link_forums\" TARGET=\"_blank\">Forums</A> - ";
?>
<A HREF="ranking.php3">Rankings</A>";
<BR><BR>
</CENTER>

<?php
include("footer.php3");
?>