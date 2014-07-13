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
// File: fader.php
?>
<script>

// Ticker Constructor
news                      = new newsTicker();

// Initialize the Ticker, You need to supply the HTML id as the argument, returns true or false.
var result                = news.initTicker('news_ticker');

// I have put in some safaty precautions, but just in case always check the return value from initTicker().
if (result == true)
{
    // Set the width of the Ticker (in pixles)
    news.Width(500);

    // Gets the Width of the Ticker (in pixles)
    var width = news.Width();

    // Sets the Interval/Update Time in seconds.
    news.Interval(5);

    // Gets the Interval/Update Time in Seconds.
    var interval = news.Interval();

    // I have decided on adding single news articles at a time due to it makes it more easier to add when using PHP or XSL.
    // We can supply the information by either of the following ways:
    // 1: Supply the information from a Database and inserting it with PHP.
    // 2: Supply the information from a Database and convert it into XML (for formatting) and have the XSLT Stylesheet extract the information and insert it.

<?php
// Database driven language entries
$langvars = Bnt\Translate::load($pdo_db, $lang, array ('news'));

$startdate = date("Y/m/d");
if ($db->inactive)
{
    echo "    url = null;\n";
    echo "    text = \"{$langvars['l_news_down']}\";\n";
    echo "    type = null;      // Not used as yet.\n";
    echo "    delay = 5;        // in seconds.\n";
    echo "    news.addArticle(url, text, type, delay);\n";
}
else
{
    $res = $db->Execute("SELECT headline, news_type FROM {$db->prefix}news WHERE date > ? AND date < ? ORDER BY news_id", array ($startdate ." 00:00:00", $startdate ." 23:59:59"));
    Bnt\Db::logDbErrors($db, $res, __LINE__, __FILE__);
    if (!$res instanceof ADORecordSet || $res->RecordCount() == 0)
    {
        echo "    url = null;\n";
        echo "    text = \"{$langvars['l_news_none']}\";\n";
        echo "    type = null;      // Not used as yet.\n";
        echo "    delay = 5;        // in seconds.\n";
        echo "    news.addArticle(url, text, type, delay);\n";
    }
    else
    {
        while (!$res->EOF)
        {
            $row = $res->fields;
            $headline = addslashes($row['headline']);
            echo "    url = 'news.php';\n";
            echo "    text = '{$headline}';\n";
            echo "    type = '{$row['news_type']}';    // Not used as yet.\n";
            echo "    delay = 5;                       // in seconds.\n";
            echo "    news.addArticle(url, text, type, delay);\n";
            echo "\n";
            $res->MoveNext();
        }
        echo "    news.addArticle(null, '{$langvars['l_news_end']}', null, 5);\n";
    }
}
?>
    // Starts the Ticker.
    news.startTicker();

    // If for some reason you need to stop the Ticker use the following line.
    // news.stopTicker();
}
</script>
