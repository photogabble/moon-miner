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
// File: bounty.php

include("config.php");
updatecookie();
include("languages/$lang");
$title=$l_by_title;
include("header.php");

if (checklogin())
{
    die();
}

if (!isset($_GET['response']))
{
    $_GET['response'] = '';
}
$response = $_GET['response'];

$res = $db->Execute("SELECT * FROM $dbtables[ships] WHERE email='$username'");
$playerinfo = $res->fields;

switch ($response) {
   case "display":
      bigtitle();
      $res5 = $db->Execute("SELECT * FROM $dbtables[ships],$dbtables[bounty] WHERE bounty_on = ship_id AND bounty_on = $bounty_on");
      $j = 0;
      if ($res5)
      {
         while (!$res5->EOF)
         {
            $bounty_details[$j] = $res5->fields;
            $j++;
            $res5->MoveNext();
         }
      }

      $num_details = $j;
      if ($num_details < 1)
      {
         echo "$l_by_nobounties<BR>";
      }
      else
      {
         echo "$l_by_bountyon " . $bounty_details[0][character_name];
         echo '<table border=1 cellspacing=1 cellpadding=2 width="50%" align=center>';
         echo "<TR BGCOLOR=\"$color_header\">";
         echo "<TD><B>$l_amount</TD>";
         echo "<TD><B>$l_by_placedby</TD>";
         echo "<TD><B>$l_by_action</TD>";
         echo "</TR>";
         $color = $color_line1;
         for ($j = 0; $j < $num_details; $j++)
         {
            $someres = $db->execute("SELECT character_name FROM $dbtables[ships] WHERE ship_id = " . $bounty_details[$j][placed_by]);
            $details = $someres->fields;
            echo "<TR BGCOLOR=\"$color\">";
            echo "<TD>" . $bounty_details[$j]['amount'] . "</TD>";
            if ($bounty_details[$j][placed_by] == 0)
            {
               echo "<TD>$l_by_thefeds</TD>";
            }
            else
            {
               echo "<TD>" . $details['character_name'] . "</TD>";
            }
            if ($bounty_details[$j][placed_by] == $playerinfo[ship_id])
            {
               echo "<TD><A HREF=bounty.php?bid=" . $bounty_details[$j][bounty_id] . "&response=cancel>$l_by_cancel</A></TD>";
            }
            else
            {
               echo "<TD>n/a</TD>";
            }

            echo "</TR>";

            if ($color == $color_line1)
            {
               $color = $color_line2;
            }
            else
            {
               $color = $color_line1;
            }
         }
         echo "</TABLE>";
      }
      break;
   case "cancel":
      $bid = (int)$_GET['bid'];

      bigtitle();
      if ($playerinfo[turns]<1 )
      {
    echo "$l_by_noturn<BR><BR>";
    TEXT_GOTOMAIN();
    include("footer.php");
    die();
      }

      $res = $db->Execute("SELECT * from $dbtables[bounty] WHERE bounty_id = $bid");
      if (!res || $res->RowCount() ==0)
      {
          echo "$l_by_nobounty<BR><BR>";
    TEXT_GOTOMAIN();
    include("footer.php");
    die();
      }
      $bty = $res->fields;
      if ($bty[placed_by] <> $playerinfo[ship_id])
      {
          echo "$l_by_notyours<BR><BR>";
    TEXT_GOTOMAIN();
    include("footer.php");
    die();
      }
      $del = $db->Execute("DELETE FROM $dbtables[bounty] WHERE bounty_id = $bid");
      $stamp = date("Y-m-d H-i-s");
      $refund = $bty['amount'];
      $db->Execute("UPDATE $dbtables[ships] SET last_login='$stamp',turns=turns-1, turns_used=turns_used+1, credits=credits+$refund where ship_id=$playerinfo[ship_id]");
      echo "$l_by_canceled<BR>";
      TEXT_GOTOMAIN();
      die();
      break;
   case "place":
      bigtitle();
      $bounty_on = stripnum($bounty_on);
      $ex = $db->Execute("SELECT * from $dbtables[ships] WHERE ship_id = $bounty_on");
      if (!$ex)
      {
    echo "$l_by_notexists<BR><BR>";
    TEXT_GOTOMAIN();
    include("footer.php");
    die();
      }
      $bty = $ex->fields;
      if ($bty[ship_destroyed] == "Y")
      {
    echo "$l_by_destroyed<BR><BR>";
    TEXT_GOTOMAIN();
    include("footer.php");
    die();
      }
      if ($playerinfo[turns]<1 )
      {
    echo "$l_by_noturn<BR><BR>";
    TEXT_GOTOMAIN();
    include("footer.php");
    die();
      }
      $amount = stripnum($amount);
      if ($amount < 0)
      {
         echo "$l_by_zeroamount<BR><BR>";
         TEXT_GOTOMAIN();
         include("footer.php");
         die();
      }
      if ($bounty_on == $playerinfo['ship_id'])
      {
         echo "$l_by_yourself<BR><BR>";
         TEXT_GOTOMAIN();
         include("footer.php");
         die();
      }
      if ($amount > $playerinfo['credits'])
      {
         echo "$l_by_notenough<BR><BR>";
         TEXT_GOTOMAIN();
         include("footer.php");
         die();
      }
      if ($bounty_maxvalue != 0)
      {
         $percent = $bounty_maxvalue * 100;
         $score = gen_score($playerinfo[ship_id]);
         $maxtrans = $score * $score * $bounty_maxvalue;
         $previous_bounty = 0;
         $pb = $db->Execute("SELECT SUM(amount) AS totalbounty FROM $dbtables[ships] WHERE bounty_on = $bounty_on AND placed_by = $playerinfo[ship_id]");
         if ($pb)
         {
            $prev = $pb->fields;
            $previous_bounty = $prev[totalbounty];
         }
         if ($amount + previous_bounty > $maxtrans)
         {
            $l_by_toomuch=str_replace("[percent]",$percent,$l_by_toomuch);
            echo "$l_by_toomuch<BR><BR>";
            TEXT_GOTOMAIN();
            include("footer.php");
            die();
         }

      }
      $insert = $db->Execute("INSERT INTO $dbtables[bounty] (bounty_on,placed_by,amount) values ($bounty_on, $playerinfo[ship_id] ,$amount)");
      $stamp = date("Y-m-d H-i-s");
      $db->Execute("UPDATE $dbtables[ships] SET last_login='$stamp',turns=turns-1, turns_used=turns_used+1, credits=credits-$amount where ship_id=$playerinfo[ship_id]");
      echo "$l_by_placed<BR>";
      TEXT_GOTOMAIN();
      die();
      break;
   default:
      bigtitle();
      $res = $db->Execute("SELECT * FROM $dbtables[ships] WHERE ship_destroyed = 'N' AND ship_id <> $playerinfo[ship_id] ORDER BY character_name ASC");
      echo "<FORM ACTION=bounty.php METHOD=POST>";
      echo "<TABLE>";
      echo "<TR><TD>$l_by_bountyon</TD><TD><SELECT NAME=bounty_on>";
      while (!$res->EOF)
      {
         if (isset($bounty_on) && $bounty_on == $res->fields[ship_id])
            $selected = "selected";
         else
            $selected = "";
         $charname = $res->fields[character_name];
         $ship_id = $res->fields[ship_id];
         echo "<OPTION VALUE=$ship_id $selected>$charname</OPTION>";
         $res->MoveNext();
      }
      echo "</SELECT></TD></TR>";
      echo "<TR><TD>$l_by_amount:</TD><TD><INPUT TYPE=TEXT NAME=amount SIZE=20 MAXLENGTH=20></TD></TR>";
      echo "<TR><TD></TD><TD><INPUT TYPE=SUBMIT VALUE=$l_by_place><INPUT TYPE=RESET VALUE=Clear></TD>";
      echo "</TABLE>";
      echo "<input type=hidden name=response value=place>";
      echo "</FORM>";


      $result3 = $db->Execute ("SELECT bounty_on, SUM(amount) as total_bounty FROM $dbtables[bounty] GROUP BY bounty_on");

      $i = 0;
      if ($result3)
      {
         while (!$result3->EOF)
         {
            $bounties[$i] = $result3->fields;
            $i++;
            $result3->MoveNext();
         }
      }

      $num_bounties = $i;
      if ($num_bounties < 1)
      {
         echo "$l_by_nobounties<BR>";
      }
      else
      {
         echo $l_by_moredetails . "<BR><BR>";
         echo "<TABLE WIDTH=\"100%\" BORDER=0 CELLSPACING=0 CELLPADDING=2>";
         echo "<TR BGCOLOR=\"$color_header\">";
         echo "<TD><B>$l_by_bountyon</B></TD>";
         echo "<TD><B>$l_amount</TD>";
         echo "</TR>";
         $color = $color_line1;
         for ($i = 0; $i < $num_bounties; $i++)
         {
            $someres = $db->execute("SELECT character_name FROM $dbtables[ships] WHERE ship_id = " . $bounties[$i][bounty_on]);
            $details = $someres->fields;
            echo "<TR BGCOLOR=\"$color\">";
            echo "<TD><A HREF=bounty.php?bounty_on=" . $bounties[$i][bounty_on] . "&response=display>". $details[character_name] ."</A></TD>";
            echo "<TD>" . $bounties[$i][total_bounty] . "</TD>";
            echo "</TR>";

            if ($color == $color_line1)
            {
               $color = $color_line2;
            }
            else
            {
               $color = $color_line1;
            }
        }
        echo "</TABLE>";
    }

    echo "<BR><BR>";

    break;
}

TEXT_GOTOMAIN();
include("footer.php");
?>
