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
// File: classes/BntSchema.php

if (strpos ($_SERVER['PHP_SELF'], 'BntSchema.php')) // Prevent direct access to this file
{
    $error_file = $_SERVER['SCRIPT_NAME'];
    include_once './error.php';
}

class BntSchema
{
    public static function destroy($db, $db_prefix)
    {
        // Need to set this or all hell breaks loose.
        $db->inactive = true;

        $i = 0;
        $schema_files = new DirectoryIterator ("schema/mysql"); // TODO: This is hardcoded for mysql right now, but needs to be extended to handle pgsql also
        $destroy_table_results = Array ();

        foreach ($schema_files as $schema_filename)
        {
            $table_timer = new BntTimer;
            $table_timer->start (); // Start benchmarking

            if ($schema_filename->isFile () && $schema_filename->getExtension() == 'sql')
            {
                // Routine to handle persistent database tables. If a SQL schema file starts with persist-, then it is a persistent table. Fix the name.
                $persist_file = (substr ($schema_filename, 0, 8) === 'persist-');
                if ($persist_file)
                {
                    $tablename = substr ($schema_filename, 8, -4);
                }
                else
                {
                    $tablename = substr ($schema_filename, 0, -4);
                }

                if (!$persist_file)
                {
                    $drop_res = $db->exec ('DROP TABLE ' . $db_prefix . $tablename);
                    BntDb::logDbErrors ($db, $drop_res, __LINE__, __FILE__);

                    if ($drop_res !== false)
                    {
                        $destroy_table_results[$i]['result'] = true;
                    }
                    else
                    {
                        $destroy_table_results[$i]['result'] = $db->errorInfo()[1] . ": " . $db->errorInfo()[2];
                    }
                }
                else
                {
                    $destroy_table_results[$i]['result'] = 'Skipped - Persistent table';
                }

                $destroy_table_results[$i]['name'] = $db_prefix . $tablename;
                $table_timer->stop ();
                $destroy_table_results[$i]['time'] = $table_timer->elapsed ();
                $i++;
            }
        }

        return $destroy_table_results;
    }

    public static function create($db, $db_prefix)
    {
        $i = 0;
        define ("PDO_SUCCESS", (string) "00000"); // PDO gives an error code of string 00000 if successful. Not extremely helpful.
        $schema_files = new DirectoryIterator ("schema/mysql/"); // TODO: This is hardcoded for mysql right now, but needs to be extended to handle pgsql also

        // New SQL Schema table creation
        $create_table_results = Array ();

        foreach ($schema_files as $schema_filename)
        {
            $table_timer = new BntTimer;
            $table_timer->start (); // Start benchmarking

            if ($schema_filename->isFile () && $schema_filename->getExtension() == 'sql')
            {
                // Routine to handle persistent database tables. If a SQL schema file starts with persist-, then it is a persistent table. Fix the name.
                $persist_file = (substr ($schema_filename, 0, 8) === 'persist-');
                if ($persist_file)
                {
                    $tablename = substr ($schema_filename, 8, -4);
                }
                else
                {
                    $tablename = substr ($schema_filename, 0, -4);
                }

                // Slurp the SQL call from schema, and turn it into an SQL string
                $sql_query = file_get_contents ("schema/mysql/" . $schema_filename);

                // Replace the default prefix (bnt_) with the chosen table prefix from the game.
                $sql_query = preg_replace ('/bnt_/', $db_prefix, $sql_query);

                // TODO: Remove all comments from SQL

                // TODO: Test handling invalid SQL to ensure it hits the error logger below AND the visible output during running
                $sth = $db->prepare ($sql_query);
                $execute_res = $sth->execute ();

                if ($db->errorCode() !== PDO_SUCCESS)
                {
                    $create_table_results[$i]['result'] = $db->errorInfo()[1] . ": " . $db->errorInfo()[2];
                }
                else
                {
                    $create_table_results[$i]['result'] = true;
                }

                BntDb::logDbErrors ($db, $execute_res, __LINE__, __FILE__);
                $create_table_results[$i]['name'] = $db_prefix . $tablename;
                $table_timer->stop ();
                $create_table_results[$i]['time'] = $table_timer->elapsed ();
                $i++;
            }
        }

        return $create_table_results;
    }
}
?>
