<?

include("config.php");

if(empty($lang))
  $lang=$default_lang;

if(!empty($newlang))
{
  $lang=$newlang;
  SetCookie("lang",$lang,time()+(3600*24)*365,$gamepath,$gamedomain);
}

include("languages/$lang");

$title=$l_login_title;

include("header.php");

?>

<CENTER>

<?php
bigtitle();
?>

<form action="login2.php" method="post">
<BR><BR>

<TABLE CELLPADDING="4">
<TR>
	<TD align="right"><? echo $l_login_email; ?></TD>
	<TD align="left"><INPUT TYPE="TEXT" NAME="email" SIZE="20" MAXLENGTH="40" VALUE="<?php echo "$username" ?>"></TD>
</TR>
<TR>
	<TD align="right"><? echo $l_login_pw;?></TD>
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
  document.write("<? echo $l_login_chooseres;?><BR>");
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
	<? echo $l_login_chooseres;?><BR>
	<INPUT TYPE="RADIO" NAME="res" VALUE="640">640x480</INPUT>
	<INPUT TYPE="RADIO" NAME="res" CHECKED VALUE="800">800x600</INPUT>
	<INPUT TYPE="RADIO" NAME="res" VALUE="1024">1024x768</INPUT></CENTER>
	</TD>
</TR>
</NOSCRIPT>
</TABLE>
<BR>
<INPUT TYPE="SUBMIT" VALUE="<? echo $l_login_title;?>">
<BR><BR>
<? echo $l_login_newp;?>
<BR><BR>
<? echo $l_login_prbs;?> <A HREF="mailto:<?php echo "$admin_mail"?>"><? echo $l_login_emailus;?></A>
</FORM>

<?php
if(!empty($link_forums))
  echo "<A HREF=\"$link_forums\" TARGET=\"_blank\">$l_forums</A> - ";
?>
<A HREF="ranking.php"><? echo $l_rankings;?></A><? echo " - "; ?>
<A HREF="settings.php"><? echo $l_login_settings;?></A>
<BR><BR>
<form action=login.php method=POST>
<?

echo "$l_login_lang&nbsp;&nbsp;<select name=newlang>";

foreach($avail_lang as $curlang)
{
  if($curlang['file'] == $lang)
    $selected = "selected";
  else
    $selected = "";

  echo "<option value=$curlang[file] $selected>$curlang[name]</option>";
}

echo "</select>&nbsp;&nbsp;<input type=submit value=$l_login_change>";
?>

</form>
</CENTER>

<?php
include("footer.php");
?>
