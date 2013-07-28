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

include './global_includes.php';
include './includes/ibank_error.php';

BntLogin::checkLogin ($db, $lang, $langvars, $bntreg);

// Database driven language entries
$langvars = BntTranslate::load ($db, $lang, array ('igb', 'common', 'global_includes', 'global_funcs', 'footer', 'news'));

$title = $langvars['l_ibank_title'];
$body_class = 'igb';
include './header.php';

// Database driven language entries
$langvars = BntTranslate::load ($db, $lang, array ('igb', 'common', 'global_includes', 'global_funcs', 'footer', 'news'));

$result = $db->Execute ("SELECT * FROM {$db->prefix}ships WHERE email=?", array ($_SESSION['username']));
DbOp::dbResult ($db, $result, __LINE__, __FILE__);
$playerinfo = $result->fields;

$result = $db->Execute ("SELECT * FROM {$db->prefix}ibank_accounts WHERE ship_id = ?;", array ($playerinfo['ship_id']));
DbOp::dbResult ($db, $result, __LINE__, __FILE__);
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
{
    ibank_error ($langvars['l_ibank_malfunction'], "main.php");
}

if (!isset ($_REQUEST['command']))
{
    $_REQUEST['command'] = '';
    $command = '';
}
else
{
    $command = $_REQUEST['command'];
}

if ($command == 'login') //main menu
{
    include_once './includes/ibank_login.php';
    ibank_login ($langvars);
}
elseif ($command == 'withdraw') //withdraw menu
{
    include_once './includes/ibank_withdraw.php';
    ibank_withdraw ($langvars);
}
elseif ($command == 'withdraw2') //withdraw operation
{
    include_once './includes/ibank_withdraw2.php';
    ibank_withdraw2 ($db, $langvars);
}
elseif ($command == 'deposit') //deposit menu
{
    BntIbank::deposit ($db, $lang, $account, $playerinfo, $langvars, $local_number_thousands_sep, $local_number_dec_point);
}
elseif ($command == 'deposit2') //deposit operation
{
    include_once './includes/ibank_deposit2.php';
    ibank_deposit2 ($db);
}
elseif ($command == 'transfer') //main transfer menu
{
    include_once './includes/ibank_transfer.php';
    ibank_transfer ($db, $langvars);
}
elseif ($command == 'transfer2') //specific transfer menu (ship or planet)
{
    include_once './includes/ibank_transfer2.php';
    ibank_transfer2 ($db);
}
elseif ($command == 'transfer3') //transfer operation
{
    include_once './includes/ibank_transfer3.php';
    ibank_transfer3 ($db);
}
elseif ($command == 'loans') //loans menu
{
    include_once './includes/ibank_loans.php';
    ibank_loans ($db);
}
elseif ($command == 'borrow') //borrow operation
{
    include_once './includes/ibank_borrow.php';
    ibank_borrow ($db);
}
elseif ($command == 'repay') //repay operation
{
    include_once './includes/ibank_repay.php';
    ibank_repay ($db, $langvars);
}
elseif ($command == 'consolidate') //consolidate menu
{
    include_once './includes/ibank_consolidate.php';
    ibank_consolidate ();
}
elseif ($command == 'consolidate2') //consolidate compute
{
    include_once './includes/ibank_consolidate2.php';
    ibank_consolidate2 ($db);
}
elseif ($command == 'consolidate3') //consolidate operation
{
    include_once './includes/ibank_consolidate3.php';
    ibank_consolidate3 ($db);
}
else
{
  echo "
  <tr>
    <td width='25%' valign='bottom' align='left'><a href=\"main.php\">" . $langvars['l_ibank_quit'] . "</a></td>
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
  echo $langvars['l_ibank_title'];
  echo "(tm)<br>";
  echo $langvars['l_ibank_humor'];
  echo "<br>&nbsp;
  </center>
  </td>
  <td width='25%' valign='bottom' align='right'><a href=\"igb.php?command=login\">" . $langvars['l_ibank_login'] . "</a></td>
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
include './footer.php';
?>
