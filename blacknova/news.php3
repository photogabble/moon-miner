<?

include("extension.inc");
include("config.$phpext");
include("includes/newsservices.$phpext");
updatecookie();

$title="BlackNova Universe News";
include("header.$phpext");

connectdb();

//Display the page title
bigtitle();

//Check to see if the player is logged on
if(checklogin())
{
    die();
}

//Check to see if the date was passed in the query string
if ($startdate == '')
{
    //The date wasn't supplied so use today's date
    $startdate = date("Y/m/d");
}

//Display title with the date of the news being shown
echo "News for ", $startdate,"<BR>";

//Show link to previous days news
$previousday = getpreviousday($startdate);
echo "<a href=news.$phpext?startdate=",$previousday,">previous day</a>";

echo "  -  ";

//Show link to next days news
$nextday = getnextday($startdate);
echo "<a href=news.$phpext?startdate=",$nextday,">next day</a><BR><BR>";


//Select news for date range
$res = mysql_query("SELECT newsdate,newstypes.description as newstypedescription,newsactions.description as newsactiondescription,newsdata from news,newstypes,newsactions where news.newstypes_id = newstypes.newstypes_id and news.action_id = newsactions.action_id and news.newsdate > '$startdate'");

//Check to see if there was any news to be shown
if(!mysql_num_rows($res))
{

    //No news
    echo "Sorry, no news today.<BR>";

    //Display link to the main page
    TEXT_GOTOMAIN();
    die();
}

echo "<TABLE BORDER=0 CELLSPACING=0 CELLPADDING=2>";
echo "<TR BGCOLOR=\"$color_header\"><TD><B>Time</B></TD><TD><B>News Type</B></TD><TD><B>Action</B></TD><TD><B>News</B></TD></TR>";
$color = $color_line1;

while($newsrow = mysql_fetch_array($res))
{
    echo "<TR BGCOLOR=\"$color\"><TD>" . date("g:i a",$newsrow[newsdate]) . "</TD><TD>" . $newsrow[newstypedescription] . "</TD><TD>" . $newsrow[newsactiondescription] . "</TD><TD>" . $newsrow[newsdata] . "</TD></TR>";
    if($color == $color_line1)
    {
      $color = $color_line2;
    }
    else
    {
      $color = $color_line1;
    }
}
echo "</TABLE>";
echo "<BR>";
//Display link to the main page
TEXT_GOTOMAIN();

?>
