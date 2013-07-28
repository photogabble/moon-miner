<?php
// Blacknova Traders - A web-based massively multiplayer space combat
// and trading game
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
// File: bounty.php

include './global_includes.php';

BntLogin::checkLogin ($db, $lang, $langvars, $bntreg);

// Database driven language entries
$langvars = BntTranslate::load ($db, $lang, array ('bounty'));
$title = $langvars['l_by_title'];
include './header.php';

// Database driven language entries
$langvars = BntTranslate::load ($db, $lang, array ('bounty', 'port', 'common',
                                'global_includes', 'global_funcs', 'combat',
                                'footer', 'news'));

$response = null;
if (isset ($_POST['response']))
{
    $response  = filter_input (INPUT_POST, 'response', FILTER_SANITIZE_STRING);
}

if (isset ($_GET['response']))
{
    $response  = filter_input (INPUT_GET, 'response', FILTER_SANITIZE_STRING);
}

$bounty_on = null;
if (isset ($_POST['bounty_on']))
{
    $bounty_on  = filter_input (INPUT_POST, 'bounty_on', FILTER_SANITIZE_NUMBER_INT);
}

if (isset ($_GET['bounty_on']))
{
    $bounty_on  = filter_input (INPUT_GET, 'bounty_on', FILTER_SANITIZE_NUMBER_INT);
}

$bid = (int) filter_input (INPUT_GET, 'bid', FILTER_SANITIZE_NUMBER_INT);
$amount  = (int) filter_input (INPUT_POST, 'amount', FILTER_SANITIZE_NUMBER_INT);

$res = $db->Execute ("SELECT * FROM {$db->prefix}ships WHERE email = ?;", array ($_SESSION['username']));
DbOp::dbResult ($db, $res, __LINE__, __FILE__);
$playerinfo = $res->fields;

switch ($response) {
    case "display":
        echo "<h1>" . $title . "</h1>\n";
        $res5 = $db->Execute ("SELECT * FROM {$db->prefix}ships, {$db->prefix}bounty WHERE bounty_on = ship_id AND bounty_on = ?;", array ($bounty_on));
        DbOp::dbResult ($db, $res5, __LINE__, __FILE__);
        $j = 0;
        if ($res5)
        {
            while (!$res5->EOF)
            {
                $bounty_details[$j] = $res5->fields;
                $j++;
                $res5->MoveNext();
            }
        }

        $num_details = $j;
        if ($num_details < 1)
        {
            echo $langvars['l_by_nobounties'] . "<br>";
        }
        else
        {
            echo $langvars['l_by_bountyon'] . " " . $bounty_details[0]['character_name'];
            echo '<table border=1 cellspacing=1 cellpadding=2 width="50%" align=center>';
            echo "<tr bgcolor=\"$color_header\">";
            echo "<td><strong>" . $langvars['l_amount'] . "</td>";
            echo "<td><strong>" . $langvars['l_by_placedby'] . "</td>";
            echo "<td><strong>" . $langvars['l_by_action'] . "</td>";
            echo "</tr>";
            $color = $color_line1;
            for ($j = 0; $j < $num_details; $j++)
            {
                $someres = $db->execute("SELECT character_name FROM {$db->prefix}ships WHERE ship_id = ?;", array ($bounty_details[$j]['placed_by']));
                DbOp::dbResult ($db, $someres, __LINE__, __FILE__);
                $details = $someres->fields;
                echo "<tr bgcolor=\"$color\">";
                echo "<td>" . $bounty_details[$j]['amount'] . "</td>";
                if ($bounty_details[$j]['placed_by'] == 0)
                {
                    echo "<td>" . $langvars['l_by_thefeds'] . "</td>";
                }
                else
                {
                    echo "<td>" . $details['character_name'] . "</td>";
                }
                if ($bounty_details[$j]['placed_by'] == $playerinfo['ship_id'])
                {
                    echo "<td><a href=bounty.php?bid=" . $bounty_details[$j]['bounty_id'] . "&response=cancel>" . $langvars['l_by_cancel'] . "</a></td>";
                }
                else
                {
                    echo "<td>n/a</td>";
                }

                echo "</tr>";

                if ($color == $color_line1)
                {
                    $color = $color_line2;
                }
                else
                {
                    $color = $color_line1;
                }
            }
            echo "</table>";
        }
        break;
    case "cancel":
        echo "<h1>" . $title . "</h1>\n";
        if ($playerinfo['turns'] < 1)
        {
            echo $langvars['l_by_noturn'] . "<br><br>";
            BntText::gotoMain ($db, $lang, $langvars);
            include './footer.php';
            die ();
        }

        $res = $db->Execute ("SELECT * FROM {$db->prefix}bounty WHERE bounty_id = ?;", array ($bid));
        DbOp::dbResult ($db, $res, __LINE__, __FILE__);
        if (!$res || $res->RowCount() ==0)
        {
            echo $langvars['l_by_nobounty'] . "<br><br>";
            BntText::gotoMain ($db, $lang, $langvars);
            include './footer.php';
            die ();
        }

        $bty = $res->fields;
        if ($bty['placed_by'] != $playerinfo['ship_id'])
        {
            echo $langvars['l_by_notyours'] . "<br><br>";
            BntText::gotoMain ($db, $lang, $langvars);
            include './footer.php';
            die ();
        }

        $del = $db->Execute ("DELETE FROM {$db->prefix}bounty WHERE bounty_id = ?;", array ($bid));
        DbOp::dbResult ($db, $del, __LINE__, __FILE__);
        $stamp = date ("Y-m-d H:i:s");
        $refund = $bty['amount'];
        $resx = $db->Execute ("UPDATE {$db->prefix}ships SET last_login = ?, turns = turns-1, turns_used = turns_used + 1, credits = credits + ? WHERE ship_id = ?;", array ($stamp, $refund, $playerinfo['ship_id']));
        DbOp::dbResult ($db, $resx, __LINE__, __FILE__);
        echo $langvars['l_by_canceled'] . "<br>";
        BntText::gotoMain ($db, $lang, $langvars);
        die ();
        break;
    case "place":
        echo "<h1>" . $title . "</h1>\n";
        $ex = $db->Execute ("SELECT * FROM {$db->prefix}ships WHERE ship_id = ?;", array ($bounty_on));
        DbOp::dbResult ($db, $ex, __LINE__, __FILE__);
        if (!$ex)
        {
            echo $langvars['l_by_notexists'] . "<br><br>";
            BntText::gotoMain ($db, $lang, $langvars);
            include './footer.php';
            die ();
        }

        $bty = $ex->fields;
        if ($bty['ship_destroyed'] == "Y")
        {
            echo $langvars['l_by_destroyed'] . "<br><br>";
            BntText::gotoMain ($db, $lang, $langvars);
            include './footer.php';
            die ();
        }

        if ($playerinfo['turns'] < 1 )
        {
            echo $langvars['l_by_noturn'] . "<br><br>";
            BntText::gotoMain ($db, $lang, $langvars);
            include './footer.php';
            die ();
        }

        if ($amount <= 0)
        {
            echo $langvars['l_by_zeroamount'] . "<br><br>";
            BntText::gotoMain ($db, $lang, $langvars);
            include './footer.php';
            die ();
        }

        if ($bounty_on == $playerinfo['ship_id'])
        {
            echo $langvars['l_by_yourself'] . "<br><br>";
            BntText::gotoMain ($db, $lang, $langvars);
            include './footer.php';
            die ();
        }

        if ($amount > $playerinfo['credits'])
        {
            echo $langvars['l_by_notenough'] . "<br><br>";
            BntText::gotoMain ($db, $lang, $langvars);
            include './footer.php';
            die ();
        }

        if ($bounty_maxvalue != 0)
        {
            $percent = $bounty_maxvalue * 100;

            include_once './includes/calc_score.php';
            $score = calc_score ($db, $playerinfo['ship_id']);
            $maxtrans = $score * $score * $bounty_maxvalue;
            $previous_bounty = 0;
            $pb = $db->Execute ("SELECT SUM(amount) AS totalbounty FROM {$db->prefix}bounty WHERE bounty_on = ? AND placed_by = ?;", array ($bounty_on, $playerinfo['ship_id']));
            DbOp::dbResult ($db, $pb, __LINE__, __FILE__);
            if ($pb)
            {
                $prev = $pb->fields;
                $previous_bounty = $prev['totalbounty'];
            }

            if ($amount + $previous_bounty > $maxtrans)
            {
                $langvars['l_by_toomuch'] = str_replace ("[percent]", $percent, $langvars['l_by_toomuch']);
                echo $langvars['l_by_toomuch'] . "<br><br>";
                BntText::gotoMain ($db, $lang, $langvars);
                include './footer.php';
                die ();
            }
        }

        $insert = $db->Execute ("INSERT INTO {$db->prefix}bounty (bounty_on,placed_by,amount) values (?,?,?);", array ($bounty_on, $playerinfo['ship_id'] ,$amount));
        DbOp::dbResult ($db, $insert, __LINE__, __FILE__);
        $stamp = date ("Y-m-d H:i:s");
        $resx = $db->Execute ("UPDATE {$db->prefix}ships SET last_login = ?, turns = turns - 1, turns_used = turns_used + 1, credits = credits - ? WHERE ship_id = ?;", array ($stamp, $amount, $playerinfo['ship_id']));
        DbOp::dbResult ($db, $resx, __LINE__, __FILE__);
        echo $langvars['l_by_placed'] . "<br>";
        BntText::gotoMain ($db, $lang, $langvars);
        die ();
        break;
    default:
        echo "<h1>" . $title . "</h1>\n";
        $res = $db->Execute ("SELECT * FROM {$db->prefix}ships WHERE ship_destroyed = 'N' AND ship_id <> ? ORDER BY character_name ASC;", array ($playerinfo['ship_id']));
        DbOp::dbResult ($db, $res, __LINE__, __FILE__);
        echo "<form action=bounty.php method=post>";
        echo "<table>";
        echo "<tr><td>" . $langvars['l_by_bountyon'] . "</td><td><select name=bounty_on>";
        while (!$res->EOF)
        {
            if (isset ($bounty_on) && $bounty_on == $res->fields['ship_id'])
            {
                $selected = "selected";
            }
            else
            {
                $selected = "";
            }

            $charname = $res->fields['character_name'];
            $ship_id = $res->fields['ship_id'];
            echo "<option value=$ship_id $selected>$charname</option>";
            $res->MoveNext();
        }

        echo "</select></td></tr>";
        echo "<tr><td>" . $langvars['l_by_amount'] . ":</td><td><input type=text name=amount size=20 maxlength=20></td></tr>";
        echo "<tr><td></td><td><input type=submit value=" . $langvars['l_by_place'] . "><input type=reset value=Clear></td>";
        echo "</table>";
        echo "<input type=hidden name=response value=place>";
        echo "</form>";

        $result3 = $db->Execute ("SELECT bounty_on, SUM(amount) as total_bounty FROM {$db->prefix}bounty GROUP BY bounty_on;");
        DbOp::dbResult ($db, $result3, __LINE__, __FILE__);

        $i = 0;
        if ($result3)
        {
            while (!$result3->EOF)
            {
                $bounties[$i] = $result3->fields;
                $i++;
                $result3->MoveNext();
            }
        }

        $num_bounties = $i;
        if ($num_bounties < 1)
        {
            echo $langvars['l_by_nobounties'] . "<br>";
        }
        else
        {
            echo $langvars['l_by_moredetails'] . "<br><br>";
            echo "<table width=\"100%\" border=0 cellspacing=0 cellpadding=2>";
            echo "<tr bgcolor=\"$color_header\">";
            echo "<td><strong>" . $langvars['l_by_bountyon'] . "</strong></td>";
            echo "<td><strong>" . $langvars['l_amount'] . "</td>";
            echo "</tr>";
            $color = $color_line1;
            for ($i = 0; $i < $num_bounties; $i++)
            {
                $someres = $db->execute("SELECT character_name FROM {$db->prefix}ships WHERE ship_id = ?;", array ($bounties[$i]['bounty_on']));
                DbOp::dbResult ($db, $someres, __LINE__, __FILE__);
                $details = $someres->fields;
                echo "<tr bgcolor=\"$color\">";
                echo "<td><a href=bounty.php?bounty_on=" . $bounties[$i]['bounty_on'] . "&response=display>". $details['character_name'] ."</A></td>";
                echo "<td>" . $bounties[$i]['total_bounty'] . "</td>";
                echo "</tr>";

                if ($color == $color_line1)
                {
                    $color = $color_line2;
                }
                else
                {
                    $color = $color_line1;
                }
            }
            echo "</table>";
        }
        echo "<br><br>";
        break;
}

BntText::gotoMain ($db, $lang, $langvars);
include './footer.php';
?>
