<?php
// Blacknova Traders - A web-based massively multiplayer space combat and trading game
// Copyright (C) 2001-2014 Ron Harwood and the BNT development team
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
// File: classes/Sessions.php

namespace Bnt;

class Sessions
{
    public $maxlifetime = 1800; // 30 mins

    public function __construct($pdo_db)
    {
        // Set the database variable for this class
        $this->pdo_db = $pdo_db;

        // Set the error mode to be exceptions, so that we can catch them
        $this->pdo_db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        // Select the current time from the database, NOT PHP
        $stmt = $this->pdo_db->prepare("SELECT now() as currenttime");
        $stmt->execute();
        $row = $stmt->fetch();

        // Set the current time for comparison to sessions to be the current database time
        $this->currenttime = $row['currenttime'];

        // Set the expiry time for sessions to be the current database time plus the maxlifetime set at top of class
        $this->expiry = gmdate('Y-m-d H:i:s', strtotime($row['currenttime']) + $this->maxlifetime);
    }

    public function __destruct()
    {
        session_write_close();
    }

    public function open($path, $name)
    {
        return true;
    }

    public function close()
    {
        return true;
    }

    public function read($sesskey)
    {
        $qry = "SELECT sessdata FROM {$this->pdo_db->prefix}sessions where sesskey=:sesskey and expiry>=:expiry";
        $stmt = $this->pdo_db->prepare($qry);
        $stmt->bindParam(':sesskey', $sesskey);
        $stmt->bindParam(':expiry', $this->currenttime);
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result['sessdata'];
    }

    public function write($sesskey, $sessdata)
    {
        try
        {
            // Try to insert the record. This will fail if the record already exists, which will trigger catch below..
            $qry = "INSERT into {$this->pdo_db->prefix}sessions (sesskey, sessdata, expiry) " .
                  'values(:sesskey, :sessdata, :expiry)';
            $stmt = $this->pdo_db->prepare($qry);
            $stmt->bindParam(':sesskey', $sesskey);
            $stmt->bindParam(':sessdata', $sessdata);
            $stmt->bindParam(':expiry', $this->expiry);
            $result = $stmt->execute();
        }
        catch (\PDOException $e)
        {
            // Insert didn't work, use update instead
            $qry = "UPDATE {$this->pdo_db->prefix}sessions SET " .
                  'sessdata=:sessdata, expiry=:expiry where sesskey=:sesskey';
            $stmt = $this->pdo_db->prepare($qry);
            $stmt->bindParam(':sesskey', $sesskey);
            $stmt->bindParam(':sessdata', $sessdata);
            $stmt->bindParam(':expiry', $this->expiry);
            $result = $stmt->execute();
        }
        return $result;
    }

    public function destroy($sesskey)
    {
        $qry = "DELETE from {$this->pdo_db->prefix}sessions where sesskey=:sesskey";
        $stmt = $this->pdo_db->prepare($qry);
        $stmt->bindParam(':sesskey', $sesskey);
        $result = $stmt->execute();
        return $result;
    }

    public function gc($maxlifetime)
    {
        $qry = "DELETE from {$this->pdo_db->prefix}sessions where expiry>:expiry";
        $stmt = $this->pdo_db->prepare($qry);
        $stmt->bindParam(':expiry', $this->expiry);
        $result = $stmt->execute();
        return $result;
    }
}
?>
