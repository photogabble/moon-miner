<?

  if (preg_match("/sched_furangee.php/i", $PHP_SELF)) {
      echo "You can not access this file directly!";
      die();
  }

  echo "<B>IBANK</B><BR><BR>";
  $ibank_result = $db->Execute("SELECT * from $dbtables[ibank_accounts]");
  $num_accounts = $ibank_result->RecordCount();

  if($num_accounts > 0)
  {
    for($i=1; $i<=$num_accounts ; $i++)
    {
	    $account = $ibank_result->fields;
	    // Check if the user actually has a balance on his acount
	    if($account[balance] > 0)
	    {
		    // Calculate Interest
		    $interest = round($ibank_interest * $account[balance]);
		    // Calculate Mortage
		    $mortage_interest = round($ibank_loaninterest * $account[loan]);
		    $mortage_payment = round($mortage_interest * 2);
		    // Update users bank account
		    $db->Execute("UPDATE $dbtables[ibank_accounts] SET balance = balance + $interest WHERE ship_id = $account[ship_id]");
		    // Check if the user has a loan
		    if($account[loan] > 0)
		    {
			    // Decide what type of repayment should be done.
			    if($account[balance] < $mortage_payment)
			    {	// The user don't have enough money on his IGB account then we start collecting from his ship account
				    // at twice the cost, for the extra trouble. This is in the Information at Manage own account.
				    $extrafee = $mortage_payment * 2;
				    $db->Execute("UPDATE $dbtables[ibank_accounts] SET loan = loan - $mortage_interest WHERE ship_id = $account[ship_id]");
				    $db->Execute("UPDATE $dbtables[ships] SET credits = credits - $extrafee WHERE ship_id = $account[ship_id]");
			    }
			    else
			    {	// Normal repayment / mortage
				    $db->Execute("UPDATE $dbtables[ibank_accounts] SET balance = balance - $mortage_payment, loan = loan - $mortage_interest WHERE ship_id = $account[ship_id]");
			    }
		    }
		    echo "ID: $account[ship_id] Balance: $account[balance] Interest: $interest - Loan: $account[loan] Mortage: $mortage_payment<br>\n";
	    }
      $ibank_result->MoveNext();
    }
  }

?>