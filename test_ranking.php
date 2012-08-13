<?php

// Include the Config File.
include "config/config.php";

// Always make sure we are using empty vars before use.
$language_vars = null;
$variables = null;

$variables['lang'] = $lang;
$variables['link'] = "test_ranking.php";

// These should be set within the template config.
$variables['color_header'] = $color_header;
$variables['color_line1'] = $color_line1;
$variables['color_line2'] = $color_line2;

// Load required language variables for the ranking page.
load_languages($db, $lang, array('main', 'ranking', 'common', 'global_includes', 'global_funcs', 'footer', 'teams'), $language_vars, $db_logging);

// Modify the requires language variables here.
$language_vars['l_ranks_title'] = str_replace("[max_ranks]", $max_ranks, $language_vars['l_ranks_title']);

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
db_op_result ($db, $rs, __LINE__, __FILE__, $db_logging);
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
            $row['insignia'] = player_insignia_name ($db, $row['email']);

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

if (empty($username))
{
    $variables['loggedin'] = (boolean) true;
    $variables['linkback'] = array("caption"=>$language_vars['l_global_mlogin'], "link"=>"index.php");
}
else
{
    $variables['loggedin'] = (boolean) false;
    $variables['linkback'] = array("caption"=>$language_vars['l_global_mmenu'], "link"=>"main.php");
}

$variables['container'] = "variable";
$template->AddVariables('variables', $variables);

// Now add the loaded language variables into the Template API.
$language_vars['container'] = "language_var";
$template->AddVariables("language_vars", $language_vars);



// Always null out the arrays before use.
$language_vars = null;

// Load required language variables for the bottom of the ranking page (i.e. footer).
load_languages($db, $lang, array('global_includes', 'footer'), $language_vars, $db_logging);

// Needs to be put into the language table.
$language_vars['l_running_update'] = "Running Update";
$language_vars['l_please_wait'] = "Please wait.";

// Now add the loaded language variables into the Template API.
$language_vars['container'] = "language_var";
$template->AddVariables("language_vars", $language_vars);




// *** Footer of Page ***

// ======================================
// Now we handle the Update Ticker.
// ======================================

// Always null out the arrays before use.
$language_vars = null;

// Needs to be put into the language table.
$language_vars['l_running_update'] = "Running Update";
$language_vars['l_please_wait'] = "Please wait.";

// Now add the loaded language variables into the Template API.
$language_vars['container'] = "language_var";
$template->AddVariables("language_vars", $language_vars);

$display_update_ticker = false;

$rs = $db->Execute("SELECT last_run FROM {$db->prefix}scheduler LIMIT 1;");
db_op_result ($db, $rs, __LINE__, __FILE__);
if ($rs instanceof ADORecordSet)
{
    $last_run = $rs->fields['last_run'];
    $seconds_left = ($sched_ticks * 60) - (time() - $last_run);
    $display_update_ticker = true;
}

// Always null out the arrays before use.
$variables = null;

$variables['update_ticker'] = array("display"=>$display_update_ticker, "seconds_left"=>$seconds_left, "sched_ticks"=>$sched_ticks);

$variables['container'] = "variable";
$template->AddVariables('variables', $variables);
// ======================================



// ======================================
// Now we handle the players online info.
// ======================================

// Always null out the arrays before use.
$variables = null;

// Add online player count.
$variables['players_online'] = (integer) players_online($db);

$variables['container'] = "variable";
$template->AddVariables('variables', $variables);
// ======================================



// ======================================
// Now we handle the Sourceforge Logo.
// ======================================

// Always null out the arrays before use.
$variables = null;

if ($footer_show_debug == true) // Make the SF logo a little bit larger to balance the extra line from the benchmark for page generation
{
    $variables['sf_logo_type'] = '14';
}
else
{
    $variables['sf_logo_type'] = '11';
}
if (preg_match("/index2.php/i", $_SERVER['PHP_SELF']) || preg_match("/igb.php/i", $_SERVER['PHP_SELF']))
{
    $variables['sf_logo_type'] +=1; // Make the SF logo darker for all pages except login
}
$variables['container'] = "variable";
$template->AddVariables('variables', $variables);
// ======================================







// Always null out the arrays before use.
$variables = null;

if ($footer_show_debug == true) // Make the SF logo a little bit larger to balance the extra line from the benchmark for page generation
{
    $variables['sf_logo_type'] = '14';
}
else
{
    $variables['sf_logo_type'] = '11';
}
if (preg_match("/index2.php/i", $_SERVER['PHP_SELF']) || preg_match("/igb.php/i", $_SERVER['PHP_SELF']))
{
    $variables['sf_logo_type'] +=1; // Make the SF logo darker for all pages except login
}
$variables['container'] = "variable";
$template->AddVariables('variables', $variables);


// ======================================
// Now we tell the Template API to out-
// put the page.
// ======================================
$template->Display("test_ranking.tpl");
// ======================================

?>
