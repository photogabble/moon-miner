<?
	include("config.php3");
	updatecookie();

	$title="Alliances";
	include("header.php3");
	connectdb();

	if (checklogin()) {die();}
bigtitle();
$testing = false; // set to false to get rid of password when creating new alliance

// get user info
$result = mysql_query("SELECT * FROM ships WHERE email='$username'");
$playerinfo = mysql_fetch_array($result);

function showinfo($whichteam,$isowner)
{
	global $playerinfo;

	$result = mysql_query("SELECT * FROM teams WHERE id=$whichteam");
	$team = mysql_fetch_array($result);
	$result = mysql_query("SELECT * FROM ships WHERE team=$whichteam");
	echo "Statistics on <B>$team[team_name]</B>:<BR>";
	while ($member = mysql_fetch_array($result)) {
		echo " - $member[character_name] (Score $member[score])";
		if ($isowner && ($member[ship_id] != $playerinfo[ship_id])) {
			echo " - <a href=\"$PHP_SELF?teamwhat=5&who=$member[ship_id]\">Eject</A><BR>";
		} else {
			if ($member[ship_id] == $team[creator])
				echo " - Co-ordinator";
			echo "<BR>";
		}
	}
	echo "<BR>";
	//if ($isowner) {
		$res = mysql_query("SELECT ship_id,character_name FROM ships WHERE team_invite=$whichteam");
		if (mysql_num_rows($res) > 0) {
			echo "The following players have been invited to be part of <B>$team[team_name]</B>:<BR>";
			while ($who = mysql_fetch_array($res)) {
				echo " - $who[character_name]<BR>";
			}
		} else {
			echo "Nobody has been invited to be part of <B>$team[team_name]</B>.<BR>";
		}
//	}
	echo "<BR>";
}

switch ($teamwhat) {
	case 1:	// INFO on sigle alliance
		showinfo($whichteam,0);
		echo "<BR><BR>Click <a href=\"$PHP_SELF\">here</a> to go back to the Alliances Menu.<BR><BR>";
		break;
	case 2:	// LEAVE 
		mysql_query("LOCK TABLES ships WRITE, teams WRITE");
		$result = mysql_query("SELECT * FROM teams WHERE id=$whichteam");
		$team = mysql_fetch_array($result);
		if (!$confirmleave) {
			echo "Are you sure you want to leave <B>$team[team_name]</B> ? <a href=\"$PHP_SELF?teamwhat=$teamwhat&confirmleave=1&whichteam=$whichteam\">YES</a> - <A HREF=\"$PHP_SELF\">NO</A><BR><BR>";
		} elseif ($confirmleave == 1) {
			if ($team[number_of_members] == 1) {
				mysql_query("DELETE FROM teams WHERE id=$whichteam");
				mysql_query("UPDATE ships SET team='0' WHERE ship_id='$playerinfo[ship_id]'");
				mysql_query("UPDATE ships SET team_invite=0 WHERE team_invite=$whichteam");
				echo "You were the only member, thus <B>$team[team_name]</B> is no more.<BR><BR>";
				playerlog($playerinfo[ship_id],"You have left the alliance <B>$team[team_name]</B>. It is no more.");
			} else {
				if ($team[creator] == $playerinfo[ship_id]) {
					echo "You are the co-ordinator of <B>$team[team_name]</B>. You must relinquish your role to another player.<BR><BR>";
					echo "<FORM ACTION='$PHP_SELF' METHOD=POST>";
					echo "<TABLE><INPUT TYPE=hidden name=teamwhat value=$teamwhat><INPUT TYPE=hidden name=confirmleave value=2><INPUT TYPE=hidden name=whichteam value=$whichteam>";
					echo "<TR><TD>New co-ordinator:</TD><TD><SELECT NAME=newcreator>";
					$res = mysql_query("SELECT character_name,ship_id FROM ships WHERE team=$whichteam ORDER BY character_name ASC");
					while($row = mysql_fetch_array($res)) {
						if ($row[ship_id] != $team[creator])
							echo "<OPTION VALUE=$row[ship_id]>$row[character_name]";
					}
					echo "</SELECT></TD></TR>";
					echo "<TR><TD><INPUT TYPE=SUBMIT VALUE=Relinquish></TD></TR>";
					echo "</TABLE>";
					echo "</FORM>";
				} else {
					mysql_query("UPDATE ships SET team='0' WHERE ship_id='$playerinfo[ship_id]'");
					mysql_query("UPDATE teams SET number_of_members=number_of_members-1 WHERE id=$whichteam");
					echo "You have left alliance <B>$team[team_name]</B>.<BR><BR>";
				}
			} 
		} elseif ($confirmleave == 2) { // owner of a team is leaving and set a new owner
			$res = mysql_query("SELECT character_name FROM ships WHERE ship_id=$newcreator");
			$newcreatorname = mysql_fetch_array($res);
			echo "You have left alliance <B>$team[team_name]</B> relinquishing the functions of co-ordinator to $newcreatorname[character_name].<BR><BR>";
			mysql_query("UPDATE ships SET team='0' WHERE ship_id='$playerinfo[ship_id]'");
			mysql_query("UPDATE ships SET team=$newcreator WHERE team=$creator");
			mysql_query("UPDATE teams SET number_of_members=number_of_members-1,id=$newcreator WHERE id=$whichteam");
			playerlog($playerinfo[ship_id],"You have left alliance <B>$team[team_name]</B> relinquishing the functions of co-ordinator to $newcreatorname[character_name].");
			playerlog($newcreator,"$newcreatorname[character_name] has left alliance <B>$team[team_name]</B> giving you the function of co-ordinator");
		}
		mysql_query("UNLOCK TABLES");

		echo "<BR><BR>Click <a href=\"$PHP_SELF\">here</a> to go back to the Alliances Menu.<BR><BR>";
		break;
	case 3: // JOIN
		mysql_query("UPDATE ships SET team=$whichteam,team_invite=0 WHERE ship_id=$playerinfo[ship_id]");
		mysql_query("UPDATE teams SET number_of_members=number_of_members+1 WHERE id=$whichteam");		
		$result = mysql_query("SELECT * FROM teams WHERE id=$whichteam");
		$team = mysql_fetch_array($result);
		echo "Welcome to alliance <B>$team[team_name]</B>.<BR><BR>";
		playerlog($playerinfo[ship_id],"You have joined <B>$team[team_name]</B>.");
		playerlog($team[creator],"$playerinfo[character_name] has joined <B>$team[team_name]</B>.");
		echo "<BR><BR>Click <a href=\"$PHP_SELF\">here</a> to go back to the Alliances Menu.<BR><BR>";
		break;
	case 4: // LEAVE + JOIN - anche per coordinatori - caso speciale ?
	// mettere nel 2 e senza break -> 3
	//CREATOR LEAVE - mettere come caso speciale si 3
		echo "Not implemented yet. LEAVE+JOIN<BR><BR>";
		echo "<BR><BR>Click <a href=\"$PHP_SELF\">here</a> to go back to the Alliances Menu.<BR><BR>";
		break;
	case 5: // Eject member
		$result = mysql_query("SELECT * FROM ships WHERE ship_id=$who");
		$whotoexpel = mysql_fetch_array($result);
		if (!$confirmed) {
			echo "Are you sure you want to eject $whotoexpel[character_name]? <A HREF=\"$PHP_SELF?teamwhat=$teamwhat&confirmed=1&who=$who\">YES</A> - <a href=\"$PHP_SELF\">No</a><BR>";
		} else {
			mysql_query("LOCK TABLES ships WRITE, teams WRITE");
			// check whether the player we are ejecting might have already left in the meantime
			// should go here	if ($whotoexpel[team] == 
			mysql_query("UPDATE ships SET team='0' WHERE ship_id='$who'");
			mysql_query("UPDATE teams SET number_of_members=number_of_members-1 WHERE id=$whotoexpel[team]");
			playerlog($who,"You have been eject from the alliance you were part of.");
			echo "$whotoexpel[character_name] has been eject from the alliance!<BR>";
			mysql_query("UNLOCK TABLES");
		}
		echo "<BR><BR>Click <a href=\"$PHP_SELF\">here</a> to go back to the Alliances Menu.<BR><BR>";
		break;
	case 6: // Create Team
		if ($testing)
			if($swordfish != $adminpass) {
				echo "<FORM ACTION=\"$PHP_SELF\" METHOD=POST>";
				echo "Testing phase...<BR><BR>";
				echo "Password: <INPUT TYPE=PASSWORD NAME=swordfish SIZE=20 MAXLENGTH=20><BR><BR>";
				echo "<INPUT TYPE=hidden name=teamwhat value=$teamwhat>";
				echo "<INPUT TYPE=SUBMIT VALUE=OK><INPUT TYPE=RESET VALUE=Reset>";
				echo "</FORM>";
				echo "<BR><BR>";
				TEXT_GOTOMAIN();
				include("footer.php3");
				die();
			}
		if (!$teamname) {
			echo "<FORM ACTION=\"$PHP_SELF\" METHOD=POST>";
			echo "Enter the name of the Alliance: ";
			if ($testing)
				echo "<INPUT TYPE=hidden NAME=swordfish value='$swordfish'>";
			echo "<INPUT TYPE=hidden name=teamwhat value=$teamwhat>";
			echo "<INPUT TYPE=TEXT NAME=teamname SIZE=40 MAXLENGTH=40>";
			echo "<INPUT TYPE=SUBMIT VALUE=OK><INPUT TYPE=RESET VALUE=Reset>";
			echo "</FORM>";
			echo "<BR><BR>";
		} else {
			mysql_query("LOCK TABLES ships WRITE, teams WRITE");
			$res = mysql_query("INSERT INTO teams (id,creator,team_name,number_of_members) VALUES ('$playerinfo[ship_id]','$playerinfo[ship_id]','$teamname','1')");
			mysql_query("UPDATE ships SET team='$playerinfo[ship_id]' WHERE ship_id='$playerinfo[ship_id]'");
			mysql_query("UNLOCK TABLES");
			echo "Alliance <B>$teamname</B> has been created and you are its leader.<BR><BR>";
			playerlog($playerinfo[ship_id],"You have created Alliance <B>$teamname</B>");
		}
		echo "<BR><BR>Click <a href=\"$PHP_SELF\">here</a> to go back to the Alliances Menu.<BR><BR>";
		break;
	case 7: // INVITE player
		if (!$invited) {
			echo "<FORM ACTION='$PHP_SELF' METHOD=POST>";
			echo "<TABLE><INPUT TYPE=hidden name=teamwhat value=$teamwhat><INPUT TYPE=hidden name=invited value=1><INPUT TYPE=hidden name=whichteam value=$whichteam>";
			echo "<TR><TD>Select the Player you want to invite:</TD><TD><SELECT NAME=who>";
			$res = mysql_query("SELECT character_name,ship_id FROM ships WHERE team<>$whichteam ORDER BY character_name ASC");
			while($row = mysql_fetch_array($res)) {
				if ($row[ship_id] != $team[creator])
					echo "<OPTION VALUE=$row[ship_id]>$row[character_name]";
			}
			echo "</SELECT></TD></TR>";
			echo "<TR><TD><INPUT TYPE=SUBMIT VALUE=Invite></TD></TR>";
			echo "</TABLE>";
			echo "</FORM>";

		} else {
			$res = mysql_query("SELECT character_name,team_invite FROM ships WHERE ship_id=$who");
			$newpl = mysql_fetch_array($res);
			if ($newpl[team_invite]) {
				echo "Sorry, but $newpl[character_name] has already been invited to be part of an alliance - only one invitation can be active at any given time.<BR><BR>";
			} else {
				$result = mysql_query("SELECT * FROM teams WHERE id=$whichteam");
				$team = mysql_fetch_array($result);
				mysql_query("UPDATE ships SET team_invite=$whichteam WHERE ship_id=$who");
				echo("Player invited.<BR>You must wait for that player to aknowledge your invitation.<BR>");
				playerlog($who,"You have been invited to be part of <B>$team[team_name]</B>.");
			}
		}
		echo "<BR><BR>Click <a href=\"$PHP_SELF\">here</a> to go back to the Alliances Menu.<BR><BR>";
		break;
	case 8: // REFUSE invitation
		$result = mysql_query("SELECT * FROM teams WHERE id=$whichteam");
		$team = mysql_fetch_array($result);
		echo "You have refused the invitation to join <B>$team[team_name]</B>.<BR><BR>";
		mysql_query("UPDATE ships SET team_invite=0 WHERE ship_id=$playerinfo[ship_id]");
		playerlog($team[creator],"$playerinfo[character_name] refused to join <B>$team[team_name]</B>");
		echo "<BR><BR>Click <a href=\"$PHP_SELF\">here</a> to go back to the Alliances Menu.<BR><BR>";
		break;
	default:
		if (!$playerinfo[team]) {
			echo "You are not a member of any alliance";
			if (!$playerinfo[team_invite]) {
				echo " and nobody has invited you to join one.<BR>";
				echo "If you want to join an alliance, send a message to its co-ordinator asking to be invited.<BR><BR>";
				echo "Click <a href=\"$PHP_SELF?teamwhat=6\">here</a> to create a new alliance.<BR><BR>";
			} else {
				$result = mysql_query("SELECT * FROM teams WHERE id=$playerinfo[team_invite]");
				$whichteam = mysql_fetch_array($result);
				echo " and you have been invited to join <a href=$PHP_SELF?teamwhat=1&whichteam=$playerinfo[team_invite]>$whichteam[team_name]</A>.";
				echo "<BR><BR>Click <A HREF=$PHP_SELF?teamwhat=3&whichteam=$playerinfo[team_invite]>here</A> to join <B>$whichteam[team_name]</B> or <A HREF=$PHP_SELF?teamwhat=8&whichteam=$playerinfo[team_invite]>here</A> to reject the invitation.<BR>";
				echo "If you want to join another alliance, send a message to its co-ordinator asking to be invited.<BR><BR>";
			} // if (!$playerinfo[team_invite])
		} else {
			if ($playerinfo[team] < 0) {
				$playerinfo[team] = -$playerinfo[team];
				$result = mysql_query("SELECT * FROM teams WHERE id=$playerinfo[team]");
				$whichteam = mysql_fetch_array($result);
				echo "<B>ATTENTION</B> - you have been ejected from <B>$whichteam[team_name]</B><BR><BR>";
				mysql_query("UPDATE ships SET team='0' WHERE ship_id='$playerinfo[ship_id]'");
				mysql_query("UPDATE teams SET number_of_members=number_of_members-1 WHERE id=$whichteam");
				playerlog($playerinfo[ship_id],"You have been ejected from <B>$whichteam[team_name]</B>");
				echo "<BR><BR>Click <a href=\"$PHP_SELF\">here</a> to go back to the Alliances Menu.<BR><BR>";
				break;
			}
			$result = mysql_query("SELECT * FROM teams WHERE id=$playerinfo[team]");
			$whichteam = mysql_fetch_array($result);
			if ($playerinfo[ship_id] == $whichteam[creator])
				echo "You are the co-ordinator";
			else
				echo "You are a member";
			echo " of <B>$whichteam[team_name]</B>";
			if ($playerinfo[team_invite]) {
				$result = mysql_query("SELECT * FROM teams WHERE id=$playerinfo[team_invite]");
				$whichinvitingteam = mysql_fetch_array($result);
				echo " and you have been invited to join <a href=$PHP_SELF?teamwhat=1&whichteam=$playerinfo[team_invite]>$whichinvitingteam[team_name]</A>.<BR>";
				echo "Click <A HREF=$PHP_SELF?teamwhat=2&whichteam=$playerinfo[team]>here</A> to leave <B>$whichteam[team_name]</B> or <A HREF=$PHP_SELF?teamwhat=8&whichteam=$playerinfo[team_invite]>here</A> to reject the invitation.<BR><BR>";
			} else {
				echo ".<BR><BR>";
				echo "Click <A HREF=$PHP_SELF?teamwhat=2&whichteam=$playerinfo[team]>here</A> to leave <B>$whichteam[team_name]</B>.<BR><BR>";
			}
			$isowner = $playerinfo[ship_id] == $whichteam[creator];
			showinfo($playerinfo[team],$isowner);
			echo "Click <A HREF=$PHP_SELF?teamwhat=7&whichteam=$playerinfo[team]>here</A> to invite a player to be part of your alliance.<BR><BR>";
		} // if (!$playerinfo[team])
		$query = "SELECT * FROM teams";
		if(!empty($sort)) {
			$query .= " ORDER BY";
			if ($sort == "name") {
				$query .= " team_name ASC";
			} elseif ($sort == "members") {
				$query .= " number_of_members ASC";
			} else
				$query .= " id ASC";
		}
		$result = mysql_query($query);
		$num_teams = 0;
		if ($result) {
			while($row = mysql_fetch_array($result)) {
				$team[$num_teams] = $row;
				$num_teams++;
			}
		}
		if ($num_teams > 0) {
			echo "Alliances present in the Galaxy: <BR>";
			echo "<TABLE WIDTH=\"100%\" BORDER=0 CELLSPACING=0 CELLPADDING=2>";
			echo "<TR BGCOLOR=\"$color_header\">";
			echo "<TD><B><A HREF=$PHP_SELF?sort=name>Name</A></B></TD>";
			echo "<TD><B><A HREF=$PHP_SELF?sort=members>Members</A></B></TD>";
			echo "<TD><B>Co-ordinator</B></TD>";
			echo "<TD><B>Score</B></TD>";
			echo "</TR>";
			for($i=0; $i<$num_teams; $i++) {
				$res = mysql_query("SELECT character_name from ships WHERE ship_id=".$team[$i][creator]);
				$creator = mysql_fetch_array($res);
				$res = mysql_query("SELECT score from ships WHERE team=".$team[$i][id]);
				$score = 0;
				while ($plscore = mysql_fetch_array($res)) {
					$score += pow($plscore[score],2);
				}
				$score = SQRT($score);
				echo "<TR BGCOLOR=\"$color\">";
				echo "<TD><a href=$PHP_SELF?teamwhat=1&whichteam=".$team[$i][id].">".$team[$i][team_name]."</A></TD>";
				echo "<TD>".$team[$i][number_of_members]."</TD>";
				echo "<TD><a href=teams-mailto.php?recipient=".$team[$i][creator].">".$creator[character_name]."</A></TD>";
				echo "<TD>$score</TD>";
				echo "</TR>";
			}
			echo "</table><BR>";
		} else {
			echo "There are no alliances in the Galaxy at this time.<BR><BR>";
		} //if ($teams)
	break;
} // switch ($teamwhat)

	echo "<BR><BR>";
	TEXT_GOTOMAIN();

	include("footer.php3");
?>

