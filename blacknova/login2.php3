<?

include("config.php3");
connectdb();

//test to see if server is closed to logins
if($server_closed)
{
  $title="Server Closed";
  include("header.php3");
  die($server_closed_message);
}

$playerfound = false;

$res = mysql_query("SELECT * FROM ships WHERE email='$email'");
if($res)
{
  $playerfound = mysql_num_rows($res);
}
$playerinfo = mysql_fetch_array($res);
mysql_free_result($res);

/* first placement of cookie - don't use updatecookie. */
setcookie("username", $email);
setcookie("password", $pass);
setcookie("res", $res);
if($playerinfo[interface]=="N")
{
  $mainfilename="main.php3";
  $interface="main.php3";
}
else
{
  $mainfilename="maintext.php3";
  $interface="maintext.php3";
}
setcookie("interface", $mainfilename);



$title="Login Phase Two"; 
include("header.php3");

bigtitle();

if($playerfound) 
{
  if($playerinfo[password] == $pass)
  {
    // password is correct
    if($playerinfo[ship_destroyed] == "N")
    {
      // player's ship has not been destroyed
      playerlog($playerinfo[ship_id], "Logged in from " . $ip);
      $stamp = date("Y-m-d H-i-s");
      $update = mysql_query("UPDATE ships SET last_login='$stamp',ip_address='$ip' WHERE ship_id=$playerinfo[ship_id]");
	  TEXT_GOTOMAIN();
      echo "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"0;URL=$interface?id=" . $playerinfo[ship_id] . "\">";
    }
    else
    {
      // player's ship has been destroyed
      if($playerinfo[dev_escapepod] == "Y") 
      {
        mysql_query("UPDATE ships SET hull=0,engines=0,power=0,computer=0,sensors=0,beams=0,torp_launchers=0,torps=0,armour=0,armour_pts=100,cloak=0,shields=0,sector=0,ship_ore=0,ship_organics=0,ship_energy=1000,ship_colonists=0,ship_goods=0,ship_fighters=100,ship_damage='',on_planet='N',dev_warpedit=0,dev_genesis=1,dev_beacon=0,dev_emerwarp=0,dev_escapepod='N',dev_fuelscoop='N',dev_minedeflector=0,ship_destroyed='N' where email='$username'");
        echo "Your ship was destroyed, but your escape pod saved you and your crew.  Click <A HREF=$interface>here</A> to continue with a new ship.";
      }
		else
		{
    	echo "You have died in a horrible incident, here is the blackbox information that was retrieved from your ships wreckage:<BR><BR>";
		include("player-log/" . $playerinfo[ship_id]);
    
        // Check if $newbie_nice is set, if so, verify ship limits
			if ($newbie_nice == "YES")
			{
				$newbie_info = mysql_query("SELECT hull,engines,power,computer,sensors,armour,shields,beams,torp_launchers,cloak FROM ships WHERE ship_id='$playerinfo[ship_id]' AND hull<='$newbie_hull' AND engines<='$newbie_engines' AND power<='$newbie_power' AND computer<='$newbie_computer' AND sensors<='$newbie_sensors' AND armour<='$newbie_armour' AND shields<='$newbie_shields' AND beams<='$newbie_beams' AND torp_launchers<='$newbie_torp_launchers' AND cloak<='$newbie_cloak'");
				$num_rows = mysql_num_rows($newbie_info);
				
				if ($num_rows)
				{
					echo "<BR><BR>Due to the seriousness of this crime and the vicious attack against you, the Federation has graciously decided to provide a free of charge cloning from your remains.  Sadly, even in this day and age, new clones can not own property, aside from the basics to make a living.  You will be provided with a basic ship and a few credits to get you started.  Hopefully your next life will be better.<BR><BR>";
					mysql_query("UPDATE ships SET hull=0,engines=0,power=0,computer=0,sensors=0,beams=0,torp_launchers=0,torps=0,armour=0,armour_pts=100,cloak=0,shields=0,sector=0,ship_ore=0,ship_organics=0,ship_energy=1000,ship_colonists=0,ship_goods=0,ship_fighters=100,credits=1000,ship_damage='',on_planet='N',dev_warpedit=0,dev_genesis=0,dev_beacon=0,dev_emerwarp=0,dev_escapepod='N',dev_fuelscoop='N',dev_minedeflector=0,ship_destroyed='N' where email='$username'");
					
					echo "Click <A HREF=$interface>here</A> to continue in a new life.";
				}
                else
				{
				echo "<BR><BR>You had no escape pod, and due to your modest success in this life, the Federation declines to clone you.  Your remains should eventually fall into a gravity well, hopefully to be used to create life elsewhere... several billion years from now.<BR><BR> Better luck next game...";
				}		
			
			} // End if $newbie_nice
			else
			{
				echo "<BR><BR>You had no escape pod, and due to your modest success in this life, the Federation declines to clone you.  Your remains should eventually fall into a gravity well, hopefully to be used to create life else where... several billion years from now.<BR><BR> Better luck next game...";
			}
		}
    }
  }
  else
  {
    // password is incorrect
    echo "The password you entered is incorrect.<BR><BR>  If you have forgotten your password, click <A HREF=mail.php3?mail=$email>here</A> to have it e-mailed to you.<BR><BR>  Otherwise, click <a href=login.php3>here</a> to try again.  Attempt logged with IP address of $ip...";
    playerlog($playerinfo[ship_id], "Bad login attempt from " . $ip);
  }
}
else
{
  echo "<B>No Such Player! - Create a new player <A HREF=new.php3>here</A>.</B><BR>";
}

include("footer.php3");

?>
