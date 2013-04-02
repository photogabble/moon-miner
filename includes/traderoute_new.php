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
// File: includes/traderoute_new.php

if (strpos ($_SERVER['PHP_SELF'], 'traderoute_new.php')) // Prevent direct access to this file
{
    $error_file = $_SERVER['SCRIPT_NAME'];
    include_once './error.php';
}

function traderoute_new ($db, $lang, $langvars, $traderoute_id)
{
    global $playerinfo, $color_line1, $color_line2, $color_header;
    global $num_traderoutes, $servertimezone;
    global $max_traderoutes_player;
    $langvars = BntTranslate::load ($db, $lang, array ('traderoutes', 'common', 'global_includes', 'global_funcs', 'footer'));
    $editroute = null;

    if (!empty ($traderoute_id))
    {
        $result = $db->Execute ("SELECT * FROM {$db->prefix}traderoutes WHERE traderoute_id=?", array ($traderoute_id));
        DbOp::dbResult ($db, $result, __LINE__, __FILE__);

        if (!$result || $result->EOF)
        {
            traderoute_die ($langvars['l_tdr_editerr']);
        }

        $editroute = $result->fields;

        if ($editroute['owner'] != $playerinfo['ship_id'])
        {
            traderoute_die ($langvars['l_tdr_notowner']);
        }
    }

    if ($num_traderoutes >= $max_traderoutes_player && is_null ($editroute))
    {
        traderoute_die ("<p>" . $langvars['l_tdr_maxtdr'] . "<p>");
    }

    echo "<p><font size=3 color=blue><strong>";

    if (is_null ($editroute))
    {
        echo $langvars['l_tdr_createnew'];
    }
    else
    {
        echo $langvars['l_tdr_editinga'] . " ";
    }

    echo $langvars['l_tdr_traderoute'] . "</strong></font><p>";

    // Get Planet info Corp and Personal

    $result = $db->Execute ("SELECT * FROM {$db->prefix}planets WHERE owner=? ORDER BY sector_id", array ($playerinfo['ship_id']));
    DbOp::dbResult ($db, $result, __LINE__, __FILE__);

    $num_planets = $result->RecordCount();
    $i=0;
    while (!$result->EOF)
    {
        $planets[$i] = $result->fields;

        if ($planets[$i]['name'] == "")
        {
            $planets[$i]['name'] = $langvars['l_tdr_unnamed'];
        }

        $i++;
        $result->MoveNext();
    }

    $result = $db->Execute ("SELECT * FROM {$db->prefix}planets WHERE corp=? AND corp!=0 AND owner<>? ORDER BY sector_id", array ($playerinfo['team'], $playerinfo['ship_id']));
    DbOp::dbResult ($db, $result, __LINE__, __FILE__);
    $num_corp_planets = $result->RecordCount();
    $i=0;
    while (!$result->EOF)
    {
        $planets_corp[$i] = $result->fields;

        if ($planets_corp[$i]['name'] == "")
        {
            $planets_corp[$i]['name'] = $langvars['l_tdr_unnamed'];
        }

        $i++;
        $result->MoveNext();
    }

    // Display Current Sector
    echo $langvars['l_tdr_cursector'] . " " . $playerinfo['sector'] . "<br>";

    // Start of form for starting location
    echo "
        <form action=traderoute.php?command=create method=post>
        <table border=0><tr>
        <td align=right><font size=2><strong>" . $langvars['l_tdr_selspoint'] . " <br>&nbsp;</strong></font></td>
        <tr>
        <td align=right><font size=2>" . $langvars['l_tdr_port'] . " : </font></td>
        <td><input type=radio name=\"ptype1\" value=\"port\"
        ";

    if (is_null ($editroute) || (!is_null ($editroute) && $editroute['source_type'] == 'P'))
    {
        echo " checked";
    }

    echo "
        ></td>
        <td>&nbsp;&nbsp;<input type=text name=port_id1 size=20 align='center'
        ";

    if (!is_null ($editroute) && $editroute['source_type'] == 'P')
    {
        echo " value=\"$editroute[source_id]\"";
    }

    echo "
        ></td>
        </tr><tr>
        ";

    // Personal Planet
    echo "
        <td align=right><font size=2>Personal " . $langvars['l_tdr_planet'] . " : </font></td>
        <td><input type=radio name=\"ptype1\" value=\"planet\"
        ";

    if (!is_null ($editroute) && $editroute['source_type'] == 'L')
    {
        echo " checked";
    }

    echo '
        ></td>
        <td>&nbsp;&nbsp;<select name=planet_id1>
        ';

    if ($num_planets == 0)
    {
        echo "<option value=none>" . $langvars['l_tdr_none'] . "</option>";
    }
    else
    {
        $i=0;
        while ($i < $num_planets)
        {
            echo "<option ";

            if ($planets[$i]['planet_id'] == $editroute['source_id'])
            {
                echo "selected ";
            }

            echo "value=" . $planets[$i]['planet_id'] . ">" . $planets[$i]['name'] . " " . $langvars['l_tdr_insector'] . " " . $planets[$i]['sector_id'] . "</option>";
            $i++;
        }
    }

    // Corp Planet
    echo "
        </tr><tr>
        <td align=right><font size=2>Corporate " . $langvars['l_tdr_planet'] . " : </font></td>
        <td><input type=radio name=\"ptype1\" value=\"corp_planet\"
        ";

    if (!is_null ($editroute) && $editroute['source_type'] == 'C')
        echo " checked";

    echo '
        ></td>
        <td>&nbsp;&nbsp;<select name=corp_planet_id1>
        ';

    if ($num_corp_planets == 0)
    {
        echo "<option value=none>" . $langvars['l_tdr_none'] . "</option>";
    }
    else
    {
        $i=0;
        while ($i < $num_corp_planets)
        {
            echo "<option ";

            if ($planets_corp[$i]['planet_id'] == $editroute['source_id'])
            {
                echo "selected ";
            }

            echo "value=" . $planets_corp[$i]['planet_id'] . ">" . $planets_corp[$i]['name'] . " " . $langvars['l_tdr_insector'] . " " . $planets_corp[$i]['sector_id'] . "</option>";
            $i++;
        }
    }

    echo "
        </select>
        </tr>";

    // Begin Ending point selection
    echo "
        <tr><td>&nbsp;
        </tr><tr>
        <td align=right><font size=2><strong>" . $langvars['l_tdr_selendpoint'] . " : <br>&nbsp;</strong></font></td>
        <tr>
        <td align=right><font size=2>" . $langvars['l_tdr_port'] . " : </font></td>
        <td><input type=radio name=\"ptype2\" value=\"port\"
        ";

    if (is_null ($editroute) || (!is_null ($editroute) && $editroute['dest_type'] == 'P'))
    {
        echo " checked";
    }

    echo '
        ></td>
        <td>&nbsp;&nbsp;<input type=text name=port_id2 size=20 align="center"
        ';

    if (!is_null ($editroute) && $editroute['dest_type'] == 'P')
    {
        echo " value=\"$editroute[dest_id]\"";
    }

    echo "
        ></td>
        </tr>";

    // Personal Planet
    echo "
        <tr>
        <td align=right><font size=2>Personal " . $langvars['l_tdr_planet'] . " : </font></td>
        <td><input type=radio name=\"ptype2\" value=\"planet\"
        ";

    if (!is_null ($editroute) && $editroute['dest_type'] == 'L')
    {
        echo " checked";
    }

    echo '
        ></td>
        <td>&nbsp;&nbsp;<select name=planet_id2>
        ';

    if ($num_planets == 0)
    {
        echo "<option value=none>" . $langvars['l_tdr_none'] . "</option>";
    }
    else
    {
        $i=0;
        while ($i < $num_planets)
        {
            echo "<option ";

            if ($planets[$i]['planet_id'] == $editroute['dest_id'])
            {
                echo "selected ";
            }

            echo "value=" . $planets[$i]['planet_id'] . ">" . $planets[$i]['name'] . " " . $langvars['l_tdr_insector'] . " " . $planets[$i]['sector_id'] . "</option>";
            $i++;
        }
    }

    // Corp Planet
    echo "
        </tr><tr>
        <td align=right><font size=2>Corporate " . $langvars['l_tdr_planet'] . " : </font></td>
        <td><input type=radio name=\"ptype2\" value=\"corp_planet\"
        ";

    if (!is_null ($editroute) && $editroute['dest_type'] == 'C')
    {
        echo " checked";
    }

    echo '
        ></td>
        <td>&nbsp;&nbsp;<select name=corp_planet_id2>
        ';

    if ($num_corp_planets == 0)
    {
        echo "<option value=none>" . $langvars['l_tdr_none'] . "</option>";
    }
    else
    {
        $i=0;
        while ($i < $num_corp_planets)
        {
            echo "<option ";

            if ($planets_corp[$i]['planet_id'] == $editroute['dest_id'])
            {
                echo "selected ";
            }

            echo "value=" . $planets_corp[$i]['planet_id'] . ">" . $planets_corp[$i]['name'] . " " . $langvars['l_tdr_insector'] . " " . $planets_corp[$i]['sector_id'] . "</option>";
            $i++;
        }
    }
    echo "
        </select>
        </tr>";

    echo "
        </select>
        </tr><tr>
        <td>&nbsp;
        </tr><tr>
        <td align=right><font size=2><strong>" . $langvars['l_tdr_selmovetype'] . " : </strong></font></td>
        <td colspan=2 valign=top><font size=2><input type=radio name=\"move_type\" value=\"realspace\"
        ";

    if (is_null ($editroute) || (!is_null ($editroute) && $editroute['move_type'] == 'R'))
    {
        echo " checked";
    }

    echo "
        >&nbsp;" . $langvars['l_tdr_realspace'] . "&nbsp;&nbsp<font size=2><input type=radio name=\"move_type\" value=\"warp\"
        ";

    if (!is_null ($editroute) && $editroute['move_type'] == 'W')
    {
        echo " checked";
    }

    echo "
        >&nbsp;" . $langvars['l_tdr_warp'] . "</font></td>
        </tr><tr>
        <td align=right><font size=2><strong>" . $langvars['l_tdr_selcircuit'] . " : </strong></font></td>
        <td colspan=2 valign=top><font size=2><input type=radio name=\"circuit_type\" value=\"1\"
        ";

    if (is_null ($editroute) || (!empty ($editroute) && $editroute['circuit'] == '1'))
    {
        echo " checked";
    }

    echo "
        >&nbsp;" . $langvars['l_tdr_oneway'] . "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type=radio name=\"circuit_type\" value=\"2\"
        ";

    if (!is_null ($editroute) && $editroute['circuit'] == '2')
    {
        echo " checked";
    }

    echo "
        >&nbsp;" . $langvars['l_tdr_bothways'] . "</font></td>
        </tr><tr>
        <td>&nbsp;
        </tr><tr>
        <td><td><td align='center'>
        ";

    if (is_null ($editroute))
    {
        echo "<input type=submit value=\"" . $langvars['l_tdr_create'] . "\">";
    }
    else
    {
        echo "<input type=hidden name=editing value=$editroute[traderoute_id]>";
        echo "<input type=submit value=\"" . $langvars['l_tdr_modify'] . "\">";
    }

    $langvars['l_tdr_returnmenu'] = str_replace ("[here]", "<a href='traderoute.php'>" . $langvars['l_here'] . "</a>", $langvars['l_tdr_returnmenu']);

    echo "
        </table>
        " . $langvars['l_tdr_returnmenu'] . "<br>
        </form>
        ";

    echo "<div style='text-align:left;'>\n";
    BntText::gotoMain ($langvars);
    echo "</div>\n";

    include './footer.php';
    die ();
}
?>
