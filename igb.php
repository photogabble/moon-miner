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
// File: igb.php

include 'global_includes.php';
include_once 'includes/ibank_error.php';
updatecookie ();

// New database driven language entries
load_languages($db, $lang, array('igb', 'common', 'global_includes', 'global_funcs', 'footer', 'news'), $langvars, $db_logging);

$title = $l_igb_title;
$body_class = 'igb';
include 'header.php';

if (checklogin () )
{
    die ();
}

$result = $db->Execute("SELECT * FROM {$db->prefix}ships WHERE email='$username'");
db_op_result ($db, $result, __LINE__, __FILE__, $db_logging);
$playerinfo = $result->fields;

$result = $db->Execute("SELECT * FROM {$db->prefix}ibank_accounts WHERE ship_id=$playerinfo[ship_id]");
db_op_result ($db, $result, __LINE__, __FILE__, $db_logging);
$account = $result->fields;

?>
<center>
<img src="images/div1.png" alt="" style="width: 600px; height:21px">
<div style="width:600px; max-width:600px;" class="igb">
<table style="width:600px; height:350px;" border="0px">
<tr><td style="background-image:URL(images/igbscreen.png); background-repeat:no-repeat;" align="center">
<table style="width:550px; height:300px;" border="0px">

<?php

if (!$allow_ibank)
  ibank_error($l_igb_malfunction, "main.php");

if (!isset($_REQUEST['command']))
{
    $_REQUEST['command'] = '';
    $command = '';
}
else
{
    $command = $_REQUEST['command'];
}

if ($command == 'login') //main menu
  IGB_login();
elseif ($command == 'withdraw') //withdraw menu
  IGB_withdraw();
elseif ($command == 'withdraw2') //withdraw operation
  IGB_withdraw2();
elseif ($command == 'deposit') //deposit menu
  IGB_deposit();
elseif ($command == 'deposit2') //deposit operation
  IGB_deposit2();
elseif ($command == 'transfer') //main transfer menu
  IGB_transfer();
elseif ($command == 'transfer2') //specific transfer menu (ship or planet)
  IGB_transfer2();
elseif ($command == 'transfer3') //transfer operation
  IGB_transfer3();
elseif ($command == 'loans') //loans menu
  IGB_loans();
elseif ($command == 'borrow') //borrow operation
  IGB_borrow();
elseif ($command == 'repay') //repay operation
  ibank_repay();
elseif ($command == 'consolidate') //consolidate menu
  ibank_consolidate();
elseif ($command == 'consolidate2') //consolidate compute
  ibank_consolidate2();
elseif ($command == 'consolidate3') //consolidate operation
  ibank_consolidate3();
else
{
  echo "
  <tr>
    <td width='25%' valign='bottom' align='left'><a href=\"main.php\">$l_igb_quit</a></td>
    <td width='50%' style='text-align:left;'>
  <pre style='text-align:left;' class='term'>
  IIIIIIIIII          GGGGGGGGGGGGG    BBBBBBBBBBBBBBBBB
  I::::::::I       GGG::::::::::::G    B::::::::::::::::B
  I::::::::I     GG:::::::::::::::G    B::::::BBBBBB:::::B
  II::::::II    G:::::GGGGGGGG::::G    BB:::::B     B:::::B
    I::::I     G:::::G       GGGGGG      B::::B     B:::::B
    I::::I    G:::::G                    B::::B     B:::::B
    I::::I    G:::::G                    B::::BBBBBB:::::B
    I::::I    G:::::G    GGGGGGGGGG      B:::::::::::::BB
    I::::I    G:::::G    G::::::::G      B::::BBBBBB:::::B
    I::::I    G:::::G    GGGGG::::G      B::::B     B:::::B
    I::::I    G:::::G        G::::G      B::::B     B:::::B
    I::::I     G:::::G       G::::G      B::::B     B:::::B
  II::::::II    G:::::GGGGGGGG::::G    BB:::::BBBBBB::::::B
  I::::::::I     GG:::::::::::::::G    B:::::::::::::::::B
  I::::::::I       GGG::::::GGG:::G    B::::::::::::::::B
  IIIIIIIIII          GGGGGG   GGGG    BBBBBBBBBBBBBBBBB
  </pre>
  <center class='term'>
  ";
  echo $l_igb_title;
  echo "(tm)<br>";
  echo $l_igb_humor;
  echo "<br>&nbsp;
  </center>
  </td>
  <td width='25%' valign='bottom' align='right'><a href=\"igb.php?command=login\">$l_igb_login</a></td>
  ";
}

?>

</table>
</td></tr>
</table>
</div>
<img src="images/div2.png" alt="" style="width: 600px; height:21px">
</center>

<?php
include 'footer.php';

function IGB_login()
{
  global $playerinfo;
  global $account;
  global $l_igb_welcometoigb, $l_igb_accountholder, $l_igb_back, $l_igb_logout;
  global $l_igb_igbaccount, $l_igb_shipaccount, $l_igb_withdraw, $l_igb_transfer;
  global $l_igb_deposit, $l_igb_credit_symbol, $l_igb_operations, $l_igb_loans;

  echo "<tr><td colspan=2 align=center valign=top>$l_igb_welcometoigb<br>---------------------------------</td></tr>" .
       "<tr valign=top>" .
       "<td width=150 align=right>$l_igb_accountholder :<br><br>$l_igb_shipaccount :<br>$l_igb_igbaccount&nbsp;&nbsp;:</td>" .
       "<td style='max-width:550px; padding-right:4px;' align=right>$playerinfo[character_name]&nbsp;&nbsp;<br><br>".NUMBER($playerinfo['credits']) . " $l_igb_credit_symbol<br>" . NUMBER($account['balance']) . " $l_igb_credit_symbol<br></td>" .
       "</tr>" .
       "<tr><td colspan=2 align=center>$l_igb_operations<br>---------------------------------<br><br><a href=\"igb.php?command=withdraw\">$l_igb_withdraw</a><br><a href=\"igb.php?command=deposit\">$l_igb_deposit</a><br><a href=\"igb.php?command=transfer\">$l_igb_transfer</a><br><a href=\"igb.php?command=loans\">$l_igb_loans</a><br>&nbsp;</td></tr>" .
       "<tr valign=bottom>" .
       "<td align='left'><a href='igb.php'>$l_igb_back</a></td><td align=right>&nbsp;<br><a href=\"main.php\">$l_igb_logout</a></td>" .
       "</tr>";
}

function IGB_withdraw()
{
  global $playerinfo;
  global $account;
  global $l_igb_withdrawfunds, $l_igb_fundsavailable, $l_igb_selwithdrawamount;
  global $l_igb_withdraw, $l_igb_back, $l_igb_logout;

  echo "<tr><td colspan=2 align=center valign=top>$l_igb_withdrawfunds<br>---------------------------------</td></tr>" .
       "<tr valign=top>" .
       "<td>$l_igb_fundsavailable :</td>" .
       "<td align=right>" . NUMBER($account['balance']) ." C<br></td>" .
       "</tr><tr valign=top>" .
       "<td>$l_igb_selwithdrawamount :</td><td align=right>" .
       "<form action='igb.php?command=withdraw2' method=POST>" .
       "<input class=term type=text size=15 maxlength=20 name=amount value=0>" .
       "<br><br><input class=term type=submit value=$l_igb_withdraw>" .
       "</form></td></tr>" .
       "<tr valign=bottom>" .
       "<td><a href='igb.php?command=login'>$l_igb_back</a></td><td align=right>&nbsp;<br><a href=\"main.php\">$l_igb_logout</a></td>" .
       "</tr>";

}

function IGB_deposit()
{
  global $playerinfo;
  global $account;
  global $l_igb_depositfunds, $l_igb_fundsavailable, $l_igb_seldepositamount;
  global $l_igb_deposit, $l_igb_back, $l_igb_logout;

  $max_credits_allowed = 18446744073709000000;
  $credit_space = ($max_credits_allowed - $account['balance']);

  if ($credit_space > $playerinfo['credits'])
  {
    $credit_space = ($playerinfo['credits']);
  }

  if ($credit_space <0)
  $credit_space = 0;

  echo "<tr><td height=53 colspan=2 align=center valign=top>$l_igb_depositfunds<br>---------------------------------</td></tr>" .
       "<tr valign=top>" .
       "<td height=30>$l_igb_fundsavailable :</td>" .
       "<td align=right>" . NUMBER($playerinfo['credits']) ." C<br></td>" .
       "</tr><tr valign=top>" .
       "<td height=90>$l_igb_seldepositamount :</td><td align=right>" .
       "<form action='igb.php?command=deposit2' method=POST>" .
       "<input class=term type=text size=15 maxlength=20 name=amount value=0>" .
       "<br><br><input class=term type=submit value=$l_igb_deposit>" .
       "</form>" .
       "</td></tr>" .
       "<tr>" .
       "  <td height=30  colspan=2 align=left>" .
       "    <span style='color:\"#00ff00\";'>You can deposit only ". NUMBER($credit_space)." credits.</span><br>" .
       "  </td>" .
       "</tr>" .
       "<tr valign=bottom>" .
       "<td><a href='igb.php?command=login'>$l_igb_back</a></td><td align=right>&nbsp;<br><a href=\"main.php\">$l_igb_logout</a></td>" .
       "</tr>";

}

function IGB_transfer()
{
  global $db, $playerinfo, $account;
  global $ibank_min_turns;
  global $l_igb_transfertype, $l_igb_toanothership, $l_igb_shiptransfer, $l_igb_fromplanet, $l_igb_source, $l_ibank_consolidate;
  global $l_igb_unnamed, $l_igb_in, $l_igb_none, $l_igb_planettransfer, $l_igb_back, $l_igb_logout, $l_igb_destination, $l_igb_conspl;

  echo "SELECT character_name, ship_id FROM {$db->prefix}ships WHERE email not like '%@xenobe' AND ship_destroyed ='N' AND turns_used > $ibank_min_turns ORDER BY character_name ASC";
  $res = $db->Execute("SELECT character_name, ship_id FROM {$db->prefix}ships WHERE email not like '%@xenobe' AND ship_destroyed ='N' AND turns_used > $ibank_min_turns ORDER BY character_name ASC");
  var_dump($res);
  db_op_result ($db, $res, __LINE__, __FILE__);
  while (!$res->EOF)
  {
    $ships[]=$res->fields;
    $res->MoveNext();
  }

  echo "Madeit here";
  $res = $db->Execute("SELECT name, planet_id, sector_id FROM {$db->prefix}planets WHERE owner=$playerinfo[ship_id] ORDER BY sector_id ASC");
  db_op_result ($db, $res, __LINE__, __FILE__);
  while (!$res->EOF)
  {
    $planets[]=$res->fields;
    $res->MoveNext();
  }

  echo "<tr><td colspan=2 align=center valign=top>$l_igb_transfertype<br>---------------------------------</td></tr>" .
       "<tr valign=top>" .
       "<form action='igb.php?command=transfer2' method=POST>" .
       "<td>$l_igb_toanothership :<br><br>" .
       "<select class=term name=ship_id style='width:200px;'>";

  foreach ($ships as $ship)
  {
    echo "<option value=$ship[ship_id]>$ship[character_name]</option>";
  }

  echo "</select></td><td valign=center align=right>" .
       "<input class=term type=submit name=shipt value=\" $l_igb_shiptransfer \">" .
       "</form>" .
       "</td></tr>" .
       "<tr valign=top>" .
       "<td><br>$l_igb_fromplanet :<br><br>" .
       "<form action='igb.php?command=transfer2' method=POST>" .
       "$l_igb_source&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<select class=term name=splanet_id>";

  if (isset($planets))
  {
    foreach ($planets as $planet)
    {
      if (empty($planet['name']))
        $planet['name'] = $l_igb_unnamed;
      echo "<option value=$planet[planet_id]>$planet[name] $l_igb_in $planet[sector_id]</option>";
    }
  }
  else
  {
     echo "<option value=none>$l_igb_none</option>";
  }

  echo "</select><br>$l_igb_destination <select class=term name=dplanet_id>";

  if (isset($planets))
  {
    foreach ($planets as $planet)
    {
      if (empty($planet['name']))
        $planet['name'] = $l_igb_unnamed;
      echo "<option value=$planet[planet_id]>$planet[name] $l_igb_in $planet[sector_id]</option>";
    }
  }
  else
  {
     echo "<option value=none>$l_igb_none</option>";
  }

  echo "</select></td><td valign=center align=right>" .
       "<br><input class=term type=submit name=planett value=\"$l_igb_planettransfer\">" .
       "</td></tr>" .
       "</form>";

// ---- begin Consol Credits form    // ---- added by Torr
  echo "<tr valign=top>" .
       "<td><br>$l_igb_conspl :<br><br>" .
       "<form action='igb.php?command=consolidate' method=POST>" .
       "$l_igb_destination <select class=term name=dplanet_id>";

  if (isset($planets))
  {
    foreach ($planets as $planet)
    {
      if (empty($planet['name']))
        $planet['name'] = $l_igb_unnamed;
      echo "<option value=$planet[planet_id]>$planet[name] $l_igb_in $planet[sector_id]</option>";
    }
  }
  else
  {
     echo "<option value=none>$l_igb_none</option>";
  }

  echo "</select></td><td valign=top align=right>" .
       "<br><input class=term type=submit name=planetc value=\"  $l_ibank_consolidate  \">" .
       "</td></tr>" .
       "</form>";
// ---- End Consol Credits form ---

  echo "</form><tr valign=bottom>" .
       "<td><a href='igb.php?command=login'>$l_igb_back</a></td><td align=right>&nbsp;<br><a href=\"main.php\">$l_igb_logout</a></td>" .
       "</tr>";
}

function IGB_transfer2()
{
  global $playerinfo;
  global $account;
  global $ship_id;
  global $splanet_id;
  global $dplanet_id;
  global $ibank_min_turns;
  global $ibank_svalue;
  global $ibank_paymentfee;
  global $ibank_trate;
  global $l_igb_sendyourself, $l_igb_unknowntargetship, $l_ibank_min_turns, $l_ibank_min_turns2;
  global $l_igb_mustwait, $l_igb_shiptransfer, $l_igb_igbaccount, $l_igb_maxtransfer;
  global $l_igb_unlimited, $l_igb_maxtransferpercent, $l_igb_transferrate, $l_igb_recipient;
  global $l_igb_seltransferamount, $l_igb_transfer, $l_igb_back, $l_igb_logout, $l_igb_in;
  global $l_igb_errplanetsrcanddest, $l_igb_errunknownplanet, $l_igb_unnamed;
  global $l_igb_errnotyourplanet, $l_igb_planettransfer, $l_igb_srcplanet, $l_igb_destplanet;
  global $l_igb_transferrate2, $l_igb_seltransferamount, $l_igb_errnobase;
  global $db, $db_logging;

  if (isset($ship_id)) //ship transfer
  {
    $res = $db->Execute("SELECT * FROM {$db->prefix}ships WHERE ship_id=$ship_id AND ship_destroyed ='N' AND turns_used > $ibank_min_turns;");
    db_op_result ($db, $res, __LINE__, __FILE__, $db_logging);

    if ($playerinfo['ship_id'] == $ship_id)
      ibank_error($l_igb_sendyourself, "igb.php?command=transfer");

    if (!$res instanceof ADORecordSet || $res->EOF)
      ibank_error($l_igb_unknowntargetship, "igb.php?command=transfer");

    $target = $res->fields;

    if ($target['turns_used'] < $ibank_min_turns)
    {
      $l_ibank_min_turns = str_replace("[ibank_min_turns]", $ibank_min_turns, $l_ibank_min_turns);
      $l_ibank_min_turns = str_replace("[igb_target_char_name]", $target['character_name'], $l_ibank_min_turns);
      ibank_error($l_ibank_min_turns, "igb.php?command=transfer");
    }

    if ($playerinfo['turns_used'] < $ibank_min_turns)
    {
      $l_ibank_min_turns2 = str_replace("[ibank_min_turns]", $ibank_min_turns, $l_ibank_min_turns2);
      ibank_error($l_ibank_min_turns2, "igb.php?command=transfer");
    }

    if ($ibank_trate > 0)
    {
      $curtime = time();
      $curtime -= $ibank_trate * 60;
      $res = $db->Execute("SELECT UNIX_TIMESTAMP(time) as time FROM {$db->prefix}IGB_transfers WHERE UNIX_TIMESTAMP(time) > $curtime AND source_id=$playerinfo[ship_id] AND dest_id=$target[ship_id]");
      db_op_result ($db, $res, __LINE__, __FILE__, $db_logging);
      if (!$res->EOF)
      {
        $time = $res->fields;
        $difftime = ($time['time'] - $curtime) / 60;
        $l_igb_mustwait = str_replace("[igb_target_char_name]", $target['character_name'], $l_igb_mustwait);
        $l_igb_mustwait = str_replace("[ibank_trate]", NUMBER($ibank_trate), $l_igb_mustwait);
        $l_igb_mustwait = str_replace("[igb_difftime]", NUMBER($difftime), $l_igb_mustwait);
        ibank_error($l_igb_mustwait, "igb.php?command=transfer");
      }
    }

    echo "<tr><td colspan=2 align=center valign=top>$l_igb_shiptransfer<br>---------------------------------</td></tr>" .
         "<tr valign=top><td>$l_igb_igbaccount :</td><td align=right>" . NUMBER($account['balance']) . " C</td></tr>";

    if ($ibank_svalue == 0)
      echo "<tr valign=top><td>$l_igb_maxtransfer :</td><td align=right>$l_igb_unlimited</td></tr>";
    else
    {
      $percent = $ibank_svalue * 100;
      $score = gen_score($playerinfo['ship_id']);
      $maxtrans = $score * $score * $ibank_svalue;

      $l_igb_maxtransferpercent = str_replace("[igb_percent]", $percent, $l_igb_maxtransferpercent);
      echo "<tr valign=top><td nowrap>$l_igb_maxtransferpercent :</td><td align=right>" . NUMBER($maxtrans) . " C</td></tr>";
    }

    $percent = $ibank_paymentfee * 100;

    $l_igb_transferrate = str_replace("[igb_num_percent]", NUMBER($percent,1), $l_igb_transferrate);
    echo "<tr valign=top><td>$l_igb_recipient :</td><td align=right>$target[character_name]&nbsp;&nbsp;</td></tr>" .
         "<form action='igb.php?command=transfer3' method=POST>" .
         "<tr valign=top>" .
         "<td><br>$l_igb_seltransferamount :</td>" .
         "<td align=right><br><input class=term type=text size=15 maxlength=20 name=amount value=0><br>" .
         "<br><input class=term type=submit value=$l_igb_transfer></td>" .
         "<input type=hidden name=ship_id value=$ship_id>" .
         "</form>" .
         "<tr><td colspan=2 align=center>" .
         "$l_igb_transferrate" .
         "<tr valign=bottom>" .
         "<td><a href='igb.php?command=transfer'>$l_igb_back</a></td><td align=right>&nbsp;<br><a href=\"main.php\">$l_igb_logout</a></td>" .
         "</tr>";
  }
  else
  {
    if ($splanet_id == $dplanet_id)
      ibank_error($l_igb_errplanetsrcanddest, "igb.php?command=transfer");

    $res = $db->Execute("SELECT name, credits, owner, sector_id FROM {$db->prefix}planets WHERE planet_id=$splanet_id");
    db_op_result ($db, $res, __LINE__, __FILE__, $db_logging);
    if (!$res || $res->EOF)
      ibank_error($l_igb_errunknownplanet, "igb.php?command=transfer");
    $source = $res->fields;

    if (empty($source['name']))
      $source['name']=$l_igb_unnamed;

    $res = $db->Execute("SELECT name, credits, owner, sector_id, base FROM {$db->prefix}planets WHERE planet_id=$dplanet_id");
    db_op_result ($db, $res, __LINE__, __FILE__, $db_logging);
    if (!$res || $res->EOF)
      ibank_error($l_igb_errunknownplanet, "igb.php?command=transfer");
    $dest = $res->fields;

    if (empty($dest['name']))
      $dest['name']=$l_igb_unnamed;
    if ($dest['base'] == 'N')
      ibank_error($l_igb_errnobase, "igb.php?command=transfer");

    if ($source['owner'] != $playerinfo['ship_id'] || $dest['owner'] != $playerinfo['ship_id'])
      ibank_error($l_igb_errnotyourplanet, "igb.php?command=transfer");

    $percent = $ibank_paymentfee * 100;

    $l_igb_transferrate2 = str_replace("[igb_num_percent]", NUMBER($percent,1), $l_igb_transferrate2);
    echo "<tr><td colspan=2 align=center valign=top>$l_igb_planettransfer<br>---------------------------------</td></tr>" .
         "<tr valign=top>" .
         "<td>$l_igb_srcplanet $source[name] $l_igb_in $source[sector_id] :" .
         "<td align=right>" . NUMBER($source['credits']) . " C" .
         "<tr valign=top>" .
         "<td>$l_igb_destplanet $dest[name] $l_igb_in $dest[sector_id] :" .
         "<td align=right>" . NUMBER($dest['credits']) . " C" .
         "<form action='igb.php?command=transfer3' method=POST>" .
         "<tr valign=top>" .
         "<td><br>$l_igb_seltransferamount :</td>" .
         "<td align=right><br><input class=term type=text size=15 maxlength=20 name=amount value=0><br>" .
         "<br><input class=term type=submit value=$l_igb_transfer></td>" .
         "<input type=hidden name=splanet_id value=$splanet_id>" .
         "<input type=hidden name=dplanet_id value=$dplanet_id>" .
         "</form>" .
         "<tr><td colspan=2 align=center>" .
         "$l_igb_transferrate2" .
         "<tr valign=bottom>" .
         "<td><a href='igb.php?command=transfer'>$l_igb_back</a></td><td align=right>&nbsp;<br><a href=\"main.php\">$l_igb_logout</a></td>" .
         "</tr>";
  }

}

function StripNonNum($str)
{
    $str = (string) $str;
    $output = preg_replace("/[^0-9]/", "", $str);

  return $output;
}
?>
