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
// File: includes/is_loan_pending.php

if (strpos ($_SERVER['PHP_SELF'], 'is_loan_pending.php')) // Prevent direct access to this file
{
    $error_file = $_SERVER['SCRIPT_NAME'];
    include_once './error.php';
}

function is_loan_pending ($db, $ship_id, $ibank_lrate)
{
    $res = $db->Execute ("SELECT loan, UNIX_TIMESTAMP(loantime) AS time FROM {$db->prefix}ibank_accounts WHERE ship_id = ?", array ($ship_id));
    DbOp::dbResult ($db, $res, __LINE__, __FILE__);
    if ($res)
    {
        $account = $res->fields;

        if ($account['loan'] == 0)
        {
            return false;
        }

        $curtime = time ();
        $difftime = ($curtime - $account['time']) / 60;
        if ($difftime > $ibank_lrate)
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    else
    {
        return false;
    }
}
?>
