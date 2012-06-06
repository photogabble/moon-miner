<?php
// Blacknova Traders - A web-based massively multiplayer space combat and trading game
// Copyright (C) 2001-2012 Ron Harwood and the BNT development team
//
//  This program is free software: you can redistribute it and/or modify
//  it under the terms of the GNU Affero General Public License as
//  published by the Free Software Foundation, either version 3 of the
//  License, or (at your option) any later version.
//
//  This program is distributed in the hope that it will be useful,
//  but WITHOUT ANY WARRANTY; without even the implied warranty of
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//  GNU Affero General Public License for more details.
//
//  You should have received a copy of the GNU Affero General Public License
//  along with this program.  If not, see <http://www.gnu.org/licenses/>.
//
// File: footer.php

global $db, $dbtables, $sched_ticks, $l_footer_until_update, $l_footer_players_on_1, $l_footer_players_on_2, $footer_show_time, $l_time_gen_page, $l_seconds, $l_local_news;
$res = $db->Execute("SELECT COUNT(*) as loggedin from $dbtables[ships] WHERE (UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP($dbtables[ships].last_login)) / 60 <= 5 and email NOT LIKE '%@xenobe'");
$row = $res->fields;
$online = $row['loggedin'];

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
 <div style='clear:both'></div><div style="text-align:center">
<?php
// Update counter

$res = $db->Execute("SELECT last_run FROM $dbtables[scheduler] LIMIT 1");
$result = $res->fields;
$mySEC = ($sched_ticks * 60) - (TIME()-$result['last_run']);
?>
  <script type="text/javascript">
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
</div><br>
<?php

if ($footer_show_time == true) // Make the SF logo a little bit larger to balance the extra line from the benchmark for page generation
{
    $sf_logo_type = '14';
}
else
{
    $sf_logo_type = '11';
}

if (preg_match("/index.php/i", $_SERVER['PHP_SELF']) || preg_match("/igb.php/i", $_SERVER['PHP_SELF']))
{
    $sf_logo_type++; // Make the SF logo darker for all pages except login 
}

echo "<div style='position:absolute; text-align:left'><a href='http://www.sourceforge.net/projects/blacknova'><img style='border:0' src='http://sflogo.sourceforge.net/sflogo.php?group_id=14248&amp;type=" . $sf_logo_type . "' alt='Blacknova Traders at SourceForge.net'></a></div>";
echo "<div style='font-size:smaller; text-align:right'><a class='new_link' href='news.php'>" . $l_local_news . "</a></div>";
echo "<div style='font-size:smaller; text-align:right'>&copy;2000-2012 Ron Harwood &amp; the Blacknova Development team</div>";
if ($footer_show_time == true)
{
    echo "<div style='font-size:smaller; text-align:right'>" . $l_time_gen_page . ": " . $elapsed . " " . $l_seconds . "</div>";
}
?>
</body>
</html>
<?php ob_end_flush(); ?>
