<?

  if(!isset($swordfish) || $swordfish != $adminpass)
    die("Script has not been called properly");

  echo "<B>IBANK</B><BR><BR>";
  $ibank_result = mysql_query("SELECT * from ibank_accounts");
  $num_accounts = mysql_num_rows($ibank_result);

  if($num_accounts > 0)
  {
    for($i=1; $i<=$num_accounts ; $i++)
    {
	    $account = mysql_fetch_array($ibank_result);
	    // Check if the user actually has a balance on his acount
	    if($account[balance] > 0)
	    {
		    // Calculate Interest
		    $interest = round($ibank_interest * $account[balance]);
		    // Calculate Mortage
		    $mortage_interest = round($ibank_loaninterest * $account[loan]);
		    $mortage_payment = round($mortage_interest * 2);
		    // Update users bank account
		    mysql_query("UPDATE ibank_accounts SET balance = balance + $interest WHERE ship_id = $account[ship_id]");
		    // Check if the user has a loan
		    if($account[loan] > 0)
		    {
			    // Decide what type of repayment should be done.
			    if($account[balance] < $mortage_payment)
			    {	// The user don't have enough money on his IGB account then we start collecting from his ship account
				    // at twice the cost, for the extra trouble. This is in the Information at Manage own account.
				    $extrafee = $mortage_payment * 2;
				    mysql_query("UPDATE ibank_accounts SET loan = loan - $mortage_interest WHERE ship_id = $account[ship_id]");
				    mysql_query("UPDATE ships SET credits = credits - $extrafee WHERE ship_id = $account[ship_id]");
			    }
			    else
			    {	// Normal repayment / mortage
				    mysql_query("UPDATE ibank_accounts SET balance = balance - $mortage_payment, loan = loan - $mortage_interest WHERE ship_id = $account[ship_id]");
			    }
		    }
		    echo "ID: $account[ship_id] Balance: $account[balance] Interest: $interest - Loan: $account[loan] Mortage: $mortage_payment<br>\n";
	    }
    }
  }

?>