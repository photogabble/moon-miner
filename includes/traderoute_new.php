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
    include './error.php';
}

function traderoute_new ($db, $traderoute_id)
{
    global $playerinfo, $color_line1, $color_line2, $color_header;
    global $num_traderoutes, $servertimezone;
    global $max_traderoutes_player;
    global $l_tdr_editerr, $l_tdr_maxtdr, $l_tdr_createnew, $l_tdr_editinga, $l_tdr_traderoute, $l_tdr_unnamed;
    global $l_tdr_cursector, $l_tdr_selspoint, $l_tdr_port, $l_tdr_planet, $l_tdr_none, $l_tdr_insector, $l_tdr_selendpoint;
    global $l_tdr_selmovetype, $l_tdr_realspace, $l_tdr_warp, $l_tdr_selcircuit, $l_tdr_oneway, $l_tdr_bothways, $l_tdr_create;
    global $l_tdr_modify, $l_tdr_returnmenu, $l_tdr_none;
    global $l_footer_until_update, $l_footer_players_on_1, $l_footer_players_on_2, $l_footer_one_player_on, $sched_ticks, $l_here;

    $editroute = null;

    if (!empty($traderoute_id))
    {
        $result = $db->Execute("SELECT * FROM {$db->prefix}traderoutes WHERE traderoute_id=?", array ($traderoute_id));
        \bnt\dbop::dbresult ($db, $result, __LINE__, __FILE__);

        if (!$result || $result->EOF)
        {
            traderoute_die ($l_tdr_editerr);
        }

        $editroute = $result->fields;

        if ($editroute['owner'] != $playerinfo['ship_id'])
        {
            traderoute_die ($l_tdr_notowner);
        }
    }

    if ($num_traderoutes >= $max_traderoutes_player && is_null ($editroute))
    {
        traderoute_die ("<p>$l_tdr_maxtdr<p>");
    }

    echo "<p><font size=3 color=blue><strong>";

    if (is_null ($editroute))
    {
        echo $l_tdr_createnew;
    }
    else
    {
        echo "$l_tdr_editinga ";
    }

    echo "$l_tdr_traderoute</strong></font><p>";

    // Get Planet info Corp and Personal

    $result = $db->Execute("SELECT * FROM {$db->prefix}planets WHERE owner=? ORDER BY sector_id", array ($playerinfo['ship_id']));
    \bnt\dbop::dbresult ($db, $result, __LINE__, __FILE__);

    $num_planets = $result->RecordCount();
    $i=0;
    while (!$result->EOF)
    {
        $planets[$i] = $result->fields;

        if ($planets[$i]['name'] == "")
        {
            $planets[$i]['name'] = $l_tdr_unnamed;
        }

        $i++;
        $result->MoveNext();
    }

    $result = $db->Execute("SELECT * FROM {$db->prefix}planets WHERE corp=? AND corp!=0 AND owner<>? ORDER BY sector_id", array ($playerinfo['team'], $playerinfo['ship_id']));
    \bnt\dbop::dbresult ($db, $result, __LINE__, __FILE__);
    $num_corp_planets = $result->RecordCount();
    $i=0;
    while (!$result->EOF)
    {
        $planets_corp[$i] = $result->fields;

        if ($planets_corp[$i]['name'] == "")
        {
            $planets_corp[$i]['name'] = $l_tdr_unnamed;
        }

        $i++;
        $result->MoveNext();
    }

    // Display Current Sector
    echo "$l_tdr_cursector $playerinfo[sector]<br>";

    // Start of form for starting location
    echo "
        <form action=traderoute.php?command=create method=post>
        <table border=0><tr>
        <td align=right><font size=2><strong>$l_tdr_selspoint <br>&nbsp;</strong></font></td>
        <tr>
        <td align=right><font size=2>$l_tdr_port : </font></td>
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
        <td align=right><font size=2>Personal $l_tdr_planet : </font></td>
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
        echo "<option value=none>$l_tdr_none</option>";
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

            echo "value=" . $planets[$i]['planet_id'] . ">" . $planets[$i]['name'] . " $l_tdr_insector " . $planets[$i]['sector_id'] . "</option>";
            $i++;
        }
    }

    // Corp Planet
    echo "
        </tr><tr>
        <td align=right><font size=2>Corporate $l_tdr_planet : </font></td>
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
        echo "<option value=none>$l_tdr_none</option>";
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

            echo "value=" . $planets_corp[$i]['planet_id'] . ">" . $planets_corp[$i]['name'] . " $l_tdr_insector " . $planets_corp[$i]['sector_id'] . "</option>";
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
        <td align=right><font size=2><strong>$l_tdr_selendpoint : <br>&nbsp;</strong></font></td>
        <tr>
        <td align=right><font size=2>$l_tdr_port : </font></td>
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
        <td align=right><font size=2>Personal $l_tdr_planet : </font></td>
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
        echo "<option value=none>$l_tdr_none</option>";
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

            echo "value=" . $planets[$i]['planet_id'] . ">" . $planets[$i]['name'] . " $l_tdr_insector " . $planets[$i]['sector_id'] . "</option>";
            $i++;
        }
    }

    // Corp Planet
    echo "
        </tr><tr>
        <td align=right><font size=2>Corporate $l_tdr_planet : </font></td>
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
        echo "<option value=none>$l_tdr_none</option>";
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

            echo "value=" . $planets_corp[$i]['planet_id'] . ">" . $planets_corp[$i]['name'] . " $l_tdr_insector " . $planets_corp[$i]['sector_id'] . "</option>";
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
        <td align=right><font size=2><strong>$l_tdr_selmovetype : </strong></font></td>
        <td colspan=2 valign=top><font size=2><input type=radio name=\"move_type\" value=\"realspace\"
        ";

    if (is_null ($editroute) || (!is_null ($editroute) && $editroute['move_type'] == 'R'))
    {
        echo " checked";
    }

    echo "
        >&nbsp;$l_tdr_realspace&nbsp;&nbsp<font size=2><input type=radio name=\"move_type\" value=\"warp\"
        ";

    if (!is_null ($editroute) && $editroute['move_type'] == 'W')
    {
        echo " checked";
    }

    echo "
        >&nbsp;$l_tdr_warp</font></td>
        </tr><tr>
        <td align=right><font size=2><strong>$l_tdr_selcircuit : </strong></font></td>
        <td colspan=2 valign=top><font size=2><input type=radio name=\"circuit_type\" value=\"1\"
        ";

    if (is_null ($editroute) || (!empty($editroute) && $editroute['circuit'] == '1'))
    {
        echo " checked";
    }

    echo "
        >&nbsp;$l_tdr_oneway&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type=radio name=\"circuit_type\" value=\"2\"
        ";

    if (!is_null ($editroute) && $editroute['circuit'] == '2')
    {
        echo " checked";
    }

    echo "
        >&nbsp;$l_tdr_bothways</font></td>
        </tr><tr>
        <td>&nbsp;
        </tr><tr>
        <td><td><td align='center'>
        ";

    if (is_null ($editroute))
    {
        echo "<input type=submit value=\"$l_tdr_create\">";
    }
    else
    {
        echo "<input type=hidden name=editing value=$editroute[traderoute_id]>";
        echo "<input type=submit value=\"$l_tdr_modify\">";
    }

    $l_tdr_returnmenu = str_replace("[here]", "<a href='traderoute.php'>" . $l_here . "</a>", $l_tdr_returnmenu);

    echo "
        </table>
        $l_tdr_returnmenu<br>
        </form>
        ";

    echo "<div style='text-align:left;'>\n";
    TEXT_GOTOMAIN();
    echo "</div>\n";

    include './footer.php';
    die();
}
?>
