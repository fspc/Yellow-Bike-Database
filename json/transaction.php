<?php

require_once('../Connections/YBDB.php');
mysql_select_db($database_YBDB, $YBDB);


	// update whether paid or not
	if(isset($_POST['paid'])) {
			if ($_POST['paid'] == 1) {			
			
				$query = "UPDATE transaction_log SET paid=1 WHERE transaction_id=" . $_POST['transaction_id'] . ";";				 
				$result = mysql_query($query, $YBDB) or die(mysql_error());
		
			} elseif($_POST['paid'] == 0) {
	  			
			  	$query = "UPDATE transaction_log SET paid=0 WHERE transaction_id=" . $_POST['transaction_id'] . ";";
			  	$result = mysql_query($query, $YBDB) or die(mysql_error());	    
		
			}
	  }

	// update payment type
	if(isset($_POST['payment_type'])) {
		
		$query = 'UPDATE transaction_log SET payment_type="' . 
					$_POST['payment_type'] . '" WHERE transaction_id=' . 
					$_POST['transaction_id'] . ";";				 
		$result = mysql_query($query, $YBDB) or die(mysql_error());
		
	}

	// Deposit Calculator
	if (isset($_POST['deposit'])) {
		print_r($_POST);	
	}

	/*
		 transaction_id, date_startstorage, date,transaction_type, amount, description, sold_to, sold_by, quantity, shop_id, paid
	    
	    Always do transactions from the first visible Deposit to the next Deposit.
	    Variations:

		Based on the results of ..
		SELECT COUNT(transaction_type) FROM transaction_log WHERE transaction_type="Deposit";
		
		However, if there are no invisible deposits go to the end from the last visible deposit, so do a comparison with 
		visible deposits, to find out if there is 1 unique non-visible deposit.
		
		COUNT == 1
      1.  Just beginning to use YBDB:  Calculation for 1 visible Deposit all the way to the first existing transaction

			SELECT  SUM(IF(payment_type="check", amount, 0.00)) AS "Check",  
		   SUM(IF(payment_type="credit", amount, 0.00)) AS "Credit",  
		   SUM(IF(payment_type="cash", amount, 0.00)) AS "Cash"
			FROM transaction_log WHERE paid=1 AND transaction_id < 74;	    
			(return sum)	    
	    
	    COUNT > 1
		 2.  Calculation for 1 or more  visible Deposits to next non-visible Deposit or no non-visible Deposit
		
			If no hidden, loop for visible deposits (#2)) ($v) with last one to end (#1 logic, except use $v variable); else;
			loop for visible deposits to the next hidden deposit (#2).
		
			foreach ( my $v in @visible_deposits ) {
		    
		    SELECT  SUM(IF(payment_type="check", amount, 0.00)) AS "Check",  
		    SUM(IF(payment_type="credit", amount, 0.00)) AS "Credit",  
		    SUM(IF(payment_type="cash", amount, 0.00)) AS "Cash"  
		    FROM transaction_log WHERE paid=1 AND transaction_id < $v AND transaction_id > 
		    (SELECT transaction_id FROM transaction_log WHERE transaction_type="Deposit" ORDER BY transaction_id LIMIT 1);			

			 push @sum, answer;			
			
			}	 				
		
	*/

?>