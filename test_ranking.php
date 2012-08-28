<?php

// Include the Config File.
include './global_includes.php';
include './includes/player_insignia_name.php';

// Always make sure we are using empty vars before use.
$langvars = null;
$variables = null;

$variables['lang'] = $lang;
$variables['link'] = "test_ranking.php";

// These should be set within the template config.
$variables['color_header'] = $color_header;
$variables['color_line1'] = $color_line1;
$variables['color_line2'] = $color_line2;

// Load required language variables for the ranking page.
load_languages($db, $lang, array('main', 'ranking', 'common', 'global_includes', 'global_funcs', 'footer', 'teams'), $langvars);

// Modify the requires language variables here.
$langvars['l_ranks_title'] = str_replace("[max_ranks]", $max_ranks, $langvars['l_ranks_title']);

// Get requested ranking order.
request_var("GET", "sort", $sort);
switch($sort)
{
    case "turns":
    {
        $by = "turns_used DESC, character_name ASC";
        break;
    }
    case "login":
    {
        $by = "last_login DESC, character_name ASC";
        break;
    }
    case "good":
    {
        $by = "rating DESC, character_name ASC";
        break;
    }
    case "bad":
    {
        $by = "rating ASC, character_name ASC";
        break;
    }
    case "team":
    {
        $by = "{$db->prefix}teams.team_name DESC, character_name ASC";
        break;
    }
    case "efficiency":
    {
        $by = "efficiency DESC";
        break;
    }
    default:
    {
        $by = "score DESC, character_name ASC";
        break;
    }
}

$variables['num_players'] = (integer) 0;

$rs = $db->Execute("SELECT {$db->prefix}ships.email,{$db->prefix}ships.score,{$db->prefix}ships.character_name,{$db->prefix}ships.turns_used,{$db->prefix}ships.last_login,UNIX_TIMESTAMP({$db->prefix}ships.last_login) as online,{$db->prefix}ships.rating, {$db->prefix}teams.team_name, if ({$db->prefix}ships.turns_used<150,0,ROUND({$db->prefix}ships.score/{$db->prefix}ships.turns_used)) AS efficiency FROM {$db->prefix}ships LEFT JOIN {$db->prefix}teams ON {$db->prefix}ships.team = {$db->prefix}teams.id  WHERE ship_destroyed='N' and email NOT LIKE '%@xenobe' AND turns_used >0 ORDER BY $by LIMIT $max_ranks");
db_op_result ($db, $rs, __LINE__, __FILE__);
if ($rs instanceof ADORecordSet)
{
    $variables['num_players'] = (integer) $rs->RecordCount();

    if ($variables['num_players'] > 0)
    {
        $player_list = array();

        while (!$rs->EOF)
        {
            $row = $rs->fields;

            // Set the players rank number.
            $row['rank'] = count($player_list)+1;

            // Calculate the players rating.
            $rating=round(sqrt( abs($row['rating']) ));
            if (abs($row['rating'])!=$row['rating'])
            {
                $rating=-1*$rating;
            }
            $row['rating'] =$rating;

            // Calculate the players online status.
            $curtime = TIME();
            $time = $row['online'];
            $difftime = ($curtime - $time) / 60;
            $temp_turns = $row['turns_used'];
            if ($temp_turns <= 0)
            {
                $temp_turns = 1;
            }

            $row['online'] = "Offline";
            if ($difftime <= 5)
            {
                $row['online'] = "Online";
            }

            // Set the players Insignia.
            $row['insignia'] = player_insignia_name ($db, $row['email'], $langvars);

            // This is just to show that we can set the type of player.
            // like: banned, admin, player, npc etc.
            if ($row['email'] == "TheMightyDude@gmail.com")
            {
                $row['type'] = "admin";
            }
            else
            {
                $row['type'] = "player";
            }

            array_push($player_list, $row);

            $rs->MoveNext();
        }
        $player_list['container']    = "player";
        $template->AddVariables("players", $player_list);
    }
}

if (empty($_SESSION['username']))
{
    $variables['loggedin'] = (boolean) true;
    $variables['linkback'] = array("caption"=>$langvars['l_global_mlogin'], "link"=>"index.php");
}
else
{
    $variables['loggedin'] = (boolean) false;
    $variables['linkback'] = array("caption"=>$langvars['l_global_mmenu'], "link"=>"main.php");
}

$variables['container'] = "variable";
$template->AddVariables('variables', $variables);

// Now add the loaded language variables into the Template API.
$langvars['container'] = "langvar";
$template->AddVariables("langvars", $langvars);



// Now we include the Footer Logic.
include_once "footer_t.php


// ======================================
// Now we tell the Template API to out-
// put the page.
// ======================================
$template->Display("test_ranking.tpl");
// ======================================

?>
