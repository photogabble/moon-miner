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
// File: option2.php

include './global_includes.php';

// Check if the user is logged in.
if (check_login ($db, $lang, $langvars)) // Checks player login, sets playerinfo
{
    // No they are not.
    die();
}

// Get POST['newlang'] returns null is not found.
if (request_var('POST', 'newlang', $newlang) === true)
{
    // Now validate the sent newlang.
    if (!preg_match("/^[\w]+$/", $newlang))
    {
        // Failed validation so use default.
        $newlang = $default_lang;
    }
}
else
{
    // Set newlang to the servers default.
    $newlang = $default_lang;
}

// New database driven language entries
load_languages($db, $newlang, array('option2', 'common', 'global_includes', 'global_funcs', 'combat', 'footer', 'news'), $langvars);

$title = $langvars['l_opt2_title'];
include './header.php';
echo "<h1>" . $title . "</h1>\n";

// Get POST['oldpass'], POST['newpass1'], POST['newpass2'] returns null is not found.
request_var('POST', 'oldpass', $oldpass);
request_var('POST', 'newpass1', $newpass1);
request_var('POST', 'newpass2', $newpass2);

// Check to see if newpass1 and newpass2 is null.
if (is_null($newpass1) && is_null($newpass2))
{
    // yes both newpass1 and newpass2 are null.
    echo $langvars['l_opt2_passunchanged'] . "<br><br>";
}
// Chack to see if newpass1 and newpass2 do not match.
elseif ($newpass1 !== $newpass2)
{
    // Yes newpass1 and newpass2 do not match.
    echo $langvars['l_opt2_newpassnomatch'] . "<br><br>";
}
// So newpass1 and newpass2 are not null and they do match.
else
{
    // Load Player information from their username (i.e. email)
    $playerinfo = false;
    $rs = $db->Execute("SELECT ship_id, password FROM {$db->prefix}ships WHERE email=? LIMIT 1;", array($_SESSION['username']));
    db_op_result ($db, $rs, __LINE__, __FILE__);

    // Do we have a valid RecordSet?
    if ($rs instanceof ADORecordSet)
    {
        // We have a valid RecorSet, so now set $playerinfo.
        $playerinfo = $rs->fields;

        // Initialize the hasher, with 8 (a base-2 log iteration count) for password stretching and without less-secure portable hashes for older systems
        $hasher = new PasswordHash(8, false);

        // Check the password against the stored hashed password
        $password_match = $hasher->CheckPassword($oldpass, $playerinfo['password']);

        // Does the oldpass and the players password match?
        if ($password_match)
        {
            // Yes they match so hash the password.  $hashedPassword will be a 60-character string.
            $hashed_pass = $hasher->HashPassword($newpass1);

            // Now update the players password.
            $rs = $db->Execute("UPDATE {$db->prefix}ships SET password = ? WHERE ship_id = ?;", array($hashed_pass, $playerinfo['ship_id']));
            db_op_result ($db, $rs, __LINE__, __FILE__);

            // Now check to see if we have a valid update and have ONLY 1 changed record.
            if ((is_bool($rs) && $rs == false) || $db->Affected_Rows() != 1)
            {
                // Either we got an error in the SQL Query or <> 1 records was changed.
                echo $langvars['l_opt2_passchangeerr'] . "<br><br>";
            }
            else
            {
                // Everything went well so update the password session to the new password.
                echo $langvars['l_opt2_passchanged'] . "<br><br>";
                $_SESSION['password'] = $newpass1;
            }
        }
        else
        {
            // The oldpass did not match the players password.
            echo $l_opt2_srcpassfalse . "<br><br>";
        }
    }
}

// Is the current language ($lang) different from the requested new language (newlang)
if ($lang != $newlang)
{
    // Yes, so update to the new requited language.
    $res = $db->Execute("UPDATE {$db->prefix}ships SET lang = ? WHERE email = ? LIMIT 1;", array($newlang, $_SESSION['username']));
    db_op_result ($db, $res, __LINE__, __FILE__);

    // Now cycle through the supported language list unto we get a match to the new language.
    foreach ($avail_lang as $curlang)
    {
        if ($newlang == $curlang['file'])
        {
            $langvars['l_opt2_chlang'] = str_replace("[lang]", "$curlang[name]", $langvars['l_opt2_chlang']);
            echo $langvars['l_opt2_chlang'] . "<p>";
            break;
        }
    }
}

echo "<br>";
TEXT_GOTOMAIN();
include './footer.php';
?>
