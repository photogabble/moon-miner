<?
//////////////////////////////////////////////////////////////////////////////////
// IGB - Inter Galactic Bank - BlackNova Traders
// Author:  Danny Froberg - danny@froberg.org
//      dfroberg@users.sourceforge.net
// Initial: 12-2-2000
// 20010903 - David Rowlands Fixed Bug #445681 
// Based upon the following post in the BlackNova Forum;
/*
sisko [guest] from BlackNova Traders  
Suggestion: The Galactic Bank?  Posted 12-2-2000 00:33  
--------------------------------------------------------------------------------
Playing with multiple accounts trying to simulate a corporation with multiple people 
I've come across the need/want for a couple of features that aren't there yet. 
What I think would be the most convenient is having some sore of "banking" feature. 
For credits, let any ship at a special port (or maybe just sector 0) transfer credits 
to any other player's account (use email address or username?). For cargo/colonists, 
it would be great if any planet could transfer to any other planet. Planets should 
also be able to transfer credits to any other player or planet. 
Maybe it would be good if planetary transfers actually required you to go to the 
planet you wish to transfer from. Then from a user interface perspective you'd have 
a "Bank" link in sector 0 (and other special ports), and a "Bank" link on the 
planetary menu screen.  
*/

//////////////////////////////////////////////////////////////////////////////////
// -- Main

include("config.php3");
updatecookie();

$title="IGB - The Inter Galactic Bank";
include("header.php3");

connectdb();
if (checklogin()) {die();}

if(!$allow_ibank)
{
  echo "The Inter Galactic Bank is currently closed.<BR><BR>";
  TEXT_GOTOMAIN();
  include("footer.php3");  
  die();
}
// Added Locking
mysql_query("LOCK TABLES ships WRITE, universe WRITE, ibank_accounts WRITE");

//////////////////////////////////////////////////////////////////////////////////
// -- Refresh data for display

function ibank_refreshdata()
{
  global $username,$playerinfo,$account,$sectorinfo,$totalnumaccounts;
  $result = mysql_query ("SELECT * FROM ships WHERE email='$username'");
  $playerinfo=mysql_fetch_array($result);
  
  $result2 = mysql_query ("SELECT * FROM universe WHERE sector_id='$playerinfo[sector]'");
  $sectorinfo=mysql_fetch_array($result2);
  
  $caccount = mysql_query ("SELECT * from ibank_accounts");
  $totalnumaccounts = mysql_num_rows($caccount);
  
  $laccount = mysql_query ("SELECT * from ibank_accounts WHERE id=$playerinfo[ship_id]");
  $account=mysql_fetch_array($laccount);
  
}
//////////////////////////////////////////////////////////////////////////////////
// -- Template Functions for Layout

function ibank_display_head($header = 'Empty', $backlink = 'ibank.php3')
{
  global $username,$playerinfo,$account,$ibank_interest,$ibank_loaninterest,$totalnumaccounts;
  $totalnumaccounts = $totalnumaccounts - 1; // Remove the default ibank account
  ibank_refreshdata();
  $mtime = filemtime ("cron.txt");
  // int mktime (int hour, int minute, int second, int month, int day, int year [, int is_dst])
  $nextupdate = strftime ("%T",mktime (date("H",$mtime),date("i",$mtime)+6,date("s",$mtime),date("m",$mtime),date("d",$mtime),date("Y",$mtime)));
  
  echo '<table width="550" cellspacing="0" cellpadding="1" bgcolor="Black"><tr><td><table width="100%" border="0" cellspacing="0" cellpadding="0" align="center" bgcolor="White">
    <TR BGCOLOR=\"$color_header\">
    <td colspan="4" align="center" bgcolor="#FFDBB7"><font face="Arial,Helvetica,sans-serif" color="Navy"><b>I N T E R&nbsp;&nbsp;G A L A C T I C&nbsp;&nbsp;B A N K I N G&nbsp;&nbsp;T E R M I N A L<br><font size="-1">-&nbsp;C r e d i t s&nbsp;&nbsp;R&nbsp;&nbsp;U s&nbsp;-<br>We are currently serving '.$totalnumaccounts.' customers in this galaxy.</font></b></font></td>
    </tr>
    <TR>
    <td colspan="4" align="center"><font face="Arial,Helvetica,sans-serif" size="-1" color="Maroon">Account Holder: '.$playerinfo[character_name].' captaining the '.$playerinfo[ship_name].'</font></b></font></td>
    </tr>
    <TR>
    <td colspan="4" align="center" bgcolor="#DDDDDD"><font face="Arial,Helvetica,sans-serif" size="-1" color="Maroon"><b>ACCOUNT STATUS</b></font></b></font></td>
    </tr>
    <tr>
    <td colspan="2" bgcolor="#FFFFCA"><font face="Arial,Helvetica,sans-serif" size="-2" color="Navy">&nbsp;Your Ship Account contains</td>
    <td colspan="2" align="right" bgcolor="#FFFFCA"><font face="Arial,Helvetica,sans-serif" size="-2" color="Navy">'.number_format($playerinfo[credits]).' credits.</font></td>
    </tr>
    <tr>
    <td colspan="2" bgcolor="#FFFFCA"><font face="Arial,Helvetica,sans-serif" size="-2" color="Navy">&nbsp;Your IGB Account contains</td>
    <td colspan="2" align="right" bgcolor="#FFFFCA"><font face="Arial,Helvetica,sans-serif" size="-2" color="Navy">'.number_format($account[ballance]).' credits.</font></td>
    </tr>';
    $interest_perturn = ($ibank_interest * $account[ballance]);
  echo '
    <tr>
    <td colspan="2" bgcolor="#FFFFCA"><font face="Arial,Helvetica,sans-serif" size="-2" color="Navy">&nbsp;At '.$nextupdate.' you will receive '.($ibank_interest *100).'% Interest of</font></td>
    <td colspan="2" align="right" bgcolor="#FFFFCA"><font face="Arial,Helvetica,sans-serif" size="-2" color="Navy">+'.number_format($interest_perturn).' credits.</font></td>
    </tr>';   
    if($account[loan] > 0)
    {
      echo '
        <tr>
        <td colspan="2" bgcolor="#FFFFCA"><font face="Arial,Helvetica,sans-serif" size="-2" color="Red">&nbsp;You have a IGB Loan of</font></td>
        <td colspan="2" align="right" bgcolor="#FFFFCA"><font face="Arial,Helvetica,sans-serif" size="-2" color="Red">-'.number_format($account[loan]).' credits.</font></td>
        </tr>';
        $loaninterest_perturn = ($ibank_loaninterest * $account[loan]);
      echo '
        <tr>
        <td colspan="2" bgcolor="#FFFFCA"><font face="Arial,Helvetica,sans-serif" size="-2" color="Red">&nbsp;At '.$nextupdate.' you will pay '.($ibank_loaninterest *100).'% Loan Interest + Mortage of</font></td>
        <td colspan="2" align="right" bgcolor="#FFFFCA"><font face="Arial,Helvetica,sans-serif" size="-2" color="Red">-'.number_format($loaninterest_perturn * 2).' credits.</font></td>
        </tr>';
    } 
    echo '
      </table></td></tr>
      </table>
      <table width="550" cellspacing="0" cellpadding="1" bgcolor="Black"><tr><td><table width="100%" border="0" cellspacing="0" cellpadding="0" align="center" bgcolor="White">
      <TR>
      <td colspan="4" align="center" bgcolor="#DDDDDD"><font face="Arial,Helvetica,sans-serif" size="-1" color="Maroon"><b>'.$header.'</b></font></b></font></td>
      </tr>
      ';
}

function ibank_display_footer($backlink = 'ibank.php3')
{
  echo '
    <TR>
    <td bgcolor="#FFDBB7"><a href='.$backlink.'>&nbsp;<b>Exit</b></a></td>
    <TD bgcolor="#FFDBB7" colspan=3>Exit Terminal Function</TD>
    </TR></TABLE></td></tr></table>
    '; 
}

//////////////////////////////////////////////////////////////////////////////////
// -- Main Menu

function ibank_display_main()
{
  global $playerinfo,$account,$sectorinfo;
  ibank_display_head($header = "Main Terminal");
  echo '
    <TR>
    <td bgcolor="#DDFAFF"><font size=-2 face="Arial,Helvetica,sans-serif" color="Navy"><b>Choice</b></font></td>
    <TD bgcolor="#DDFAFF" colspan=3><font size=-2 face="Arial,Helvetica,sans-serif" color="Navy"><b>Description</b></font></TD>
    <TR>
    <TD>&nbsp;<a href=ibank.php3?op=1>Own Account</a></TD>
    <TD colspan=3>Deposit / Withdrawal / Loans / Repayments</TD>
    </TR>
    <TR>
    <TD>&nbsp;<a href=ibank.php3?op=2>Transfers</a></TD>
    <TD colspan=3>Planet to Ship to Planet Transfers</TD>
    <TR>
    <TD>&nbsp;<a href=ibank.php3?op=3>Payments</a></TD>
    <TD colspan=3>Write a check to another ships captain</TD>
    <TR>
    ';
    /* TO be done later
    echo '  <TD>&nbsp;<a href=ibank.php3?op=4>Realestate</a></TD>
    <TD colspan=3>Buy / Sell / Planetary Quotes &amp; Valuations</TD>
    ';
    */
    // Figure out the default exit.
    // So user dont go completely confused...
    if    ($sectorinfo[port_type]=="special" && $sectorinfo[planet_owner] ==$playerinfo[ship_id]) 
    { $exit = $interface; }
    elseif  ($sectorinfo[port_type]=="special") 
    { $exit = "port.php3"; }
    elseif  ($sectorinfo[planet_owner] ==$playerinfo[ship_id]) 
    { $exit = "planet.php3"; }
    else
    { $exit = $interface; }
    
    ibank_display_footer($exit); // Use Defaults
}

//////////////////////////////////////////////////////////////////////////////////
// -- Create the IGB Account for firsttime user

function ibank_display_createaccount()
{
  global $playerinfo,$account,$deposit,$create;
  if(!isset($create))
  {
    ibank_display_head("Create A New Account"); // Use Defaults
    echo '<form action="ibank.php3" method="post">
      <tr>
      <td colspan="4" bgcolor="#FFFFCA">To create your bank account select the number of credits to deposit and press "Make Account"</td>
      </tr>
      <TR>
      <TD>1.</TD>
      <TD>Deposit</TD>
      <TD colspan=2><input type="text" name="deposit" value="0" size="10" maxlength="20"></TD>
      </TR>
      <TR>
      <TD colspan=4 align=center><input type="submit" name="create" value="Make Account"></TD>
      </TR>
      <input type="hidden" name="op" value="5">
      </form>
      '; 
      ibank_display_footer(); // Use Defaults
  }
  else
  { 
    // Normalize Deposit
    if($deposit < 0)
      $deposit = 0;
    // Lets make the account and deposit the credits into it.
    mysql_query("INSERT INTO ibank_accounts (id,ballance,loan,ibank_shareholder,ibank_employee,ibank_owner) VALUES ($playerinfo[ship_id],$deposit,0,0,0,0);");
    mysql_query("UPDATE ships SET credits=credits-$deposit WHERE ship_id = $playerinfo[ship_id]");
    // SInce we made changes lets update account data
    ibank_refreshdata();
    ibank_display_head("Create A New Account - Completed"); // Use Defaults
    echo '<form action="ibank.php3" method="post">
      <tr>
      <td colspan="4" align=center bgcolor="#FFFFCA">Account Created</td>
      </tr> 
      <tr>
      <td colspan="4" align=center bgcolor="#FFFFCA">Thank you for using IGB</td>
      </tr>
      <input type="hidden" name="op" value="5">
      </form>
      ';
      ibank_display_footer(); // Use Defaults
  }
}

//////////////////////////////////////////////////////////////////////////////////
// -- Manage Users Account

function ibank_display_ownaccount()
{
  global $username,$playerinfo,$account,$deposit,$withdraw,$loan,$payloan,$ibank_ownaccount_info,$ibank_loanfactor,$ibank_loanlimit;
  if(isset($deposit))
  {
    // Normalize Deposit
    if($deposit < 0)
      $deposit = 0;
    $deposit = round($deposit);
    if($deposit <= $playerinfo[credits])
    {
      // Lets make the account and deposit the credits into it.
      mysql_query("UPDATE ibank_accounts SET ballance = ballance + $deposit WHERE id=$playerinfo[ship_id]");
      mysql_query("UPDATE ships SET credits=credits-$deposit where ship_id=$playerinfo[ship_id]");
      // SInce we made changes lets update account data
      ibank_refreshdata();
      ibank_display_head("Manage Your Account - Deposit");
      echo '<tr><td align="center" colspan=4>Your Deposit is Done!</td></tr>';
      echo '<tr><td align="center" colspan=4><b>Thank you for using IGB!</b></td></tr>';
      ibank_display_footer();
    }
    else
    {
      ibank_display_head("Manage Your Account - Deposit");
      echo '<tr><td align="center" colspan=4><b><font color="Red">NOT ENOUGH CREDITS TO COMPLETE TRANSACTION</font></b></td></tr>';
      ibank_display_footer("ibank.php3?op=1");
    }
  } 
  elseif(isset($withdraw))
  {
    // Normalize withdraw
    if($withdraw < 0)
      $withdraw = 0;
    $withdraw = round($withdraw);
    if($withdraw <= $account[ballance])
    {   
      // Lets make the account and deposit the credits into it.
      mysql_query("UPDATE ibank_accounts SET ballance = ballance - $withdraw WHERE id=$playerinfo[ship_id]");
      mysql_query("UPDATE ships SET credits=credits+$withdraw where ship_id=$playerinfo[ship_id]");
      // SInce we made changes lets update account data
      ibank_refreshdata();
      ibank_display_head("Manage Your Account - Withdrawal");
      echo '<tr><td align="center" colspan=4>Your Withdrawal is Done!</td></tr>';
      echo '<tr><td align="center" colspan=4><b>Thank you for using IGB!</b></td></tr>';
      ibank_display_footer();
    }
    else
    {
      ibank_display_head("Manage Your Account - Withdrawal");
      echo '<tr><td align="center" colspan=4><b><font color="Red">NOT ENOUGH CREDITS TO COMPLETE TRANSACTION</font></b></td></tr>';
      ibank_display_footer("ibank.php3?op=1");
    }
  } 
  elseif(isset($loan))
  {
    // Normalize loan
    if($loan < 0)
      $loan = 0;
    $loan = round($loan);
    $biggestloan = round(($account[ballance] - $account[loan]) * $ibank_loanfactor);
    if($biggestloan > $ibank_loanlimit)
    { $biggestloan = ($ibank_loanlimit - $account[loan]); }
    if($biggestloan <= 0)
    { $biggestloan = 0; }
    if($loan <= $biggestloan && $biggestloan > 0)
    {
      // Lets make the account and deposit the credits into it.
      mysql_query("UPDATE ibank_accounts SET ballance = ballance + $loan WHERE id=$playerinfo[ship_id]");
      mysql_query("UPDATE ibank_accounts SET loan = loan + $loan WHERE id=$playerinfo[ship_id]");
      // SInce we made changes lets update account data
      $laccount = mysql_query ("SELECT * from ibank_accounts WHERE id=$playerinfo[ship_id]");
      $account=mysql_fetch_array($laccount);
      ibank_display_head("Manage Your Account - Loan");
      echo '<tr><td align="center" colspan=4>Your Loan Application was Accepted!</td></tr>';
      echo '<tr><td align="center" colspan=4><b>Thank you for using IGB!</b></td></tr>';
      ibank_display_footer();
    }
    else
    {
      ibank_display_head("Manage Your Account - Loan");
      echo '<tr><td align="center" colspan=4><b>You may not take out a loan greater than '.$biggestloan.' credits.</b></td></tr>';
      echo '<tr><td align="center" colspan=4><b>You can <font color="Red">not</font> take out a new loan against ballance created by a previous loan.<br>Unless you repay part of the previous loan, then you can take out a new loan on the difference (Loans - Account Ballance = Available Ballance to loan against.)<br>Please note that Ship Treassury ballance does not apply only what you have in your IGB account.</b></td></tr>';   
      echo '<tr><td align="center" colspan=4><b><font color="Red">LOAN APPLICATION NOT ACCEPTED</font></b></td></tr>';
      ibank_display_footer("ibank.php3?op=1");
    }
  }
  elseif(isset($payloan))
  {
    // Normalize payloan
    if($payloan < 0)
      $payloan = 0;
    $payloan = round($payloan);
    if($payloan > $account[loan])
    { $payloan = $account[loan]; }
    
    if($payloan <= $account[ballance] && $payloan <= $account[loan] )
    {
      // Lets make the account and deposit the credits into it.
      mysql_query("UPDATE ibank_accounts SET ballance = ballance - $payloan WHERE id=$playerinfo[ship_id]");
      mysql_query("UPDATE ibank_accounts SET loan = loan - $payloan WHERE id=$playerinfo[ship_id]");
      // SInce we made changes lets update account data
      $laccount = mysql_query ("SELECT * from ibank_accounts WHERE id=$playerinfo[ship_id]");
      $account=mysql_fetch_array($laccount);
      ibank_display_head("Manage Your Account - Loan Repayment");
      echo '<tr><td align="center" colspan=4>Your Loan Repayment is Done!</td></tr>';
      echo '<tr><td align="center" colspan=4><b>Thank you for using IGB!</b></td></tr>';
      ibank_display_footer();
    }
    else
    {
      ibank_display_head("Manage Your Account - Loan");
      echo '<tr><td align="center" colspan=4><b><font color="Red">NOT ENOUGH CREDITS TO COMPLETE TRANSACTION</font></b></td></tr>';
      ibank_display_footer("ibank.php3?op=1");
    }
  } 
  else 
  {
    ibank_display_head("Manage Your Account"); // Use Defaults
    echo '
      <TR>
      <TD colspan=2><form action="ibank.php3" method="post"><table width="100%" border="0" cellspacing="0" cellpadding="1" align="center" bgcolor="Black"><tr><td><table width="100%" border="0" cellspacing="0" cellpadding="2" align="center" bgcolor="White"><tr>
      <td>Deposit</td><td><input type="text" name="deposit" value="0" size="10" maxlength="20"></td><td><input type="submit" value="DO"></td>
      </tr><tr><td colspan=3>Take credits from Ship account and Deposit into IGB Account.</td></tr></table></td></tr></table><input type="hidden" name="op" value="1"></form></TD>
      <TD rowspan=4 valign="top">&nbsp;</td>
      <TD rowspan=4 valign="top">
      <table width="100%" border="0" cellspacing="0" cellpadding="1" align="center" bgcolor="Black"><tr><td><table width="100%" border="0" cellspacing="0" cellpadding="2" align="center" bgcolor="White"><tr>
      <td>
      <font face="Arial,Helvetica,sans-serif" size="-1"><font color="Maroon"><b>Information:</b></font><br>
      '.$ibank_ownaccount_info.'
      </font>
      </td></tr></table></td></tr></table>
      </td>
      </tr>
      <tr>
      <TD colspan=2><form action="ibank.php3" method="post"><table width="100%" border="0" cellspacing="0" cellpadding="1" align="center" bgcolor="Black"><tr><td><table width="100%" border="0" cellspacing="0" cellpadding="2" align="center" bgcolor="White"><tr>
      <td>Withdraw</td><td><input type="text" name="withdraw" value="0" size="10" maxlength="20"></td><td><input type="submit" value="DO"></td>
      </tr><tr><td colspan=3>Take credits from IGB Account and deposit into Ship account.</td></tr></table></td></tr></table><input type="hidden" name="op" value="1"></form></TD>
      </tr>
      <tr>
      <TD colspan=2><form action="ibank.php3" method="post"><table width="100%" border="0" cellspacing="0" cellpadding="1" align="center" bgcolor="Black"><tr><td><table width="100%" border="0" cellspacing="0" cellpadding="2" align="center" bgcolor="White"><tr>
      <td>Loan</td><td><input type="text" name="loan" value="0" size="10" maxlength="20"></td><td><input type="submit" value="DO"></td>
      </tr><tr><td colspan=3>Take a loan and Deposit credits into IGB Account.</td></tr></table></td></tr></table><input type="hidden" name="op" value="1"></form></TD>
      </tr> 
      <tr>
      <TD colspan=2><form action="ibank.php3" method="post"><table width="100%" border="0" cellspacing="0" cellpadding="1" align="center" bgcolor="Black"><tr><td><table width="100%" border="0" cellspacing="0" cellpadding="2" align="center" bgcolor="White"><tr>
      <td>Pay Loan</td><td><input type="text" name="payloan" value="0" size="10" maxlength="20"></td><td><input type="submit" value="DO"></td>
      </tr><tr><td colspan=3>Take credits from IGB Account and Repay Loan.</td></tr></table></td></tr></table><input type="hidden" name="op" value="1"></form></TD>
      </tr>   
      
      '; 
      ibank_display_footer(); // Use Defaults
  }
}

//////////////////////////////////////////////////////////////////////////////////
// -- Planet - IGB - Planet Transfers
// DAF - This one need rewrite bad!

function ibank_display_transfers()
{
  global $username,$playerinfo,$account,$payto,$amount,$confirmed,$ibank_paymentfee,$direction;
  if(!isset($payto)) {
    $lresult = mysql_query ("SELECT * from universe WHERE planet_owner=$playerinfo[ship_id] ORDER BY planet_name ASC");
  } else {
    $lresult = mysql_query ("SELECT * from universe WHERE planet_owner=$playerinfo[ship_id] AND sector_id = $payto");
  }
  
  echo '<form action="ibank.php3" method="post">';
  ibank_display_head("Transfers");
  $num_planets = mysql_num_rows($lresult);
  
  if(!isset($payto))
  {
    if($num_planets > 0)
    {
      echo '<TR>
        <TD>Send credits <input type="text" name="amount" value="0" size="10" maxlength="15"></TD>
        <TD><input type="radio" name="direction" value="to" checked>To or <input type="radio" name="direction" value="from">From</TD>
        <TD>Planet:</TD>
        <TD><select name="payto">'; 
        
        for ($i=1; $i<=$num_planets ; $i++)
        {
          $row=mysql_fetch_array($lresult);
          echo "<option value=\"$row[sector_id]\">$row[planet_name]</option>";
        }
        echo '</select></TD></TR>
          <tr><td colspan=4 align=center><input type="submit" value="MAKE TRANSFER"></td></tr>
          <TR>';
    }
    else
    {
      echo '<tr><td colspan=4 align=center><font color="Red"><b>YOU HAVE ZERO PLANETS TO MAKE TRANSFERS TO / FROM</b></font></td></tr>';
    }
  }
  else 
  {
    // Normalize amount
    if($amount < 0)
      $amount = 0;
    
    $row=mysql_fetch_array($lresult);
    $fee = $ibank_paymentfee * $amount;
    $feepct = $ibank_paymentfee * 100;
    $totalamount = round($fee + $amount);
    

    if(isset($confirmed) && ($totalamount<$playerinfo[credits]))
    {
      // Payment is confirmed
      // Lets actually do it
	  
	  
      if($direction == "to")
      {
        @playerlog($ibank_owner,"IGB Interplanetary proceeds from $playerinfo[character_name] to $row[planet_name] of ".number_format($amount)." credits");
        $query = "UPDATE ibank_accounts SET ballance=ballance+$fee where id=$ibank_owner";
        mysql_query("$query");  
        $query = "UPDATE ibank_accounts SET ballance=ballance-$totalamount where id=$playerinfo[ship_id]";
        mysql_query("$query");
        $query = "UPDATE universe SET planet_credits=planet_credits+$amount where sector_id='$payto'";
        mysql_query("$query");    
        // Since we made changes lets update playerinfo;
        ibank_refreshdata();      
        echo '
          <tr>
          <td colspan=3>'.$planetinfo[planet_name].' has recieved </td><td align="right">'.number_format(round($amount)).' credits.</td>
          </tr>
          <tr>
          <td colspan="4" align="center"><b>Thank you for using IGB.</b></td>
          </tr>
          ';  
      }
      elseif($direction == "from")
      {
	  	  $testresult = mysql_query ("SELECT sector_id,planet_owner from universe WHERE sector_id='$payto'");
		  $testrow=mysql_fetch_array($testresult);
		  if($playerinfo[ship_id] = $testrow[planet_owner])
		  {
	        @playerlog($ibank_owner,"IGB Interplanetary proceeds from $playerinfo[character_name] to $row[planet_name] of ".number_format($amount)." credits");
	        $query = "UPDATE ibank_accounts SET ballance=ballance+$fee where id=$ibank_owner";
	        mysql_query("$query");  
	        $query = "UPDATE ibank_accounts SET ballance=ballance+$amount where id=$playerinfo[ship_id]";
	        mysql_query("$query");
	        $query = "UPDATE universe SET planet_credits=planet_credits-$totalamount where sector_id=$payto";
	        mysql_query("$query");    
	        // Since we made changes lets update playerinfo;
	        ibank_refreshdata();
	        echo '
	          <tr>
	          <td colspan=3>'.$planetinfo[planet_name].' has transfered </td><td align="right">'.number_format(round($amount)).' credits.</td>
	          </tr>
	          <tr>
	          <td colspan="4" align="center"><b>Thank you for using IGB.</b></td>
	          </tr>
	          ';  
			}
	      else
	      {
	        echo '
	          <tr>
	          <td colspan="4" align="center"><font color="Red"><b>You Can not Transfer From Planets You do not own.</b></font></td>
	          </tr>
	          ';
	      }		
      }
      else
      {
        echo '
          <tr>
          <td colspan="4" align="center"><font color="Red"><b>DO NOT MESS WITH IGB.</b></font></td>
          </tr>
          ';
      }
    }
    else
    {
      // Payment not yet confirmed
      // Let the user confirm or cancel
      // Since we made changes lets update playerinfo;
      $result2 = mysql_query ("SELECT * from universe WHERE planet_owner=$playerinfo[ship_id] AND sector_id = $payto");
      $planetinfo=mysql_fetch_array($result2);
      
      if($direction == "to")
      { // The transfer is to a planet
        echo '
          <tr>
          <td colspan=4>Are you ready to transfer <b>'.number_format($amount).'</b> credits to <b>'.$planetinfo[planet_name].'</b></td>
          </tr>
          <tr>
          <td colspan="4" bgcolor="#FFFFCA">- PAYMENT SLIP -</td>
          </tr>
          <tr>
          <td>1.</td>
          <td colspan=2>Transfer amount</td>
          <td align="right">'.number_format($amount).' credits.</td>
          </tr>   
          <tr>
          <td>2.</td>
          <td colspan=2>Banking fee is '.$feepct.'%</td>
          <td align="right">'.number_format($fee).' credits.</td>
          </tr>
          <tr>
          <td>3.</td>
          <td colspan=2>Total withdrawal from your account</td>
          <td align="right">'.number_format($totalamount).' credits.</td>
          </tr>
          <tr>';
          if($totalamount > $account[credits])
          {
            echo '    <td colspan="4" align="center"><b><font color="Red">*** INSUFFICIENT FUNDS TO COMPLETE TRANSFER ***</font></b></td>';
          }
          else
          {
            echo '    <td colspan="4" align="center"><input type="submit" name="confirmed" value="Make Transfer"></td>';
          }
          echo '  </tr>   
            <input type="hidden" name="amount" value="'.$amount.'">
            <input type="hidden" name="payto" value="'.$payto.'">
            <input type="hidden" name="direction" value="'.$direction.'">
            ';  
      }
      else
      { // The transfer is from a planet
        echo '
          <tr>
          <td colspan=4>Are you ready to recieve transfer of <b>'.number_format($amount).'</b> credits from <b>'.$planetinfo[planet_name].'</b></td>
          </tr>
          <tr>
          <td colspan="4" bgcolor="#FFFFCA">- PAYMENT SLIP -</td>
          </tr>
          <tr>
          <td>1.</td>
          <td colspan=2>Transfer amount</td>
          <td align="right">'.number_format($amount).' credits.</td>
          </tr>   
          <tr>
          <td>2.</td>
          <td colspan=2>Banking fee is '.$feepct.'%</td>
          <td align="right">'.number_format($fee).' credits.</td>
          </tr>
          <tr>
          <td>3.</td>
          <td colspan=2>Total withdrawal from planets account</td>
          <td align="right">'.number_format($totalamount).' credits.</td>
          </tr>
          <tr>';
          if($totalamount > $planetinfo[planet_credits])
          {
            echo '    <td colspan="4" align="center"><b><font color="Red">*** INSUFFICIENT PLANETARY FUNDS TO COMPLETE TRANSFER ***</font></b></td>';
          }
          else
          {
            echo '    <td colspan="4" align="center"><input type="submit" name="confirmed" value="Make Transfer"></td>';
          }
          echo '  </tr>   
            <input type="hidden" name="amount" value="'.$amount.'">
            <input type="hidden" name="payto" value="'.$payto.'">
            <input type="hidden" name="direction" value="'.$direction.'">
            ';  
      }
    }
}

echo '  <input type="hidden" name="op" value="2">
</form>
'; 
ibank_display_footer();
}

//////////////////////////////////////////////////////////////////////////////////
// -- Make Check / Payments to Other Captains
//    Makes life easier for those who must have multiple accounts,
//    ALso makes personal loans, Bribes etc possible.
function ibank_display_payments()
{
  global $playerinfo,$payto,$amount,$confirmed,$ibank_paymentfee,$account;
  if(!isset($payto)) {
    $lresult = mysql_query ("SELECT ship_id,ship_name,ship_destroyed,character_name FROM ships WHERE ship_destroyed !='Y' AND ship_id != $playerinfo[ship_id] ORDER BY character_name ASC");
  } else {
    $lresult = mysql_query ("SELECT ship_id,ship_name,ship_destroyed,character_name FROM ships WHERE ship_id = $payto");
  }
  
  echo '<form action="ibank.php3" method="post">';
  ibank_display_head("Payments");
  if(!isset($payto))
  {
    echo '<TR>
      <TD>Pay to:</TD>
      <TD><select name="payto">'; 
      $num_players = mysql_num_rows($lresult);
    for ($i=1; $i<=$num_players ; $i++)
    {
      $row=mysql_fetch_array($lresult);
      echo "<option value=\"$row[ship_id]\">$row[character_name]</option>";
    }
    echo '</select></TD>
      <TD>Amount: <input type="text" name="amount" value="0" size="10" maxlength="15"></TD>
      <TD><input type="submit" value="PAY"></TD></TR>
      <TR>';
  }
  else 
  {
    // Normalize amount
    if($amount <= 0)
      $amount = 0;
    $row=mysql_fetch_array($lresult);
    $fee = $ibank_paymentfee * $amount;
    $feepct = $ibank_paymentfee * 100;
    $totalamount = round($fee + $amount);
    if(isset($confirmed) && ($totalamount<$playerinfo[credits]))
    {
      // Payment is confirmed
      // Lets actually do it
      @playerlog($row[ship_id],"You recieved a IGB Money wire from $playerinfo[character_name] of ".number_format($amount)." credits");
      $query = "UPDATE ships SET credits=credits+$amount where ship_id=$row[ship_id]";
      mysql_query("$query");
      @playerlog($playerinfo[ship_id],"You sent a IGB Money wire to $row[character_name] of ".number_format($amount)." credits"); 
      $query = "UPDATE ibank_accounts SET ballance=ballance-$totalamount where id=$playerinfo[ship_id]";
      mysql_query("$query");
      @playerlog($ibank_owner,"IGB Money wire proceeds from $playerinfo[character_name] to $row[character_name] of ".number_format($amount)." credits");
      $query = "UPDATE ibank_accounts SET ballance=ballance+$fee where id=$ibank_owner";
      mysql_query("$query");
      // Since we made changes lets update playerinfo;
      $result = mysql_query ("SELECT * FROM ships WHERE email='$username'");
      $playerinfo=mysql_fetch_array($result);
      
      echo '
        <tr>
        <td colspan=3>'.$row[character_name].' has recieved </td><td align="right">'.number_format(round($amount)).' credits.</td>
        </tr>
        <tr>
        <td colspan="4" align="center"><b>Thank you for using IGB.</b></td>
        </tr>
        ';
    }
    else
    {
      // Payment not yet confirmed
      // Let the user confirm or cancel
      echo '
        <tr>
        <td colspan=4>Are you ready to pay <b>'.number_format($amount).'</b> credits to <b>'.$row[character_name].'</b></td>
        </tr>
        <tr>
        <td colspan="4" bgcolor="#FFFFCA">- PAYMENT SLIP -</td>
        </tr>
        <tr>
        <td>1.</td>
        <td colspan=2>Payment amount</td>
        <td align="right">'.number_format($amount).' credits.</td>
        </tr>   
        <tr>
        <td>2.</td>
        <td colspan=2>Banking fee is '.$feepct.'%</td>
        <td align="right">'.number_format($fee).' credits.</td>
        </tr>
        <tr>
        <td>3.</td>
        <td colspan=2>Total withdrawal from your account</td>
        <td align="right">'.number_format($totalamount).' credits.</td>
        </tr>
        <tr>
        ';
        if($totalamount > $account[ballance])
        {
          echo '    <td colspan="4" align="center"><b><font color="Red">*** INSUFFICIENT FUNDS TO COMPLETE PAYMENT ***</font></b></td>';
        }
        else
        {
          echo '    <td colspan="4" align="center"><input type="submit" name="confirmed" value="Make Payment"></td>';
        }
        echo '  </tr>   
          <input type="hidden" name="amount" value="'.$amount.'">
          <input type="hidden" name="payto" value="'.$payto.'">
          ';  
    }
  }
  echo '  <input type="hidden" name="op" value="3">
    </form>
    '; 
    ibank_display_footer();
}

//////////////////////////////////////////////////////////////////////////////////
// -- The program begins here
//    Get all needed data
ibank_refreshdata();
bigtitle();

if  ($sectorinfo[port_type]=="special" || $sectorinfo[planet_owner] ==$playerinfo[ship_id]) {
  if ($sectorinfo[sector_id]=="0") 
  {
    echo "Welcome to the Intergalactic Bank Main Office!<BR>Logged on...<BR>";
  } else {
    echo "Welcome to this Intergalactic Bank terminal.<BR>Logged on...<BR>";
  }
  if($account[id] > 0)
  {   // User has an account lets proceed
    // 
  }
  else 
  { // User has no account send him to account signup
    $op = 5;
  }
  
  if(!isset($op)) { $op = 0; }
  switch($op)
  {
  case 1: // Own account
    ibank_display_ownaccount();
    break;
  case 2: // Transfers
    ibank_display_transfers();
    break;
  case 3: // Payments
    ibank_display_payments();
    break;
  case 4: // Realestate
    ibank_display_realestate();
    break;
  case 5: // Realestate
    ibank_display_createaccount();
    break;
  default:// Main menu
    ibank_display_main();
    break;
  }
}
else 
{
  echo '<font color="Red"><b>You do not have access to the bank terminal here!</b></font><BR><BR>';
}

mysql_query("UNLOCK TABLES");
include("footer.php3");  

// -- EOF
//////////////////////////////////////////////////////////////////////////////////
?>
