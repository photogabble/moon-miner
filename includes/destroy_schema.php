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
// File: includes/destroy_schema.php

if (strpos ($_SERVER['PHP_SELF'], 'destroy_schema.php')) // Prevent direct access to this file
{
    $error_file = $_SERVER['SCRIPT_NAME'];
    include_once './error.php';
}

function destroy_schema ($db, $ADODB_SESSION_DB, $db_prefix)
{
    // Delete all tables in the database
    table_header("Dropping Tables --- Stage 3");

    $i = 0;
    $schema_files = new DirectoryIterator("schema/");

    $create_table_results = Array();

    foreach ($schema_files as $schema_filename)
    {
        // New XML Schema table creation
        $schema = new adoSchema3 ($db);
        $schema->setPrefix($db_prefix);

        // This is to get around the issue of not having DirectoryIterator::getExtension.
        $file_ext = pathinfo ($schema_filename->getFilename(), PATHINFO_EXTENSION);

        if ($schema_filename->isFile() && $file_ext == 'xml')
        {
            $tablename = substr($schema_filename, 0, -4);
            $res = $db->Execute('DROP TABLE ' . $db_prefix . $tablename);
            \bnt\dbop::dbresult ($db, $res, __LINE__, __FILE__);
            $err = true_or_false (0, $db->ErrorMsg(), "No errors found", $db->ErrorNo() . ": " . $db->ErrorMsg());
            table_row ($db, "Dropping table " . $tablename, "Failed", "Passed");
            $i++;
        }
    }

    table_footer("Hover over the failed line to see the error.");
    echo "<strong>Dropping stage complete.</strong><p>";
}
?>
