<?
	include("config.php3");
	updatecookie();

	$title="Options"; 
	include("header.php3");
        bigtitle();

	if ($username=="" or $password=="") {echo  "You need to log in, click <a href=login.php3>here</a>."; die();} 
	
	mysql_connect($dbhost, $dbuname, $dbpass);
	@mysql_select_db("$dbname") or die ("Unable to select database");
	
		$result = mysql_query ("SELECT * FROM ships WHERE email='$username'");
		$playerinfo=mysql_fetch_array($result);

		if ($playerinfo[ship_destroyed]=="Y") 
		{
			if ($playerinfo[dev_escapepod]="Y") 
			{
				$result1b = mysql_query ("UPDATE ships SET hull=0, engines=0, power=0, computer=0,sensors=0, beams=0, torp_launchers=0, torps=0, armour=0, armour_pts=100, cloak=0, shields=0, sector=0, ship_ore=0, ship_organics=0, ship_energy=1000, ship_colonists=0, ship_goods=0, ship_fighters=100, ship_damage='', on_planet='N', dev_warpedit=0, dev_genesis=1, dev_beacon=0, dev_emerwarp=0, dev_escapepod='N', dev_fuelscoop='N', dev_minedeflector=0, ship_destroyed='N' where email='$username'");	
			$huh=mysql_fetch_array($result1b);
			echo "Your ship was destroyed, but your escape pods saved you and your crew.  Click <a href=$interface>here</a> to continue with a new ship.";
			die();
			}else{		
				echo "Player is DEAD!  Here's what happened:<BR><BR>";

				include("player-log/".$playerinfo[ship_id]);
				unlink("player-log/".$playerinfo[ship_id]);
				$result = mysql_query ("DELETE FROM ships WHERE ship_id = $playerinfo[ship_id]");
				echo "Dead player has now been deleted.  Click <a href=new.php3>here</a> to start with a new player.";
				die();
			}
		}
		echo "You may change your password:<BR><BR>";
		echo "<form action=\"option2.php3\" method=\"post\">";
		echo "<table  width=\"\" border=\"0\" cellspacing=\"0\" cellpadding=\"4\">
	<tr>
		<td >
			Enter Old Password:
		</td>
		<td >
			<input type=\"password\" name=\"oldpass\" size=\"16\" maxlength=\"16\" value=\"\">
		</td>
	</tr>
	<tr>
		<td >
			Enter New Password:
		</td>
		<td >
			<input type=\"password\" name=\"newpass1\" size=\"16\" maxlength=\"16\" value=\"\">
		</td>
	</tr>
	<tr>
		<td >
			Re-enter New Password:
		</td>
		<td >
			<input type=\"password\" name=\"newpass2\" size=\"16\" maxlength=\"16\" value=\"\">
		</td>
	</tr>
	<tr>
		<td>";
		$intrf="checked";
		if($interface=="O")
			$intrf="";
		echo "Use new interface&nbsp;&nbsp;<input type=checkbox name=intrf value=\"N\" $intrf></input>
		</td>
	</tr>
</table>";

		echo "<input type=\"submit\" value=\"Submit\"><input type=\"reset\" value=\"Reset\">";


		echo "</form>";
		echo "Click <a href=$interface>here</a> to return to Main Menu.";

	include("footer.php3");
?>

