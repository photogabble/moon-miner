<?
include("extension.inc");
	include("config.php3");
	updatecookie();

	$title="Alliances";
	include("header.php3");
	connectdb();

	if (checklogin()) {die();}
   bigtitle();
   $testing = false; // set to false to get rid of password when creating new alliance

/*
   Setting up some recordsets.
   
   I noticed before the rewriting of this page 
   that in some case recordset may be fetched 
   more thant once, which is NOT optimized.
*/

/* Get user info */
$result        = mysql_query(" SELECT ships.*, ships.ship_id, teams.team_name, teams.description, teams.creator, teams.id    
                        FROM `ships`
                        LEFT JOIN teams ON ships.team = teams.id
                        WHERE ships.email='$username'") or die(mysql_error());
$playerinfo    = mysql_fetch_array($result);

/*
   We do not want to query the database 
   if it is not necessary.
*/
if ($playerinfo[team_invite] != "") {
   /* Get invite info */
   $invite        = mysql_query(" SELECT  ships.ship_id, ships.team_invite, teams.team_name,teams.id    
                        FROM `ships`
                        LEFT JOIN teams ON ships.team_invite = teams.id
                        WHERE ships.email='$username'") or die(mysql_error());
   $invite_info   = mysql_fetch_array($invite);
}

/* 
   Get Team Info
*/
if ($whichteam) 
{
   $result_team   = mysql_query("SELECT * FROM teams WHERE id=$whichteam") or die(mysql_error());
   $team          = mysql_fetch_array($result_team);
} else {
   $result_team   = mysql_query("SELECT * FROM teams WHERE id=$playerinfo[team]") or die(mysql_error());
   $team          = mysql_fetch_array($result_team);   
}


function LINK_BACK()
{
   global $PHP_SELF;
   echo "<BR><BR>Click <a href=\"$PHP_SELF\">here</a> to go back to the Alliances Menu.<BR><BR>";
}

/*
   Rewrited display of alliances list 
*/ 
function DISPLAY_ALL_ALLIANCES() 
{
   global $color, $color_header, $order, $type, $PHP_SELF;
   
   echo "<br><br>Alliances present in the Galaxy: <BR>";
   echo "<TABLE WIDTH=\"100%\" BORDER=0 CELLSPACING=0 CELLPADDING=2>";
   echo "<TR BGCOLOR=\"$color_header\">";
   
   if ($type == "d") {
      $type = "a";
      $by = "ASC";
   } else {
      $type = "d";
      $by = "DESC";
   }
   echo "<TD><B><A HREF=$PHP_SELF?order=team_name&type=$type>Name</A></B></TD>";
   echo "<TD><B><A HREF=$PHP_SELF?order=number_of_members&type=$type>Members</A></B></TD>";
   echo "<TD><B><A HREF=$PHP_SELF?order=character_name&type=$type>Co-ordinator</A></B></TD>";
   echo "<TD><B><A HREF=$PHP_SELF?order=total_score&type=$type>Score</A></B></TD>";
   echo "</TR>";
   $sql_query = "SELECT ships.character_name,
                     COUNT(*) as number_of_members,
                     SUM(ships.score) as total_score,
                     teams.id,
                     teams.team_name,
                     teams.creator
                  FROM ships 
                  LEFT JOIN teams ON ships.team = teams.id
                  WHERE ships.team = teams.id 
                  GROUP BY teams.team_name";
   /*
      Setting if the order is Ascending or descending, if any.
      Default is ordered by teams.team_name 
   */
   if ($order)
   {
      $sql_query = $sql_query ." ORDER BY " . $order . " $by"; 
   }
   $res = mysql_query($sql_query) or die(mysql_error());
   while($row = mysql_fetch_array($res)) {
   	echo "<TR BGCOLOR=\"$color\">";
   	echo "<TD><a href=$PHP_SELF?teamwhat=1&whichteam=".$row[id].">".$row[team_name]."</A></TD>";
   	echo "<TD>".$row[number_of_members]."</TD>";
   	echo "<TD><a href=teams-mailto.php?recipient=".$row[creator].">".$row[character_name]."</A></TD>";
   	echo "<TD>$row[total_score]</TD>";
   	echo "</TR>";
   }
   echo "</table><BR>";
}


function DISPLAY_INVITE_INFO() 
{
   global $playerinfo, $invite_info, $PHP_SELF;
   if (!$playerinfo[team_invite]) {
      echo "<br><br><font color=blue size=2><b>Nobody has invited you to join an alliance.</b></font><BR>";
      echo "If you want to join an alliance, send a message to its co-ordinator asking to be invited.<BR>";
      echo "Click <a href=\"$PHP_SELF?teamwhat=6\">here</a> to create a new alliance.<BR><BR>";
   } else {
	   echo "<br><br><font color=blue size=2><b>You have been invited to join ";
	   echo "<a href=$PHP_SELF?teamwhat=1&whichteam=$playerinfo[team_invite]>$invite_info[team_name]</A>.</b></font><BR>";
	   echo "Click <A HREF=$PHP_SELF?teamwhat=3&whichteam=$playerinfo[team_invite]>here</A> to join <B>$playerinfo[team_name]</B> or <A HREF=$PHP_SELF?teamwhat=8&whichteam=$playerinfo[team_invite]>here</A> to reject the invitation.<BR><BR>";
   }
}


function showinfo($whichteam,$isowner)
{
	global $playerinfo, $invite_info, $team;	

	/* Heading */
   echo"<div align=center>";
   echo "<h3><font color=white><B>$team[team_name]</B>";
 	echo "<br><font size=2>\"<i>$team[description]</i>\"</font></H3>";
   if ($playerinfo[team] == $team[id]) 
   {
      echo "<font color=white>";
   	if ($playerinfo[ship_id] == $team[creator]) {
   	   echo "Co-ordinator ";
      }
   	else
   	{
   		echo "Member ";
   	}
   	echo "Options<br><font size=2>";
   	if ($playerinfo[ship_id] == $team[creator]) 
   	{
   	   echo "[<a href=$PHP_SELF?teamwhat=9&whichteam=$playerinfo[team]>Edit</a>] - ";
   	}
   	echo "[<a href=$PHP_SELF?teamwhat=7&whichteam=$playerinfo[team]>Invite</a>] - [<a href=$PHP_SELF?teamwhat=2&whichteam=$playerinfo[team]>Leave</a>]</font></font>";	
   }
   DISPLAY_INVITE_INFO();
   echo "</div>";
   
   /* Main table */
	echo "<table border=2 cellspacing=2 cellpadding=2 bgcolor=\"#400040\" width=\"75%\" align=center>";
	echo "<tr>";
	echo "<td><font color=white>Members</font></td>";
	echo "</tr><tr bgcolor=$color_line2>";
	$result  = mysql_query("SELECT * FROM ships WHERE team=$whichteam");
	while ($member = mysql_fetch_array($result)) {
		echo "<td> - $member[character_name] (Score $member[score])";
		if ($isowner && ($member[ship_id] != $playerinfo[ship_id])) {
			echo " - <font size=2>[<a href=\"$PHP_SELF?teamwhat=5&who=$member[ship_id]\">Eject</A>]</font></td>";
		} else {
			if ($member[ship_id] == $team[creator])
			{
				echo " - Co-ordinator</td>";
			}
		}
		echo "</tr><tr bgcolor=$color_line2>";
	}
   /* Displays for members name */
   $res = mysql_query("SELECT ship_id,character_name FROM ships WHERE team_invite=$whichteam");
	echo "<td bgcolor=$color_line2><font color=white>Invitation pending for <B>$team[team_name]</B></font></td>";
   echo "</tr><tr>";
	if (mysql_num_rows($res) > 0) {
		echo "</tr><tr bgcolor=$color_line2>";
		while ($who = mysql_fetch_array($res)) {
			echo "<td> - $who[character_name]</td>";
		   echo "</tr><tr bgcolor=$color_line2>";
		}
	} else {
		echo "<td>Nobody has been invited to be part of <B>$team[team_name]</B>.</td>";
      echo "</tr><tr>";
	}
	echo "</tr></table>";
}

switch ($teamwhat) {
	case 1:	// INFO on sigle alliance
		showinfo($whichteam, 0);
      LINK_BACK();
		break;
	case 2:	// LEAVE 
		if (!$confirmleave) {
			echo "Are you sure you want to leave <B>$team[team_name]</B> ? <a href=\"$PHP_SELF?teamwhat=$teamwhat&confirmleave=1&whichteam=$whichteam\">YES</a> - <A HREF=\"$PHP_SELF\">NO</A><BR><BR>";
		} elseif ($confirmleave == 1) {
			if ($team[number_of_members] == 1) {
				mysql_query("DELETE FROM teams WHERE id=$whichteam");
				mysql_query("UPDATE ships SET team='0' WHERE ship_id='$playerinfo[ship_id]'");
				mysql_query("UPDATE ships SET team_invite=0 WHERE team_invite=$whichteam");


        $res = mysql_query("SELECT DISTINCT sector_id FROM planets WHERE owner=$playerinfo[ship_id] AND base='Y' AND corp!=0");

        $i=0;

        while($row = mysql_fetch_array($res))

        {

          $sectors[$i] = $row[sector_id];

          $i++;

        }

				

        mysql_query("UPDATE planets SET corp=0 WHERE owner=$playerinfo[ship_id]");

        if(!empty($sectors))

        {

          foreach($sectors as $sector)

          {

            calc_ownership($sector);

          }

        }

        

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


          $res = mysql_query("SELECT DISTINCT sector_id FROM planets WHERE owner=$playerinfo[ship_id] AND base='Y' AND corp!=0");

          $i=0;

          while($row = mysql_fetch_array($res))

          {

            $sectors[$i] = $row[sector_id];

            $i++;

          }

				

          mysql_query("UPDATE planets SET corp=0 WHERE owner=$playerinfo[ship_id]");

          if(!empty($sectors))

          {

            foreach($sectors as $sector)

            {

              calc_ownership($sector);

            }

          }



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


      $res = mysql_query("SELECT DISTINCT sector_id FROM planets WHERE owner=$playerinfo[ship_id] AND base='Y' AND corp!=0");

      $i=0;

      while($row = mysql_fetch_array($res))

      {

        $sectors[$i] = $row[sector_id];

        $i++;

      }

				

      mysql_query("UPDATE planets SET corp=0 WHERE owner=$playerinfo[ship_id]");

      if(!empty($sectors))

      {

        foreach($sectors as $sector)

        {

          calc_ownership($sector);

        }

      }



			playerlog($playerinfo[ship_id],"You have left alliance <B>$team[team_name]</B> relinquishing the functions of co-ordinator to $newcreatorname[character_name].");
			playerlog($newcreator,"$newcreatorname[character_name] has left alliance <B>$team[team_name]</B> giving you the function of co-ordinator");
		}

		LINK_BACK();
		break;
	case 3: // JOIN
		mysql_query("UPDATE ships SET team=$whichteam,team_invite=0 WHERE ship_id=$playerinfo[ship_id]");
		mysql_query("UPDATE teams SET number_of_members=number_of_members+1 WHERE id=$whichteam");
		echo "Welcome to alliance <B>$team[team_name]</B>.<BR><BR>";
		playerlog($playerinfo[ship_id],"You have joined <B>$team[team_name]</B>.");
		playerlog($team[creator],"$playerinfo[character_name] has joined <B>$team[team_name]</B>.");
		LINK_BACK();
		break;
	case 4: 
   	/* 
   	   Can you comment in english please ??
   
      	// LEAVE + JOIN - anche per coordinatori - caso speciale ?
      	// mettere nel 2 e senza break -> 3
      	// CREATOR LEAVE - mettere come caso speciale si 3
   		
   	*/
		echo "Not implemented yet. LEAVE+JOIN<BR><BR>";
		LINK_BACK();
		break;
	case 5: // Eject member
		$result = mysql_query("SELECT * FROM ships WHERE ship_id=$who");
		$whotoexpel = mysql_fetch_array($result);
		if (!$confirmed) {
			echo "Are you sure you want to eject $whotoexpel[character_name]? <A HREF=\"$PHP_SELF?teamwhat=$teamwhat&confirmed=1&who=$who\">YES</A> - <a href=\"$PHP_SELF\">No</a><BR>";
		} else {
			/* 
			   check whether the player we are ejecting might have already left in the meantime
			   should go here	if ($whotoexpel[team] == 
			*/
			mysql_query("UPDATE ships SET team='0' WHERE ship_id='$who'");
         /*
            No more necessary due to COUNT(*) in previous SQL statement 
         
         	mysql_query("UPDATE teams SET number_of_members=number_of_members-1 WHERE id=$whotoexpel[team]");
         */
			playerlog($who,"You have been ejected from the alliance you were part of.");
			echo "$whotoexpel[character_name] has been eject from the alliance!<BR>";
		}
		LINK_BACK();
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
			echo "<INPUT TYPE=TEXT NAME=teamname SIZE=40 MAXLENGTH=40><BR>";
			echo "Enter a description of the Alliance: ";
			echo "<INPUT TYPE=TEXT NAME=teamdesc SIZE=40 MAXLENGTH=254><BR>";
			echo "<INPUT TYPE=SUBMIT VALUE=OK><INPUT TYPE=RESET VALUE=Reset>";
			echo "</FORM>";
			echo "<BR><BR>";
		} else {
			$res = mysql_query("INSERT INTO teams (id,creator,team_name,number_of_members,description) VALUES ('$playerinfo[ship_id]','$playerinfo[ship_id]','$teamname','1','$teamdesc')");
         mysql_query("INSERT INTO zones VALUES('','$teamname\'s Empire', $playerinfo[ship_id], 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 0)");
         mysql_query("UPDATE ships SET team='$playerinfo[ship_id]' WHERE ship_id='$playerinfo[ship_id]'");
			echo "Alliance <B>$teamname</B> has been created and you are its leader.<BR><BR>";
			playerlog($playerinfo[ship_id],"You have created Alliance <B>$teamname</B>");
		}
		LINK_BACK();
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
				mysql_query("UPDATE ships SET team_invite=$whichteam WHERE ship_id=$who");
				echo("Player invited.<BR>You must wait for that player to aknowledge your invitation.<BR>");
				playerlog($who,"You have been invited to be part of <B>$invite_info[team_name]</B>.");
			}
		}
		echo "<BR><BR>Click <a href=\"$PHP_SELF\">here</a> to go back to the Alliances Menu.<BR><BR>";
		break;
	case 8: // REFUSE invitation
		echo "You have refused the invitation to join <B>$invite_info[team_name]</B>.<BR><BR>";
		mysql_query("UPDATE ships SET team_invite=0 WHERE ship_id=$playerinfo[ship_id]");
		playerlog($team[creator],"$playerinfo[character_name] refused to join <B>$invite_info[team_name]</B>");
		LINK_BACK();
		break;
	case 9: // Edit Team
		if ($testing){
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
	   }
	   if ($playerinfo[team] == $whichteam) {
   		if (!$update) {
   			echo "<FORM ACTION=\"$PHP_SELF\" METHOD=POST>";
   			echo "Edit the name of your Alliance: <BR>";
   			echo "<INPUT TYPE=hidden NAME=swordfish value='$swordfish'>";
   			echo "<INPUT TYPE=hidden name=teamwhat value=$teamwhat>";
   			echo "<INPUT TYPE=hidden name=whichteam value=$whichteam>";
   			echo "<INPUT TYPE=hidden name=update value=true>";
   			echo "<INPUT TYPE=TEXT NAME=teamname SIZE=40 MAXLENGTH=40 VALUE=\"".$team[team_name]."\"><BR>";
   			echo "Edit the description for your Alliance: <BR>";
   			echo "<INPUT TYPE=TEXT NAME=teamdesc SIZE=40 MAXLENGTH=254 VALUE=\"".$team[description]."\"><BR>";
   			echo "<INPUT TYPE=SUBMIT VALUE=SUBMIT><INPUT TYPE=RESET VALUE=Reset>";
   			echo "</FORM>";
   			echo "<BR><BR>";
   		} else {
   			$res = mysql_query("UPDATE teams SET team_name='$teamname', description='$teamdesc' WHERE id=$whichteam") or die("<font color=red>error: " . mysql_error() . "</font>");
   			echo "Alliance <B>$teamname</B> has been renamed.<BR><BR>";
   			/*
   			   Adding a log entry to all members of the renamed alliance
   			*/
   		   $result_team_name = mysql_query("SELECT ship_id FROM ships WHERE team=$whichteam AND ship_id<>$playerinfo[ship_id]") or die("<font color=red>error: " . mysql_error() . "</font>");
   			playerlog($playerinfo[ship_id],"You have renamed your alliance in <B>$teamname</B>");
   			while($teamname_array = mysql_fetch_array($result_team_name)) {
   			   playerlog($teamname_array[ship_id],"Your leader has renamed alliance in <B>$teamname</B>");
            }
     		}
   		LINK_BACK();
   		break;
	   }
	   else
	   {
   		echo "<b><font color=red>An error occured</font></b><br>You are not the leader of this Alliance.";
   		LINK_BACK();
   		break;	      
	   }
	default:
		if (!$playerinfo[team]) {
			echo "You are not a member of any alliance";
			DISPLAY_INVITE_INFO();
		} else {
			if ($playerinfo[team] < 0) {
				$playerinfo[team] = -$playerinfo[team];
				$result = mysql_query("SELECT * FROM teams WHERE id=$playerinfo[team]");
				$whichteam = mysql_fetch_array($result);
				echo "<B>ATTENTION</B> - you have been ejected from <B>$whichteam[team_name]</B><BR><BR>";
            /*
               No more necessary due to COUNT(*) in previous SQL statement 
               AND already done in case 5:
               
               mysql_query("UPDATE ships SET team='0' WHERE ship_id='$playerinfo[ship_id]'");
   				mysql_query("UPDATE teams SET number_of_members=number_of_members-1 WHERE id=$whichteam");
				   playerlog($playerinfo[ship_id],"You have been ejected from <B>$whichteam[team_name]</B>");
            */
				LINK_BACK();
				break;
			}
			$result = mysql_query("SELECT * FROM teams WHERE id=$playerinfo[team]");
			$whichteam = mysql_fetch_array($result);
			if ($playerinfo[team_invite]) {
				$result = mysql_query("SELECT * FROM teams WHERE id=$playerinfo[team_invite]");
				$whichinvitingteam = mysql_fetch_array($result);
			}
			$isowner = $playerinfo[ship_id] == $whichteam[creator];
			showinfo($playerinfo[team],$isowner);
		} 
		$res= mysql_query("SELECT COUNT(*) as TOTAL FROM teams");
		$num_res = mysql_fetch_array($res);
		if ($num_res[TOTAL] > 0) {
         DISPLAY_ALL_ALLIANCES();
		} else {
			echo "There are no alliances in the Galaxy at this time.<BR><BR>";
		}
	break;
} // switch ($teamwhat)

	echo "<BR><BR>";
	TEXT_GOTOMAIN();

	include("footer.php3");
?>

