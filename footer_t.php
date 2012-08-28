<?php

// to stop people from loading this page.
if (preg_match("/footer_t.php/i", $_SERVER['PHP_SELF']))
{
#      echo "You can not access this file directly!";
      die();
}

// ======================================
// Now we handle the Update Ticker.
// ======================================

// Always null out the arrays before use.
$langvars = null;

// Needs to be put into the language table.
$langvars['l_running_update'] = "Running Update";
$langvars['l_please_wait'] = "Please wait.";

// Now add the loaded language variables into the Template API.
$langvars['container'] = "langvar";
$template->AddVariables("langvars", $langvars);

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

?>
