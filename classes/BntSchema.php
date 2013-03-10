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
// Todo: create universe should iterate the results from the $destroy_table_results, there should be no output in this file.

if (strpos ($_SERVER['PHP_SELF'], 'BntSchema.php')) // Prevent direct access to this file
{
    $error_file = $_SERVER['SCRIPT_NAME'];
    include_once './error.php';
}

class BntSchema
{
	static function destroy ($db, $ADODB_SESSION_DB, $db_prefix)
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
		return $destroy_table_results
	}
}
?>
