<?

include("config.php3");
updatecookie();

$title="The Intergalactic Bank";
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
<table width=520 height=300 border=0>

<?

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
  echo '
  <tr><td width=25% valign=bottom><a href="main.php3"><font size=2 face="courier new" color=#00FF00>Quit</a></td><td width=50%>
  <font size=2 face="courier new" color=#00FF00>
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
  </center>
  <td width=25% valign=bottom align=right><font size=2 color=#00FF00 face="courier new"><a href="IGB.php?command=login">Login</a></td>
  ';
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

  echo "<tr><td colspan=2 align=center valign=top><font size=2 face=\"courier new\" color=#00FF00>Welcome to this Intergalactic Banking Terminal<br>---------------------------------</td></tr>" .
       "<tr valign=top>" .
       "<td><font size=2 face=\"courier new\" color=#00FF00>Account Holder :<br><br>Ship Account :<br>IGB Account&nbsp;&nbsp;:</td>" .
       "<td align=right><font size=2 face=\"courier new\" color=#00FF00>$playerinfo[character_name]&nbsp;&nbsp;<br><br>" . NUMBER($playerinfo[credits]) . " C<br>" . NUMBER($account[balance]) . " C<br></td>" .
       "</tr>" .
       "<tr><td colspan=2 align=center><font size=2 face=\"courier new\" color=#00FF00>Operations<br>---------------------------------<br><br><a href=\"IGB.php?command=withdraw\">Withdraw</a><br><a href=\"IGB.php?command=deposit\">Deposit</a><br><a href=\"IGB.php?command=transfer\">Transfer</a><br>&nbsp;</td></tr>" .
       "<tr valign=bottom>" .
       "<td><font size=2 face=\"courier new\" color=#00FF00><a href=IGB.php>Back</a></td><td align=right><font size=2 face=\"courier new\" color=#00FF00>&nbsp;<br><a href=\"main.php3\">Logout</a></td>" .
       "</tr>";
}

function IGB_withdraw()
{
  global $playerinfo;
  global $account;

  echo "<tr><td colspan=2 align=center valign=top><font size=2 face=\"courier new\" color=#00FF00>Withdraw Funds<br>---------------------------------</td></tr>" .
       "<tr valign=top>" .
       "<td><font size=2 face=\"courier new\" color=#00FF00>Funds available :</td>" .
       "<td align=right><font size=2 face=\"courier new\" color=#00FF00>" . NUMBER($account[balance]) ." C<br></td>" .
       "</tr><tr valign=top>" .
       "<td><font size=2 face=\"courier new\" color=#00FF00>Please select amount to withdraw :</td><td align=right>" .
       "<form action=IGB.php?command=withdraw2 method=POST>" .
       "<input style=\"background-color: #000000; color: #00FF00; font-family:Courier New; font-size:10pt\" type=text size=15 maxlength=20 name=amount value=0>" .
       "<br><br><input style=\"background-color: #000000; color: #00FF00; font-family:Courier New; font-size:10pt\" type=submit value=Withdraw>" .
       "</form></td></tr>" .
       "<tr valign=bottom>" .
       "<td><font size=2 face=\"courier new\" color=#00FF00><a href=IGB.php?command=login>Back</a></td><td align=right><font size=2 face=\"courier new\" color=#00FF00>&nbsp;<br><a href=\"main.php3\">Logout</a></td>" .
       "</tr>";

}

function IGB_deposit()
{
  global $playerinfo;
  global $account;

  echo "<tr><td colspan=2 align=center valign=top><font size=2 face=\"courier new\" color=#00FF00>Deposit Funds<br>---------------------------------</td></tr>" .
       "<tr valign=top>" .
       "<td><font size=2 face=\"courier new\" color=#00FF00>Funds available :</td>" .
       "<td align=right><font size=2 face=\"courier new\" color=#00FF00>" . NUMBER($playerinfo[credits]) ." C<br></td>" .
       "</tr><tr valign=top>" .
       "<td><font size=2 face=\"courier new\" color=#00FF00>Please select amount to deposit :</td><td align=right>" .
       "<form action=IGB.php?command=deposit2 method=POST>" .
       "<input style=\"background-color: #000000; color: #00FF00; font-family:Courier New; font-size:10pt\" type=text size=15 maxlength=20 name=amount value=0>" .
       "<br><br><input style=\"background-color: #000000; color: #00FF00; font-family:Courier New; font-size:10pt\" type=submit value=Deposit>" .
       "</form></td></tr>" .
       "<tr valign=bottom>" .
       "<td><font size=2 face=\"courier new\" color=#00FF00><a href=IGB.php?command=login>Back</a></td><td align=right><font size=2 face=\"courier new\" color=#00FF00>&nbsp;<br><a href=\"main.php3\">Logout</a></td>" .
       "</tr>";

}

function IGB_transfer()
{
  global $playerinfo;
  global $account;

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


  echo "<tr><td colspan=2 align=center valign=top><font size=2 face=\"courier new\" color=#00FF00>Transfer Type<br>---------------------------------</td></tr>" .
       "<tr valign=top>" .
       "<form action=IGB.php?command=transfer2 method=POST>" .
       "<td><font size=2 face=\"courier new\" color=#00FF00>To another ship :<br><br>" .
       "<select style=\"background-color: #000000; color: #00FF00; font-family:Courier New; font-size:10pt\" name=ship_id>";
  
  foreach($ships as $ship)
  {
    echo "<option value=$ship[ship_id]>$ship[character_name]</option>";
  }
       
  echo "</select></td><td valign=center align=right>" .
       "<input style=\"background-color: #000000; color: #00FF00; font-family:Courier New; font-size:10pt\" type=submit name=shipt value=\" Ship Transfer \">" .
       "</form>" .
       "</td></tr>" .
       "<tr valign=top>" .
       "<td><br><font size=2 face=\"courier new\" color=#00FF00>From a planet to another :<br><br>" .
       "<form action=IGB.php?command=transfer2 method=POST>" .
       "Source&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<select style=\"background-color: #000000; color: #00FF00; font-family:Courier New; font-size:10pt\" name=splanet_id>";
  
  if(isset($planets))
  {
    foreach($planets as $planet)
    {
      if(empty($planet[name]))
        $planet[name] = "Unnamed";
      echo "<option value=$planet[planet_id]>$planet[name] in $planet[sector_id]</option>";
    }
  }
  else
  {
     echo "<option value=none>None</option>";
  }
  
  echo "</select><br>Destination <select style=\"background-color: #000000; color: #00FF00; font-family:Courier New; font-size:10pt\" name=dplanet_id>";

  if(isset($planets))
  {
    foreach($planets as $planet)
    {
      if(empty($planet[name]))
        $planet[name] = "Unnamed";
      echo "<option value=$planet[planet_id]>$planet[name] in $planet[sector_id]</option>";
    }
  }
  else
  {
     echo "<option value=none>None</option>";
  }


  echo "</select></td><td valign=center align=right>" .
       "<br><input style=\"background-color: #000000; color: #00FF00; font-family:Courier New; font-size:10pt\" type=submit name=planett value=\"Planet Transfer\">" .
       "</td></tr>" .
       "</form><tr valign=bottom>" .
       "<td><font size=2 face=\"courier new\" color=#00FF00><a href=IGB.php?command=login>Back</a></td><td align=right><font size=2 face=\"courier new\" color=#00FF00>&nbsp;<br><a href=\"main.php3\">Logout</a></td>" .
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

  if(isset($ship_id)) //ship transfer
  {
    $res = mysql_query("SELECT * FROM ships WHERE ship_id=$ship_id");
    
    if($playerinfo[ship_id] == $ship_id)
      IGB_error("You can't send money to yourself!", "IGB.php?command=transfer");
    
    if(!$res || mysql_num_rows($res) == 0)
      IGB_error("Unknown target ship!", "IGB.php?command=transfer");
    
    $target = mysql_fetch_array($res);

    if($target[turns_used] < $IGB_min_turns)
      IGB_error("Player $target[character_name] must have played at least $IGB_min_turns turns before he can receive money.", "IGB.php?command=transfer");

    if($playerinfo[turns_used] < $IGB_min_turns)
      IGB_error("You must have played at least $IGB_min_turns turns before you can send money.", "IGB.php?command=transfer");

    if($IGB_trate > 0)
    {
      $curtime = time();
      $curtime -= $IGB_trate * 60;
      $res = mysql_query("SELECT UNIX_TIMESTAMP(time) as time FROM IGB_transfers WHERE UNIX_TIMESTAMP(time) > $curtime AND source_id=$playerinfo[ship_id] AND dest_id=$target[ship_id]");
      if(mysql_num_rows($res) != 0)
      {
        $time = mysql_fetch_array($res);
        $difftime = ($time[time] - $curtime) / 60;
        IGB_error("You have already made a transfer to $target[character_name] in the last " . NUMBER($IGB_trate) . " minutes. You must wait " . NUMBER($difftime) . " minutes before you can transfer credits to that player again.", "IGB.php?command=transfer");
      }
    }

    echo "<tr><td colspan=2 align=center valign=top><font size=2 face=\"courier new\" color=#00FF00>Ship Transfer<br>---------------------------------</td></tr>" .
         "<tr valign=top><td><font size=2 face=\"courier new\" color=#00FF00>IGB Account :</td><td align=right><font size=2 face=\"courier new\" color=#00FF00>" . NUMBER($account[balance]) . " C</td></tr>";
    
    if($IGB_svalue == 0)
      echo "<tr valign=top><td><font size=2 face=\"courier new\" color=#00FF00>Maximum transfer allowed :</td><td align=right><font size=2 face=\"courier new\" color=#00FF00>Unlimited</td></tr>";
    else
    {
      $percent = $IGB_svalue * 100;
      $score = gen_score($playerinfo[ship_id]);
      $maxtrans = $score * $score * $IGB_svalue;

      echo "<tr valign=top><td nowrap><font size=2 face=\"courier new\" color=#00FF00>Maximum transfer allowed ($percent% of net worth) :</td><td align=right><font size=2 face=\"courier new\" color=#00FF00>" . NUMBER($maxtrans) . " C</td></tr>";
    }

    $percent = $ibank_paymentfee * 100;
    
    echo "<tr valign=top><td><font size=2 face=\"courier new\" color=#00FF00>Recipient :</td><td align=right><font size=2 face=\"courier new\" color=#00FF00>$target[character_name]&nbsp;&nbsp;</td></tr>" .
         "<form action=IGB.php?command=transfer3 method=POST>" .
         "<tr valign=top>" .
         "<td><br><font size=2 face=\"courier new\" color=#00FF00>Please select amount to transfer :</td>" .
         "<td align=right><br><input style=\"background-color: #000000; color: #00FF00; font-family:Courier New; font-size:10pt\" type=text size=15 maxlength=20 name=amount value=0><br>" .
         "<br><input style=\"background-color: #000000; color: #00FF00; font-family:Courier New; font-size:10pt\" type=submit value=Transfer></td>" .
         "<input type=hidden name=ship_id value=$ship_id>" .
         "</form>" .
         "<tr><td colspan=2 align=center><font size=2 face=\"courier new\" color=#00FF00>" .
         "Current rate for transfers is " . NUMBER($percent,1) . "% of total amount. It will automatically be deduced from the amount you transfer." .
         "<tr valign=bottom>" . 
         "<td><font size=2 face=\"courier new\" color=#00FF00><a href=IGB.php?command=transfer>Back</a></td><td align=right><font size=2 face=\"courier new\" color=#00FF00>&nbsp;<br><a href=\"main.php3\">Logout</a></td>" .
         "</tr>";
  }
  else
  {
    if($splanet_id == $dplanet_id)
      IGB_error("The same planet can't be both source and destination.", "IGB.php?command=transfer");
    
    $res = mysql_query("SELECT name, credits, owner, sector_id FROM planets WHERE planet_id=$splanet_id");
    if(!$res || mysql_num_rows($res) == 0)
      IGB_error("DB Error! Unkown Planet!", "IGB.php?command=transfer");
    $source = mysql_fetch_array($res);

    if(empty($source[name]))
      $source[name]="Unnamed";

    $res = mysql_query("SELECT name, credits, owner, sector_id FROM planets WHERE planet_id=$dplanet_id");
    if(!$res || mysql_num_rows($res) == 0)
      IGB_error("DB Error! Unkown Planet!", "IGB.php?command=transfer");
    $dest = mysql_fetch_array($res);
    
    if(empty($dest[name]))
      $dest[name]="Unnamed";

    if($source[owner] != $playerinfo[ship_id] || $dest[owner] != $playerinfo[ship_id])
      IGB_error("You can't transfer money from/to a planet you do not own.", "IGB.php?command=transfer");

    $percent = $ibank_paymentfee * 100;

    echo "<tr><td colspan=2 align=center valign=top><font size=2 face=\"courier new\" color=#00FF00>Planet Transfer<br>---------------------------------</td></tr>" .
         "<tr valign=top>" .
         "<td><font size=2 face=\"courier new\" color=#00FF00>Src -> Planet $source[name] in $source[sector_id] :" .
         "<td align=right><font size=2 face=\"courier new\" color=#00FF00>" . NUMBER($source[credits]) . " C" .
         "<tr valign=top>" .
         "<td><font size=2 face=\"courier new\" color=#00FF00>Dst -> Planet $dest[name] in $dest[sector_id] :" .
         "<td align=right><font size=2 face=\"courier new\" color=#00FF00>" . NUMBER($dest[credits]) . " C" .
         "<form action=IGB.php?command=transfer3 method=POST>" .
         "<tr valign=top>" .
         "<td><br><font size=2 face=\"courier new\" color=#00FF00>Please select amount to transfer :</td>" .
         "<td align=right><br><input style=\"background-color: #000000; color: #00FF00; font-family:Courier New; font-size:10pt\" type=text size=15 maxlength=20 name=amount value=0><br>" .
         "<br><input style=\"background-color: #000000; color: #00FF00; font-family:Courier New; font-size:10pt\" type=submit value=Transfer></td>" .
         "<input type=hidden name=splanet_id value=$splanet_id>" .
         "<input type=hidden name=dplanet_id value=$dplanet_id>" .
         "</form>" .
         "<tr><td colspan=2 align=center><font size=2 face=\"courier new\" color=#00FF00>" .
         "Current rate for transfers is " . NUMBER($percent,1) . "% of total amount. It will automatically be deduced from the amount you transfer." .
         "<tr valign=bottom>" . 
         "<td><font size=2 face=\"courier new\" color=#00FF00><a href=IGB.php?command=transfer>Back</a></td><td align=right><font size=2 face=\"courier new\" color=#00FF00>&nbsp;<br><a href=\"main.php3\">Logout</a></td>" .
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

  if(isset($ship_id)) //ship transfer
  {
    //Need to check again to prevent cheating by manual posts
    
    $res = mysql_query("SELECT * FROM ships WHERE ship_id=$ship_id");
    
    if($playerinfo[ship_id] == $ship_id)
      IGB_error("You can't send money to yourself!", "IGB.php?command=transfer");
    
    if(!$res || mysql_num_rows($res) == 0)
      IGB_error("Unknown target ship!", "IGB.php?command=transfer");
    
    $target = mysql_fetch_array($res);

    if($target[turns_used] < $IGB_min_turns)
      IGB_error("Player $target[character_name] must have played at least $IGB_min_turns turns before he can receive money.", "IGB.php?command=transfer");

    if($playerinfo[turns_used] < $IGB_min_turns)
      IGB_error("You must have played at least $IGB_min_turns turns before you can send money.", "IGB.php?command=transfer");

    if($IGB_trate > 0)
    {
      $curtime = time();
      $curtime -= $IGB_trate * 60;
      $res = mysql_query("SELECT UNIX_TIMESTAMP(time) as time FROM IGB_transfers WHERE UNIX_TIMESTAMP(time) > $curtime AND source_id=$playerinfo[ship_id] AND dest_id=$target[ship_id]");
      if(mysql_num_rows($res) != 0)
      {
        $time = mysql_fetch_array($res);
        $difftime = ($time[time] - $curtime) / 60;
        IGB_error("You have already made a transfer to $target[character_name] in the last " . NUMBER($IGB_trate) . " minutes. You must wait " . NUMBER($difftime) . " minutes before you can transfer credits to that player again.", "IGB.php?command=transfer");
      }
    }
    
    $amount = StripNonNum($amount);

    if(($amount * 1) != $amount)
      IGB_error("Invalid input for transfer.", "IGB.php?command=transfer");
  
    if($amount == 0)
      IGB_error("Amount to transfer must not be zero.", "IGB.php?command=transfer");

    if($amount > $account[balance])
      IGB_error("You do not have enough credits to perform this operation.", "IGB.php?command=transfer");

    if($IGB_svalue != 0)
    {
      $percent = $IGB_svalue * 100;
      $score = gen_score($playerinfo[ship_id]);
      $maxtrans = $score * $score * $IGB_svalue;

      if($amount > $maxtrans)
        IGB_error("The amount you entered was greater than your maximum transfer allowed.", "IGB.php?command=transfer");
    }

    $account[balance] -= $amount;
    $amount2 = $amount * $ibank_paymentfee;
    $transfer = $amount - $amount2;

    echo "<tr><td colspan=2 align=center valign=top><font size=2 face=\"courier new\" color=#00FF00>Transfer Successful<br>---------------------------------</td></tr>" .
         "<tr valign=top><td colspan=2 align=center><font size=2 face=\"courier new\" color=#00FF00>" . NUMBER($transfer) . " credits have been transferred to $target[character_name].</tr>" .
         "<tr valign=top>" .
         "<td><font size=2 face=\"courier new\" color=#00FF00>Transfer Amount :</td><td align=right><font size=2 face=\"courier new\" color=#00FF00>" . NUMBER($amount) . " C<br>" .
         "<tr valign=top>" .
         "<td><font size=2 face=\"courier new\" color=#00FF00>Transfer Fee :</td><td align=right><font size=2 face=\"courier new\" color=#00FF00>" . NUMBER($amount2) . " C<br>" .
         "<tr valign=top>" .
         "<td><font size=2 face=\"courier new\" color=#00FF00>Amount Transferred :</td><td align=right><font size=2 face=\"courier new\" color=#00FF00>" . NUMBER($transfer) . " C<br>" .
         "<tr valign=top>" .
         "<td><font size=2 face=\"courier new\" color=#00FF00>IGB Account :</td><td align=right><font size=2 face=\"courier new\" color=#00FF00>" . NUMBER($account[balance]) . " C<br>" .
         "<tr valign=bottom>" . 
         "<td><font size=2 face=\"courier new\" color=#00FF00><a href=IGB.php?command=login>Back</a></td><td align=right><font size=2 face=\"courier new\" color=#00FF00>&nbsp;<br><a href=\"main.php3\">Logout</a></td>" .
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
      IGB_error("The same planet can't be both source and destination.", "IGB.php?command=transfer");
    
    $res = mysql_query("SELECT name, credits, owner, sector_id FROM planets WHERE planet_id=$splanet_id");
    if(!$res || mysql_num_rows($res) == 0)
      IGB_error("DB Error! Unkown Planet!", "IGB.php?command=transfer");
    $source = mysql_fetch_array($res);

    if(empty($source[name]))
      $source[name]="Unnamed";

    $res = mysql_query("SELECT name, credits, owner, sector_id FROM planets WHERE planet_id=$dplanet_id");
    if(!$res || mysql_num_rows($res) == 0)
      IGB_error("DB Error! Unkown Planet!", "IGB.php?command=transfer");
    $dest = mysql_fetch_array($res);
    
    if(empty($dest[name]))
      $dest[name]="Unnamed";

    if($source[owner] != $playerinfo[ship_id] || $dest[owner] != $playerinfo[ship_id])
      IGB_error("You can't transfer money from/to a planet you do not own.", "IGB.php?command=transfer");

    if($amount > $source[credits])
      IGB_error("There are not enough credits on source planet to complete that transaction.", "IGB.php?command=transfer");
    
    $percent = $ibank_paymentfee * 100;

    $source[credits] -= $amount;
    $amount2 = $amount * $ibank_paymentfee;
    $transfer = $amount - $amount2;
    $dest[credits] += $transfer;

    echo "<tr><td colspan=2 align=center valign=top><font size=2 face=\"courier new\" color=#00FF00>Transfer Successful<br>---------------------------------</td></tr>" .
         "<tr valign=top><td colspan=2 align=center><font size=2 face=\"courier new\" color=#00FF00>" . NUMBER($transfer) . " credits have been transferred from $source[name] to $dest[name].</tr>" .
         "<tr valign=top>" .
         "<td><font size=2 face=\"courier new\" color=#00FF00>Transfer Amount :</td><td align=right><font size=2 face=\"courier new\" color=#00FF00>" . NUMBER($amount) . " C<br>" .
         "<tr valign=top>" .
         "<td><font size=2 face=\"courier new\" color=#00FF00>Transfer Fee :</td><td align=right><font size=2 face=\"courier new\" color=#00FF00>" . NUMBER($amount2) . " C<br>" .
         "<tr valign=top>" .
         "<td><font size=2 face=\"courier new\" color=#00FF00>Amount Transferred :</td><td align=right><font size=2 face=\"courier new\" color=#00FF00>" . NUMBER($transfer) . " C<br>" .
         "<tr valign=top>" .
         "<td><font size=2 face=\"courier new\" color=#00FF00>Src -> Planet $source[name] in $source[sector_id] :</td><td align=right><font size=2 face=\"courier new\" color=#00FF00>" . NUMBER($source[credits]) . " C<br>" .
         "<tr valign=top>" .
         "<td><font size=2 face=\"courier new\" color=#00FF00>Dst -> Planet $dest[name] in $dest[sector_id] :</td><td align=right><font size=2 face=\"courier new\" color=#00FF00>" . NUMBER($dest[credits]) . " C<br>" .
         "<tr valign=bottom>" . 
         "<td><font size=2 face=\"courier new\" color=#00FF00><a href=IGB.php?command=login>Back</a></td><td align=right><font size=2 face=\"courier new\" color=#00FF00>&nbsp;<br><a href=\"main.php3\">Logout</a></td>" .
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

  $amount = StripNonNum($amount);
  if(($amount * 1) != $amount)
    IGB_error("Invalid input for deposit.", "IGB.php?command=deposit");
  
  if($amount == 0)
    IGB_error("Amount to deposit must not be zero.", "IGB.php?command=deposit");

  if($amount > $playerinfo[credits])
    IGB_error("You do not have enough credits to perform this operation.", "IGB.php?command=deposit");

  $account[balance] += $amount;
  $playerinfo[credits] -= $amount;

  echo "<tr><td colspan=2 align=center valign=top><font size=2 face=\"courier new\" color=#00FF00>Operation successful<br>---------------------------------</td></tr>" .
       "<tr valign=top>" .
       "<td colspan=2 align=center><font size=2 face=\"courier new\" color=#00FF00>" . NUMBER($amount) ." Credits have been transfered to your IGB account.</td>" .
       "<tr><td colspan=2 align=center><font size=2 face=\"courier new\" color=#00FF00>Accounts<br>---------------------------------</td></tr>" .
       "<tr valign=top>" .
       "<td><font size=2 face=\"courier new\" color=#00FF00>Ship Account :<br>IGB Account :</td>" .
       "<td align=right><font size=2 face=\"courier new\" color=#00FF00>" . NUMBER($playerinfo[credits]) . " C<br>" . NUMBER($account[balance]) . " C</tr>" .
       "<tr valign=bottom>" . 
       "<td><font size=2 face=\"courier new\" color=#00FF00><a href=IGB.php?command=login>Back</a></td><td align=right><font size=2 face=\"courier new\" color=#00FF00>&nbsp;<br><a href=\"main.php3\">Logout</a></td>" .
       "</tr>";

  mysql_query("UPDATE ibank_accounts SET balance=balance+$amount WHERE ship_id=$playerinfo[ship_id]");
  mysql_query("UPDATE ships SET credits=credits-$amount WHERE ship_id=$playerinfo[ship_id]");
}

function IGB_withdraw2()
{
  global $playerinfo;
  global $amount;
  global $account;

  $amount = StripNonNum($amount);
  if(($amount * 1) != $amount)
    IGB_error("Invalid input for withdraw.", "IGB.php?command=withdraw");
  
  if($amount == 0)
    IGB_error("Amount to withdraw must not be zero.", "IGB.php?command=withdraw");

  if($amount > $account[balance])
    IGB_error("You do not have enough credits to perform this operation.", "IGB.php?command=withdraw");

  $account[balance] -= $amount;
  $playerinfo[credits] += $amount;

  echo "<tr><td colspan=2 align=center valign=top><font size=2 face=\"courier new\" color=#00FF00>Operation successful<br>---------------------------------</td></tr>" .
       "<tr valign=top>" .
       "<td colspan=2 align=center><font size=2 face=\"courier new\" color=#00FF00>" . NUMBER($amount) ." Credits have been transfered to your ship account.</td>" .
       "<tr><td colspan=2 align=center><font size=2 face=\"courier new\" color=#00FF00>Accounts<br>---------------------------------</td></tr>" .
       "<tr valign=top>" .
       "<td><font size=2 face=\"courier new\" color=#00FF00>Ship Account :<br>IGB Account :</td>" .
       "<td align=right><font size=2 face=\"courier new\" color=#00FF00>" . NUMBER($playerinfo[credits]) . " C<br>" . NUMBER($account[balance]) . " C</tr>" .
       "<tr valign=bottom>" . 
       "<td><font size=2 face=\"courier new\" color=#00FF00><a href=IGB.php?command=login>Back</a></td><td align=right><font size=2 face=\"courier new\" color=#00FF00>&nbsp;<br><a href=\"main.php3\">Logout</a></td>" .
       "</tr>";

  mysql_query("UPDATE ibank_accounts SET balance=balance-$amount WHERE ship_id=$playerinfo[ship_id]");
  mysql_query("UPDATE ships SET credits=credits+$amount WHERE ship_id=$playerinfo[ship_id]");
}

function IGB_error($errmsg, $backlink, $title="IGB Error Report")
{
  echo "<tr><td colspan=2 align=center valign=top><font size=2 face=\"courier new\" color=#00FF00>$title<br>---------------------------------</td></tr>" .
       "<tr valign=top>" .
       "<td colspan=2 align=center><font size=2 face=\"courier new\" color=#00FF00>$errmsg</td>" .
       "</tr>" .
       "<tr valign=bottom>" .
       "<td><font size=2 face=\"courier new\" color=#00FF00><a href=$backlink>Back</a></td><td align=right><font size=2 face=\"courier new\" color=#00FF00>&nbsp;<br><a href=\"main.php3\">Logout</a></td>" .
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
