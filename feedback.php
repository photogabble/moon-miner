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
// File: feedback.php

include "config.php";
updatecookie ();
include "languages/$lang";
$title = $l_feedback_title;
include "header.php";

if ( checklogin () )
{
    die ();
}

if (!isset($_SESSION['content']))
{
    $_SESSION['content'] = $_POST['content'];
    $_SESSION['sendemail'] = false;
}

$result = $db->Execute ("SELECT * FROM {$db->prefix}ships WHERE email='$username'");
db_op_result ($db, $result, __LINE__, __FILE__, $db_logging);
$playerinfo = $result->fields;
bigtitle ();
if (is_null($_SESSION['content']))
{
    echo "<form action=feedback.php method=post>";
    echo "<table>";
    echo "<tr><td>$l_feedback_to</td><td><input disabled type=text name=dummy size=40 maxlength=40 value=GameAdmin></td></tr>";
    echo "<tr><td>$l_feedback_from</td><td><input disabled type=text name=dummy size=40 maxlength=40 value=\"$playerinfo[character_name] - $playerinfo[email]\"></td></tr>";
    echo "<tr><td>$l_feedback_topi</td><td><input disabled type=text name=dummy size=40 maxlength=40 value=$l_feedback_feedback></td></tr>";
    echo "<tr><td>$l_feedback_message</td><td><textarea name=content rows=5 cols=40></textarea></td></tr>";
    echo "<tr><td></td><td><input type=submit value=$l_submit><input type=reset value=$l_reset></td>";
    echo "</table>";
    echo "</form>";
    echo "<br>$l_feedback_info<br>";
}
else
{
    require_once "includes/mailer_class.php";
    $mailer = new Mailer ();

    if ($_SESSION['sendemail'] == false)
    {
        $_SESSION['sendemail'] = true;

        $image = "images/unknown.png";

        echo "<div style='font-size:10px;'>\n";
        echo "<table style='width:400px; border:#fff 1px solid; color:#ff0;'>\n";
        echo "  <tr>\n";
        echo "    <td style='background-color:#C0C0C0; border:#fff 1px solid; text-align:center; font-size:14px; color:#000;' colspan='2'>Sending Feedback</td>\n";
        echo "  </tr>\n";
        echo "  <tr>\n";
        echo "    <td style='background-color:#C0C0C0; border:#fff 1px solid; width:100px; text-align:center;'><img src='{$image}' width='64' height='64' borders='0' /></td>\n";
        echo "    <td style='background-color:#C0C0C0; border:#fff 1px solid; width:300px; text-align:left; font-size:14px; padding:6px;'>Sending Feedback.<br>This may take a few seconds to send, so Please Wait.</td>\n";
        echo "  </tr>\n";
        echo "  <tr>\n";
        echo "    <td colspan='2' style='background-color:#C0C0C0; border:#fff 1px solid; font-size:10px; color:#000;'>{$mailer->getInfo()}</td>\n";
        echo "  </tr>\n";
        echo "</table>\n";
        echo "</div>\n";

        echo "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"0;URL={$_SERVER['PHP_SELF']}\">\n";
    }
    else
    {
        $mailer->setDebugMode (false);

        $mailer->setMailHost ($ExtMailCfg['host']);
        $ret = $mailer->Authenticate ($ExtMailCfg);

        $mailer->setDomain ($email_server );
        $mailer->setSender ( $playerinfo[character_name], $playerinfo[email] );
        $mailer->setRecipient ( $adminname, $admin_mail );
        $mailer->setSubject ( $l_feedback_subj );
        $mailer->setMessage ( "IP address - $ip\r\nGame Name - $playerinfo[character_name] - $gamedomain \r\n\r\n{$_SESSION['content']}\r\n" );
        $ret = $mailer->sendMail ();
        if ($ret == true)
        {
            $image = "images/tick.png";
            $result = "<span style='color:#00f;'>Send Feedback Passed.</span>";
            $errorResult = null;
        }
        else
        {
            $err = $mailer->getError ();
            if ($err['no'] == 2)
            {
                $image = "images/greylist.png";
                $result = "<span style='color:#f00;'>Send Feedback Failed.<br>Detected Greylisting...<br>Please notify an admin on the forums.</span>";
            }
            else
            {
                $image = "images/cross.png";
                $result = "<span style='color:#f00;'>Send Feedback Failed.<br>{$err['msg']}</span>";
            }
        }

        echo "<div style='font-size:10px;'>\n";
        echo "<table style='width:400px; border:#fff 1px solid;'>\n";
        echo "  <tr>\n";
        echo "    <td style='background-color:#C0C0C0; border:#fff 1px solid; text-align:center; font-size:14px; color:#000;' colspan='2'>Send Feedback</td>\n";
        echo "  </tr>\n";
        echo "  <tr>\n";
        echo "    <td style='background-color:#C0C0C0; border:#fff 1px solid; width:100px; text-align:center;'><img src='{$image}' width='64' height='64' borders='0' /></td>\n";
        echo "    <td style='background-color:#C0C0C0; border:#fff 1px solid; width:300px; text-align:left; font-size:14px; padding:6px;'>{$result}</td>\n";
        echo "  </tr>\n";
        echo "  <tr>\n";
        echo "    <td colspan='2' style='background-color:#C0C0C0; border:#fff 1px solid; font-size:10px; color:#000;'>{$mailer->getInfo()}</td>\n";
        echo "  </tr>\n";
        echo "</table>\n";
        echo "</div>\n";
        unset($_SESSION['content'], $_SESSION['sendemail']);
    }
}

echo "<br>\n";
if (empty($username))
{
    TEXT_GOTOLOGIN();
}
else
{
    TEXT_GOTOMAIN();
}

include "footer.php";
?>
