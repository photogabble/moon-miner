<?
	include("config.php3");
	updatecookie();

	$title="Help!";
	include("header.php3");

	connectdb();

	if (checklogin()) {die();}

	$result = mysql_query ("SELECT * FROM ships WHERE email='$username'");
	$playerinfo=mysql_fetch_array($result);

        bigtitle();

	echo "Greetings and welcome to $game_name.";
	echo "<BR><BR>The basic premise of the games is to generate revenue and power - and crush those who oppose you.  Pretty Simple, huh?<BR><BR>";
	echo "<H2>Main Menu Commands:</H2>";
	echo "<B>Move:</B>  You may move from one sector to another through 'warp-links', by clicking on them from the main menu.  'Links lead to the following sectors (click to move):' would be the visual cue.<BR><BR>";
	echo "<B>LR Scan:</B>  You may 'peek' into a sector with your scanners without actually moving there.  Simply click on the sector number after 'LR scan a sector:'.<BR><BR>";
	echo "<B>Scan a ship:</B>  You may scan a ship (if there are other ships in the same sector as you) by clicking on the ship name following 'The are other ships in this sector (click to scan - if blank, there may be cloaked ships):'.  This may or may not work depending on your sensor level, vs. your target's cloak level.<BR><BR>";
	echo "<B>Attack a ship:</B>  You may attack a ship (again, if there's one to attack) by clicking on 'attack' after the ship name.<BR><BR>";
	echo "<B>Mail a Captain:</B>  You may send an e-mail to a ship's captain by clicking on the 'mail' link following the ship name.  You may also send a message to any player in the game by clicking 'Send Message' at the bottom of the page.";
	echo "<B>Dock at port:</B>  If there is a port in your sector, you may dock in order to trade commodities, or purchase items for your ship by clicking on the port type in the description (eg. 'There is a goods port here.'.  If the type is 'special' you may purchase ship upgrades and supplies there.<BR><BR>";
	echo "<B>Use Device:</B>  Takes you to the 'Use Device' menu.<BR><BR>";
	echo "<B>Report:</B>  Gives a report on your ship, and its contents.<BR><BR>";
	echo "<B>View Log:</B>  Let's you view (and optionally clear) a log of events that have happened to your ship.<BR><BR>";
	echo "<B>Realspace Move:</B>  Based on the tech level of your engines you may move your ship through 'real space' rather than taking warp-links.  If you have a fuel scoop, you will collect energy units along the way.<BR><BR>";
	echo "<B>Logout:</B>  Removes any game cookies from your system, ending your session.<BR><BR>";
	echo "<B>Options:</B>  Allows you to change your password.<BR><BR>";
	echo "<B>Rankings:</B>  Shows a list of all players, with scores based on aquisitions, in a rank structure.<BR><BR>";
	echo "<B>Feedback:</B>  Send the webmaster a message.<BR><BR>";
	echo "<H2>Use Device</H2>";
	echo "<B>Genesis Device:</B>  Create a planet in current sector.<BR>";
	echo "<B>Warp Editor:</B>  Allows you to create/destroy warp-links to another sector.<BR><BR>";
	echo "<B>Space Beacon:</B>  Allows you to post a 'sign' for all other players to see in a sector.<BR><BR>";
	echo "<B>Emergency Warp:</B>  If engaged manually, this device warps you to a random sector in the galaxy.  If you have one of these, and you are attacked - you will randomly warp to another sector in the galaxy.<BR><BR>";
	echo "<B>Escape Pod:</B>  If you are attacked and your ship is destroyed, this device will allow you to start over, maintaining you credit balance, and planet ownership.<BR><BR>";
	echo "<B>Fuelscoop:</B>  See Real Space Move.<BR><BR>";
	echo "<B>Mine Deflector:</B>  When Mines are enabled in the game (RSN) each of these devices will take out 1 mine so that it does not damage your ship.  The device is destroyed in the process.<BR><BR>";
	echo "<B>Sector 0 warning!</B>  if you remain in sector 0, there is a chance of damage due to collision - the bigger your hull, the bigger the chance... and collisions will damge your armour - potentially destorying your ship...<BR><BR>";	


	echo "Click <a href=main.php3>here</a> to return to main menu.";
	include("footer.php3");

?> 
