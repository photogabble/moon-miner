<?php
global $db,$dbtables;
connectdb();
$res = $db->Execute("SELECT COUNT(*) as loggedin from $dbtables[ships] WHERE (UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP($dbtables[ships].last_login)) / 60 <= 5 and email NOT LIKE '%@xenobe'");
$row = $res->fields;
$online = $row[loggedin];

global $BenchmarkTimer;
if (is_object($BenchmarkTimer))
{
    $stoptime = $BenchmarkTimer->stop();
    $elapsed = $BenchmarkTimer->elapsed();
    $elapsed = substr($elapsed,0,5);
}
else
{
    $elapsed = 999;
}
?><br>
 <center>
<?php
// Update counter

$res = $db->Execute("SELECT last_run FROM $dbtables[scheduler] LIMIT 1");
$result = $res->fields;
$mySEC = ($sched_ticks * 60) - (TIME()-$result[last_run]);
?>
  <script language="javascript" type="text/javascript">
   var myi = '<?php echo $mySEC; ?>';
   setTimeout("rmyx();",1000);

   function rmyx()
    {
     myi = myi - 1;
     if (myi <= 0)
      {
      myi = <?php echo ($sched_ticks * 60); echo "\n";?>
      }
     document.getElementById("myx").innerHTML = myi;
     setTimeout("rmyx();",1000);
    }
  </script>
<?php
echo "  <b><span id=myx>$mySEC</span></b> $l_footer_until_update <br>\n";
// End update counter

if($online == 1)
{
   echo "  ";
   echo $l_footer_one_player_on;
}
else
{
echo "  ";
echo $l_footer_players_on_1;
echo " ";
echo $online;
echo " ";
echo $l_footer_players_on_2;
}
?>
</center><br>
  <table width="100%" border=0 cellspacing=0 cellpadding=0>
   <tr>
    <td><font color=silver size=-4><a href="http://www.sourceforge.net/projects/blacknova">BlackNova Traders</a></font></td>
    <td align=right><font color=silver size=-4>&copy;2000-2012 Ron Harwood &amp; the Blacknova Development team</font></td>
   </tr>
   <tr>
    <td><font color=silver size=-4><a href="news.php">Local BlackNova News</a></font></td>
<?php
if ($footer_show_time == true)
{
    echo "<td align=right><font color=silver size=-4>Time to generate page: " . $elapsed . " seconds</td>";
}
?>
   </tr>
  </table>
</body>
</html>
