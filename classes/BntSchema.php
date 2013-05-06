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
// File: classes/BntSchema.php
// Todo: create universe should iterate the results from the $(destroy/create)_table_results, there should be no output in this file.

if (strpos ($_SERVER['PHP_SELF'], 'BntSchema.php')) // Prevent direct access to this file
{
    $error_file = $_SERVER['SCRIPT_NAME'];
    include_once './error.php';
}

class BntSchema
{
    static function destroy ($db, $db_prefix)
    {
        // Need to set this or all hell breaks loose.
        $db->inactive = true;

        $i = 0;
        $schema_files = new DirectoryIterator ("schema/");
        $destroy_table_results = Array ();

        foreach ($schema_files as $schema_filename)
        {
            // This is to get around the issue of not having DirectoryIterator::getExtension.
            $file_ext = pathinfo ($schema_filename->getFilename (), PATHINFO_EXTENSION);

            if ($schema_filename->isFile () && $file_ext == 'xml')
            {
                $tablename = substr ($schema_filename, 0, -4);
                $res = $db->Execute ('DROP TABLE ' . $db_prefix . $tablename);
                DbOp::dbResult ($db, $res, __LINE__, __FILE__);
                $destroy_table_results[$i] = true_or_false (0, $db->ErrorMsg (), "No errors found", $db->ErrorNo () . ": " . $db->ErrorMsg ());
                table_row ($db, "Dropping table " . $tablename, "Failed", "Passed");
                $i++;
            }
        }
        return $destroy_table_results;
    }

    static function create ($db, $db_prefix)
    {
        $i = 0;
        $schema_files = new DirectoryIterator("schema/");

        // New XML Schema table creation
        $schema = new adoSchema3 ($db);
        $schema->setPrefix ($db_prefix);
        $create_table_results = Array ();

        foreach ($schema_files as $schema_filename)
        {
            $schema->clearSQL ();
            $table_timer = new Timer;
            $table_timer->start (); // Start benchmarking

            // This is to get around the issue of not having DirectoryIterator::getExtension.
            $file_ext = pathinfo ($schema_filename->getFilename (), PATHINFO_EXTENSION);

            if ($schema_filename->isFile () && $file_ext == 'xml')
            {
                $tablename = substr ($schema_filename, 0, -4);
                $message = TestXml::parse ("schema/" . $schema_filename); // Test the xml file for validity before processing

                if ($message !== true)
                {
                    $err = true_or_false (true, false, "No errors found in table " . $tablename, "XML Schema " . $schema_filename . " could not be parsed because of error:" . $message);
                    $table_timer->stop ();
                    $elapsed = $table_timer->elapsed ();
                    $elapsed = substr ($elapsed, 0, 5);
                    table_row_xml ($db, "Creating " . $tablename . " table took " . $elapsed . " seconds. ","Failed","Passed", $err);
                    $i++;
                }
                else
                {
                    // Call ParseSchema () to build SQL from the XML schema file. Then call ExecuteSchema () to apply the resulting SQL to the database.
                    $parsed_xml = '';
                    $parsed_xml = $schema->ParseSchema ("schema/" . $schema_filename);

                    foreach ($parsed_xml as $execute_sql)
                    {
                        $res = $db->Execute ($execute_sql);
                        DbOp::dbResult ($db, $res, __LINE__, __FILE__);
                    }
                    $err = true_or_false (true, $db->ErrorMsg (),"No errors found in table " . $tablename, $db->ErrorNo () . ": " . $db->ErrorMsg ());
                    $table_timer->stop ();
                    $elapsed = $table_timer->elapsed ();
                    $elapsed = substr ($elapsed, 0, 5);
                    table_row ($db, "Creating " . $tablename . " table took " . $elapsed . " seconds. ","Failed","Passed");
                    $i++;
                }
            }
        }
    }
}
?>
