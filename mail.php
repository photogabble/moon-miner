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
// File: mail.php

include("config.php");
include("languages/$lang");

$title=$l_mail_title;
include("header.php");

connectdb();
bigtitle();

if(!isset($_SESSION['sendemail']))
{
    $_SESSION['sendemail'] = false;
}

$result = $db->Execute ("select character_name, email, password from $dbtables[ships] where email='$mail'");

if(!$result->EOF)
{
    $playerinfo=$result->fields;
    $l_mail_message=str_replace("[pass]", $playerinfo['password'], $l_mail_message);
    $l_mail_message=str_replace("[name]", $playerinfo['character_name'], $l_mail_message);

    require_once("includes/mailer_class.php");
    $mailer = new Mailer();
    $mailerInfo = $mailer->getInfo();

    if($_SESSION['sendemail'] == false)
    {
        $_SESSION['sendemail'] = true;

        $image = "images/unknown.png";

        echo "<div style='font-family:Verdana, Arial, Helvetica, sans-serif; font-size:10px;'>\n";
        echo "<table style='width:500px; border:#FFFFFF 1px solid; color:#000000;'>\n";
        echo "  <tr>\n";
        echo "    <td style='background-color:#C0C0C0; border:#FFFFFF 1px solid; text-align:center; font-size:14px; color:#000000;' colspan='2'>Sending Email Request</td>\n";
        echo "  </tr>\n";
        echo "  <tr>\n";
        echo "    <td style='background-color:#C0C0C0; border:#FFFFFF 1px solid; padding:8px; width:100px; text-align:center;'><img src='{$image}' width='64' height='64' borders='0' /></td>\n";
        echo "    <td style='background-color:#C0C0C0; border:#FFFFFF 1px solid; width:400px; text-align:left; font-size:14px; padding:6px;'>Sending Email Request.<br />This may take a few seconds to send, so Please Wait.</td>\n";
        echo "  </tr>\n";
        echo "  <tr>\n";
        echo "    <td colspan='2' style='background-color:#C0C0C0; border:#FFFFFF 1px solid; font-size:10px; color:#000000;'>{$mailerInfo}</td>\n";
        echo "  </tr>\n";
        echo "</table>\n";
        echo "</div>\n";
        sleep(1);
        header("Location: {$_SERVER['PHP_SELF']}?mail={$_GET['mail']}");
        die();
    }
    else
    {
        $mailer->setDebugMode(false);

        $mailer->setMailHost($ExtMailCfg['host']);
        $ret = $mailer->Authenticate($ExtMailCfg);

        $mailer->setDomain($email_server);
        $mailer->setSender( "Blacknova Mail", $admin_mail );
        $mailer->setRecipient( $playerinfo['character_name'], $playerinfo['email'] );
        $mailer->setSubject( $l_mail_topic );
        $mailer->setMessage( "$l_mail_message\r\n\r\nhttp://{$SERVER_NAME}\r\n" );

        $ret = $mailer->sendMail();
        if($ret == true)
        {
            $image = "images/tick.png";
            $result = "<div style='font-size:12px; font-weight:bold;'>Email sent to:&nbsp;&nbsp;{$playerinfo['email']}<br /><br />You should receive your email within 5 to 10 mins.</div><br /><div style='font-size:10px; font-weight:bold;'>PLEASE NOTE: This email may apear in your spam, trash or junk folder so check.</div>";
            $colors = array("#005500", "#00FF00");
            $errorResult = null;
        }
        else
        {
            $err = $mailer->getError();
            $image = "images/cross.png";
            $result = "{$err['msg']}<br />[Err: {$err['no']}]<br /><span style='font-size:9px;'>". implode("<br />\n", $err['debug']) ."</span><br />Please notify an admin on the forums.";
            $colors = array("#550000", "#FF0000");
        }

        echo "<div style='font-family:Verdana, Arial, Helvetica, sans-serif; font-size:10px;'>\n";
        echo "<table style='width:500px; border:#FFFFFF 1px solid;'>\n";
        echo "  <tr>\n";
        echo "    <td style='background-color:#C0C0C0; border:#FFFFFF 1px solid; text-align:center; font-size:14px; color:#000000;' colspan='2'>Send Email Request</td>\n";
        echo "  </tr>\n";
        echo "  <tr>\n";
        echo "    <td style='background-color:#C0C0C0; border:#FFFFFF 1px solid; padding:8px; width:100px; margin:auto; text-align:center;'><img style='width:64px; height:64px; margin:auto; border:none;' src='{$image}' /></td>\n";
        echo "    <td style='background-color:{$colors[0]}; border:{$colors[1]} 1px solid; color:#FFFFFF; width:400px; text-align:left; font-size:14px; padding:6px;'>{$result}</td>\n";
        echo "  </tr>\n";
        echo "  <tr>\n";
        echo "    <td colspan='2' style='background-color:#C0C0C0; border:#FFFFFF 1px solid; font-size:10px; color:#000000;'>{$mailerInfo}</td>\n";
        echo "  </tr>\n";
        echo "</table>\n";
        echo "</div>\n";
        unset($_SESSION['sendemail']);

        echo "<br />\n";
        echo "<div style='font-size:14px; font-weight:bold; color:#FF0000;'>Please Note: If you do not receive your emails within 5 to 10 mins of it being sent, please notify us as soon as possible either by email or on the forums.<br />DO NOT CREATE ANOTHER ACCOUNT, YOU MAY GET BANNED.</div>\n";

        echo "<br />\n";

        if($_SESSION['logged_in'] == true)
        {
            TEXT_GOTOMAIN();
        }
        else
        {
            TEXT_GOTOLOGIN();
        }
    }
}
else
{
    require_once("includes/mailer_class.php");
    $mailer = new Mailer();

    $mailerInfo = $mailer->getInfo();

    $image = "images/cross.png";
    $result = "Send Email Request Failed.<br />{$err['msg']}";
    $colors = array("#550000", "#FF0000");

    echo "<div style='font-family:Verdana, Arial, Helvetica, sans-serif; font-size:10px;'>\n";
    echo "<table style='width:500px; border:#FFFFFF 1px solid;'>\n";
    echo "  <tr>\n";
    echo "    <td style='background-color:#C0C0C0; border:#FFFFFF 1px solid; text-align:center; font-size:14px; color:#000000;' colspan='2'>Send Email Request</td>\n";
    echo "  </tr>\n";
    echo "  <tr>\n";
    echo "    <td style='background-color:#C0C0C0; border:#FFFFFF 1px solid; padding:8px; width:100px; text-align:center;'><img src='{$image}' width='64' height='64' borders='0' /></td>\n";
    echo "    <td style='background-color:{$colors[0]}; border:{$colors[1]} 1px solid; color:#FFFFFF; width:400px; text-align:left; font-size:12px; padding:6px;'>{$l_mail_noplayer}</td>\n";
    echo "  </tr>\n";
    echo "  <tr>\n";
    echo "    <td colspan='2' style='background-color:#C0C0C0; border:#FFFFFF 1px solid; font-size:10px; color:#000000;'>{$mailerInfo}</td>\n";
    echo "  </tr>\n";
    echo "</table>\n";
    echo "</div>\n";
    unset($_SESSION['sendemail']);
    echo "<br />\n";
    echo "<div style='font-size:14px; font-weight:bold; color:#FF0000;'>Please Note: If you do not receive your emails within 5 to 10 mins of it being sent, please notify us as soon as possible either by email or on the forums.<br />DO NOT CREATE ANOTHER ACCOUNT, YOU MAY GET BANNED.</div>\n";

    echo "<br />\n";
    if($_SESSION['logged_in'] == true)
    {
        TEXT_GOTOMAIN();
    }
    else
    {
        TEXT_GOTOLOGIN();
    }
}
include("footer.php");
?>
