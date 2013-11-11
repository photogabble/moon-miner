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
// File: new2.php

include './global_includes.php';

if (!isset ($_GET['lang']))
{
    $_GET['lang'] = null;
    $lang = $default_lang;
    $link = '';
}
else
{
    $lang = $_GET['lang'];
    $link = "?lang=" . $lang;
}

$title = $langvars['l_new_title2'];
include './header.php';

// Database driven language entries
$langvars = BntTranslate::load ($db, $lang, array ('new', 'login', 'common', 'global_includes', 'combat', 'footer', 'news'));
echo "<h1>" . $title . "</h1>\n";

if ($account_creation_closed)
{
    die ($langvars['l_new_closed_message']);
}

// Get the user supplied post vars.
$username  = null;
$shipname  = null;
$character = null;
if (array_key_exists ('character', $_POST))
{
    $character  = $_POST['character'];
}

if (array_key_exists ('shipname', $_POST))
{
    $shipname   = $_POST['shipname'];
}

if (array_key_exists ('username', $_POST))
{
    $username   = $_POST['username'];
}

if (array_key_exists ('lang', $_POST))
{
    $lang   = $_POST['lang'];
}
else
{
    $lang = $default_lang;
}

$character = htmlspecialchars ($character);
$shipname = htmlspecialchars ($shipname);
$character = preg_replace ('/[^A-Za-z0-9\_\s\-\.\']+/', ' ', $character);
$shipname = preg_replace ('/[^A-Za-z0-9\_\s\-\.\']+/', ' ', $shipname);

$result = $db->Execute ("SELECT email, character_name, ship_name FROM {$db->prefix}ships WHERE email=? || character_name=? || ship_name=?;", array ($username, $character, $shipname));
BntDb::logDbErrors ($db, $result, __LINE__, __FILE__);
$flag = 0;

if ($username === null || $character === null || $shipname === null )
{
    echo $langvars['l_new_blank'] . '<br>';
    $flag = 1;
}

while (($result instanceof ADORecordSet) && !$result->EOF)
{
    $row = $result->fields;
    if (mb_strtolower ($row['email']) == mb_strtolower ($username))
    {
        echo $langvars['l_new_inuse'] . " " .  $langvars['l_new_4gotpw1'] . " <a href=mail.php?mail=$username>" . $langvars['l_clickme'] . "</a> " . $langvars['l_new_4gotpw2'] . "<br>";
        $flag = 1;
    }
    if (mb_strtolower ($row['character_name']) == mb_strtolower($character))
    {
        $langvars['l_new_inusechar'] = str_replace ("[character]", $character, $langvars['l_new_inusechar']);
        echo $langvars['l_new_inusechar'] . '<br>';
        $flag = 1;
    }
    if (mb_strtolower ($row['ship_name']) == mb_strtolower ($shipname))
    {
        $langvars['l_new_inuseship'] = str_replace ("[shipname]", $shipname, $langvars['l_new_inuseship']);
        echo $langvars['l_new_inuseship'] . '<br>';
        $flag = 1;
    }
    $result->MoveNext();
}

if ($flag == 0)
{
    // Insert code to add player to database
    $makepass = "";
    $syllables = "er,in,tia,wol,fe,pre,vet,jo,nes,al,len,son,cha,ir,ler,bo,ok,tio,nar,sim,ple,bla,ten,toe,cho,co,lat,spe,ak,er,po,co,lor,pen,cil,li,ght,wh,at,the,he,ck,is,mam,bo,no,fi,ve,any,way,pol,iti,cs,ra,dio,sou,rce,sea,rch,pa,per,com,bo,sp,eak,st,fi,rst,gr,oup,boy,ea,gle,tr,ail,bi,ble,brb,pri,dee,kay,en,be,se";
    $syllable_array = explode (",", $syllables);
    for ($count = 1; $count <= 4; $count++)
    {
        if (BntRand::betterRand ()%10 == 1)
        {
            $makepass .= sprintf ("%0.0f", (BntRand::betterRand ()%50)+1);
        }
        else
        {
            $makepass .= sprintf ("%s", $syllable_array[BntRand::betterRand ()%62]);
        }
    }
    $stamp=date ("Y-m-d H:i:s");
    $query = $db->Execute ("SELECT MAX(turns_used + turns) AS mturns FROM {$db->prefix}ships;");
    BntDb::logDbErrors ($db, $query, __LINE__, __FILE__);
    $res = $query->fields;

    $mturns = $res['mturns'];

    if ($mturns > $max_turns)
    {
        $mturns = $max_turns;
    }

    // Hash the password.  $hashedPassword will be a 60-character string.
    $hasher = new PasswordHash (10, false); // The first number is the hash strength, or number of iterations of bcrypt to run.
    $hashed_pass = $hasher->HashPassword($makepass);

    $result2 = $db->Execute ("INSERT INTO {$db->prefix}ships (ship_name, ship_destroyed, character_name, password, email, armor_pts, credits, ship_energy, ship_fighters, turns, on_planet, dev_warpedit, dev_genesis, dev_beacon, dev_emerwarp, dev_escapepod, dev_fuelscoop, dev_minedeflector, last_login, ip_address, trade_colonists, trade_fighters, trade_torps, trade_energy, cleared_defences, lang, dev_lssd)
                             VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?);", array ($shipname, 'N', $character, $hashed_pass, $username, $start_armor, $start_credits, $start_energy, $start_fighters, $mturns, 'N', $start_editors, $start_genesis, $start_beacon, $start_emerwarp, $start_escape_pod, $start_scoop, $start_minedeflectors, $stamp, $ip, 'Y', 'N', 'N', 'Y', NULL, $lang, $start_lssd));
    BntDb::logDbErrors ($db, $result2, __LINE__, __FILE__);

    if (!$result2)
    {
        echo $db->ErrorMsg() . "<br>";
    }
    else
    {
        $result2 = $db->Execute ("SELECT ship_id FROM {$db->prefix}ships WHERE email = ?;", array ($username));
        BntDb::logDbErrors ($db, $result2, __LINE__, __FILE__);

        $shipid = $result2->fields;

        // To do: build a bit better "new player" message
        $langvars['l_new_message'] = str_replace ("[pass]", $makepass, $langvars['l_new_message']);
        $langvars['l_new_message'] = str_replace ("[ip]", $ip, $langvars['l_new_message']);

        // Some reason \r\n is broken, so replace them now.
        $langvars['l_new_message'] = str_replace ('\r\n', "\r\n", $langvars['l_new_message']);

        $link_to_game = "http://";
        $link_to_game .= ltrim ($gamedomain, ".");// Trim off the leading . if any
        //$link_to_game .= str_replace ($_SERVER['DOCUMENT_ROOT'],"",dirname(__FILE__));
        $link_to_game .= $gamepath;
        mail ("$username", $langvars['l_new_topic'], $langvars['l_new_message'] . "\r\n\r\n$link_to_game", "From: $admin_mail\r\nReply-To: $admin_mail\r\nX-Mailer: PHP/" . phpversion ());

        BntLogMove::writeLog ($db, $shipid['ship_id'], 0); // A new player is placed into sector 0. Make sure his movement log shows it, so they see it on the galaxy map.
        $resx = $db->Execute ("INSERT INTO {$db->prefix}zones VALUES (NULL, ?, ?, 'N', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 0);", array ($character ."\'s Territory", $shipid['ship_id']));
        BntDb::logDbErrors ($db, $resx, __LINE__, __FILE__);

        $resx = $db->Execute ("INSERT INTO {$db->prefix}ibank_accounts (ship_id,balance,loan) VALUES (?,0,0);", array ($shipid['ship_id']));
        BntDb::logDbErrors ($db, $resx, __LINE__, __FILE__);

        if ($display_password == true)
        {
            echo $langvars['l_new_pwis'] . " " . $makepass . "<br><br>";
        }
        $langvars['l_new_pwsent'] = str_replace ("[username]", $_POST['username'], $langvars['l_new_pwsent']);
        echo $langvars['l_new_pwsent'] . '<br><br>';
        echo "<a href=index.php" . $link . ">" . $langvars['l_clickme'] . "</a> " . $langvars['l_new_login'];
    }
}
else
{
    $langvars['l_new_err'] = str_replace ("[here]", "<a href='new.php'>" . $langvars['l_here'] . "</a>", $langvars['l_new_err']);
    echo $langvars['l_new_err'];
}

include './footer.php';
?>
