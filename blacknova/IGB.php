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

if($command == 'login')
  IGB_login();
elseif($command == 'withdraw')
  IGB_withdraw();
elseif($command == 'deposit')
  IGB_deposit();
elseif($command == 'transfer')
  IGB_transfer();
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
  
  $res = mysql_query("SELECT name, planet_id, sector_id FROM planets WHERE owner=$playerinfo[ship_id]");
  while($row = mysql_fetch_array($res))
  {
    $planets[]=$row;
  }


  echo "<tr><td colspan=2 align=center valign=top><font size=2 face=\"courier new\" color=#00FF00>Transferring Funds<br>---------------------------------</td></tr>" .
       "<tr valign=top>" .
       "<td><font size=2 face=\"courier new\" color=#00FF00>Funds available :</td>" .
       "<td align=right><font size=2 face=\"courier new\" color=#00FF00>" . NUMBER($account[balance]) ." C<br></td>" .
       "</tr><tr valign=top>" .
       "<td><font size=2 face=\"courier new\" color=#00FF00>Please select amount to transfer :</td><td align=right>" .
       "<form action=IGB.php?command=withdraw2 method=POST>" .
       "<input style=\"background-color: #000000; color: #00FF00; font-family:Courier New; font-size:10pt\" type=text size=15 maxlength=20 name=amount value=0>" .
       "</td></tr>" .
       "<tr><td colspan=2 align=center valign=top><font size=2 face=\"courier new\" color=#00FF00>Transfer Type<br>---------------------------------</td></tr>" .
       "<tr valign=top>" .
       "<td><font size=2 face=\"courier new\" color=#00FF00>To another ship :<br><br>" .
       "<select style=\"background-color: #000000; color: #00FF00; font-family:Courier New; font-size:10pt\" name=ship_id>";
  
  foreach($ships as $ship)
  {
    echo "<option value=$ship[ship_id]>$ship[character_name]</option>";
  }
       
  echo "</select></td><td valign=center align=right>" .
       "<input style=\"background-color: #000000; color: #00FF00; font-family:Courier New; font-size:10pt\" type=submit name=shipt value=\"Transfer\">" .
       "</td></tr>" .
       "<tr valign=top>" .
       "<td><br><font size=2 face=\"courier new\" color=#00FF00>From a planet to another :<br><br>" .
       "Source&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<select style=\"background-color: #000000; color: #00FF00; font-family:Courier New; font-size:10pt\" name=splanet_id>";
  
  if(isset($planets))
  {
    foreach($planets as $planet)
    {
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
      echo "<option value=$planet[planet_id]>$planet[name] in $planet[sector_id]</option>";
    }
  }
  else
  {
     echo "<option value=none>None</option>";
  }


  echo "</select></td><td valign=center align=right>" .
       "<br><input style=\"background-color: #000000; color: #00FF00; font-family:Courier New; font-size:10pt\" type=submit name=planett value=\"Transfer\">" .
       "</td></tr>" .
       "</form><tr valign=bottom>" .
       "<td><font size=2 face=\"courier new\" color=#00FF00><a href=IGB.php?command=login>Back</a></td><td align=right><font size=2 face=\"courier new\" color=#00FF00>&nbsp;<br><a href=\"main.php3\">Logout</a></td>" .
       "</tr>";
}

?>
