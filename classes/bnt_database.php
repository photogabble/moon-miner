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
// File: classes/bnt_database.php

if (strpos ($_SERVER['PHP_SELF'], 'bnt_database.php')) // Prevent direct access to this file
{
    $error_file = $_SERVER['SCRIPT_NAME'];
    include 'error.php';
}

class bnt_database
{
    // Returns true or false
    function connect ($ADODB_SESSION_CONNECT, $ADODB_SESSION_DRIVER, $ADODB_SESSION_USER, $ADODB_SESSION_PWD, $ADODB_SESSION_DB, $dbport, $db_persistent)
    {
        global $db;

        // Check to see if we are already connected to the database.
        // If so just return true.
        if ($db instanceof ADOConnection)
        {
            return true;
        }

        // Ok, seems that we are not connected to the database at this current time.
        // So we now need to setup all the database connection now.
        if (!empty($dbport))
        {
            $ADODB_SESSION_CONNECT.= ":$dbport";
        }

        $db = NewADOConnection ($ADODB_SESSION_DRIVER);
        $db->SetFetchMode (ADODB_FETCH_ASSOC); // Uncommenting this causes a huge number of bugs in other areas.

        if ($db_persistent == 1)
        {
            $result = @$db->PConnect ("$ADODB_SESSION_CONNECT", "$ADODB_SESSION_USER", "$ADODB_SESSION_PWD", "$ADODB_SESSION_DB");
        }
        else
        {
            $result = @$db->Connect ("$ADODB_SESSION_CONNECT", "$ADODB_SESSION_USER", "$ADODB_SESSION_PWD", "$ADODB_SESSION_DB");
        }

        // Check to see if we have connected
        if ( ($db instanceof ADOConnection) && (is_resource($db->_connectionID) || is_object($db->_connectionID)) )
        {
            // Set our character set to utf-8
            $db->Execute ("SET NAMES 'utf8'");

            // Yes we connected ok, so return true.
            return true;
        }
        else
        {
            // We failed to connect to the database.
            // At the moment we force this on.
            //        if ($do_die)
            //        {
            // We need to display the error message onto the screen.
            echo "Unable to connect to the Database.<br>\n";
            echo "Database Error: ". $db->ErrorNo () .": ". $db->ErrorMsg () ."<br>\n";

            // We need to stop, as we cannot function without a database.
            die ("SYSTEM HALT<br>\n");
            return false;
        }
    }
}
?>
