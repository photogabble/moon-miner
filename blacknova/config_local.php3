<?php

// Path on the filesystem where the blacknova files
// will reside:
$gameroot = "/usr/local/www/blacknova";

// Domain & path of the game on your webserver (used to validate login cookie)
// This is the domain name part of the URL people enter to access your game.
// So if your game is at www.blah.com you would have:
// $gamedomain = "www.blah.com";
// Do not enter slashes for $gamedomain or anything that would come after a slash
$gamedomain = "blacknova.net";
// This is the trailing part of the URL, that is not part of the domain.
// If you enter www.blah.com/blacknova to access the game, you would leave the line as it is.
// If you do not need to specify blacknova, just enter a single slash eg:
// $gamepath = "/";
$gamepath = "/blacknova/";

// Hostname and port of the database server:
// These are defaults, you normally won't have to change them
$dbhost = "localhost";
$dbport = "3306";

// Username and password to connect to the database:
$dbuname = "blacknova_user";
$dbpass = "blacknova_pass";

// Name of the database in MySQL:
$dbname = "blavcknova_db";

// Administrator's password and email:
// Be sure to change these. Don't leave them as is.
$adminpass = "secret";
$admin_mail = "billg@microsoft.com";

// Address the forum link, link's to:
$link_forums = "http://thegeek.org/forum.php";

?>
