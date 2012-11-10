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
// File: db_config.php

if (strpos ($_SERVER['PHP_SELF'], 'db_config.php')) // Prevent direct access to this file
{
    die ('Please do not access this file directly');
}

// The ADOdb db module is required to run BNT. You can find it at http://php.weblogs.com/ADODB. Enter the
// path where it is installed here. We suggest putting
// ADOdb into a subdirectory (adodb) under a subdirectory of BNT called classes.
$ADOdbpath = "./classes/adodb";

// Port to connect to database on. Note : if you do not know the port, set this to "" for default. Ex, MySQL default is 3306
$dbport = "";

// Hostname and port of the database server:
// These are defaults, you normally won't have to change them
$ADODB_SESSION_CONNECT = "127.0.0.1";

// Username and password to connect to the database:
$ADODB_SESSION_USER = "bnt";
$ADODB_SESSION_PWD = "bnt";

// Name of the SQL database:
$ADODB_SESSION_DB = "bnt";

// Type of the SQL database. This can be anything supported by ADOdb. Here are a few:
// "mysql" for MySQL - please don't use this one, it doesn't support transactions, which we now use
// "mysqlt" for MySQLi - needed for transaction support
// "postgres8" for PostgreSQL ver 8 and up
// "postgres9" for PostgreSQL ver 9 and up
// NOTE: only mysqlt works as of this release.
$ADODB_SESSION_DRIVER = "mysqlt";

// Table prefix for the database. If you want to run more than
// one game of BNT on the same database, or if the current table
// names conflict with tables you already have in your db, you will
// need to change this
$db_prefix = "bnt_";
?>
