<?
include("config.php3");
include("includes/newsservices.php3");

include_once($gameroot . "/languages/$lang");
$title=$l_news_title;
include("header.php3");

connectdb();

//Check to see if the date was passed in the query string
if ($startdate == '')
{
    //The date wasn't supplied so use today's date
    $startdate = date("Y/m/d");
}

$previousday = getpreviousday($startdate);
$nextday = getnextday($startdate);
?>
 <table width="73%" border="0" cellspacing="2" cellpadding="2">
    <tr>
      <td height="73" width="27%"><img src="images/bnnhead.gif" width="312" height="123"></td>
      <td height="73" width="73%" bgcolor="#000000" valign="bottom" align="right">
        <p><font size="-1"><?php echo $l_news_info?></font></p>
        <p><?php echo $l_news_for ?> <?php echo $startdate?></p>
      </td>
    </tr>
    <tr>
      <td height="22" width="27%" bgcolor="#00001A">&nbsp;</td>
      <td height="22" width="73%" bgcolor="#00001A" align="right"><a href=news.php?startdate=<?php echo $previousday ?>><?php echo $l_news_prev ?></a> - <a href=news.php?startdate=<?php echo $nextday ?>><?php echo $l_news_next ?></a></td>
    </tr>
<?php


//Select news for date range
$res = mysql_query("SELECT * from bn_news where date = '$startdate' order by news_id desc");

//Check to see if there was any news to be shown
if(!mysql_num_rows($res))
{

    //No news
    echo "<tr><td bgcolor=\"#00001A\" align=\"center\">$l_news_flash</td><td bgcolor=\"#00001A\" align=\"right\">$l_news_none</td></tr></table><p align=left>";

    //Display link to the main page
    TEXT_GOTOMAIN();
    die();
}

while ($row = mysql_fetch_array($res)) {
?>
<tr>
      <td bgcolor="#000033" align="center"><?php echo $row[headline]?></td>
      <td bgcolor="#000033">
        <p align="justify"><?php echo $row[newstext]?></p><br>
      </td>
</tr>

<?php
}
?>
</table>
<p align=left>
<?php
TEXT_GOTOMAIN();
?>
