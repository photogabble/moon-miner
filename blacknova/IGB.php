<?

include("config.php3");
updatecookie();

include($gameroot . $default_lang);

$title=$l_igb_title;
$no_body = 1;
include("header.php3");

connectdb();
if (checklogin()) {die();}

$result = mysql_query("SELECT * FROM ships WHERE email='$username'");
$playerinfo = mysql_fetch_array($result);

$result = mysql_query("SELECT * FROM ibank_accounts WHERE ship_id=$playerinfo[ship_id]");
$account = mysql_fetch_array($result);

echo "<BODY bgcolor=#666666 text=\"#F0F0F0\" link=\"#00ff00\" vlink=\"#00ff00\" alink=\"#ff0000\">";
?>

<center>
<img src=images/div1.gif>
<table width=600 height=350 border=0>
<tr><td align=center background=images/IGBscreen.gif>
<table background="" width=520 height=300 border=0>

<?

if(!$allow_ibank)
  IGB_error($l_igb_malfunction, "main.php3");

if($command == 'login') //main menu
  IGB_login();
elseif($command == 'withdraw') //withdraw menu
  IGB_withdraw();
elseif($command == 'withdraw2') //withdraw operation
  IGB_withdraw2();
elseif($command == 'deposit') //deposit menu
  IGB_deposit();
elseif($command == 'deposit2') //deposit operation
  IGB_deposit2();
elseif($command == 'transfer') //main transfer menu
  IGB_transfer();
elseif($command == 'transfer2') //specific transfer menu (ship or planet)
  IGB_transfer2();
elseif($command == 'transfer3') //transfer operation
  IGB_transfer3();
else
{
  echo "
  <tr><td width=25% valign=bottom><a href=\"main.php3\"><font size=2 face=\"courier new\" color=#00FF00>$l_igb_quit</a></td><td width=50%>
  <font size=2 face=\"courier new\" color=#00FF00>
  <pre>
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
  <center>
  <p>
  The Intergalactic Bank (tm)<br>
  All your base are belong to us<br>&nbsp;
  </center></td>
  <td width=25% valign=bottom align=right><font size=2 color=#00FF00 face=\"courier new\"><a href=\"IGB.php?command=login\">$l_igb_login</a></td>
  ";
}

?>

</table>
</td></tr>
</table>
<img src=images/div2.gif>
</center>

<?
include("footer.php3");

function IGB_login()
{
  global $playerinfo;
  global $account;
  global $l_igb_welcometoigb, $l_igb_accountholder, $l_igb_back, $l_igb_logout;
  global $l_igb_igbaccount, $l_igb_shipaccount, $l_igb_withdraw, $l_igb_transfer;
  global $l_igb_deposit, $l_igb_operations;

  echo "<tr><td colspan=2 align=center valign=top><font size=2 face=\"courier new\" color=#00FF00>$l_igb_welcometoigb<br>---------------------------------</td></tr>" .
       "<tr valign=top>" .
       "<td><font size=2 face=\"courier new\" color=#00FF00>$l_igb_accountholder :<br><br>$l_igb_shipaccount :<br>$l_igb_igbaccount&nbsp;&nbsp;:</td>" .
       "<td align=right><font size=2 face=\"courier new\" color=#00FF00>$playerinfo[character_name]&nbsp;&nbsp;<br><br>" . NUMBER($playerinfo[credits]) . " C<br>" . NUMBER($account[balance]) . " C<br></td>" .
       "</tr>" .
       "<tr><td colspan=2 align=center><font size=2 face=\"courier new\" color=#00FF00>$l_igb_operations<br>---------------------------------<br><br><a href=\"IGB.php?command=withdraw\">$l_igb_withdraw</a><br><a href=\"IGB.php?command=deposit\">$l_igb_deposit</a><br><a href=\"IGB.php?command=transfer\">$l_igb_transfer</a><br>&nbsp;</td></tr>" .
       "<tr valign=bottom>" .
       "<td><font size=2 face=\"courier new\" color=#00FF00><a href=IGB.php>$l_igb_back</a></td><td align=right><font size=2 face=\"courier new\" color=#00FF00>&nbsp;<br><a href=\"main.php3\">$l_igb_logout</a></td>" .
       "</tr>";
}

function IGB_withdraw()
{
  global $playerinfo;
  global $account;
  global $l_igb_withdrawfunds, $l_igb_fundsavailable, $l_igb_selwithdrawamount;
  global $l_igb_withdraw, $l_igb_back, $l_igb_logout;

  echo "<tr><td colspan=2 align=center valign=top><font size=2 face=\"courier new\" color=#00FF00>$l_igb_withdrawfunds<br>---------------------------------</td></tr>" .
       "<tr valign=top>" .
       "<td><font size=2 face=\"courier new\" color=#00FF00>$l_igb_fundsavailable :</td>" .
       "<td align=right><font size=2 face=\"courier new\" color=#00FF00>" . NUMBER($account[balance]) ." C<br></td>" .
       "</tr><tr valign=top>" .
       "<td><font size=2 face=\"courier new\" color=#00FF00>$l_igb_selwithdrawamount :</td><td align=right>" .
       "<form action=IGB.php?command=withdraw2 method=POST>" .
       "<input style=\"background-color: #000000; color: #00FF00; font-family:Courier New; font-size:10pt\" type=text size=15 maxlength=20 name=amount value=0>" .
       "<br><br><input style=\"background-color: #000000; color: #00FF00; font-family:Courier New; font-size:10pt\" type=submit value=$l_igb_withdraw>" .
       "</form></td></tr>" .
       "<tr valign=bottom>" .
       "<td><font size=2 face=\"courier new\" color=#00FF00><a href=IGB.php?command=login>$l_igb_back</a></td><td align=right><font size=2 face=\"courier new\" color=#00FF00>&nbsp;<br><a href=\"main.php3\">$l_igb_logout</a></td>" .
       "</tr>";

}

function IGB_deposit()
{
  global $playerinfo;
  global $account;
  global $l_igb_depositfunds, $l_igb_fundsavailable, $l_igb_seldepositamount;
  global $l_igb_deposit, $l_igb_back, $l_igb_logout;

  echo "<tr><td colspan=2 align=center valign=top><font size=2 face=\"courier new\" color=#00FF00>$l_igb_depositfunds<br>---------------------------------</td></tr>" .
       "<tr valign=top>" .
       "<td><font size=2 face=\"courier new\" color=#00FF00>$l_igb_fundsavailable :</td>" .
       "<td align=right><font size=2 face=\"courier new\" color=#00FF00>" . NUMBER($playerinfo[credits]) ." C<br></td>" .
       "</tr><tr valign=top>" .
       "<td><font size=2 face=\"courier new\" color=#00FF00>$l_igb_seldepositamount :</td><td align=right>" .
       "<form action=IGB.php?command=deposit2 method=POST>" .
       "<input style=\"background-color: #000000; color: #00FF00; font-family:Courier New; font-size:10pt\" type=text size=15 maxlength=20 name=amount value=0>" .
       "<br><br><input style=\"background-color: #000000; color: #00FF00; font-family:Courier New; font-size:10pt\" type=submit value=$l_igb_deposit>" .
       "</form></td></tr>" .
       "<tr valign=bottom>" .
       "<td><font size=2 face=\"courier new\" color=#00FF00><a href=IGB.php?command=login>$l_igb_back</a></td><td align=right><font size=2 face=\"courier new\" color=#00FF00>&nbsp;<br><a href=\"main.php3\">$l_igb_logout</a></td>" .
       "</tr>";

}

function IGB_transfer()
{
  global $playerinfo;
  global $account;
  global $l_igb_transfertype, $l_igb_toanothership, $l_igb_shiptransfer, $l_igb_fromplanet, $l_igb_source;
  global $l_igb_unnamed, $l_igb_in, $l_igb_none, $l_igb_planettransfer, $l_igb_back, $l_igb_logout;

  $res = mysql_query("SELECT character_name, ship_id FROM ships ORDER BY character_name ASC");
  while($row = mysql_fetch_array($res))
  {
    $ships[]=$row;
  }

  $res = mysql_query("SELECT name, planet_id, sector_id FROM planets WHERE owner=$playerinfo[ship_id] ORDER BY sector_id ASC");
  while($row = mysql_fetch_array($res))
  {
    $planets[]=$row;
  }


  echo "<tr><td colspan=2 align=center valign=top><font size=2 face=\"courier new\" color=#00FF00>$l_igb_transfertype<br>---------------------------------</td></tr>" .
       "<tr valign=top>" .
       "<form action=IGB.php?command=transfer2 method=POST>" .
       "<td><font size=2 face=\"courier new\" color=#00FF00>$l_igb_toanothership :<br><br>" .
       "<select style=\"background-color: #000000; color: #00FF00; font-family:Courier New; font-size:10pt\" name=ship_id>";

  foreach($ships as $ship)
  {
    echo "<option value=$ship[ship_id]>$ship[character_name]</option>";
  }

  echo "</select></td><td valign=center align=right>" .
       "<input style=\"background-color: #000000; color: #00FF00; font-family:Courier New; font-size:10pt\" type=submit name=shipt value=\" $l_igb_shiptransfer \">" .
       "</form>" .
       "</td></tr>" .
       "<tr valign=top>" .
       "<td><br><font size=2 face=\"courier new\" color=#00FF00>$l_igb_fromplanet :<br><br>" .
       "<form action=IGB.php?command=transfer2 method=POST>" .
       "$l_igb_source&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<select style=\"background-color: #000000; color: #00FF00; font-family:Courier New; font-size:10pt\" name=splanet_id>";

  if(isset($planets))
  {
    foreach($planets as $planet)
    {
      if(empty($planet[name]))
        $planet[name] = $l_igb_unnamed;
      echo "<option value=$planet[planet_id]>$planet[name] $l_igb_in $planet[sector_id]</option>";
    }
  }
  else
  {
     echo "<option value=none>$l_igb_none</option>";
  }

  echo "</select><br>$l_igb_destination <select style=\"background-color: #000000; color: #00FF00; font-family:Courier New; font-size:10pt\" name=dplanet_id>";

  if(isset($planets))
  {
    foreach($planets as $planet)
    {
      if(empty($planet[name]))
        $planet[name] = $l_igb_unnamed;
      echo "<option value=$planet[planet_id]>$planet[name] $l_igb_in $planet[sector_id]</option>";
    }
  }
  else
  {
     echo "<option value=none>$l_igb_none</option>";
  }


  echo "</select></td><td valign=center align=right>" .
       "<br><input style=\"background-color: #000000; color: #00FF00; font-family:Courier New; font-size:10pt\" type=submit name=planett value=\"$l_igb_planettransfer\">" .
       "</td></tr>" .
       "</form><tr valign=bottom>" .
       "<td><font size=2 face=\"courier new\" color=#00FF00><a href=IGB.php?command=login>$l_igb_back</a></td><td align=right><font size=2 face=\"courier new\" color=#00FF00>&nbsp;<br><a href=\"main.php3\">$l_igb_logout</a></td>" .
       "</tr>";
}

function IGB_transfer2()
{
  global $playerinfo;
  global $account;
  global $ship_id;
  global $splanet_id;
  global $dplanet_id;
  global $IGB_min_turns;
  global $IGB_svalue;
  global $ibank_paymentfee;
  global $IGB_trate;
  global $l_igb_sendyourself, $l_igb_unknowntargetship, $l_igb_min_turns, $l_igb_min_turns2;
  global $l_igb_mustwait, $l_igb_shiptransfer, $l_igb_igbaccount, $l_igb_maxtransfer;
  global $l_igb_unlimited, $l_igb_maxtransferpercent, $l_igb_transferrate, $l_igb_recipient;
  global $l_igb_seltransferamount, $l_igb_transfer, $l_igb_back, $l_igb_logout, $l_igb_in;
  global $l_igb_errplanetsrcanddest, $l_igb_errunknownplanet, $l_igb_unnamed;
  global $l_igb_errnotyourplanet, $l_igb_planettransfer, $l_igb_srcplanet, $l_igb_destplanet;
  global $l_igb_transferrate2, $l_igb_seltransferamount;

  if(isset($ship_id)) //ship transfer
  {
    $res = mysql_query("SELECT * FROM ships WHERE ship_id=$ship_id");

    if($playerinfo[ship_id] == $ship_id)
      IGB_error($l_igb_sendyourself, "IGB.php?command=transfer");

    if(!$res || mysql_num_rows($res) == 0)
      IGB_error($l_igb_unknowntargetship, "IGB.php?command=transfer");

    $target = mysql_fetch_array($res);

    if($target[turns_used] < $IGB_min_turns)
    {
      $l_igb_min_turns = str_replace("[igb_min_turns]", $IGB_min_turns, $l_igb_min_turns);
      $l_igb_min_turns = str_replace("[igb_target_char_name]", $target[character_name], $l_igb_min_turns);
      IGB_error($l_igb_min_turns, "IGB.php?command=transfer");
    }

    if($playerinfo[turns_used] < $IGB_min_turns)
    {
      $l_igb_min_turns2 = str_replace("[igb_min_turns]", $IGB_min_turns, $l_igb_min_turns2);
      IGB_error($l_igb_min_turns2, "IGB.php?command=transfer");
    }

    if($IGB_trate > 0)
    {
      $curtime = time();
      $curtime -= $IGB_trate * 60;
      $res = mysql_query("SELECT UNIX_TIMESTAMP(time) as time FROM IGB_transfers WHERE UNIX_TIMESTAMP(time) > $curtime AND source_id=$playerinfo[ship_id] AND dest_id=$target[ship_id]");
      if(mysql_num_rows($res) != 0)
      {
        $time = mysql_fetch_array($res);
        $difftime = ($time[time] - $curtime) / 60;
        $l_igb_mustwait = str_replace("[igb_target_char_name]", $target[character_name], $l_igb_mustwait);
        $l_igb_mustwait = str_replace("[igb_trate]", NUMBER($IGB_trate), $l_igb_mustwait);
        $l_igb_mustwait = str_replace("[igb_difftime]", NUMBER($difftime), $l_igb_mustwait);
        IGB_error($l_igb_mustwait, "IGB.php?command=transfer");
      }
    }

    echo "<tr><td colspan=2 align=center valign=top><font size=2 face=\"courier new\" color=#00FF00>$l_igb_shiptransfer<br>---------------------------------</td></tr>" .
         "<tr valign=top><td><font size=2 face=\"courier new\" color=#00FF00>$l_igb_igbaccount :</td><td align=right><font size=2 face=\"courier new\" color=#00FF00>" . NUMBER($account[balance]) . " C</td></tr>";

    if($IGB_svalue == 0)
      echo "<tr valign=top><td><font size=2 face=\"courier new\" color=#00FF00>$l_igb_maxtransfer :</td><td align=right><font size=2 face=\"courier new\" color=#00FF00>$l_igb_unlimited</td></tr>";
    else
    {
      $percent = $IGB_svalue * 100;
      $score = gen_score($playerinfo[ship_id]);
      $maxtrans = $score * $score * $IGB_svalue;

      $l_igb_maxtransferpercent = str_replace("[igb_percent]", $percent, $l_igb_maxtransferpercent);
      echo "<tr valign=top><td nowrap><font size=2 face=\"courier new\" color=#00FF00>$l_igb_maxtransferpercent :</td><td align=right><font size=2 face=\"courier new\" color=#00FF00>" . NUMBER($maxtrans) . " C</td></tr>";
    }

    $percent = $ibank_paymentfee * 100;

    $l_igb_transferrate = str_replace("[igb_num_percent]", NUMBER($percent,1), $l_igb_transferrate);
    echo "<tr valign=top><td><font size=2 face=\"courier new\" color=#00FF00>$l_igb_recipient :</td><td align=right><font size=2 face=\"courier new\" color=#00FF00>$target[character_name]&nbsp;&nbsp;</td></tr>" .
         "<form action=IGB.php?command=transfer3 method=POST>" .
         "<tr valign=top>" .
         "<td><br><font size=2 face=\"courier new\" color=#00FF00>$l_igb_seltransferamount :</td>" .
         "<td align=right><br><input style=\"background-color: #000000; color: #00FF00; font-family:Courier New; font-size:10pt\" type=text size=15 maxlength=20 name=amount value=0><br>" .
         "<br><input style=\"background-color: #000000; color: #00FF00; font-family:Courier New; font-size:10pt\" type=submit value=$l_igb_transfer></td>" .
         "<input type=hidden name=ship_id value=$ship_id>" .
         "</form>" .
         "<tr><td colspan=2 align=center><font size=2 face=\"courier new\" color=#00FF00>" .
         "$l_igb_transferrate" .
         "<tr valign=bottom>" .
         "<td><font size=2 face=\"courier new\" color=#00FF00><a href=IGB.php?command=transfer>$l_igb_back</a></td><td align=right><font size=2 face=\"courier new\" color=#00FF00>&nbsp;<br><a href=\"main.php3\">$l_igb_logout</a></td>" .
         "</tr>";
  }
  else
  {
    if($splanet_id == $dplanet_id)
      IGB_error($l_igb_errplanetsrcanddest, "IGB.php?command=transfer");

    $res = mysql_query("SELECT name, credits, owner, sector_id FROM planets WHERE planet_id=$splanet_id");
    if(!$res || mysql_num_rows($res) == 0)
      IGB_error($l_igb_errunknownplanet, "IGB.php?command=transfer");
    $source = mysql_fetch_array($res);

    if(empty($source[name]))
      $source[name]=$l_igb_unnamed;

    $res = mysql_query("SELECT name, credits, owner, sector_id FROM planets WHERE planet_id=$dplanet_id");
    if(!$res || mysql_num_rows($res) == 0)
      IGB_error($l_igb_errunknownplanet, "IGB.php?command=transfer");
    $dest = mysql_fetch_array($res);

    if(empty($dest[name]))
      $dest[name]=$l_igb_unnamed;

    if($source[owner] != $playerinfo[ship_id] || $dest[owner] != $playerinfo[ship_id])
      IGB_error($l_igb_errnotyourplanet, "IGB.php?command=transfer");

    $percent = $ibank_paymentfee * 100;

    $l_igb_transferrate2 = str_replace("[igb_num_percent]", NUMBER($percent,1), $l_igb_transferrate2);
    echo "<tr><td colspan=2 align=center valign=top><font size=2 face=\"courier new\" color=#00FF00>$l_igb_planettransfer<br>---------------------------------</td></tr>" .
         "<tr valign=top>" .
         "<td><font size=2 face=\"courier new\" color=#00FF00>$l_igb_srcplanet $source[name] $l_igb_in $source[sector_id] :" .
         "<td align=right><font size=2 face=\"courier new\" color=#00FF00>" . NUMBER($source[credits]) . " C" .
         "<tr valign=top>" .
         "<td><font size=2 face=\"courier new\" color=#00FF00>$l_igb_destplanet $dest[name] $l_igb_in $dest[sector_id] :" .
         "<td align=right><font size=2 face=\"courier new\" color=#00FF00>" . NUMBER($dest[credits]) . " C" .
         "<form action=IGB.php?command=transfer3 method=POST>" .
         "<tr valign=top>" .
         "<td><br><font size=2 face=\"courier new\" color=#00FF00>$l_igb_seltransferamount :</td>" .
         "<td align=right><br><input style=\"background-color: #000000; color: #00FF00; font-family:Courier New; font-size:10pt\" type=text size=15 maxlength=20 name=amount value=0><br>" .
         "<br><input style=\"background-color: #000000; color: #00FF00; font-family:Courier New; font-size:10pt\" type=submit value=$l_igb_transfer></td>" .
         "<input type=hidden name=splanet_id value=$splanet_id>" .
         "<input type=hidden name=dplanet_id value=$dplanet_id>" .
         "</form>" .
         "<tr><td colspan=2 align=center><font size=2 face=\"courier new\" color=#00FF00>" .
         "$l_igb_transferrate2" .
         "<tr valign=bottom>" .
         "<td><font size=2 face=\"courier new\" color=#00FF00><a href=IGB.php?command=transfer>$l_igb_back</a></td><td align=right><font size=2 face=\"courier new\" color=#00FF00>&nbsp;<br><a href=\"main.php3\">$l_igb_logout</a></td>" .
         "</tr>";
  }

}

function IGB_transfer3()
{
  global $playerinfo;
  global $account;
  global $ship_id;
  global $splanet_id;
  global $dplanet_id;
  global $IGB_min_turns;
  global $IGB_svalue;
  global $ibank_paymentfee;
  global $amount;
  global $IGB_trate;
  global $l_igb_errsendyourself, $l_igb_unknowntargetship, $l_igb_min_turns3, $l_igb_min_turns4, $l_igb_mustwait2;
  global $l_igb_invalidtransferinput, $l_igb_nozeroamount, $l_igb_notenoughcredits, $l_igb_notenoughcredits2, $l_igb_in, $l_igb_to;
  global $l_igb_amounttoogreat, $l_igb_transfersuccessful, $l_igb_creditsto, $l_igb_transferamount, $l_igb_amounttransferred;
  global $l_igb_transferfee, $l_igb_igbaccount, $l_igb_back, $l_igb_logout, $l_igb_errplanetsrcanddest, $l_igb_errnotyourplanet;
  global $l_igb_errunknownplanet, $l_igb_unnamed, $l_igb_ctransferred, $l_igb_srcplanet, $l_igb_destplanet;

  if(isset($ship_id)) //ship transfer
  {
    //Need to check again to prevent cheating by manual posts

    $res = mysql_query("SELECT * FROM ships WHERE ship_id=$ship_id");

    if($playerinfo[ship_id] == $ship_id)
      IGB_error($l_igb_errsendyourself, "IGB.php?command=transfer");

    if(!$res || mysql_num_rows($res) == 0)
      IGB_error($l_igb_unknowntargetship, "IGB.php?command=transfer");

    $target = mysql_fetch_array($res);

    if($target[turns_used] < $IGB_min_turns)
    {
      $l_igb_min_turns3 = str_replace("[igb_min_turns]", $IGB_min_turns, $l_igb_min_turns3);
      $l_igb_min_turns3 = str_replace("[igb_target_char_name]", $target[character_name], $l_igb_min_turns3);
      IGB_error($l_igb_min_turns3, "IGB.php?command=transfer");
    }

    if($playerinfo[turns_used] < $IGB_min_turns)
    {
      $l_igb_min_turns4 = str_replace("[igb_min_turns]", $IGB_min_turns, $l_igb_min_turns4);
      IGB_error($l_igb_min_turns4, "IGB.php?command=transfer");
    }

    if($IGB_trate > 0)
    {
      $curtime = time();
      $curtime -= $IGB_trate * 60;
      $res = mysql_query("SELECT UNIX_TIMESTAMP(time) as time FROM IGB_transfers WHERE UNIX_TIMESTAMP(time) > $curtime AND source_id=$playerinfo[ship_id] AND dest_id=$target[ship_id]");
      if(mysql_num_rows($res) != 0)
      {
        $time = mysql_fetch_array($res);
        $difftime = ($time[time] - $curtime) / 60;
        $l_igb_mustwait2 = str_replace("[igb_target_char_name]", $target[character_name], $l_igb_mustwait2);
        $l_igb_mustwait2 = str_replace("[igb_trate]", NUMBER($IGB_trate), $l_igb_mustwait2);
        $l_igb_mustwait2 = str_replace("[igb_difftime]", NUMBER($difftime), $l_igb_mustwait2);
        IGB_error($l_igb_mustwait2, "IGB.php?command=transfer");
      }
    }

    $amount = StripNonNum($amount);

    if(($amount * 1) != $amount)
      IGB_error($l_igb_invalidtransferinput, "IGB.php?command=transfer");

    if($amount == 0)
      IGB_error($l_igb_nozeroamount, "IGB.php?command=transfer");

    if($amount > $account[balance])
      IGB_error($l_igb_notenoughcredits, "IGB.php?command=transfer");

    if($IGB_svalue != 0)
    {
      $percent = $IGB_svalue * 100;
      $score = gen_score($playerinfo[ship_id]);
      $maxtrans = $score * $score * $IGB_svalue;

      if($amount > $maxtrans)
        IGB_error($l_igb_amounttoogreat, "IGB.php?command=transfer");
    }

    $account[balance] -= $amount;
    $amount2 = $amount * $ibank_paymentfee;
    $transfer = $amount - $amount2;

    echo "<tr><td colspan=2 align=center valign=top><font size=2 face=\"courier new\" color=#00FF00>$l_igb_transfersuccessful<br>---------------------------------</td></tr>" .
         "<tr valign=top><td colspan=2 align=center><font size=2 face=\"courier new\" color=#00FF00>" . NUMBER($transfer) . " $l_igb_creditsto $target[character_name].</tr>" .
         "<tr valign=top>" .
         "<td><font size=2 face=\"courier new\" color=#00FF00>$l_igb_transferamount :</td><td align=right><font size=2 face=\"courier new\" color=#00FF00>" . NUMBER($amount) . " C<br>" .
         "<tr valign=top>" .
         "<td><font size=2 face=\"courier new\" color=#00FF00>$l_igb_transferfee :</td><td align=right><font size=2 face=\"courier new\" color=#00FF00>" . NUMBER($amount2) . " C<br>" .
         "<tr valign=top>" .
         "<td><font size=2 face=\"courier new\" color=#00FF00>$l_igb_amounttransferred :</td><td align=right><font size=2 face=\"courier new\" color=#00FF00>" . NUMBER($transfer) . " C<br>" .
         "<tr valign=top>" .
         "<td><font size=2 face=\"courier new\" color=#00FF00>$l_igb_igbaccount :</td><td align=right><font size=2 face=\"courier new\" color=#00FF00>" . NUMBER($account[balance]) . " C<br>" .
         "<tr valign=bottom>" .
         "<td><font size=2 face=\"courier new\" color=#00FF00><a href=IGB.php?command=login>$l_igb_back</a></td><td align=right><font size=2 face=\"courier new\" color=#00FF00>&nbsp;<br><a href=\"main.php3\">$l_igb_logout</a></td>" .
         "</tr>";

    mysql_query("UPDATE ibank_accounts SET balance=balance-$amount WHERE ship_id=$playerinfo[ship_id]");
    mysql_query("UPDATE ibank_accounts SET balance=balance+$transfer WHERE ship_id=$target[ship_id]");

    mysql_query("INSERT INTO IGB_transfers VALUES('', $playerinfo[ship_id], $target[ship_id], NOW())");
    echo mysql_error();
    //TODO: Log transfers.
  }
  else
  {
    if($splanet_id == $dplanet_id)
      IGB_error($l_igb_errplanetsrcanddest, "IGB.php?command=transfer");

    $res = mysql_query("SELECT name, credits, owner, sector_id FROM planets WHERE planet_id=$splanet_id");
    if(!$res || mysql_num_rows($res) == 0)
      IGB_error($l_igb_errunknownplanet, "IGB.php?command=transfer");
    $source = mysql_fetch_array($res);

    if(empty($source[name]))
      $source[name]=$l_igb_unnamed;

    $res = mysql_query("SELECT name, credits, owner, sector_id FROM planets WHERE planet_id=$dplanet_id");
    if(!$res || mysql_num_rows($res) == 0)
      IGB_error($l_igb_errunknownplanet, "IGB.php?command=transfer");
    $dest = mysql_fetch_array($res);

    if(empty($dest[name]))
      $dest[name]=$l_igb_unnamed;

    if($source[owner] != $playerinfo[ship_id] || $dest[owner] != $playerinfo[ship_id])
      IGB_error($l_igb_errnotyourplanet, "IGB.php?command=transfer");

    if($amount > $source[credits])
      IGB_error($l_igb_notenoughcredits2, "IGB.php?command=transfer");

    $percent = $ibank_paymentfee * 100;

    $source[credits] -= $amount;
    $amount2 = $amount * $ibank_paymentfee;
    $transfer = $amount - $amount2;
    $dest[credits] += $transfer;

    echo "<tr><td colspan=2 align=center valign=top><font size=2 face=\"courier new\" color=#00FF00>$l_igb_transfersuccessful<br>---------------------------------</td></tr>" .
         "<tr valign=top><td colspan=2 align=center><font size=2 face=\"courier new\" color=#00FF00>" . NUMBER($transfer) . " $l_igb_ctransferredfrom $source[name] $l_igb_to $dest[name].</tr>" .
         "<tr valign=top>" .
         "<td><font size=2 face=\"courier new\" color=#00FF00>$l_igb_transferamount :</td><td align=right><font size=2 face=\"courier new\" color=#00FF00>" . NUMBER($amount) . " C<br>" .
         "<tr valign=top>" .
         "<td><font size=2 face=\"courier new\" color=#00FF00>$l_igb_transferfee :</td><td align=right><font size=2 face=\"courier new\" color=#00FF00>" . NUMBER($amount2) . " C<br>" .
         "<tr valign=top>" .
         "<td><font size=2 face=\"courier new\" color=#00FF00>$l_igb_amounttransferred :</td><td align=right><font size=2 face=\"courier new\" color=#00FF00>" . NUMBER($transfer) . " C<br>" .
         "<tr valign=top>" .
         "<td><font size=2 face=\"courier new\" color=#00FF00>$l_igb_srcplanet $source[name] $l_igb_in $source[sector_id] :</td><td align=right><font size=2 face=\"courier new\" color=#00FF00>" . NUMBER($source[credits]) . " C<br>" .
         "<tr valign=top>" .
         "<td><font size=2 face=\"courier new\" color=#00FF00>$l_igb_destplanet $dest[name] $l_igb_in $dest[sector_id] :</td><td align=right><font size=2 face=\"courier new\" color=#00FF00>" . NUMBER($dest[credits]) . " C<br>" .
         "<tr valign=bottom>" .
         "<td><font size=2 face=\"courier new\" color=#00FF00><a href=IGB.php?command=login>$l_igb_back</a></td><td align=right><font size=2 face=\"courier new\" color=#00FF00>&nbsp;<br><a href=\"main.php3\">$l_igb_logout</a></td>" .
         "</tr>";

    mysql_query("UPDATE planets SET credits=credits-$amount WHERE planet_id=$splanet_id");
    mysql_query("UPDATE planets SET credits=credits+$transfer WHERE planet_id=$dplanet_id");
  }
}

function IGB_deposit2()
{
  global $playerinfo;
  global $amount;
  global $account;
  global $l_igb_invaliddepositinput, $l_igb_nozeroamount2, $l_igb_notenoughcredits, $l_igb_accounts, $l_igb_logout;
  global $l_igb_operationsuccessful, $l_igb_creditstoyou, $l_igb_igbaccount, $l_igb_shipaccount, $l_igb_back;

  $amount = StripNonNum($amount);
  if(($amount * 1) != $amount)
    IGB_error($l_igb_invaliddepositinput, "IGB.php?command=deposit");

  if($amount == 0)
    IGB_error($l_igb_nozeroamount2, "IGB.php?command=deposit");

  if($amount > $playerinfo[credits])
    IGB_error($l_igb_notenoughcredits, "IGB.php?command=deposit");

  $account[balance] += $amount;
  $playerinfo[credits] -= $amount;

  echo "<tr><td colspan=2 align=center valign=top><font size=2 face=\"courier new\" color=#00FF00>$l_igb_operationsuccessful<br>---------------------------------</td></tr>" .
       "<tr valign=top>" .
       "<td colspan=2 align=center><font size=2 face=\"courier new\" color=#00FF00>" . NUMBER($amount) ." $l_igb_creditstoyou</td>" .
       "<tr><td colspan=2 align=center><font size=2 face=\"courier new\" color=#00FF00>$l_igb_accounts<br>---------------------------------</td></tr>" .
       "<tr valign=top>" .
       "<td><font size=2 face=\"courier new\" color=#00FF00>$l_igb_shipaccount :<br>$l_igb_igbaccount :</td>" .
       "<td align=right><font size=2 face=\"courier new\" color=#00FF00>" . NUMBER($playerinfo[credits]) . " C<br>" . NUMBER($account[balance]) . " C</tr>" .
       "<tr valign=bottom>" .
       "<td><font size=2 face=\"courier new\" color=#00FF00><a href=IGB.php?command=login>$l_igb_back</a></td><td align=right><font size=2 face=\"courier new\" color=#00FF00>&nbsp;<br><a href=\"main.php3\">$l_igb_logout</a></td>" .
       "</tr>";

  mysql_query("UPDATE ibank_accounts SET balance=balance+$amount WHERE ship_id=$playerinfo[ship_id]");
  mysql_query("UPDATE ships SET credits=credits-$amount WHERE ship_id=$playerinfo[ship_id]");
}

function IGB_withdraw2()
{
  global $playerinfo;
  global $amount;
  global $account;
  global $l_igb_invalidwithdrawinput, $l_igb_nozeroamount3, $l_igb_notenoughcredits, $l_igb_accounts;
  global $l_igb_operationsuccessful, $l_igb_creditstoyourship, $l_igb_igbaccount, $l_igb_back, $l_igb_logout;

  $amount = StripNonNum($amount);
  if(($amount * 1) != $amount)
    IGB_error($l_igb_invalidwithdrawinput, "IGB.php?command=withdraw");

  if($amount == 0)
    IGB_error($l_igb_nozeroamount3, "IGB.php?command=withdraw");

  if($amount > $account[balance])
    IGB_error($l_igb_notenoughcredits, "IGB.php?command=withdraw");

  $account[balance] -= $amount;
  $playerinfo[credits] += $amount;

  echo "<tr><td colspan=2 align=center valign=top><font size=2 face=\"courier new\" color=#00FF00>$l_igb_operationsuccessful<br>---------------------------------</td></tr>" .
       "<tr valign=top>" .
       "<td colspan=2 align=center><font size=2 face=\"courier new\" color=#00FF00>" . NUMBER($amount) ." $l_igb_creditstoyourship</td>" .
       "<tr><td colspan=2 align=center><font size=2 face=\"courier new\" color=#00FF00>$l_igb_accounts<br>---------------------------------</td></tr>" .
       "<tr valign=top>" .
       "<td><font size=2 face=\"courier new\" color=#00FF00>Ship Account :<br>$l_igb_igbaccount :</td>" .
       "<td align=right><font size=2 face=\"courier new\" color=#00FF00>" . NUMBER($playerinfo[credits]) . " C<br>" . NUMBER($account[balance]) . " C</tr>" .
       "<tr valign=bottom>" .
       "<td><font size=2 face=\"courier new\" color=#00FF00><a href=IGB.php?command=login>$l_igb_back</a></td><td align=right><font size=2 face=\"courier new\" color=#00FF00>&nbsp;<br><a href=\"main.php3\">$l_igb_logout</a></td>" .
       "</tr>";

  mysql_query("UPDATE ibank_accounts SET balance=balance-$amount WHERE ship_id=$playerinfo[ship_id]");
  mysql_query("UPDATE ships SET credits=credits+$amount WHERE ship_id=$playerinfo[ship_id]");
}

function IGB_error($errmsg, $backlink, $title)
{
  global $l_igb_igberrreport, $l_igb_back, $l_igb_logout;

  $title = $l_igb_igberrreport;
  echo "<tr><td colspan=2 align=center valign=top><font size=2 face=\"courier new\" color=#00FF00>$title<br>---------------------------------</td></tr>" .
       "<tr valign=top>" .
       "<td colspan=2 align=center><font size=2 face=\"courier new\" color=#00FF00>$errmsg</td>" .
       "</tr>" .
       "<tr valign=bottom>" .
       "<td><font size=2 face=\"courier new\" color=#00FF00><a href=$backlink>$l_igb_back</a></td><td align=right><font size=2 face=\"courier new\" color=#00FF00>&nbsp;<br><a href=\"main.php3\">$l_igb_logout</a></td>" .
       "</tr>" .
       "</table>" .
       "</td></tr>" .
       "</table>" .
       "<img src=images/div2.gif>" .
       "</center>";

  include("footer.php3");
  die();
}

function StripNonNum($str)
{
  $str=(string)$str;
  $output = ereg_replace("[^0-9]","",$str);
  return $output;
}

?>
