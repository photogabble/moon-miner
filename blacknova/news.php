<?
include("config.php3");
include("includes/newsservices.php3");

$title="BlackNova Universe News";
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
        <p><font size="-1">BNN is among the galaxy's leaders<br>
          in news and information delivery.<br>
          Staffed 24 hours, seven days a week by a <br>
          dedicated staff in BNN's galaxy headquarters <br>
          in Alpha Centaury and in bureaus galaxywide!</font></p>
        <p> News of <?php echo $startdate?></p>
      </td>
    </tr>
    <tr>
      <td height="22" width="27%" bgcolor="#00001A">&nbsp;</td>
      <td height="22" width="73%" bgcolor="#00001A" align="right"><a href=news.php?startdate=<?php echo $previousday?>>previous day</a> - <a href=news.php?startdate=<?php echo $nextday?>>next day</a></td>
    </tr>
<?php


//Select news for date range
$res = mysql_query("SELECT * from nnews where date = '$startdate' order by news_id desc");

//Check to see if there was any news to be shown
if(!mysql_num_rows($res))
{

    //No news
    echo "<tr><td bgcolor=\"#00001A\" align=\"center\">News Flash</td><td bgcolor=\"#00001A\" align=\"right\">Sorry, no news today.</td></tr></table><p align=left>";

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
