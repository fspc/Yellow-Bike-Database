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
		
		$visible_count = count($_POST['deposit']);
		$c = $visible_count - 1;		
		$deposit = $_POST['deposit'];

		$query = 'SELECT COUNT(transaction_type) AS "count" FROM transaction_log WHERE transaction_type="Deposit";';
		$sql = mysql_query($query, $YBDB) or die(mysql_error());
		$result = mysql_fetch_assoc($sql);
			
		
		if ( $visible_count == $result["count"] ) { // 1 or more deposits, and all deposits are visible
	
			foreach ( $deposit as $key => $value ) {
				if ( $c > $key ) {				
					$query = 'SELECT  SUM(IF(payment_type="check", amount, 0)) AS "check",  
				    			SUM(IF(payment_type="credit", amount, 0)) AS "credit",  
				    			SUM(IF(payment_type="cash", amount, 0)) AS "cash"  
				    			FROM transaction_log WHERE paid=1 AND transaction_id <' . $deposit[$key] . ' AND transaction_id >' 
				    			. $deposit[$key + 1] . ';';
					$sql = mysql_query($query, $YBDB) or die(mysql_error());
					$result = mysql_fetch_assoc($sql);
				} else {
					$query = 'SELECT  SUM(IF(payment_type="check", amount, 0)) AS "check",  
							   			SUM(IF(payment_type="credit", amount, 0)) AS "credit",  
							   			SUM(IF(payment_type="cash", amount, 0)) AS "cash"
											FROM transaction_log WHERE paid=1 AND transaction_id <' . $deposit[$key] . ';'; 
					$sql = mysql_query($query, $YBDB) or die(mysql_error());
					$result = mysql_fetch_assoc($sql);				
						
				}
			
				echo json_encode($result);

			}
		}  else {  // more deposits than visible

				$limit = $visible_count + 1;
				$query = 'SELECT transaction_id FROM transaction_log 
							WHERE transaction_type="Deposit" ORDER BY transaction_id DESC LIMIT ' . $limit . ';';
				$sql = mysql_query($query, $YBDB) or die(mysql_error());
				
				while ( $result = mysql_fetch_assoc($sql) ) {
					$transaction_id[] = $result['transaction_id'];					
				} 
				
				foreach ( $transaction_id as $key => $value ) {
				
						if ($key <= $c) {
						$query = 'SELECT  SUM(IF(payment_type="check", amount, 0)) AS "check",  
					    			SUM(IF(payment_type="credit", amount, 0)) AS "credit",  
					    			SUM(IF(payment_type="cash", amount, 0)) AS "cash"  
					    			FROM transaction_log WHERE paid=1 AND transaction_id <' . $transaction_id[$key] . ' AND transaction_id >' 
					    			. $transaction_id[$key + 1] . ';';
						$sql = mysql_query($query, $YBDB) or die(mysql_error());
						$result = mysql_fetch_assoc($sql);
						echo json_encode($result);
					}
				
				} // foreach								
		} // end  else for invisibles

	}

?>