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
// File: readmail.php

include "config.php";
updatecookie();

include "languages/$lang";
$title = $l_readm_title;
include "header.php";

bigtitle();

if (checklogin())
{
    die();
}

$res = $db->Execute("SELECT * FROM $dbtables[ships] WHERE email='$username'");
$playerinfo = $res->fields;

if (!isset($_GET['action']))
{
    $_GET['action'] = '';
}

if ($_GET['action']=="delete")
{
    $db->Execute("DELETE FROM $dbtables[messages] WHERE ID='".$ID."' AND recp_id='".$playerinfo[ship_id]."'");
}
else if ($_GET['action']=="delete_all")
{
    $db->Execute("DELETE FROM $dbtables[messages] WHERE recp_id='".$playerinfo[ship_id]."'");
}

$cur_D = date("Y-m-d");
$cur_T = date("H:i:s");

$res = $db->Execute("SELECT * FROM $dbtables[messages] WHERE recp_id='".$playerinfo['ship_id']."' ORDER BY sent DESC");
?>
<div align="center">
  <table border="0" cellspacing="0" width="70%" bgcolor="silver" cellpadding="0">
    <tr>
      <td width="100%">
        <div align="center">
          <center>
          <table border="0" cellspacing="1" width="100%">
            <tr>
              <td width="100%" bgcolor="black">
                <div align="center">
                  <table border="1" cellspacing="1" width="100%" bgcolor="gray" bordercolorlight="black" bordercolordark="silver">
                    <tr>
                      <td width="75%" align="left"><font color="white" size="2"><b><? echo $l_readm_center ?> (<span style='color:#00C0C0;'>Subspace</span>)</b></font></td>
                      <td width="21%" align="center" nowrap><font color="white" size="2"><?echo "$cur_D" ?>&nbsp;<?echo "$cur_T" ?></font></td>
                      <td width="4%" align="center" bordercolorlight="black" bordercolordark="gray"><a href="main.php"><img alt="Click here to return to the main menu" src="images/close.png" width="16" height="14" border="0"></a></td>
                    </tr>
                  </table>
                </div>
              </td>
            </tr>

<?php
 if ($res->EOF)
 {
//  echo "$l_readm_nomessage";
?>
            <tr>
              <td width="100%" bgcolor="black" bordercolorlight="black" bordercolordark="silver">
                <div align="center">
                  <table border="1" cellspacing="1" width="100%" bgcolor="white" bordercolorlight="black" bordercolordark="silver">
                    <tr>
                      <td width="100%" align="center" bgcolor="white"><font color="red"><? echo $l_readm_nomessage ?></font></td>
                    </tr>
                  </table>
                </div>
              </td>
            </tr>
<?php
 }
 else
 {
  $line_counter = true;
  while (!$res->EOF)
  {
   $msg = $res->fields;
   $result = $db->Execute("SELECT * FROM $dbtables[ships] WHERE ship_id='".$msg[sender_id]."'");
   $sender = $result->fields;
   $isAdmin = isAdmin($sender);
?>
            <tr>
              <td width="100%" align="center" bgcolor="black" height="4"></td>
            </tr>
            <tr>
              <td width="100%" bgcolor="black" bordercolorlight="black" bordercolordark="silver">
                <div align="center">
                  <table border="0" cellspacing="1" width="100%" bgcolor="gray" cellpadding="0">
                    <tr>
                      <td width="20%" style="text-align:left;"><font color="white" size="2"><b><? echo $l_readm_sender; ?></b></td>
                      <td width="55%" style="text-align:left;"><font color="yellow" size="2">
<?php
echo "<span style='vertical-align:middle;'>{$sender['character_name']}</span>";
if ($isAdmin === true)
{
    echo "&nbsp;<img style='border:none; padding:0px; vertical-align:text-bottom;' src='images/validated_administrator2.gif' alt='Validated as Admin' />";
}
?>
</font></td>
                      <td width="21%" align="center"><font color="white" size="2"><? echo "$msg[sent]" ?></font></td>
                      <td width="4%" align="center" bordercolorlight="black" bordercolordark="gray"><a class="but" href="readmail.php?action=delete&ID=<? echo $msg[ID]; ?>"><img src="images/close.png" width="16" height="14" border="0"></a></td>
                    </tr>
                  </table>
                </div>
              </td>
            </tr>
            <tr>
              <td width="100%" bgcolor="black" bordercolorlight="black" bordercolordark="silver">
                <div align="center">
                  <table border="0" cellspacing="1" width="100%" bgcolor="gray" cellpadding="0">
                    <tr>
                      <td width="20%" style="text-align:left;"><font color="white" size="2"><b><? echo $l_readm_captn ?></b></font></td>
                      <td width="80%" style="text-align:left;"><font color="yellow" size="2"><? echo $sender[ship_name] ?></font></td>
                    </tr>
                  </table>
                </div>
              </td>
            </tr>
            <tr>
              <td width="100%" bgcolor="black" bordercolorlight="black" bordercolordark="silver">
                <div align="center">
                  <table border="0" cellspacing="1" width="100%" bgcolor="gray" cellpadding="0">
                    <tr>
                      <td width="20%" style="text-align:left;"><font color="white" size="2"><b>Subject</b></font></td>
                      <td width="80%" style="text-align:left;"><b><font color="yellow" size="2"><? echo $msg[subject]; ?></font></b></td>
                    </tr>
                  </table>
                </div>
              </td>
            </tr>
            <tr>
              <td width="100%" bgcolor="black" bordercolorlight="black" bordercolordark="silver">
                <div align="center">
                  <table border="1" cellspacing="1" width="100%" bgcolor="white" bordercolorlight="black" bordercolordark="silver">
                    <tr>
                      <td width="100%" style="text-align:left; vertical-align:text-top;"><font color="black" size="2"><? echo nl2br($msg[message]); ?></font></td>
                    </tr>
                  </table>
                </div>
              </td>
            </tr>
            <tr>
              <td width="100%" align="center" bgcolor="black" bordercolorlight="black" bordercolordark="silver">
                <div align="center">
                  <table border="1" cellspacing="1" width="100%" bgcolor="gray" bordercolorlight="black" bordercolordark="silver" cellpadding="0">
                    <tr>
                      <td width="100%" align="center" valign="middle"><a class="but" href="readmail.php?action=delete&ID=<? echo $msg[ID]; ?>"><? echo $l_readm_del ?></A> |
        <a class="but" href="mailto2.php?name=<? echo $sender[character_name]; ?>&subject=<? echo $msg[subject] ?>"><? echo $l_readm_repl ?></A>
                      </td>
                    </tr>
                  </table>
                </div>
              </td>
            </tr>
<?php
    $res->MoveNext();
  }
}
?>
            <tr>
              <td width="100%" align="center" bgcolor="black" height="4"></td>
            </tr>
            <tr>
              <td width="100%" align="center" bgcolor="#000" height="4">
                <div align="center">
                  <table border="1" cellspacing="1" width="100%" bgcolor="#808080" bordercolorlight="#000" bordercolordark="#C0C0C0" height="8">
                    <tr>
                      <td width="50%"><p align="left"><font color="#fff" size="2">Mail Reader </font></td>
                      <td width="50%"><p align="right"><font color="#fff" size="2"><a class="but" href="readmail.php?action=delete_all">Delete All</a></font></td>
                    </tr>
                  </table>
                </div>
              </td>
            </tr>
          </table>
          </center>
        </div>
      </td>
    </tr>
  </table>
</div>
<br>
<?php
 //}

TEXT_GOTOMAIN();

include "footer.php";
?>
