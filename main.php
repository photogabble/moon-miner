<?php
include("config.php");
include("languages/$lang");

updatecookie();

$basefontsize = 0;
$stylefontsize = "8Pt";
$picsperrow = 5;

if($screenres == 640)
{
	$picsperrow = 3;
}

if($screenres >= 1024)
{
	$basefontsize = 1;
	$stylefontsize = "12Pt";
	$picsperrow = 7;
}

connectdb();

if(checklogin())
{
	die();
}

$title=$l_main_title;
include("header.php");

//-------------------------------------------------------------------------------------------------

$res = $db->Execute("SELECT * FROM $dbtables[ships] WHERE email='$username'");
$playerinfo = $res->fields;
if($playerinfo['cleared_defences'] > ' ')
{
	echo "$l_incompletemove <BR>";
	echo "<a href=$playerinfo[cleared_defences]>$l_clicktocontinue</a>";
	die();
}

$res = $db->Execute("SELECT * FROM $dbtables[universe] WHERE sector_id='$playerinfo[sector]'");
$sectorinfo = $res->fields;

srand((double)microtime() * 1000000);

if($playerinfo['on_planet'] == "Y")
{
	$res2 = $db->Execute("SELECT planet_id, owner FROM $dbtables[planets] WHERE planet_id=$playerinfo[planet_id]");
	if($res2->RecordCount() != 0)
	{
		echo "<A HREF=planet.php?planet_id=$playerinfo[planet_id]>$l_clickme</A> $l_toplanetmenu    <BR>";
		echo "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"0;URL=planet.php?planet_id=$playerinfo[planet_id]&id=".$playerinfo[ship_id]."\">";
		
		//-------------------------------------------------------------------------------------------------
		die();
	}
	else
	{
		$db->Execute("UPDATE $dbtables[ships] SET on_planet='N' WHERE ship_id=$playerinfo[ship_id]");
		echo "<BR>$l_nonexistant_pl<BR><BR>";
	}
}

$res = $db->Execute("SELECT * FROM $dbtables[links] WHERE link_start='$playerinfo[sector]' ORDER BY link_dest ASC");

$i = 0;
if($res != false)
{
	while(!$res->EOF)
	{
		$links[$i] = $res->fields[link_dest];
		$i++;
		$res->MoveNext();
	}
}
$num_links = $i;

$res = $db->Execute("SELECT * FROM $dbtables[planets] WHERE sector_id='$playerinfo[sector]'");

$i = 0;
if($res != false)
{
	while(!$res->EOF)
	{
		$planets[$i] = $res->fields;
		$i++;
		$res->MoveNext();
	}
}
$num_planets = $i;

$res = $db->Execute("SELECT * FROM $dbtables[sector_defence],$dbtables[ships] WHERE $dbtables[sector_defence].sector_id='$playerinfo[sector]'
                                                    AND $dbtables[ships].ship_id = $dbtables[sector_defence].ship_id ");
$i = 0;
if($res != false)
{
	while(!$res->EOF)
	{
		$defences[$i] = $res->fields;
		$i++;
		$res->MoveNext();
	}
}
$num_defences = $i;

$res = $db->Execute("SELECT zone_id,zone_name FROM $dbtables[zones] WHERE zone_id='$sectorinfo[zone_id]'");
$zoneinfo = $res->fields;

$shiptypes[0]= "tinyship.gif";
$shiptypes[1]= "smallship.gif";
$shiptypes[2]= "mediumship.gif";
$shiptypes[3]= "largeship.gif";
$shiptypes[4]= "hugeship.gif";

$planettypes[0]= "tinyplanet.gif";
$planettypes[1]= "smallplanet.gif";
$planettypes[2]= "mediumplanet.gif";
$planettypes[3]= "largeplanet.gif";
$planettypes[4]= "hugeplanet.gif";

$signame = player_insignia_name($username);

echo "<div style='width:90%; margin:auto; background-color:#400040; color:#C0C0C0; text-align:center; border:#FFFFFF 1px solid; padding:4px;'>\n";
echo "{$signame} <span style='color:#FFFFFF; font-weight:bold;'>{$playerinfo['character_name']}</span>{$l_abord}<span style='color:#FFFFFF; font-weight:bold;'><a class='new_link' style='font-size:14px;' href='report.php'>{$playerinfo['ship_name']}</a></span>\n";
echo "</div>\n";

$result = $db->Execute("SELECT * FROM $dbtables[messages] WHERE recp_id=? AND notified=?;", array($playerinfo['ship_id'], "N") );
if ($result->RecordCount() > 0)
{
	$alert_message = "{$l_youhave}{$result->RecordCount()}{$l_messages_wait}";
	echo "<script language='javascript' type='text/javascript'>\n";
	echo "  // Due to be put into its own javascript file.\n";
	echo "  // Yeah I know it may not be worth it for just 3 lines but it keeps the code clean.\n";
	echo "  alert('{$alert_message}');\n";
	echo "</script>\n";

	$db->Execute("UPDATE $dbtables[messages] SET notified='Y' WHERE recp_id='".$playerinfo[ship_id]."'");
}

$ply_turns		= NUMBER($playerinfo['turns']);
$ply_turnsused	= NUMBER($playerinfo['turns_used']);
$ply_score		= NUMBER($playerinfo['score']);
$ply_credits	= NUMBER($playerinfo['credits']);

echo "<table style='width:90%; margin:auto; text-align:center;' border='0'>\n";
echo "  <tr>\n";
echo "    <td style='width:33%; text-align:left; color:#CCCCCC; font-size:12px;'>&nbsp;{$l_turns_have} <span style='color:#FFFFFF; font-weight:bold;'>{$ply_turns}</span></td>\n";
echo "    <td style='width:33%; text-align:center; color:#CCCCCC; font-size:12px;'>{$l_turns_used} <span style='color:#FFFFFF; font-weight:bold;'>{$ply_turnsused}</span></td>\n";
echo "    <td style='width:33%; text-align:right; color:#CCCCCC; font-size:12px;'>{$l_score} <span style='color:#FFFFFF; font-weight:bold;'>{$ply_score}&nbsp;</span></td>\n";
echo "  </tr>\n";
echo "  <tr>\n";
echo "    <td colspan='3' style='width:33%; text-align:right; color:#CCCCCC; font-size:12px;'>&nbsp;{$l_credits}: <span style='color:#FFFFFF; font-weight:bold;'>{$ply_credits}</span></td>\n";
echo "  </tr>\n";

echo "  <tr>\n";
echo "    <td style='text-align:left; color:#CCCCCC; font-size:12px;'>&nbsp;{$l_sector} <span style='color:#FFFFFF; font-weight:bold;'>{$playerinfo['sector']}</span></td>\n";
if(empty($sectorinfo['beacon']) || strlen(trim($sectorinfo['beacon'])) <=0)
{
	$sectorinfo['beacon'] = NULL; 
}
echo "    <td style='text-align:center; color:#FFFFFF; font-size:12px; font-weight:bold;'>&nbsp;{$sectorinfo['beacon']}&nbsp;</td>\n";

if($zoneinfo['zone_id'] < 5)
{
	$zoneinfo['zone_name'] = $l_zname[$zoneinfo['zone_id']];
}
echo "    <td style='text-align:right; color:#CCCCCC; font-size:12px; font-weight:bold;'><a href='zoneinfo.php?zone={$zoneinfo['zone_id']}'>{$zoneinfo['zone_name']}</a>&nbsp;</td>\n";
echo "  </tr>\n";
echo "</table>\n";

echo "<br>\n";

echo "<table style='width:90%; margin:auto;' border='0' cellpadding='0' cellspacing='0'>\n";
echo "  <tr>\n";
// Left Side.
echo "    <td style='width:200px; vertical-align:top; text-align:center;'>\n";

// Caption
        echo "<table width='140' border='0' cellpadding='0' cellspacing='0' align='center'>\n";
        echo "  <tr valign='top'>\n";
        echo "    <td align='right' width='8'><img align='right' src='images/lcorner.gif' width='8' height='18' border='0' alt=''></td>\n";
        echo "    <td style='white-space:nowrap; background-color:#400040; text-align:center; vertical-align:middle;'><font face='verdana' size='1' color='#ffffff'><b>$l_commands</b></font></td>\n";
        echo "    <td align='left' width='8'><img align='left' src='images/rcorner.gif' width='8' height='18' border='0' alt=''></td>\n";
        echo "  </tr>\n";
        echo "</table>\n";

// Menu
echo "<table style='width:150px; margin:auto; text-align:center;' border='0' cellspacing='0' cellpadding='0'>\n";
echo "  <tr>\n";
echo "    <td style='white-space:nowrap; border:#FFFFFF 1px solid; background-color:#500050;'>\n";
echo "      <div style='padding-left:4px; text-align:left;'><a class='mnu' href='device.php'>{$l_devices}</a></div>\n";
echo "      <div style='padding-left:4px; text-align:left;'><a class='mnu' href='planet_report.php'>{$l_planets}</a></div>\n";
echo "      <div style='padding-left:4px; text-align:left;'><a class='mnu' href='log.php'>{$l_log}</a></div>\n";
echo "      <div style='padding-left:4px; text-align:left;'><a class='mnu' href='defence_report.php'>{$l_sector_def}</a></div>\n";
echo "      <div style='padding-left:4px; text-align:left;'><a class='mnu' href='readmail.php'>{$l_read_msg}</a></div>\n";
echo "      <div style='padding-left:4px; text-align:left;'><a class='mnu' href='mailto2.php'>{$l_send_msg}</a></div>\n";
echo "      <div style='padding-left:4px; text-align:left;'><a class='mnu' href='ranking.php'>{$l_rankings}</a></div>\n";

echo "      <div style='padding-left:4px; text-align:left;'><a class='mnu' href='settings.php'>Settings</a></div>\n";
echo "      <div style='padding-left:4px; text-align:left;'><a class='mnu' href='teams.php'>{$l_teams}</a></div>\n";
echo "      <div style='padding-left:4px; text-align:left;'><a class='mnu' href='self_destruct.php'>{$l_ohno}</a></div>\n";
echo "      <div style='padding-left:4px; text-align:left;'><a class='mnu' href='options.php'>{$l_options}</a></div>\n";
echo "      <div style='padding-left:4px; text-align:left;'><a class='mnu' href='navcomp.php'>{$l_navcomp}</a></div>\n";

if ($ksm_allowed == true)
{
	echo "      <div style='padding-left:4px; text-align:left;'><a class='mnu' href='galaxy.php'>{$l_map}</a></div>\n";
}
echo "    </td>\n";
echo "  </tr>\n";

echo "  <tr>\n";
echo "    <td style='white-space:nowrap; height:2px; background-color:transparent;'></td>\n";
echo "  </tr>\n";

echo "  <tr>\n";
echo "    <td style='white-space:nowrap; border:#FFFFFF 1px solid; background-color:#500050;'>\n";
echo "      <div style='padding-left:4px; text-align:left;'><a class='mnu' href='faq.html'>{$l_faq}</a></div>\n";
echo "      <div style='padding-left:4px; text-align:left;'><a class='mnu' href='feedback.php'>{$l_feedback}</a></div>\n";
#echo "      <div style='padding-left:4px; text-align:left;'><a class='mnu' href='main.php' title='Not implemented'><span style='font-size:8px; color:#FFFF00; font-style:normal;'>NEW</span> Support</a></div>\n";
echo "      <div style='padding-left:4px; text-align:left;'><a class='mnu' href='maint_info.php' title='This will display the Scheduled Maintenance information for this game or Core Code.'><span style='font-size:8px; color:#FFFF00; font-style:normal;'>NEW</span> Maint Info</a></div>\n";
echo "      <div style='padding-left:4px; text-align:left;'><a class='mnu' href='rules.php' title='These are our Rules that you have agreed to.'><span style='font-size:8px; color:#FFFF00; font-style:normal;'>NEW</span> Our Rules</a></div>\n";
#echo "      <div style='padding-left:4px; text-align:left;'><a class='mnu' href='mail.php?mail={$username}' title='Request your login information to be emailed to you.'><span style='font-size:8px; color:#FFFF00; font-style:normal;'>TMP</span> REQ Password</a></div>\n";

if(!empty($link_forums))
{
	echo "      <div style='padding-left:4px; text-align:left;'><a class='mnu' href='{$link_forums}'>{$l_forums}</a></div>\n";
}

echo "    </td>\n";
echo "  </tr>\n";

echo "  <tr>\n";
echo "    <td style='white-space:nowrap; height:2px; background-color:transparent;'></td>\n";
echo "  </tr>\n";

echo "  <tr>\n";
echo "    <td style='white-space:nowrap; border:#FFFFFF 1px solid; background-color:#500050;'>\n";
echo "      <div style='padding-left:4px; text-align:left;'><a class='mnu' href='logout.php'>{$l_logout}</a></div>\n";
echo "    </td>\n";
echo "  </tr>\n";
echo "</table>\n";

echo "<br>\n";

// Caption
echo "<table width='140' border='0' cellpadding='0' cellspacing='0' align='center'>\n";
echo "  <tr valign='top'>\n";
echo "    <td align='right' width='8'><img align='right' src='images/lcorner.gif' width='8' height='18' border='0' alt=''></td>\n";
echo "    <td style='white-space:nowrap; background-color:#400040; text-align:center; vertical-align:middle;'><font face='verdana' size='1' color='#ffffff'><b>$l_traderoutes</b></font></td>\n";
echo "    <td align='left' width='8'><img align='left' src='images/rcorner.gif' width='8' height='18' border='0' alt=''></td>\n";
echo "  </tr>\n";
echo "</table>\n";

// Menu
$i=0;
$num_traderoutes = 0;

// Port querry
$query = $db->Execute("SELECT * FROM $dbtables[traderoutes] WHERE source_type=? AND source_id=? AND owner=? ORDER BY dest_id ASC;", array("P", $playerinfo['sector'], $playerinfo['ship_id']) );
while(!$query->EOF)
{
	$traderoutes[$i]=$query->fields;
	$i++;
	$num_traderoutes++;
	$query->MoveNext();
}

// Sector Defense Trade route query - this is still under developement
$query = $db->Execute("SELECT * FROM $dbtables[traderoutes] WHERE source_type='D' AND source_id=$playerinfo[sector] AND owner=$playerinfo[ship_id] ORDER BY dest_id ASC");
while(!$query->EOF)
{
	$traderoutes[$i]=$query->fields;
	$i++;
	$num_traderoutes++;
	$query->MoveNext();
}

// Personal planet traderoute type query
$query = $db->Execute("SELECT * FROM $dbtables[planets], $dbtables[traderoutes] WHERE source_type='L' AND source_id=$dbtables[planets].planet_id AND $dbtables[planets].sector_id=$playerinfo[sector] AND $dbtables[traderoutes].owner=$playerinfo[ship_id]");
while(!$query->EOF)
{
	$traderoutes[$i]=$query->fields;
	$i++;
	$num_traderoutes++;
	$query->MoveNext();
}

// Team planet traderoute type query
$query = $db->Execute("SELECT * FROM $dbtables[planets], $dbtables[traderoutes] WHERE source_type='C' AND source_id=$dbtables[planets].planet_id AND $dbtables[planets].sector_id=$playerinfo[sector] AND $dbtables[traderoutes].owner=$playerinfo[ship_id]");
while(!$query->EOF)
{
	$traderoutes[$i]=$query->fields;
	$i++;
	$num_traderoutes++;
	$query->MoveNext();
}

echo "<table style='width:150px; margin:auto; text-align:center;' border='0' cellspacing='0' cellpadding='0'>\n";
echo "  <tr>\n";
echo "    <td  style='white-space:nowrap; border:#FFFFFF 1px solid; background-color:#500050;'>\n";
if($num_traderoutes == 0)
{
	echo "  <div style='text-align:center;'><a class='dis'>&nbsp;$l_none &nbsp;</a></div>";
}
else
{
	$i=0;
	while($i<$num_traderoutes)
	{
		echo "<div style='text-align:center;'>&nbsp;<a class=mnu href=traderoute.php?engage={$traderoutes[$i]['traderoute_id']}>";
		if($traderoutes[$i][source_type] == 'P')
		{
			echo "$l_port&nbsp;";
		}
		elseif($traderoutes[$i][source_type] == 'D')
		{
			echo "Def's ";
		}
		else
		{
			$query = $db->Execute("SELECT name FROM $dbtables[planets] WHERE planet_id=?;", array($traderoutes[$i]['source_id']) );
			if(!$query || $query->RecordCount() == 0)
			{
				echo $l_unknown;
			}
			else
			{
				$planet = $query->fields;
				if($planet[name] == "")
				{
					echo "$l_unnamed ";
				}
				else
				{
					echo "$planet[name] ";
				}
			}
		}

		if($traderoutes[$i][circuit] == '1')
		{
			echo "=&gt;&nbsp;";
		}
		else
		{
			echo "&lt;=&gt;&nbsp;";
		}

		if($traderoutes[$i][dest_type] == 'P')
		{
			echo $traderoutes[$i][dest_id];
		}
		elseif($traderoutes[$i][dest_type] == 'D')
		{
			echo "Def's in " .  $traderoutes[$i][dest_id] . "";
		}
		else
		{
			$query = $db->Execute("SELECT name FROM $dbtables[planets] WHERE planet_id=" . $traderoutes[$i][dest_id]);

			if(!$query || $query->RecordCount() == 0)
			{
				echo $l_unknown;
			}
			else
			{
				$planet = $query->fields;
				if($planet[name] == "")
				{
					echo $l_unnamed;
				}
				else
				{
					echo $planet[name];
				}
			}
		}
		echo "</a>&nbsp;<br>";
		$i++;
echo "</div>\n";
	}
}


echo "    </td>\n";
echo "  </tr>\n";

echo "  <tr>\n";
echo "    <td style='white-space:nowrap; height:2px; background-color:transparent;'></td>\n";
echo "  </tr>\n";

echo "  <tr>\n";
echo "    <td style='white-space:nowrap; border:#FFFFFF 1px solid; background-color:#500050;'>\n";
echo "      <div style='padding-left:4px; text-align:center;'><a class='mnu' href='traderoute.php'>{$l_trade_control}</a></div>\n";

echo "    </td>\n";
echo "  </tr>\n";

echo "</table>\n";
echo "<br>\n";

echo "</td>\n";

echo "<td style='vertical-align:top;'>\n";

if($sectorinfo['port_type'] != "none" && strlen($sectorinfo['port_type']) >0)
{
	echo "<div style='color:#FFFFFF; text-align:center; font-size:14px;'>\n";
	echo "{$l_tradingport}:&nbsp;<span style='color:#00FF00;'>". ucfirst(t_port($sectorinfo['port_type'])) ."</span>\n";
	echo "<br>\n";
	echo "<a class='new_link' style='font-size:14px;' href='port.php' title='Dock with Space Port'><img style='width:100px; height:70px;' class='mnu' src='images/space_station_port.png' alt='Space Station Port'></a>\n";
	echo "</div>\n";
}
else
{
	echo "<div style='color:#FFFFFF; text-align:center;'>{$l_tradingport}&nbsp;{$l_none}</div>\n";
}

echo "<br>\n";

// Put all the Planets into a div container and center it.
echo "<div align='center' style='text-align:center; border:transparent 1px solid;'>\n"; 
echo "<div style='text-align:center; font-size:12px; color:#FFFFFF; font-weight:bold;'>{$l_planet_in_sec} {$sectorinfo['sector_id']}</div>\n";
echo "<table style='height:150px; text-align:center; margin:auto;' border='0'>\n";
echo "  <tr>\n";

if($num_planets > 0)
{
	$totalcount=0;
	$curcount=0;
	$i=0;

	while($i < $num_planets)
	{
		if($planets[$i][owner] != 0)
		{
			$result5 = $db->Execute("SELECT * FROM $dbtables[ships] WHERE ship_id=?;", array($planets[$i]['owner']) );
			$planet_owner = $result5->fields;
			$planetavg = get_avg_tech($planet_owner, "planet");
			
			if($planetavg < 8)
			{
				$planetlevel = 0;
			}
			else if ($planetavg < 12)
			{
				$planetlevel = 1;
			}
			else if ($planetavg < 16)
			{
				$planetlevel = 2;
			}
			else if ($planetavg < 20)
			{
				$planetlevel = 3;
			}
			else
			{
				$planetlevel = 4;
			}
		}
		else
		{
			$planetlevel=0;
		}

		echo "<td align='center' valign='top' style='width:79px; height:90px; padding:4px;'>";
		echo "<a href='planet.php?planet_id={$planets[$i]['planet_id']}'>";
		echo "<img class='mnu' width='79' height='90' title='Interact with Planet' src=\"images/$planettypes[$planetlevel]\" border=0></a><BR><font size=", $basefontsize + 1, " color=#ffffff face=\"arial\">";

		if(empty($planets[$i][name]))
		{
			echo $l_unnamed;
			$planet_bnthelper_string="<!--planet:Y:Unnamed:";
		}
		else
		{
			echo $planets[$i][name];
			$planet_bnthelper_string="<!--planet:Y:" . $planets[$i][name] . ":";
		}

		if($planets[$i][owner] == 0)
		{
			echo "<br>($l_unowned)";
			$planet_bnthelper_string=$planet_bnthelper_string . "Unowned:-->";
		}
		else
		{
			echo "<br>($planet_owner[character_name])";
			$planet_bnthelper_string=$planet_bnthelper_string . $planet_owner[character_name] . ":N:-->";
		}
		echo "</font></td>";

		$totalcount++;
		if($curcount == $picsperrow - 1)
		{
			echo "</tr><tr>";
			$curcount=0;
		}
		else
		{
			$curcount++;
		}
		$i++;
	}
}
else
{
	echo "<td align=center valign=top>";
	echo "<br><font color=white size=", $basefontsize +2, ">$l_none</font><br><br>";
	$planet_bnthelper_string="<!--planet:N:::-->";
}

echo "</td>\n";
echo "</tr>\n";
echo "</table>\n";

echo "</div>\n";


// Put all the Planets into a div container and center it.
echo "<div style='text-align:center; border:transparent 1px solid;'>\n"; 
echo "<div style='text-align:center; font-size:12px; color:#FFFFFF; font-weight:bold;'>{$l_ships_in_sec} {$sectorinfo['sector_id']}</div>\n";

if($playerinfo[sector] != 0)
{

	$sql  = NULL;
	$sql .= "SELECT $dbtables[ships].*, $dbtables[teams].team_name, $dbtables[teams].id ";
	$sql .= "FROM $dbtables[ships] LEFT OUTER JOIN $dbtables[teams] ON $dbtables[ships].team = $dbtables[teams].id ";
	$sql .= "WHERE $dbtables[ships].ship_id<>$playerinfo[ship_id] AND $dbtables[ships].sector=$playerinfo[sector] AND $dbtables[ships].on_planet='N' ";
#	$sql .= "WHERE $dbtables[ships].sector=$playerinfo[sector] AND $dbtables[ships].on_planet='N' ";
	$sql .= "ORDER BY RAND();";
	$result4 = $db->Execute($sql);

	if($result4 != false )
	{
		$ships_detected = 0;
		$ship_detected = NULL;
		while(!$result4->EOF)
		{
			$row=$result4->fields;
			$success = SCAN_SUCCESS($playerinfo[sensors], $row[cloak]);
			if($success < 5)
			{
				$success = 5;
			}
			if($success > 95)
			{
				$success = 95;
			}
			$roll = rand(1, 100);
			
			if($roll < $success)
			{
				$shipavg = get_avg_tech($row, "ship");
			
				if($shipavg < 8)
					$shiplevel = 0;
				else if ($shipavg < 12)
					$shiplevel = 1;
				else if ($shipavg < 16)
					$shiplevel = 2;
				else if ($shipavg < 20)
					$shiplevel = 3;
				else
					$shiplevel = 4;
					
				$row['shiplevel'] = $shiplevel;
				$ship_detected[] = $row;
					
				$ships_detected ++;
			}
			$result4->MoveNext();
		}
		if($ships_detected <= 0)
		{
			echo "<div style='color:#FFFFFF;'>{$l_none}</div>\n";
		}
		else
		{
			echo "<div style='padding-top:4px; padding-bottom:4px; width:500px; margin:auto; background-color:#303030;'>Ship(s) Detected</div>\n";
			echo "<div style='width:498px; margin:auto; overflow:auto; height:145px; scrollbar-base-color: #303030; scrollbar-arrow-color: #FFFFFF; padding:0px;'>\n";
			echo "<table cellpadding='0' cellspacing='1'>\n";
			echo "  <tr>\n";
		
			for ($iPlayer=0; $iPlayer < count($ship_detected); $iPlayer++)
			{ 
				echo "<td style='text-align:center; vertical-align:top; padding:1px;'>\n";
				echo "<div style='width:160px; height:120px; background: URL(images/bg_alpha.png) repeat; padding:1px;'>\n";
				echo "<a href=ship.php?ship_id={$ship_detected[$iPlayer]['ship_id']}>\n";
				echo "  <img class='mnu' width='80' height='60' title='Interact with Ship' src=\"images/", $shiptypes[$ship_detected[$iPlayer]['shiplevel']],"\" border='0'>\n";
				echo "</a>\n";
				echo "<div style='color:#FFFFFF; font-family:Arial; font-size:12px; white-space:nowrap;'>{$ship_detected[$iPlayer]['ship_name']}<br>\n";
				echo "(<span style='color:#FFFF00; white-space:nowrap;'>{$ship_detected[$iPlayer]['character_name']}</span>)<br>\n";
				if ($ship_detected[$iPlayer][team_name])
				{
					echo "(<span style='color:#00FF00; white-space:nowrap;'>{$ship_detected[$iPlayer]['team_name']}</span>)\n";
				}
				echo "</div>\n";

				echo "</div>\n";
				echo "</td>\n";
			}
			echo "  </tr>\n";
			echo "</table>\n";
			echo "</div>\n"; 
		}
	}
	else
	{
		echo "<div style='color:#FFFFFF;'>{$l_none}</div>\n";
	}
}
else
{
		echo "<div style='color:#FFFFFF;'>{$l_sector_0}</div>\n";
}
echo "</div>";

if($num_defences>0)
{
	echo "<b>\n";
	echo "  <center>\n";
	echo "    <font face='arial' color='#FFFFFF'>$l_sector_def</font>\n";
	echo "    <br>\n";
	echo "  </center>\n";
	echo "</b>\n";
}
?>
<table border='0' width="100%">
<tr>
<?php
if($num_defences > 0)
{
  $totalcount=0;
  $curcount=0;
  $i=0;
  while($i < $num_defences)
  {

    $defence_id = $defences[$i]['defence_id'];
    echo "<td align=center valign=top>";
    if($defences[$i]['defence_type'] == 'F')
    {
       echo "<a href=modify_defences.php?defence_id=$defence_id><img src=\"images/fighters.gif\" border=0></a><BR><font size=", $basefontsize + 1, " color=#ffffff face=\"arial\">";
       $def_type = $l_fighters;
       $mode = $defences[$i]['fm_setting'];
       if($mode == 'attack')
         $mode = $l_md_attack;
       else
        $mode = $l_md_toll;
       $def_type .= $mode;
    }
    elseif($defences[$i]['defence_type'] == 'M')
    {
       echo "<a href=modify_defences.php?defence_id=$defence_id><img src=\"images/mines.gif\" border=0></a><BR><font size=", $basefontsize + 1, " color=#ffffff face=\"arial\">";
       $def_type = $l_mines;
    }
    $char_name = $defences[$i]['character_name'];
    $qty = $defences[$i]['quantity'];
    echo "$char_name ( $qty $def_type )";
    echo "</font></td>";

    $totalcount++;
    if($curcount == $picsperrow - 1)
    {
      echo "</tr><tr>";
      $curcount=0;
    }
    else
      $curcount++;
    $i++;
  }
  echo "</td></tr></table>";
}
else
{
  echo "<td align=center valign=top>";
//  echo "<br><font color=white size=", $basefontsize +2, ">None</font><br><br>";
  echo "</td></tr></table>";
}
?>
<br>
<td width='200' valign=top>

<?php
echo "<table width='140' border='0' cellpadding='0' cellspacing='0' align='center'>\n";
echo "  <tr valign='top'>\n";
echo "    <td align='right' width='8'><img align='right' src='images/lcorner.gif' width='8' height='18' border='0' alt=''></td>\n";
echo "    <td style='white-space:nowrap; background-color:#400040; text-align:center; vertical-align:middle;'><font face='verdana' size='1' color='#ffffff'><b>$l_cargo</b></font></td>\n";
echo "    <td align='left' width='8'><img align='left' src='images/rcorner.gif' width='8' height='18' border='0' alt=''></td>\n";
echo "  </tr>\n";
echo "</table>\n";
?>



<table width='150' BORDER=0 CELLPADDING=0 CELLSPACING=0 align="center"> 
  <tr>
    <td style='white-space:nowrap; border:#FFFFFF 1px solid; background-color:#500050;'>
      <table width='100%' border='0' cellpadding='1' cellspacing='0' bgcolor="#500050" align="center"> 
        <tr>
          <td style='vertical-align:middle;'  nowrap align='left'>&nbsp;<img height=12 width=12 alt="<?php echo $l_ore ?>" src="images/ore.gif">&nbsp;<?php echo $l_ore ?>&nbsp;</td>
        </tr> 
        <tr>
          <td style='vertical-align:middle;' nowrap align='right'><span class=mnu>&nbsp;<?php echo NUMBER($playerinfo[ship_ore]); ?>&nbsp;</span></td>
        </tr>
        <tr>
          <td nowrap align='left'>&nbsp;<img height=12 width=12 alt="<?php echo $l_organics ?>" src="images/organics.gif">&nbsp;<?php echo $l_organics ?>&nbsp;</td>
        </tr> 
        <tr>
          <td nowrap align='right'><span class=mnu>&nbsp;<?php echo NUMBER($playerinfo[ship_organics]); ?>&nbsp;</span></td>
        </tr>
        <tr>
          <td nowrap align='left'>&nbsp;<img height=12 width=12 alt="<?php echo $l_goods ?>" src="images/goods.gif">&nbsp;<?php echo $l_goods ?>&nbsp;</td>
        </tr> 
        <tr>
          <td nowrap align='right'><span class=mnu>&nbsp;<?php echo NUMBER($playerinfo[ship_goods]); ?>&nbsp;</span></td>
        </tr>
        <tr>
          <td nowrap align='left'>&nbsp;<img height=12 width=12 alt="<?php echo $l_energy ?>" src="images/energy.gif">&nbsp;<?php echo $l_energy ?>&nbsp;</td>
        </tr> 
        <tr>
          <td nowrap align='right'><span class=mnu>&nbsp;<?php echo NUMBER($playerinfo[ship_energy]); ?>&nbsp;</span></td>
        </tr>
        <tr>
          <td nowrap align='left'>&nbsp;<img height=12 width=12 alt="<?php echo $l_colonists ?>" src="images/colonists.gif">&nbsp;<?php echo $l_colonists ?>&nbsp;</td>
        </tr> 
        <tr>
          <td nowrap align='right'><span class=mnu>&nbsp;<?php echo NUMBER($playerinfo[ship_colonists]); ?>&nbsp;</span></td>
        </tr>
      </table>
    </td>
   </tr>
</table>

<br>

<?php
echo "<table width='140' border='0' cellpadding='0' cellspacing='0' align='center'>\n";
echo "  <tr valign='top'>\n";
echo "    <td align='right' width='8'><img align='right' src='images/lcorner.gif' width='8' height='18' border='0' alt=''></td>\n";
echo "    <td nowrap bgcolor='#400040' style=' text-align:center; vertical-align:middle;'><font face='verdana' size='1' color='#ffffff'><b>$l_realspace</b></font></td>\n";
echo "    <td align='left' width='8'><img align='left' src='images/rcorner.gif' width='8' height='18' border='0' alt=''></td>\n";
echo "  </tr>\n";
echo "</table>\n";
?>

<TABLE width='150' BORDER=0 CELLPADDING=0 CELLSPACING=0 align="center">
<TR><TD  style='white-space:nowrap; border:#FFFFFF 1px solid; background-color:#500050;'>

<table style="width:100%;">
<tr>
  <td style="text-align:left;"><a class=mnu href="rsmove.php?engage=1&amp;destination=<?php echo $playerinfo[preset1]; ?>">=&gt;&nbsp;<?php echo $playerinfo[preset1]; ?></a></td>
  <td style="text-align:right;">[<a class=mnu href=preset.php><?php echo ucwords($l_set); ?></a>]</td>
</tr>
<tr>
  <td style="text-align:left;"><a class=mnu href="rsmove.php?engage=1&amp;destination=<?php echo $playerinfo[preset2]; ?>">=&gt;&nbsp;<?php echo $playerinfo[preset2]; ?></a></td>
  <td style="text-align:right;">[<a class=mnu href=preset.php><?php echo ucwords($l_set); ?></a>]</td>
</tr>
<tr>
  <td style="text-align:left;"><a class=mnu href="rsmove.php?engage=1&amp;destination=<?php echo $playerinfo[preset3]; ?>">=&gt;&nbsp;<?php echo $playerinfo[preset3]; ?></a></td>
  <td style="text-align:right;">[<a class=mnu href=preset.php><?php echo ucwords($l_set); ?></a>]</td>
</tr>
</table>

</td></tr>
<?php
echo "  <tr>\n";
echo "    <td style='white-space:nowrap; height:2px; background-color:transparent;'></td>\n";
echo "  </tr>\n";
?>
<TR><TD  style='white-space:nowrap; border:#FFFFFF 1px solid; background-color:#500050;'>
&nbsp;<a class=mnu href="rsmove.php">=&gt;&nbsp;<?php echo $l_main_other;?></a>&nbsp;<br>
</td></tr>

</table>

<br>

<?php
echo "<table width='140' border='0' cellpadding='0' cellspacing='0' align='center'>\n";
echo "  <tr valign='top'>\n";
echo "    <td align='right' width='8'><img align='right' src='images/lcorner.gif' width='8' height='18' border='0' alt=''></td>\n";
echo "    <td style='white-space:nowrap; background-color:#400040; text-align:center; vertical-align:middle;'><font face='verdana' size='1' color='#ffffff'><b>$l_main_warpto</b></font></td>\n";
echo "    <td align='left' width='8'><img align='left' src='images/rcorner.gif' width='8' height='18' border='0' alt=''></td>\n";
echo "  </tr>\n";
echo "</table>\n";
?>

<TABLE width='150' BORDER=0 CELLSPACING=0 CELLPADDING=0 align="center">
<TR><TD style='white-space:nowrap; border:#FFFFFF 1px solid; background-color:#500050; text-align:center;'>
<div class=mnu>

<?php

if(!$num_links)
{
  echo "&nbsp;<a class=dis>$l_no_warplink</a>&nbsp;<br>";
  $link_bnthelper_string="<!--links:N";
}
else
{

echo "<table style='width:100%;'>\n";

  $link_bnthelper_string="<!--links:Y";
  for($i=0; $i<$num_links;$i++)
  {
#     echo "&nbsp;<a class=\"mnu\" href=\"move.php?sector=$links[$i]\">=&gt;&nbsp;$links[$i]</a>&nbsp;<a class=dis href=\"lrscan.php?sector=$links[$i]\">[$l_scan]</a>&nbsp;<br>";
     $link_bnthelper_string=$link_bnthelper_string . ":" . $links[$i];

echo "<tr>\n";
echo "  <td style='text-align:left;'><a class='mnu' href='move.php?sector={$links[$i]}'>=&gt;&nbsp;$links[$i]</a></td>\n";
echo "  <td style='text-align:right;'>[<a class='mnu' href='lrscan.php?sector={$links[$i]}'>$l_scan</a>]</td>\n";
echo "</tr>\n";

  }
echo "</table>\n";
}
$link_bnthelper_string=$link_bnthelper_string . ":-->";
echo "</div>";
echo "</td></tr>";

echo "  <tr>\n";
echo "    <td style='white-space:nowrap; height:2px; background-color:transparent;'></td>\n";
echo "  </tr>\n";

echo "<tr><td style='white-space:nowrap; border:#FFFFFF 1px solid; background-color:#500050; text-align:center;'>";
echo "<div class=mnu>";
echo "&nbsp;<a class=dis href=\"lrscan.php?sector=*\">[$l_fullscan]</a>&nbsp;<br>";
?>

</div>
</td></tr>
</table>

</td>
</tr>

</table>

<?php

$player_bnthelper_string="<!--player info:" . $playerinfo[hull] . ":" .  $playerinfo[engines] . ":"  .  $playerinfo[power] . ":" .  $playerinfo[computer] . ":" . $playerinfo[sensors] . ":" .  $playerinfo[beams] . ":" . $playerinfo[torp_launchers] . ":" .  $playerinfo[torps] . ":" . $playerinfo[shields] . ":" .  $playerinfo[armor] . ":" . $playerinfo[armor_pts] . ":" .  $playerinfo[cloak] . ":" . $playerinfo[credits] . ":" .  $playerinfo[sector] . ":" . $playerinfo[ship_ore] . ":" .  $playerinfo[ship_organics] . ":" . $playerinfo[ship_goods] . ":" .  $playerinfo[ship_energy] . ":" . $playerinfo[ship_colonists] . ":" .  $playerinfo[ship_fighters] . ":" . $playerinfo[turns] . ":" .  $playerinfo[on_planet] . ":" . $playerinfo[dev_warpedit] . ":" .  $playerinfo[dev_genesis] . ":" . $playerinfo[dev_beacon] . ":" .  $playerinfo[dev_emerwarp] . ":" . $playerinfo[dev_escapepod] . ":" .  $playerinfo[dev_fuelscoop] . ":" . $playerinfo[dev_minedeflector] . ":-->";
$rspace_bnthelper_string="<!--rspace:" . $sectorinfo[distance] . ":" . $sectorinfo[angle1] . ":" . $sectorinfo[angle2] . ":-->";
echo $player_bnthelper_string;
echo $link_bnthelper_string;
echo $port_bnthelper_string;
echo $planet_bnthelper_string;
echo $rspace_bnthelper_string;

echo "\n";

echo "<table align='center' style='border:#FFFFFF solid 1px; text-align:center; background-color:#000000; color:#000000;' cellpadding='2' cellspacing='1' title='news ticker v'>\n";
echo "  <tr>\n";
echo "    <td id='news_ticker' align='center' class='faderlines' style='text-align:center; color:#FFFFFF; font-size:12px; width:600px;'>Loading News, Please wait...</td>\n";
echo "  </tr>\n";
echo "</table>\n";

include("fader.php");
include("footer.php");

?>
