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
            $table_timer = new Timer;
            $table_timer->start (); // Start benchmarking

            // This is to get around the issue of not having DirectoryIterator::getExtension.
            $file_ext = pathinfo ($schema_filename->getFilename (), PATHINFO_EXTENSION);

            if ($schema_filename->isFile () && $file_ext == 'xml')
            {
                $tablename = substr ($schema_filename, 0, -4);
                $res = $db->Execute ('DROP TABLE ' . $db_prefix . $tablename);
                DbOp::dbResult ($db, $res, __LINE__, __FILE__);
                if ($db->ErrorMsg() === 0 || $db->ErrorMsg() == '') // Adodb gives either a 0 OR a null string for success. Thanks, that is helpful (not)!
                {
                    $destroy_table_results[$i]['result'] = true;
                }
                else
                {
                    $destroy_table_results[$i]['result'] = $db->ErrorNo () . ": " . $db->ErrorMsg ();
                }

                $destroy_table_results[$i]['name'] = $db_prefix . $tablename;
                $table_timer->stop ();
                $elapsed = $table_timer->elapsed ();
                $elapsed = substr ($elapsed, 0, 5);
                $destroy_table_results[$i]['time'] = $elapsed;
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
                    $table_timer->stop ();
                    $elapsed = $table_timer->elapsed ();
                    $elapsed = substr ($elapsed, 0, 5);

                    // TODO: This needs to be translated text
                    $create_table_results[$i]['result'] = "XML Schema " . $schema_filename . " could not be parsed because of error: " . $message;
                    $create_table_results[$i]['name'] = $db_prefix . $tablename;
                    $create_table_results[$i]['time'] = $elapsed;
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
                        if ($db->ErrorMsg() === 0 || $db->ErrorMsg() == '') // Adodb gives either a 0 OR a null string for success. Thanks, that is helpful (not)!
                        {
                            // TODO: This needs to be translated text
                            $create_table_results[$i]['result'] = true;
                        }
                        else
                        {
                            $create_table_results[$i]['result'] = $db->ErrorNo () . ": " . $db->ErrorMsg ();
                        }

                        DbOp::dbResult ($db, $res, __LINE__, __FILE__);
                        $create_table_results[$i]['name'] = $db_prefix . $tablename;
                        $table_timer->stop ();
                        $elapsed = $table_timer->elapsed ();
                        $elapsed = substr ($elapsed, 0, 5);
                        $create_table_results[$i]['time'] = $elapsed;
                    }
                    $i++;
                }
            }
        }
        return $create_table_results;
    }
}
?>
