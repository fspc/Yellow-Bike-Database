<?php

require_once('../Connections/database_functions.php');
require_once('../Connections/YBDB.php');
mysql_select_db($database_YBDB, $YBDB);

$change_fund = CHANGE_FUND;

	// Is there a current shop?
	if(isset($_POST['shop_exist'])) {
		if(current_shop_by_ip()>=1) { 
			echo "current_shop"; 
		} else {
			echo "no_shop";
		}
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

	// Patron who made a transaction not logged in.
	if (isset($_POST['not_logged_in'])) {
		$query = "SELECT CONCAT(contacts.last_name, ', ', contacts.first_name, ' ',contacts.middle_initial) AS full_name, 
					transaction_log.sold_to
					FROM transaction_log, contacts 
					WHERE transaction_id=" . $_POST['transaction_id'] .
					" AND contacts.contact_id = transaction_log.sold_to;";
		 $sql = mysql_query($query, $YBDB) or die(mysql_error());
		 $result = mysql_fetch_assoc($sql);
		 echo json_encode($result);		
	}

	// Anonymous transaction - save and communicate back settings
	if(isset($_POST['anonymous'])) {
		
		if ($_POST['anonymous'] == 1) {
			$query = 'UPDATE transaction_log SET anonymous=1, sold_to=NULL WHERE transaction_id="' . 
						$_POST['transaction_id'] . '";';
			$result = mysql_query($query, $YBDB) or die(mysql_error());
		} else {
			$query = 'UPDATE transaction_log SET anonymous=0 WHERE transaction_id="' . 
						$_POST['transaction_id'] . '";';
			$result = mysql_query($query, $YBDB) or die(mysql_error());		
		}
	} 

	// check if start storage date has been changed since original shop date
	if(isset($_POST['date_startstorage'])) {
		$query = 'SELECT shops.date FROM transaction_log, shops WHERE transaction_id=' . $_POST['transaction_id'] .
					 ' AND transaction_log.shop_id = shops.shop_id;';
		$sql = mysql_query($query, $YBDB) or die(mysql_error());	
		$result = mysql_fetch_assoc($sql);	
		if ($result['date'] != $_POST['date_startstorage']) {
			echo $result['date'];		
		}
	}

	// reset payment_type && amount for storage transaction
	if(isset($_POST['storage_payment_reset'])) {
		
		$query = 'UPDATE transaction_log SET payment_type=NULL, amount=NULL WHERE transaction_id="' . 
					$_POST['transaction_id'] . '";';
		$result = mysql_query($query, $YBDB) or die(mysql_error());
	}

	// populate transaction slider for accounting programs
	if (isset($_POST['transaction_slider'])) {
		$query = 'SELECT transaction_id, IF(amount > 0, "yes", "no") AS "deposited", date 
					FROM transaction_log WHERE transaction_type= "Deposit";';
		$sql = mysql_query($query, $YBDB) or die(mysql_error());
		while ( $result = mysql_fetch_assoc($sql) ) {
					$slider_range[] = $result;					
		}
		
		// this is the first real deposit
		if ( ($slider_range && !$slider_range[1]["transaction_id"]) || ($slider_range && $slider_range[1]["deposited"] == "no") ) { 
			$fake_trans_id = 0;
			$real_trans = $slider_range[0];
			$year = date("Y");
			$slider_range[0] = array("transaction_id" => "$fake_trans_id","deposited" => "yes","date" => "$year-01-01 22:22:22");
			$slider_range[1] = $real_trans;
			echo json_encode($slider_range);
		
		// no real deposits exist				
		} elseif (!$slider_range) {
		// send fake data
			$year = date("Y");
			$slider_range = array
							(	array("transaction_id" => "0","deposited" => "yes","date" => "$year-01-01 22:22:22"),
								array("transaction_id" => "1","deposited" => "yes","date" => "$year-01-02 22:22:22"),
							);
			echo json_encode($slider_range);
		
		// more than 1 deposit exists		
		} else {
			echo json_encode($slider_range);
		}
		
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
							WHERE transaction_type="Deposit" AND transaction_id<=' . $deposit[0] . 
							' ORDER BY transaction_id DESC LIMIT ' . $limit . ';';	
						   
				$sql = mysql_query($query, $YBDB) or die(mysql_error());
				
				while ( $result = mysql_fetch_assoc($sql) ) {
					$transaction_id[] = $result['transaction_id'];					
				} 
				
				foreach ( $transaction_id as $key => $value ) { 
						
					if ($key <= $c && $transaction_id[$key + 1]) {
						$query = 'SELECT  SUM(IF(payment_type="check", amount, 0)) AS "check",  
					    			SUM(IF(payment_type="credit", amount, 0)) AS "credit",  
					    			SUM(IF(payment_type="cash", amount, 0)) AS "cash"  
					    			FROM transaction_log WHERE paid=1 AND transaction_id <' . $transaction_id[$key] . ' AND transaction_id >' 
					    			. $transaction_id[$key + 1] . ';';
						$sql = mysql_query($query, $YBDB) or die(mysql_error());
						$result = mysql_fetch_assoc($sql);
						$result_obj[$transaction_id[$key]] = $result; 	
					} elseif ($key <= $c && !$transaction_id[$key + 1] ) {
						$query = 'SELECT  SUM(IF(payment_type="check", amount, 0)) AS "check",  
						    		SUM(IF(payment_type="credit", amount, 0)) AS "credit",  
						    		SUM(IF(payment_type="cash", amount, 0)) AS "cash"  
						    		FROM transaction_log WHERE paid=1 AND transaction_id <' . $transaction_id[$key] .  ';';
						$sql = mysql_query($query, $YBDB) or die(mysql_error());
						$result = mysql_fetch_assoc($sql);
						$result_obj[$transaction_id[$key]] = $result; 					
					}									
				
				} // foreach
				echo json_encode($result_obj);								
		} // end  else for invisibles

	} // End Deposit Calculator

?>