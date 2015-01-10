<?php

require_once('../Connections/database_functions.php');
require_once('../Connections/YBDB.php');
mysql_select_db($database_YBDB, $YBDB);

$change_fund = CHANGE_FUND;

	// Is there a current shop?
	if(isset($_POST['shop_exist'])) {
		if(current_shop_by_ip()>=1) echo "current_shop"; else echo "no_shop";
	}

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

	// If payment_type check is selected - return check number if exists 
	if (isset($_POST['check_number'])) {
		
			$query = 'SELECT check_number FROM transaction_log WHERE transaction_id="' . $_POST['transaction_id'] . '";';
			$sql = mysql_query($query, $YBDB) or die(mysql_error());
			$result = mysql_fetch_assoc($sql);
			echo json_encode($result);		
		
	}

	// Editable Change Fund
	if(isset($_POST['editable_change'])) {
		
		$transaction_id = split('_', $_POST['id'], 1);
		$query = 'UPDATE transaction_log set change_fund="' . $_POST['editable_change'] . '" WHERE transaction_id="' . $transaction_id[0] . '";';
		$result = mysql_query($query, $YBDB) or die(mysql_error());
		$send_back = array(
			"changed_change" => $_POST['editable_change'], 
			"change" => $change_fund,
		);
		echo json_encode($send_back);	
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
					$result_obj[$deposit[$key]] = $result; 
				} else {
					$query = 'SELECT  SUM(IF(payment_type="check", amount, 0)) AS "check",  
							   			SUM(IF(payment_type="credit", amount, 0)) AS "credit",  
							   			SUM(IF(payment_type="cash", amount, 0)) AS "cash"
											FROM transaction_log WHERE paid=1 AND transaction_id <' . $deposit[$key] . ';'; 
					$sql = mysql_query($query, $YBDB) or die(mysql_error());
					$result = mysql_fetch_assoc($sql);				
					$result_obj[$deposit[$key]] = $result;	
				}

			}
			echo json_encode($result_obj);			
			
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
						$result_obj[$transaction_id[$key]] = $result; 	
					}									
				
				} // foreach
				echo json_encode($result_obj);								
		} // end  else for invisibles

	} // End Deposit Calculator

?>