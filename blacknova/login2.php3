<?
	/* first placment of cookie - don't use updatecookie. */
	setcookie("username", $email);
	setcookie("password", $pass);
 
	$title="Login Phase Two"; 
	include("header.php3");

	include("config.php3");
	connectdb();


	bigtitle();
	$result = mysql_query ("select * from ships where email='$email'");
	
	if(mysql_num_rows($result)) 
	{
		$playerinfo=mysql_fetch_array($result);
	} else {
		echo "<b>No Such Player! - Create a new player <a href=new.php3>here</a>.</b><br>";
		
	}

	if ($result>0 && $playerinfo[password]==$pass && $playerinfo[ship_destroyed]=="N")
	{
		/* player exists, password is correct, and player is not dead */
		playerlog($playerinfo[ship_id],"Logged in from ".$ip);
		$stamp=date("Y-m-d H-i-s");
		$update = mysql_query("UPDATE ships SET last_login='$stamp' WHERE ship_id=$playerinfo[ship_id]");
		echo "Click <a href=main.php3>here</a> to go to the main menu.<BR>"; 
		echo "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"0;URL=main.php3?id=".$playerinfo[ship_id]."\">";

	} elseif ($result>0 && $playerinfo[password]==$pass && $playerinfo[ship_destroyed]=="Y") 
	{
		/* player exists, password is correct, and player IS dead */
		if ($playerinfo[dev_escapepod]="Y") 
		{
			$result1b = mysql_query ("UPDATE ships SET hull=0, engines=0, power=0,computer=0,sensors=0,beams=0,torp_launchers=0,torps=0,armour=0,armour_pts=100,cloak=0,shields=0,sector=0,ship_ore=0,ship_organics=0,ship_energy=1000,ship_colonists=0,ship_goods=0,ship_fighters=100,ship_damage='',on_planet='n',dev_warpedit=0,dev_genesis=1,dev_beacon=0,dev_emerwarp=0,dev_escapepod='N',dev_fuelscoop='N',dev_minedeflector=0, ship_destroyed='N' where email='$username'");
			echo "Your ship was destroyed, but your escape pods saved you and your crew.  Click <a href=main.php3>here</a> to continue with a new ship.";
		}else{		
			echo "Player is DEAD!  Here's what happened:<BR><BR>";
			include("player-log/".$playerinfo[ship_id]);
			unlink("player-log/".$playerinfo[ship_id]);
			$result = mysql_query ("DELETE FROM ships WHERE ship_id = $playerinfo[ship_id]");
			echo "Dead player has now been deleted.  Click <a href=new.php3>here</a> to start with a new player.";
		}
	} elseif ($result>0 && strtolower($playerinfo[email])==strtolower($email) && $playerinfo[password]!=$pass) 
	{
		/* player exists, password is INcorrect */		
		echo "The password you entered is incorrect.<BR><BR>  If you have forgotten your password, click <a href=mail.php3?mail=$email>here</a> to have it e-mailed to you.<BR><BR>  Otherwise, click <a href=login.php3>here</a> to try again.  Attempt logged with IP address of $ip...";
		playerlog($playerinfo[ship_id],"Bad login attempt from ".$ip);
		
	}  

	include("footer.php3");

?>
