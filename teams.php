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
// File: teams.php

// Added a quick fix for creating a new team with the same name
// This file needs to be completely recoded from scratch :(

include("config.php");
updatecookie();

include("languages/$lang");
$title=$l_team_title;
include("header.php");

if (checklogin())
{
    die();
}

bigtitle();
$testing = false; // set to false to get rid of password when creating new team

$whichteam = stripnum($_REQUEST['whichteam']);
$teamwhat  = stripnum($_REQUEST['teamwhat']);
$confirmleave = stripnum($_REQUEST['confirmleave']);
$invited      = stripnum($_REQUEST['invited']);

// Setting up some recordsets.
// I noticed before the rewriting of this page that in some case recordset may be fetched more thant once, which is NOT optimized.

// Get user info.
$result = $db->Execute("SELECT $dbtables[ships].*, $dbtables[teams].team_name, $dbtables[teams].description, $dbtables[teams].creator, $dbtables[teams].id
            FROM $dbtables[ships]
            LEFT JOIN $dbtables[teams] ON $dbtables[ships].team = $dbtables[teams].id
            WHERE $dbtables[ships].email='$username'") or die($db->ErrorMsg());
$playerinfo    = $result->fields;

// Typecast into integers (this also removes all non numbers)
$teamwhat = NULL;
if(isset($_REQUEST['teamwhat']))
{
    $teamwhat  = (int)$_REQUEST['teamwhat'];
}

$whichteam = NULL;
if(isset($_REQUEST['whichteam']))
{
    $whichteam = (int)$_REQUEST['whichteam'];
}

// We do not want to query the database, if it is not necessary.
if ($playerinfo[team_invite] != "")
{
    // Get invite info
    $invite = $db->Execute(" SELECT $dbtables[ships].ship_id, $dbtables[ships].team_invite, $dbtables[teams].team_name,$dbtables[teams].id
            FROM $dbtables[ships]
            LEFT JOIN $dbtables[teams] ON $dbtables[ships].team_invite = $dbtables[teams].id
            WHERE $dbtables[ships].email='$username'") or die($db->ErrorMsg());
    $invite_info  = $invite->fields;
}

// Get Team Info
if (!is_null($whichteam))
{
    $result_team = $db->Execute("SELECT * FROM $dbtables[teams] WHERE id=$whichteam") or die($db->ErrorMsg());
    $team        = $result_team->fields;
}
else
{
    $result_team = $db->Execute("SELECT * FROM $dbtables[teams] WHERE id=$playerinfo[team]") or die($db->ErrorMsg());
    $team        = $result_team->fields;
}

switch ($teamwhat)
{
    case 1: // INFO on single team
    {
        showinfo($whichteam, 0);
        LINK_BACK();
        break;
    }

    case 2: // LEAVE
    {
        if(!isTeamMember($team, $playerinfo))
        {
            echo "<b><font color=red>An error occured</font></b><br>You are not a member of this Team.";
            LINK_BACK();
            break;
        }

        if (!$confirmleave)
        {
            echo "$l_team_confirmleave <B>$team[team_name]</B> ? <a href=\"teams.php?teamwhat=$teamwhat&confirmleave=1&whichteam=$whichteam\">$l_yes</a> - <A HREF=\"teams.php\">$l_no</A><BR><BR>";
        }
        elseif ($confirmleave == 1)
        {
            if ($team[number_of_members] == 1)
            {
                if(!isTeamOwner($team, $playerinfo))
                {
                    echo "$l_team_error";
                    LINK_BACK();
                    continue;
                }

                $db->Execute("DELETE FROM $dbtables[teams] WHERE id=$whichteam");
                $db->Execute("UPDATE $dbtables[ships] SET team='0' WHERE ship_id='$playerinfo[ship_id]'");
                $db->Execute("UPDATE $dbtables[ships] SET team_invite=0 WHERE team_invite=$whichteam");

                $res = $db->Execute("SELECT DISTINCT sector_id FROM $dbtables[planets] WHERE owner=$playerinfo[ship_id] AND base='Y'");
                $i=0;
                while(!$res->EOF)
                {
                    $row = $res->fields;
                    $sectors[$i] = $row[sector_id];
                    $i++;
                    $res->MoveNext();
                }

                $db->Execute("UPDATE $dbtables[planets] SET corp=0 WHERE owner=$playerinfo[ship_id]");
                if(!empty($sectors))
                {
                    foreach($sectors as $sector)
                    {
                        calc_ownership($sector);
                    }
                }
                defence_vs_defence($playerinfo[ship_id]);
                kick_off_planet($playerinfo[ship_id],$whichteam);

                $l_team_onlymember = str_replace("[team_name]", "<b>$team[team_name]</b>", $l_team_onlymember);
                echo "$l_team_onlymember<BR><BR>";
                playerlog($playerinfo[ship_id], LOG_TEAM_LEAVE, "$team[team_name]");
            }
            else
            {
                if (isTeamOwner($team, $playerinfo))
                {
                    echo "$l_team_youarecoord <B>$team[team_name]</B>. $l_team_relinq<BR><BR>";
                    echo "<FORM ACTION='teams.php' METHOD=POST>";
                    echo "<TABLE><INPUT TYPE=hidden name=teamwhat value=$teamwhat><INPUT TYPE=hidden name=confirmleave value=2><INPUT TYPE=hidden name=whichteam value=$whichteam>";
                    echo "<TR><TD>$l_team_newc</TD><TD><SELECT NAME=newcreator>";
                    $res = $db->Execute("SELECT character_name,ship_id FROM $dbtables[ships] WHERE team=$whichteam ORDER BY character_name ASC");
                    while(!$res->EOF)
                    {
                        $row = $res->fields;
                        if (!isTeamOwner($team, $row))
                        {
                            echo "<OPTION VALUE=$row[ship_id]>$row[character_name]";
                        }
                        $res->MoveNext();
                    }
                    echo "</SELECT></TD></TR>";
                    echo "<TR><TD><INPUT TYPE=SUBMIT VALUE=$l_submit></TD></TR>";
                    echo "</TABLE>";
                    echo "</FORM>";
                }
                else
                {
                    $db->Execute("UPDATE $dbtables[ships] SET team='0' WHERE ship_id='$playerinfo[ship_id]'");
                    $db->Execute("UPDATE $dbtables[teams] SET number_of_members=number_of_members-1 WHERE id=$whichteam");

                    $res = $db->Execute("SELECT DISTINCT sector_id FROM $dbtables[planets] WHERE owner=$playerinfo[ship_id] AND base='Y' AND corp!=0");
                    $i=0;
                    while(!$res->EOF)
                    {
                        $sectors[$i] = $res->fields[sector_id];
                        $i++;
                        $res->MoveNext();
                    }

                    $db->Execute("UPDATE $dbtables[planets] SET corp=0 WHERE owner=$playerinfo[ship_id]");
                    if(!empty($sectors))
                    {
                        foreach($sectors as $sector)
                        {
                            calc_ownership($sector);
                        }
                    }

                    echo "$l_team_youveleft <B>$team[team_name]</B>.<BR><BR>";
                    defence_vs_defence($playerinfo[ship_id]);
                    kick_off_planet($playerinfo[ship_id],$whichteam);
                    playerlog($playerinfo[ship_id], LOG_TEAM_LEAVE, "$team[team_name]");
                    playerlog($team[creator], LOG_TEAM_NOT_LEAVE, "$playerinfo[character_name]");
                }
            }
        }
        elseif ($confirmleave == 2)
        {
            // owner of a team is leaving and set a new owner
            $res = $db->Execute("SELECT character_name FROM $dbtables[ships] WHERE ship_id=$newcreator");
            $newcreatorname = $res->fields;
            echo "$l_team_youveleft <B>$team[team_name]</B> $l_team_relto $newcreatorname[character_name].<BR><BR>";
            $db->Execute("UPDATE $dbtables[ships] SET team='0' WHERE ship_id='$playerinfo[ship_id]'");
            $db->Execute("UPDATE $dbtables[ships] SET team=$newcreator WHERE team=$creator");
            $db->Execute("UPDATE $dbtables[teams] SET number_of_members=number_of_members-1,creator=$newcreator WHERE id=$whichteam");

            $res = $db->Execute("SELECT DISTINCT sector_id FROM $dbtables[planets] WHERE owner=$playerinfo[ship_id] AND base='Y' AND corp!=0");
            $i=0;
            while(!$res->EOF)
            {
                $sectors[$i] = $res->fields[sector_id];
                $i++;
                $res->MoveNext();
            }

            $db->Execute("UPDATE $dbtables[planets] SET corp=0 WHERE owner=$playerinfo[ship_id]");
            if(!empty($sectors))
            {
                foreach($sectors as $sector)
                {
                    calc_ownership($sector);
                }
            }

            playerlog($playerinfo[ship_id], LOG_TEAM_NEWLEAD, "$team[team_name]|$newcreatorname[character_name]");
            playerlog($newcreator, LOG_TEAM_LEAD,"$team[team_name]");
        }

        LINK_BACK();
        break;
    }

    case 3: // JOIN
    {
        if($playerinfo[team] <> 0)
        {
            echo $l_team_leavefirst . "<BR>";
        }
        else
        {
            if($playerinfo[team_invite] == $whichteam)
            {
                $db->Execute("UPDATE $dbtables[ships] SET team=$whichteam,team_invite=0 WHERE ship_id=$playerinfo[ship_id]");
                $db->Execute("UPDATE $dbtables[teams] SET number_of_members=number_of_members+1 WHERE id=$whichteam");
                echo "$l_team_welcome <B>$team[team_name]</B>.<BR><BR>";
                playerlog($playerinfo[ship_id], LOG_TEAM_JOIN, "$team[team_name]");
                playerlog($team[creator], LOG_TEAM_NEWMEMBER, "$team[team_name]|$playerinfo[character_name]");
            }
            else
            {
                echo "$l_team_noinviteto<BR>";
            }
        }
        LINK_BACK();
        break;
    }

    case 4:
    {
        echo "Not implemented yet. Sorry! :)<BR><BR>";
        LINK_BACK();
        break;
    }

    case 5: // Eject member
    {
        // Check if Co-ordinator of team.
        // If not display "An error occured, You are not the leader of this Team." message.
        // Then show link back and break;

        if(!isTeamOwner($team, $playerinfo))
        {
            echo "$l_team_error";
            LINK_BACK();
            continue;
        }
        else
        {
            $who = stripnum($who);
            $result = $db->Execute("SELECT * FROM $dbtables[ships] WHERE ship_id=$who");
            $whotoexpel = $result->fields;
            if (!$confirmed)
            {
                echo "$l_team_ejectsure $whotoexpel[character_name]? <A HREF=\"teams.php?teamwhat=$teamwhat&confirmed=1&who=$who\">$l_yes</A> - <a href=\"teams.php\">$l_no</a><BR>";
            }
            else
            {
                // check whether the player we are ejecting might have already left in the meantime
                // should go here if ($whotoexpel[team] ==

                $db->Execute("UPDATE $dbtables[planets] SET corp='0' WHERE owner='$who'");
                $db->Execute("UPDATE $dbtables[ships] SET team='0' WHERE ship_id='$who'");

                // No more necessary due to COUNT(*) in previous SQL statement
                // $db->Execute("UPDATE $dbtables[teams] SET number_of_members=number_of_members-1 WHERE id=$whotoexpel[team]");

                playerlog($who, LOG_TEAM_KICK, "$team[team_name]");
                echo "$whotoexpel[character_name] $l_team_ejected<BR>";
            }
            LINK_BACK();
        }
        break;
    }

    case 6: // Create Team
    {
        if($playerinfo['team'] <> 0)
        {
            echo $l_team_leavefirst . "<BR>";
            LINK_BACK();
            continue;
        }

        if (!$teamname)
        {
            echo "<FORM ACTION=\"teams.php\" METHOD=POST>";
            echo "$l_team_entername: ";
            if ($testing)
            {
                echo "<INPUT TYPE=hidden NAME=swordfish value='$swordfish'>";
            }
            echo "<INPUT TYPE=hidden name=teamwhat value=$teamwhat>";
            echo "<INPUT TYPE=TEXT NAME=teamname SIZE=40 MAXLENGTH=40><BR>";
            echo "$l_team_enterdesc: ";
            echo "<INPUT TYPE=TEXT NAME=teamdesc SIZE=40 MAXLENGTH=254><BR>";
            echo "<INPUT TYPE=SUBMIT VALUE=$l_submit><INPUT TYPE=RESET VALUE=$l_reset>";
            echo "</FORM>";
            echo "<BR><BR>";
        }
        else
        {
            $teamname = trim(htmlspecialchars($teamname));
            $teamdesc = trim(htmlspecialchars($teamdesc));

            if(!validate_team($teamname, $teamdesc, $playerinfo[ship_id]))
            {
                echo "<span style='color:#f00;'>Team Creation Failed</span><br>Sorry you have either entered an invalid Team name or Team Description.<br>\n";
                LINK_BACK();
                break;
            }

            $res = $db->Execute("INSERT INTO $dbtables[teams] (id,creator,team_name,number_of_members,description) VALUES ('$playerinfo[ship_id]','$playerinfo[ship_id]','$teamname','1','$teamdesc')");
            $db->Execute("INSERT INTO $dbtables[zones] VALUES(NULL,'$teamname\'s Empire', $playerinfo[ship_id], 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 0)");
            $db->Execute("UPDATE $dbtables[ships] SET team='$playerinfo[ship_id]' WHERE ship_id='$playerinfo[ship_id]'");
            echo "$l_team_team <B>$teamname</B> $l_team_hcreated.<BR><BR>";
            playerlog($playerinfo[ship_id], LOG_TEAM_CREATE, "$teamname");
        }
        LINK_BACK();
        break;
    }

    case 7: // INVITE player
    {
        if (!isTeamMember($team, $playerinfo))
        {
            echo "<BR>You are not in this team!<BR>";
            LINK_BACK();
            break;
        }

        if (!$invited)
        {
            echo "<FORM ACTION='teams.php' METHOD=POST>";
            echo "<TABLE><INPUT TYPE=hidden name=teamwhat value=$teamwhat><INPUT TYPE=hidden name=invited value=1><INPUT TYPE=hidden name=whichteam value=$whichteam>";
            echo "<TR><TD>$l_team_selectp:</TD><TD><SELECT NAME=who style='width:200px;'>";

            $res = $db->Execute("SELECT character_name,ship_id FROM $dbtables[ships] WHERE team<>$whichteam AND ship_destroyed ='N' AND turns_used >0 ORDER BY character_name ASC");
            while(!$res->EOF)
            {
                $row = $res->fields;
                if (!isTeamOwner($team, $row))
                {
                    echo "<OPTION VALUE=$row[ship_id]>$row[character_name]";
                }
                $res->MoveNext();
            }

            echo "</SELECT></TD></TR>";
            echo "<TR><TD><INPUT TYPE=SUBMIT VALUE=$l_submit></TD></TR>";
            echo "</TABLE>";
            echo "</FORM>";
        }
        else
        {
            if($playerinfo[team] == $whichteam)
            {

                if(is_null($who))
                {
                    echo "No player was selected.<br />\n";
                            echo "<br /><br /><a href=\"teams.php\">$l_clickme</a> $l_team_menu<br /><br />";
                            break;
                }
                $res = $db->Execute("SELECT character_name,team_invite FROM $dbtables[ships] WHERE ship_id=$who");
                $newpl = $res->fields;
                if ($newpl[team_invite])
                {
                    $l_team_isorry = str_replace("[name]", $newpl[character_name], $l_team_isorry);
                    echo "$l_team_isorry<BR><BR>";
                }
                else
                {
                    $db->Execute("UPDATE $dbtables[ships] SET team_invite=$whichteam WHERE ship_id=$who");
                    echo("$l_team_plinvted<BR>");
                    playerlog($who,LOG_TEAM_INVITE, "$team[team_name]");
                }
            }
            else
            {
                echo "$l_team_notyours<BR>";
            }
        }
        echo "<BR><BR><a href=\"teams.php\">$l_clickme</a> $l_team_menu<BR><BR>";
        break;
    }
    case 8: // REFUSE invitation
    {
        echo "$l_team_refuse <B>$invite_info[team_name]</B>.<BR><BR>";
        $db->Execute("UPDATE $dbtables[ships] SET team_invite=0 WHERE ship_id=$playerinfo[ship_id]");
        playerlog($team[creator], LOG_TEAM_REJECT, "$playerinfo[character_name]|$invite_info[team_name]");
        LINK_BACK();
        break;
    }

    case 9: // Edit Team
    {
        // Check if Co-ordinator of team.
        // If not display "An error occured, You are not the leader of this Team." message.
        // Then show link back and break;

        if (isTeamOwner($team, $playerinfo) != true)
        {
            echo "$l_team_error";
            LINK_BACK();
            break;
        }

        if (!$update)
        {
            echo "<FORM ACTION=\"teams.php\" METHOD=POST>";
            echo "$l_team_edname: <BR>";
            echo "<INPUT TYPE=hidden NAME=swordfish value='$swordfish'>";
            echo "<INPUT TYPE=hidden name=teamwhat value=$teamwhat>";
            echo "<INPUT TYPE=hidden name=whichteam value=$whichteam>";
            echo "<INPUT TYPE=hidden name=update value=true>";
            echo "<INPUT TYPE=TEXT NAME=teamname SIZE=40 MAXLENGTH=40 VALUE=\"".$team[team_name]."\"><BR>";
            echo "$l_team_eddesc: <BR>";
            echo "<INPUT TYPE=TEXT NAME=teamdesc SIZE=40 MAXLENGTH=254 VALUE=\"".$team[description]."\"><BR>";
            echo "<INPUT TYPE=SUBMIT VALUE=$l_submit><INPUT TYPE=RESET VALUE=$l_reset>";
            echo "</FORM>";
            echo "<BR><BR>";
        }
        else
        {
            $teamname = trim(htmlspecialchars($teamname));
            $teamdesc = trim(htmlspecialchars($teamdesc));

            if(!validate_team($teamname, $teamdesc, $playerinfo[ship_id]))
            {
                echo "<span style='color:#f00;'>Team Edit Failed</span><br>Sorry you have either entered an invalid Team name or Team Description.<br>\n";
                LINK_BACK();
                break;
            }

            $res = $db->Execute("UPDATE $dbtables[teams] SET team_name='$teamname', description='$teamdesc' WHERE id=$whichteam") or die("<font color=red>error: " . $db->ErrorMSG() . "</font>");
            echo "$l_team_team <B>$teamname</B> $l_team_hasbeenr<BR><BR>";

            // Adding a log entry to all members of the renamed team
            $result_team_name = $db->Execute("SELECT ship_id FROM $dbtables[ships] WHERE team=$whichteam AND ship_id<>$playerinfo[ship_id]") or die("<font color=red>error: " . $db->ErrorMsg() . "</font>");
            playerlog($playerinfo[ship_id], LOG_TEAM_RENAME, "$teamname");
            while(!$result_team_name->EOF)
            {
                $teamname_array = $result_team_name->fields;
                playerlog($teamname_array[ship_id], LOG_TEAM_M_RENAME, "$teamname");
                $result_team_name->MoveNext();
            }
        }
        LINK_BACK();
        break;
    }

    default:
    {
        if (!$playerinfo[team])
        {
            echo "$l_team_notmember";
            DISPLAY_INVITE_INFO();
        }
        else
        {
            if ($playerinfo[team] < 0)
            {
                $playerinfo[team] = -$playerinfo[team];
                $result = $db->Execute("SELECT * FROM $dbtables[teams] WHERE id=$playerinfo[team]");
                $whichteam = $result->fields;
                echo "$l_team_urejected <B>$whichteam[team_name]</B><BR><BR>";

                // No more necessary due to COUNT(*) in previous SQL statement
                // AND already done in case 5:
                # $db->Execute("UPDATE $dbtables[ships] SET team='0' WHERE ship_id='$playerinfo[ship_id]'");
                # $db->Execute("UPDATE $dbtables[teams] SET number_of_members=number_of_members-1 WHERE id=$whichteam");
                # playerlog($playerinfo[ship_id], LOG_TEAM_KICK, "$whichteam[team_name]");

                LINK_BACK();
                break;
            }
            $result = $db->Execute("SELECT * FROM $dbtables[teams] WHERE id=$playerinfo[team]");
            $whichteam = $result->fields;;
            if ($playerinfo[team_invite])
            {
                $result = $db->Execute("SELECT * FROM $dbtables[teams] WHERE id=$playerinfo[team_invite]");
                $whichinvitingteam = $result->fields;
            }
            $isowner = isTeamOwner($whichteam, $playerinfo);
            showinfo($playerinfo[team], $isowner);
        }
        $res= $db->Execute("SELECT COUNT(*) as TOTAL FROM $dbtables[teams] WHERE admin='N';");
        $num_res = $res->fields;

        if ($num_res[TOTAL] > 0)
        {
            DISPLAY_ALL_TEAMS();
        }
        else
        {
            echo "$l_team_noteams<BR><BR>";
        }
        break;
    }
} // End of switch.

echo "<BR><BR>";
TEXT_GOTOMAIN();

function isTeamMember($team, $playerinfo)
{
    // Check to see if the player is in a team?  if not return false right there, else carry on.
    if($playerinfo['team'] == 0)
    {
        return false;
    }

    // Check to see if the player is a member of $team['id'] if so return true, else return false.
    if($playerinfo['team'] == $team['id'])
    {
        return true;
    }
    else
    {
        return false;
    }
}

function isTeamOwner($team, $playerinfo)
{
    // Check to see if the player is in a team?  if not return false right there, else carry on.
    if($playerinfo['team'] == 0)
    {
        return false;
    }

    // Check to see if the player is the Owner of $team['creator'] if so return true, else return false.
    if($playerinfo['ship_id'] == $team['creator'])
    {
        return true;
    }
    else
    {
        return false;
    }

}

function LINK_BACK()
{
    global $l_clickme, $l_team_menu;
    echo "<BR><BR><a href=\"teams.php\">$l_clickme</a> $l_team_menu.<BR><BR>";
}

// Rewritten display of teams list
function DISPLAY_ALL_TEAMS()
{
    global $color, $color_line1, $color_line2, $color_header, $order, $type, $l_team_galax, $l_team_members, $l_team_member, $l_team_coord, $l_score, $l_name;
    global $db, $dbtables;

    echo "<br><br>$l_team_galax<BR>";
    echo "<table style='width:100%; border:#fff 1px solid;' border='0' cellspacing='0' cellpadding='2'>";
    echo "<TR BGCOLOR=\"$color_header\">";

    if ($type == "d")
    {
        $type = "a";
        $by = "ASC";
    }
    else
    {
        $type = "d";
        $by = "DESC";
    }
    echo "<TD><B><A class='new_link' style='font-size:14px;' HREF=teams.php?order=team_name&type=$type>$l_name</A></B></TD>";
    echo "<TD><B><A class='new_link' style='font-size:14px;' HREF=teams.php?order=number_of_members&type=$type>$l_team_members</A></B></TD>";
    echo "<TD><B><A class='new_link' style='font-size:14px;' HREF=teams.php?order=character_name&type=$type>$l_team_coord</A></B></TD>";
    echo "<TD><B><A class='new_link' style='font-size:14px;' HREF=teams.php?order=total_score&type=$type>$l_score</A></B></TD>";
    echo "</TR>";
    $sql_query = "SELECT $dbtables[ships].character_name,
                COUNT(*) as number_of_members,
                ROUND(SQRT(SUM(POW($dbtables[ships].score,2)))) as total_score,
                $dbtables[teams].id,
                $dbtables[teams].team_name,
                $dbtables[teams].creator
                FROM $dbtables[ships]
                LEFT JOIN $dbtables[teams] ON $dbtables[ships].team = $dbtables[teams].id
                WHERE $dbtables[ships].team = $dbtables[teams].id AND admin='N'
                GROUP BY $dbtables[teams].team_name";

    // Setting if the order is Ascending or descending, if any.
    // Default is ordered by teams.team_name
    if ($order)
    {
        $sql_query = $sql_query ." ORDER BY " . $order . " $by";
    }

    $res = $db->Execute($sql_query) or die($db->ErrorMsg());
    $color = $color_line1;

    while(!$res->EOF)
    {
        $row = $res->fields;
        echo "<TR BGCOLOR=\"$color\">";
        echo "<TD><a href=teams.php?teamwhat=1&whichteam=".$row[id].">".$row[team_name]."</A></TD>";
        echo "<TD>".$row[number_of_members]."</TD>";

        // This fixes it so that it actually displays the coordinator, and not the first member of the team.
        $sql_query_2 = "SELECT character_name FROM $dbtables[ships] WHERE ship_id = $row[creator]";
        $res2 = $db->Execute($sql_query_2) or die($db->ErrorMsg());
        while(!$res2->EOF)
        {
            $row2 = $res2->fields;
            $res2->MoveNext();
        }

        // If there is a way to redo the original sql query instead, please, do so, but I didnt see a way to.
        echo "<TD><a href=mailto2.php?name=".$row2[character_name].">".$row2[character_name]."</A></TD>";
        echo "<TD>$row[total_score]</TD>";
        echo "</TR>";
        if($color == $color_line1)
        {
            $color = $color_line2;
        }
        else
        {
            $color = $color_line1;
        }

        $res->MoveNext();
    }
    echo "</table><BR>";
}

function DISPLAY_INVITE_INFO()
{
    global $playerinfo, $invite_info, $l_team_noinvite, $l_team_ifyouwant, $l_team_tocreate, $l_clickme, $l_team_injoin, $l_team_tojoin, $l_team_reject, $l_team_or;
    if (!$playerinfo[team_invite])
    {
        echo "<br><br><font color=blue size=2><b>$l_team_noinvite</b></font><BR>";
        echo "$l_team_ifyouwant<BR>";
        echo "<a href=\"teams.php?teamwhat=6\">$l_clickme</a> $l_team_tocreate<BR><BR>";
    }
    else
    {
        echo "<br><br><font color=blue size=2><b>$l_team_injoin ";
        echo "<a href=teams.php?teamwhat=1&whichteam=$playerinfo[team_invite]>$invite_info[team_name]</A>.</b></font><BR>";
        echo "<A HREF=teams.php?teamwhat=3&whichteam=$playerinfo[team_invite]>$l_clickme</A> $l_team_tojoin <B>$invite_info[team_name]</B> $l_team_or <A HREF=teams.php?teamwhat=8&whichteam=$playerinfo[team_invite]>$l_clickme</A> $l_team_reject<BR><BR>";
    }
}

function showinfo($whichteam,$isowner)
{
    global $playerinfo, $invite_info, $team, $l_team_coord, $l_team_member, $l_options, $l_team_ed, $l_team_inv, $l_team_leave, $l_team_members, $l_score, $l_team_noinvites, $l_team_pending;
    global $db, $dbtables, $l_team_eject;

    // Heading
    echo"<div align=center>";
    echo "<h3><font color=white><B>$team[team_name]</B>";
    echo "<br><font size=2>\"<i>$team[description]</i>\"</font></H3>";
    if ($playerinfo[team] == $team[id])
    {
        echo "<font color=white>";
        if ($playerinfo[ship_id] == $team[creator])
        {
            echo "$l_team_coord ";
        }
        else
        {
            echo "$l_team_member ";
        }
        echo "$l_options<br><font size=2>";
        if ( isTeamOwner($team, $playerinfo) == true)
        {
            echo "[<a href=teams.php?teamwhat=9&whichteam=$playerinfo[team]>$l_team_ed</a>] - ";
        }
        echo "[<a href=teams.php?teamwhat=7&whichteam=$playerinfo[team]>$l_team_inv</a>] - [<a href=teams.php?teamwhat=2&whichteam=$playerinfo[team]>$l_team_leave</a>]</font></font>";
    }
    DISPLAY_INVITE_INFO();
    echo "</div>";

    // Main table
    echo "<table border=2 cellspacing=2 cellpadding=2 bgcolor=\"#400040\" width=\"75%\" align=center>";
    echo "<tr>";
    echo "<td><font color=white>$l_team_members</font></td>";
    echo "</tr><tr bgcolor=$color_line2>";
    $result  = $db->Execute("SELECT * FROM $dbtables[ships] WHERE team=$whichteam");
    while (!$result->EOF)
    {
        $member = $result->fields;
        echo "<td> - $member[character_name] ($l_score $member[score])";
        if ($isowner && ($member[ship_id] != $playerinfo[ship_id]))
        {
            echo " - <font size=2>[<a href=\"teams.php?teamwhat=5&who=$member[ship_id]\">$l_team_eject</A>]</font></td>";
        }
        else
        {
            if ($member[ship_id] == $team[creator])
            {
                echo " - $l_team_coord</td>";
            }
        }
        echo "</tr><tr bgcolor=$color_line2>";
        $result->MoveNext();
    }

    // Displays for members name
    $res = $db->Execute("SELECT ship_id,character_name FROM $dbtables[ships] WHERE team_invite=$whichteam");
    echo "<td bgcolor=$color_line2><font color=white>$l_team_pending <B>$team[team_name]</B></font></td>";
    echo "</tr><tr>";
    if ($res->RecordCount() > 0)
    {
        echo "</tr><tr bgcolor=$color_line2>";
        while (!$res->EOF)
        {
            $who = $res->fields;
            echo "<td> - $who[character_name]</td>";
            echo "</tr><tr bgcolor=$color_line2>";
            $res->MoveNext();
        }
    }
    else
    {
        echo "<td>$l_team_noinvites <B>$team[team_name]</B>.</td>";
        echo "</tr><tr>";
    }
    echo "</tr></table>";
}

function validate_team($name = NULL, $desc = NULL, $creator = NULL)
{
    global $db,$dbtables, $db_type;

    $name = trim($name);
    $desc = trim($desc);
    $creator = (int)$creator;

    if( (is_null($name) || empty($name)) || (is_null($desc) || empty($desc)) || (is_null($creator) || empty($creator)) )
    {
        return false;
    }

    if(!ereg("(^[a-zA-Z0-9]+([a-zA-Z\_0-9\.-\ ]*))$" , $name))
    {
        return false;
    }

    if(!ereg("(^[a-zA-Z0-9]+([a-zA-Z\_0-9\.-\ ]*))$" , $desc))
    {
        return false;
    }

    // Just a test to see if an team with a name of $name exists.
    // This is just a temp fix until we find a better one.
    $res= $db->Execute("SELECT COUNT(*) as found FROM $dbtables[teams] WHERE team_name = '{$name}' AND creator != $creator;");
    $num_res = $res->fields;
    if ($num_res[found] > 0)
    {
        return false;
    }

    return true;
}

include("footer.php");
?>
