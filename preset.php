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
// File: preset.php

include './global_includes.php';

BntLogin::checkLogin ($db, $lang, $langvars, $bntreg, $template);

$body_class = 'preset';
$langvars = BntTranslate::load ($db, $lang, array ('presets'));
$title = $langvars['l_pre_title'];
include './header.php';


// Database driven language entries
$langvars = BntTranslate::load ($db, $lang, array ('presets', 'common', 'global_includes', 'global_funcs', 'combat', 'footer', 'news'));
echo "<h1>" . $title . "</h1>\n";
echo "<body class ='" . $body_class . "'>";
$result = $db->Execute ("SELECT * FROM {$db->prefix}ships WHERE email = ?;", array ($_SESSION['username']));
BntDb::logDbErrors ($db, $result, __LINE__, __FILE__);
$playerinfo = $result->fields;

$preset_list = array();

// Returns null if it doesn't have it set, boolean false if its set but fails to validate and the actual value if it all passes.
$preset_list[1]  = filter_input (INPUT_POST, 'preset1', FILTER_VALIDATE_INT, array('options'=>array('min_range'=>1, 'max_range'=>$sector_max)));
$preset_list[2]  = filter_input (INPUT_POST, 'preset2', FILTER_VALIDATE_INT, array('options'=>array('min_range'=>1, 'max_range'=>$sector_max)));
$preset_list[3]  = filter_input (INPUT_POST, 'preset3', FILTER_VALIDATE_INT, array('options'=>array('min_range'=>1, 'max_range'=>$sector_max)));

$change = filter_input (INPUT_POST, 'change', FILTER_VALIDATE_INT, array('options'=>array('min_range'=>0, 'max_range'=>1)));

foreach ($preset_list as $index=>$preset)
{
    if ($preset === false)
    {
        $change = 0;
        $_langvars['l_pre_invalid'] = str_replace ("[preset]", $index, $langvars['l_pre_invalid']);
        $_langvars['l_pre_invalid'] = str_replace ("[sector_max]", $sector_max, $_langvars['l_pre_invalid']);
        echo $_langvars['l_pre_invalid'] . "<br>\n";
    }
}
echo "<br>\n";

if ($change !== 1)
{
    echo "<form action='preset.php' method='post'>";
    echo "<div style='padding:2px;'>Preset 1: <input type='text' name='preset1' size='6' maxlength='6' value='{$playerinfo['preset1']}'></div>";
    echo "<div style='padding:2px;'>Preset 2: <input type='text' name='preset2' size='6' maxlength='6' value='{$playerinfo['preset2']}'></div>";
    echo "<div style='padding:2px;'>Preset 3: <input type='text' name='preset3' size='6' maxlength='6' value='{$playerinfo['preset3']}'></div>";
    echo "<input type='hidden' name='change' value='1'>";
    echo "<div style='padding:2px;'><input type='submit' value=" . $langvars['l_pre_save'] . "></div>";
    echo "</form>";
    echo "<br>\n";
}
else
{
    $update = $db->Execute ("UPDATE {$db->prefix}ships SET preset1 = ?, preset2 = ?, preset3 = ? WHERE ship_id = ?;", array ($preset_list[1], $preset_list[2], $preset_list[3], $playerinfo['ship_id']));
    BntDb::logDbErrors ($db, $update, __LINE__, __FILE__);
    $langvars['l_pre_set'] = str_replace ("[preset1]", "<a href=rsmove.php?engage=1&destination=$preset_list[1]>$preset_list[1]</a>", $langvars['l_pre_set']);
    $langvars['l_pre_set'] = str_replace ("[preset2]", "<a href=rsmove.php?engage=1&destination=$preset_list[2]>$preset_list[2]</a>", $langvars['l_pre_set']);
    $langvars['l_pre_set'] = str_replace ("[preset3]", "<a href=rsmove.php?engage=1&destination=$preset_list[3]>$preset_list[3]</a>", $langvars['l_pre_set']);
    echo $langvars['l_pre_set'] . "<br><br>";
}

BntText::gotoMain ($db, $lang, $langvars);
include './footer.php';

?>
