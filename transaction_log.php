<?php

error_reporting(E_STRICT);
require_once('Connections/YBDB.php');
require_once('Connections/database_functions.php'); 

$page_edit_contact = PAGE_EDIT_CONTACT; 
$page_individual_history_log = INDIVIDUAL_HISTORY_LOG;
$storage_period = STORAGE_PERIOD;
$default_transaction_type = DEFAULT_TRANSACTION_TYPE;
$number_of_transactions = NUMBER_OF_TRANSACTIONS;
$change_fund = CHANGE_FUND;
$show_shop_id = SHOW_SHOP_ID;

//transaction ID	
if($_GET['trans_id']>0){
	$trans_id = $_GET['trans_id'];
} else {
	$trans_id =-1;}

//error
switch ($_GET['error']) {
case 'transactioncomplete':
   $error_message = 'Paypal transaction was successful';
   break;
case 'transactioncanceled':	//this is a sample error message.  insert error case here		
   $error_message = 'Paypal transaction was cancelled';
   break;
default:
   $error_message = '';
   break;
}
	
//delete transaction ID	
if($_GET['delete_trans_id']>0){
	$delete_trans_id = $_GET['delete_trans_id'];
} else {
	$delete_trans_id =-1;}
	
//shop_date ($trans_date => SQL) ($trans_date_state => state)
if($_GET['trans_date']>0){
	$trans_date = "AND date <= ADDDATE('{$_GET['trans_date']}',1)" ;
	$trans_date_state = $_GET['trans_date'];
} else {
	$datetoday = current_date();
	$trans_date_state = $datetoday;
	$trans_date ="AND date <= ADDDATE('{$datetoday}',1)"; 
	$trans_date = "";  
}

//dayname ($shop_dayname => SQL) ($shop_dayname_state => state)
if($_GET['shop_dayname']=='alldays'){
	$shop_dayname = '';
	$shop_dayname_state = 'alldays';
} elseif(isset($_GET['shop_dayname'])) {
	$shop_dayname = "AND DAYNAME(date) = '" . $_GET['shop_dayname'] . "'";
	$shop_dayname_state = $_GET['shop_dayname'];
} else {
	$shop_dayname = '';
	$shop_dayname_state = 'alldays';
}	

//Transaction_type ($trans_type => SQL) ($trans_type_state => state)
if($_GET['trans_type']=='all_types'){
	$trans_type = '';
	$trans_type_state = 'all_types';
} elseif(isset($_GET['trans_type'])) {
	$trans_type = "AND transaction_log.transaction_type = '" . $_GET['trans_type'] . "'";
	$trans_type_state = $_GET['trans_type'];
} else {
	$trans_type = '';
	$trans_type_state = 'all_types';
}	

//record_count (SQL or state)
if($_GET['record_count']>0){
	$record_count = $_GET['record_count'];
	$number_of_transactions = $record_count;
} else {
	$record_count = $number_of_transactions;
}

// create a string to remember state
$search_state_array = array(
								"trans_date" => $trans_date_state, 
								"trans_type" => $trans_type_state, 
								"shop_dayname" => $shop_dayname_state, 
								"record_count" => $record_count
							);
$count = count($search_state_array);
$c = 1;
foreach ( $search_state_array as $key => $value ) {
	if (isset($value)) {
		$search_state .= $key . "=" . $value;
		if ($c < $count) {
			$search_state = $search_state . "&";
		}	
	}	
	$c++;
}

// This is the recordset for the list of logged transactions	
// What is seen on the main page.

mysql_select_db($database_YBDB, $YBDB);
$query_Recordset1 = "SELECT *,
DATE_FORMAT(date,'%m/%d/%y (%a)') as date_wday,
CONCAT('$',FORMAT(amount,2)) as format_amount,
CONCAT(contacts.last_name, ', ', contacts.first_name, ' ',contacts.middle_initial) AS full_name,
LEFT(IF(show_startdate, CONCAT(' [',
		DATE_FORMAT(DATE_ADD(date_startstorage,INTERVAL $storage_period DAY),'%W, %M %D'), '] ', transaction_log.description),
		IF(community_bike,CONCAT('Quantity(', quantity, ')  ', transaction_log.description), description)),2000) 
		as description_with_locations
FROM transaction_log
LEFT JOIN contacts ON transaction_log.sold_to=contacts.contact_id
LEFT JOIN transaction_types ON transaction_log.transaction_type=transaction_types.transaction_type_id
WHERE 1=1 {$trans_date} {$shop_dayname} {$trans_type} ORDER BY transaction_id DESC LIMIT  0, $record_count;";
$Recordset1 = mysql_query($query_Recordset1, $YBDB) or die(mysql_error());
$totalRows_Recordset1 = mysql_num_rows($Recordset1);

//Action on form update
$editFormAction = "";


//Form Submit New Transaction===================================================================
if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "FormNew")) {


	$trans_type = $_POST['transaction_type'];
	$shop_id = current_shop_by_ip(); 
	
	mysql_select_db($database_YBDB, $YBDB);
	$query_Recordset5 = "SELECT show_startdate FROM transaction_types WHERE transaction_type_id = \"$trans_type\";";
	//echo $query_Recordset5;
	
	$Recordset5 = mysql_query($query_Recordset5, $YBDB) or die(mysql_error());
	$row_Recordset5 = mysql_fetch_assoc($Recordset5);
	$totalRows_Recordset5 = mysql_num_rows($Recordset5);
	$initial_date_startstorage = $row_Recordset5['show_startdate'];

	// Note: storage of time via current_datetime()) seems futile since updated or customized dates do not have a time	
	if ($initial_date_startstorage) {
		$date_startstorage = current_datetime();
		$date = "NULL";
		$amount = "NULL";
	} else {
		$date_startstorage = "NULL";
		$date = current_datetime();
		$amount = "NULL";
	} //end if


	// gets newest transaction ID
	//mysql_select_db($database_YBDB, $YBDB);
	$query_Recordset4 = "SELECT MAX(transaction_id) as newtrans FROM transaction_log;";
	$Recordset4 = mysql_query($query_Recordset4, $YBDB) or die(mysql_error());
	$row_Recordset4 = mysql_fetch_assoc($Recordset4);
	$totalRows_Recordset4 = mysql_num_rows($Recordset4);
	$newtrans = $row_Recordset4['newtrans'];  //This field is used to set edit box preferences

	$newtrans = $newtrans + 1;
	
	$insertSQL = sprintf("INSERT INTO transaction_log (transaction_type,shop_id, date_startstorage, date, quantity, amount, transaction_id) 
								VALUES (%s,%s, %s ,%s,%s, %s, %s)",
					   GetSQLValueString($_POST['transaction_type'], "text"),
					   GetSQLValueString($shop_id, "text"),
					   GetSQLValueString($date_startstorage, "date"),
					   GetSQLValueString($date, "date"),
					   GetSQLValueString(1, "int"),
					   GetSQLValueString($amount, "float"),
					   GetSQLValueString($newtrans, "int")
					   );
					   
	//echo $insertSQL; 
	//mysql_select_db($database_YBDB, $YBDB);
	$Result1 = mysql_query($insertSQL, $YBDB); // or die(mysql_error());
	// Here is the error to check for: "Column 'shop_id' cannot be null" when there is no shop and create transaction is pressed	
	if (mysql_error()) {
		header("Refresh:0;");
		exit();
	}	
	
	$LoadPage = "?trans_id={$newtrans}";
	header(sprintf("Location: %s", $LoadPage));
} // end Form Submit New Transaction


// Form Close Record
if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "FormEdit") && ($_POST["EditSubmit"] == "Close")) {
	  
	header(sprintf("Location: %s",$editFormAction . "?" . $search_state)); //$editFormAction
}

//Form Edit Record ===============================================================================
if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "FormEdit") && ($_POST["EditSubmit"] == "Save")) {
	

	//Error Correction & good place for jquery 
	$sold_to = (($_POST['sold_to'] == 'no_selection') ? 1268 : $_POST['sold_to'] );
	$sold_by = (($_POST['sold_by'] == 'no_selection') ? 1268 : $_POST['sold_by'] );
	$date_startstorage = date_update_wo_timestamp($_POST['date_startstorage'], $_POST['db_date_startstorage']);
	$date = date_update_wo_timestamp($_POST['date'], $_POST['db_date']);
	$description = $_POST['description'];	
	$check_number = (($_POST['check_number'] == "") ? "" : $_POST['check_number'] );
	$transaction_id = $_POST['transaction_id'];

	// If storage transaction finalized, change transaction_id to most recent transaction_id
	$current_date = current_date();
	$storage_date = split(' ', $_POST['db_date_startstorage']);	
	$transaction_date = split(' ', $_POST['date']);

	
	mysql_select_db($database_YBDB, $YBDB);
	$query = 'SELECT MAX(transaction_id) AS "ti" FROM transaction_log;';
	$sql = mysql_query($query, $YBDB) or die(mysql_error());
	$result = mysql_fetch_assoc($sql);

	// percolate transaction_id for completed storage transactions		
	if($date_startstorage) {

		$new_transaction_id = $result['ti'] + 1;		
		
		// If startstorage > current date (transaction_id stays the same)
		// If startstorage =< current date (transaction_id becomes > than last)
		// not necessary - && $storage_date[0] != $transaction_date[0]
		if ($current_date >= $storage_date[0] ) {
			if($_POST['amount'] != "" && $_POST['payment_type'] != "") {			
				$query = 'UPDATE transaction_log SET transaction_id="' . $new_transaction_id . 
				'" WHERE transaction_id="' . $_POST['transaction_id'] . '";';
				$sql = mysql_query($query, $YBDB) or die(mysql_error());
				$transaction_id = $new_transaction_id;						
			} else {
				$new_transaction_id = "";				
			}
			
		}		

	}



	$query = 'SELECT anonymous FROM transaction_log WHERE transaction_id="' . $transaction_id . '";';
	$sql = mysql_query($query, $YBDB) or die(mysql_error());
	$result = mysql_fetch_assoc($sql);
	
	if($result['anonymous']) {

		// keep the order
		$updateSQL = sprintf("UPDATE transaction_log SET transaction_type=%s, date_startstorage=%s,
																	 date=%s, amount=%s, quantity=%s, description=%s, 
																	 sold_by=%s, 
																	 shop_id=%s, check_number=%s WHERE transaction_id=%s",
						   GetSQLValueString($_POST['transaction_type'], "text"),
						   GetSQLValueString($date_startstorage, "date"),
						   GetSQLValueString($date, "date"),
						   GetSQLValueString($_POST['amount'], "double"),
						   GetSQLValueString($_POST['quantity'], "int"),
						   GetSQLValueString($description, "text"),
						   GetSQLValueString($sold_by, "int"),
						   GetSQLValueString($_POST['shop_id'], "int"),
						   GetSQLValueString($check_number, "text"),
						   GetSQLValueString($transaction_id, "int")
						   );
	} else {
		$updateSQL = sprintf("UPDATE transaction_log SET transaction_type=%s, date_startstorage=%s,
																	 date=%s, amount=%s, quantity=%s, description=%s, 
																	 sold_to=%s, sold_by=%s, 
																	 shop_id=%s, check_number=%s WHERE transaction_id=%s",
						   GetSQLValueString($_POST['transaction_type'], "text"),
						   GetSQLValueString($date_startstorage, "date"),
						   GetSQLValueString($date, "date"),
						   GetSQLValueString($_POST['amount'], "double"),
						   GetSQLValueString($_POST['quantity'], "int"),
						   GetSQLValueString($description, "text"),
						   GetSQLValueString($sold_to, "int"),
						   GetSQLValueString($sold_by, "int"),
						   GetSQLValueString($_POST['shop_id'], "int"),
						   GetSQLValueString($check_number, "text"),
						   GetSQLValueString($transaction_id, "int")
						   );	
	}
	
	//mysql_select_db($database_YBDB, $YBDB);
	$Result1 = mysql_query($updateSQL, $YBDB) or die(mysql_error());	
	
	$trans_id = $transaction_id;
		   
	header(sprintf("Location: %s",$editFormAction . "?trans_id={$trans_id}&" . $search_state));  //$editFormAction

}

//Form Edit Record Delete ===============================================================================
if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "FormEdit") && ($_POST["EditSubmit"] == "Delete")) {

	$trans_id = $_POST['transaction_id'];
	header(sprintf("Location: %s",$editFormAction . "?delete_trans_id={$trans_id}&" . $search_state ));   //$editFormAction
}

//Form Confirm Delete ===============================================================================
if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "ConfirmDelete") && ($_POST["DeleteConfirm"] == "Confirm Delete")) {

	$delete_trans_id = $_POST['delete_trans_id'];
	$insertSQL = "DELETE FROM transaction_log WHERE transaction_id = {$delete_trans_id}";
	mysql_select_db($database_YBDB, $YBDB);
	$Result1 = mysql_query($insertSQL, $YBDB) or die(mysql_error());
	
	header(sprintf("Location: %s", PAGE_SALE_LOG . "?" . $search_state));   //$editFormAction

//Cancel and go back to transaction ================================================================
} elseif ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "ConfirmDelete") && ($_POST["DeleteConfirm"] == "Cancel")) { 
	$delete_trans_id = $_POST['delete_trans_id'];
	header(sprintf("Location: %s", PAGE_SALE_LOG . "?trans_id={$delete_trans_id}&" . $search_state ));   //$editFormAction
}

//Change Date     isset($_POST["MM_update"]) =========================================================
if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "ChangeDate")) {
  $editFormAction = "?trans_date={$_POST['trans_date']}&trans_type={$_POST['trans_type']}&shop_dayname={$_POST['dayname']}&record_count={$_POST['record_count']}";
  header(sprintf("Location: %s",$editFormAction ));   //$editFormAction
}

?>

<?php include("include_header.html"); ?>

<input type="hidden" name="cancel_return" value="transaction_log.php?error=transactioncanceled" />
<table border="0" cellpadding="1" cellspacing="0">
  <tr>
    <td align="left" valign="bottom"><?php echo $error_message ?> </td>
    </tr>
  
  <!-- All elements of edit transaction contained in this row -->  
  <tr>
    <td>
      <table border="1" cellpadding="1" cellspacing="0" bordercolor="#CCCCCC">
        <tr bordercolor="#CCCCCC" bgcolor="#99CC33">
          <td colspan="9" bgcolor="#99CC33"><div align="center"><strong>Bike, Sale and Donation Log</strong></div></td>
		  </tr>
        <?php 		// show delete transaction confirmation =========================================
		if($delete_trans_id <> -1 ) { ?>
        <form method="post" name="FormConfirmDelete" action="<?php echo $editFormAction; ?>">
          <tr bordercolor="#CCCCCC" bgcolor="#CCCC33">
            <td colspan="9"><p><strong>Edit Transaction:
              <input type="submit" name="DeleteConfirm" value="Confirm Delete" />
              <input type="submit" name="DeleteConfirm" value="Cancel" />
              <input type="hidden" name="delete_trans_id" value="<?php echo $delete_trans_id; ?>">
              <input type="hidden" name="MM_insert" value="ConfirmDelete">
              </strong></p>	      </td>
			  </tr>
          </form>
        
	  
	    <?php       //Form to edit preexisting records ================================================
	  } elseif($trans_id <> -1 ) {
	  
	  // Gets data for the transaction being edited
	  // shows transaction if edit link is clicked
	  mysql_select_db($database_YBDB, $YBDB);
	  $query_Recordset2 = "SELECT *,
								DATE_FORMAT(date_startstorage,'%Y-%m-%d') as date_startstorage_day,
								DATE_FORMAT(date,'%Y-%m-%d') as date_day,
								DATE_FORMAT(DATE_ADD(date_startstorage,INTERVAL $storage_period DAY),'%W, %M %D') as storage_deadline,
								DATEDIFF(DATE_ADD(date_startstorage,INTERVAL $storage_period DAY),CURRENT_DATE()) as storage_days_left,
								FORMAT(amount,2) as format_amount
								FROM transaction_log WHERE transaction_id = $trans_id; ";
	  $Recordset2 = mysql_query($query_Recordset2, $YBDB) or die(mysql_error());
	  $row_Recordset2 = mysql_fetch_assoc($Recordset2);
	  $totalRows_Recordset2 = mysql_num_rows($Recordset2);
	  $trans_type = $row_Recordset2['transaction_type'];  //This field is used to set edit box preferences
	  
	  // gets preferences of edit based on Transaction Type
	  //mysql_select_db($database_YBDB, $YBDB);
	  $query_Recordset3 = "SELECT * FROM transaction_types WHERE transaction_type_id = \"$trans_type\";";
	  $Recordset3 = mysql_query($query_Recordset3, $YBDB) or die(mysql_error());
	  $row_Recordset3 = mysql_fetch_assoc($Recordset3);
	  $totalRows_Recordset3 = mysql_num_rows($Recordset3);
	   
	  ?>
        
        <!-- The actual row for edit transactions -->
        <tr bgcolor="#CCCC33">

			<!-- the column for the edit transactions form -->          
          <td colspan="8">
            <form method="post" name="FormEdit" action="<?php echo $editFormAction; ?>">
              <table border="0" cellspacing="0" cellpadding="1">
                  
                   <td></td><td></td>
                   <td>
                    <input id="save_transaction" type="submit" name="EditSubmit" value="Save" align="right">
                    <input id="close_transaction" type="submit" name="EditSubmit" value="Close">
                    <input type="submit" name="EditSubmit" value="Delete">
                    <span id="transaction_start_error"></span>
                    <!-- Save before using paypal ->> -->
                    </td>
				  	</tr>
               
          	<?php if ($show_shop_id) { ?> 
	          	<tr><td>&nbsp;</td>
	            	
			  	    	<td><label>Transaction ID:</label></td>
	               <td><?php echo $row_Recordset2['transaction_id']; ?><em>
	               <?php echo $row_Recordset3['message_transaction_id']; ?></em>
	               </td>
			  	 	
			  	 	</tr>
			  	 	<tr><td>&nbsp;</td>
			  	   
			  	    <td><label for="shop_id">ShopID:</label> </td>
	                <td><input name="shop_id" type="text" id="shop_id" 
	                value="<?php echo $row_Recordset2['shop_id']; ?>" size="6" />
	                </td>
	
			  	  	</tr>
		  	  	<?php } else { ?>
         		<tr><td>&nbsp;</td>
	            	
			  	    	<td><label>Transaction #:</label></td>
	               <td><em><?php echo $row_Recordset2['transaction_id']; ?>
	               -
	               <?php echo $row_Recordset2['shop_id'];
	               		echo "&nbsp;&nbsp" . $row_Recordset3['message_transaction_id'];?></em>
	               <input name="shop_id" type="hidden" id="shop_id" 
	                value="<?php echo $row_Recordset2['shop_id']; ?>" />
	               </td>
			  	 	
			  	 	</tr>		  	  
		  	  	<?php } ?>
		  	  	
                <?php ?>
                <tr><td>&nbsp;</td><td><label for="trans_type_info">Transaction Type:</label></td>
		  	    <td><?php echo "<span id='trans_type_info'>" . $row_Recordset2['transaction_type'] . "</span>";
		  	    			  echo "<input type='hidden' id='transaction_type' name='transaction_type' value='" . 
		  	    			  			$row_Recordset2['transaction_type'] . "'>";
		  	    
		  	    			//list_transaction_types('transaction_type',$row_Recordset2['transaction_type'] ); 
		  	    
		  	    ?></td>
		  	  </tr>
                <?php //date_startstorage ==============================================================
			if($row_Recordset3['show_startdate']){?>
                <tr><td>&nbsp;</td>
		  	    <td for="date_startstorage"><label>Storage Start Date:</label></td>
		  	    <td><input name="date_startstorage" type="text" id="date_startstorage" value="<?php 
			  echo $row_Recordset2['date_startstorage_day']; ?>" size="10" maxlength="10" />
		  	    <span id="original_shop_date"></span></td>
		  	  </tr>
                <?php } //end if storage | start of date ================================================
			?>
                <tr><td>&nbsp;</td>
		  	    <td><label for="date"><?php echo $row_Recordset3['fieldname_date']; ?>:</label></td>
		  	    <td><input name="date" type="text" id="date" value="<?php echo $row_Recordset2['date_day']; ?>" size="10" maxlength="10" />
		  	        <span id="date_error"></span>
		  	
		  	        <SCRIPT>
					function FillDate() { 
						document.FormEdit.date.value = '<?php echo current_date(); ?>' }
				</SCRIPT>
		  	        <input type="button" name="date_fill" id="date_fill" value="Fill Current Date" onclick="FillDate()" />
		  	        <br /><?php 
				if ($row_Recordset3['show_startdate']) {  // If there is a start date show storage expiration message.
					
					if ( $row_Recordset2['date_day'] == "0000-00-00" ||
						  !isset($row_Recordset2['date_day']) ) { 
						echo $row_Recordset2['storage_days_left'] . 
								" days of storage remaining.  Bike must be finished by " . 
								$row_Recordset2['storage_deadline'] . "."; 
					} else {						 
						echo "Bike is marked as complete and should no longer be stored in the shop.";
					}
		
				} ?></td>
		  	  </tr>
           
                <?php  // start show amount
			if($row_Recordset3['community_bike']){ //community bike will allow a quantity to be selected for Yellow Bikes and Kids Bikes?>
                <tr>
                  <td>&nbsp;</td>
		  	    <td valign="top"><label for="quantity">Quantity:</label></td>
		  	    <td><input name="quantity" type="text" id="quantity" value="<?php echo $row_Recordset2['quantity']; ?>" size="3" maxlength="3" />
		  	    <span id="quantity_error"></span></td>
		  	    </tr>
                <?php } // end if show quantity for community bikes
			if($row_Recordset3['show_description']){ ?>
                <tr><td>&nbsp;</td>
		  	  <td valign="top"><label><?php echo $row_Recordset3['fieldname_description']; ?>:</label></td>
		  	  <td><textarea id="description" name="description" cols="45" rows="3"><?php echo $row_Recordset2['description']; ?></textarea>
		  	  <span id="description_error"></span></td>
		  	  </tr>
		  	  
			 <?php if($row_Recordset3['show_amount']){ ?>
           <tr id="price"><td>&nbsp;</td>
				<?php if($row_Recordset3['transaction_type_id'] == "Deposit"){?>           
			  		<td><label>Deposited:</label></td>	
				<?php } else { ?>
					<td><label>Paid:</label></td>
				<?php } ?>
			  	<td><input name="amount" type="text" id="amount" value="<?php echo $row_Recordset2['format_amount']; ?>" size="6" />
			  	<span id="payment_error"></span></td>
			  </tr>
			  <?php } ?>		  	  
		  	  
		  	  <?php if($row_Recordset3['show_payment']) { ?>
		  	  <tr id="payment_type">
		  	  		<td></td>
					<td><label for="payment_type" id="payment_type_label">Payment Type:</label></td>
					<td>
						<input type="radio" name="payment_type" value="cash"
						<?php if ($row_Recordset2['payment_type'] == "cash") { echo "  checked"; }  ?>	>
						<label id="cash" class="payment_type" for="payment_type">Cash</label>
						<input type="radio" name="payment_type" value="credit"
						<?php if ($row_Recordset2['payment_type'] == "credit") { echo "  checked"; } ?>  >
						<label id="credit_card" class="payment_type" for="payment_type">Credit Card</label>
						<input type="radio" name="payment_type" value="check"
						<?php if ($row_Recordset2['payment_type'] == "check") { echo "  checked"; } ?>   >
						
						<label id="check" class="payment_type" for="payment_type">Check</label>	
						<span id="payment_type_error"></span>					
						<?php if ($row_Recordset2['payment_type'] == "check") { 						
									echo '&nbsp;<input type="text" id="check_number" size="10" name="check_number" value="' . 
											$row_Recordset2['check_number'] .  '".>'; 
								} 
						?>	
					<span id="check_number_error"></span></td>		  	  
		  	  </tr>
		  	  <?php } ?>
                <?php } // end if show_payment 
			
				// Patron
			if($row_Recordset3['show_soldto_signed_in'] ||$row_Recordset3['show_soldto_not_signed_in'] ){ // if location show row?>                <tr><td>&nbsp;</td>
		  	  
		  	  <td><label><?php echo $row_Recordset3['fieldname_soldto']; ?>:</label></td>		  	 
		  	 <?php  
			   if($row_Recordset3['show_soldto_signed_in']){				
					echo "<td>";				
					list_CurrentShopUsers_select('sold_to', $row_Recordset2['sold_to']);	
					echo "<span id='sold_to_error'></span>";	
					$record_trans_id = $row_Recordset2['transaction_id'];
					if ($row_Recordset3['anonymous']) {
						echo "<span id='anon' style='display:show;'><label for='anonymous' id='anonymous_label'>Anonymous:</label>";
						if ($row_Recordset2['anonymous']) {					
								echo "<input type='checkbox' id='anonymous' checked>";
						} else {
								echo "<input type='checkbox' id='anonymous'>";
						}
						echo "</span>";
					} else {				
						echo "<span id='anon' style='display:none;'><label for='anonymous' id='anonymous_label'>Anonymous:</label></span>";
					}
					echo "<span id='anonymous_error'></span>";
					echo "<span id='paid_member'></span>";
					echo "<span id='volunteer_hours'></span>";
					echo "</td>";

				} elseif($row_Recordset3['show_soldto_not_signed_in']) {
					echo "<td>";
					list_donation_locations_withheader('sold_to', $row_Recordset2['sold_to']); //- not required to be signed in.
					echo "<span id='sold_to_error'></span></td>"; 
					// echo " <a href=\"location_add_edit.php?trans_id={$record_trans_id}&contact_id=new_contact\">Create New Location</a> | 
					// <a href=\"location_add_edit_select.php?trans_id={$record_trans_id}&contact_id=new_contact\">Edit Existing Location</a>";
					$record_trans_id = $row_Recordset2['transaction_id'];
					if ($row_Recordset3['anonymous']) {
						echo "<span id='anon' style='display:show;'><label for='anonymous' id='anonymous_label'>Anonymous:</label>";
						if ($row_Recordset2['anonymous']) {					
								echo "<input type='checkbox' id='anonymous' checked>";
						} else {
								echo "<input type='checkbox' id='anonymous'>";
						}
						echo "</span>";
					} else {				
						echo "<span id='anon' style='display:none;'><label for='anonymous' id='anonymous_label'>Anonymous:</label></span>";
					}
					echo "<span id='anonymous_error'></span>";
					echo "<span id='paid_member'></span>";
					echo "<span id='volunteer_hours'></span>";
					echo "</td>";				
				} 
				?>
			
		  	  </tr> <?php } //end if show location row ?>
                <tr><td>&nbsp;</td>
			  
			  
			  <td><label><?php  // sold by or received by 
			  echo $row_Recordset3['fieldname_soldby']; ?>:</label></td>
			  <td><?php if(current_shop_by_ip()>0) list_current_coordinators_select('sold_by', $row_Recordset2['sold_by']); 
			  				else list_contacts_coordinators('sold_by//,', $row_Recordset2['sold_by']); 
			  //list_contacts_coordinators('sold_by', $row_Recordset2['sold_by']);
              //list_current_coordinators_select('sold_by', $row_Recordset2['sold_by']);
			  ?>
               <span id="sold_by_error"></span></td>
			  </tr>
                </table>
		    <input type="hidden" name="MM_insert" value="FormEdit">
              <input type="hidden" name="transaction_id" value="<?php echo $trans_id; ?>">
              <input type="hidden" name="db_date_startstorage" value="<?php echo $row_Recordset2['date_startstorage']; ?>">
              <input type="hidden" name="db_date" value="<?php echo $row_Recordset2['date']; ?>">
              </form></td>
        
        <!-- PayPal column for edit transactions -->     
		  <td colspan="1" align="right" valign="top"> 
		    <form action="https://www.paypal.com/cgi-bin/webscr" method="post">
		      <input type="hidden" name="cmd" value="_xclick" />
		      <input type="hidden" name="business" value="donate@positivespin.org" />
		      <input type="hidden" name="item_name" value="PS Transaction <?php echo $row_Recordset2['transaction_id']; ?>: <?php echo $row_Recordset2['transaction_type']; ?> - <?php echo $row_Recordset2['description']; ?>" />
		      <input type="hidden" name="amount" value="<?php echo $row_Recordset2['format_amount']; ?>" />
		      <!-- <input type="hidden" name="item_number" value="" /> -->
		      <input type="hidden" name="no_shipping" value="1" />
		      <input type="hidden" name="return" value="transaction_log.php?error=transactioncomplete" />
		      <input type="hidden" name="no_note" value="1" />
		      <input type="hidden" name="currency_code" value="USD" />
		      <input type="hidden" name="tax" value="0" />
		      <input type="hidden" name="bn" value="PP-DonationsBF" />
		      <input type="image" src="https://www.paypal.com/en_US/i/btn/x-click-but04.gif" border="0" name="submit" alt="Make payments with PayPal - it's fast, free and secure!" />
		      <img alt="Donate" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1" />
		      </form>		 </td>
	    </tr>
        
        
        <?php    // Form to create a transaction
	  } else { //This section executes if it is not the transaction_id selected NOT FOR EDIT ?>
        
        <form method="post" name="FormNew" action="<?php echo $editFormAction; ?>">
          <tr  bordercolor="#CCCCCC" bgcolor="#CCCC33">
            <td colspan="9"><p><strong>Start New Transaction:</strong><br />&nbsp;&nbsp;&nbsp;&nbsp;Select Type: <?php list_transaction_types('transaction_type',$default_transaction_type); ?> 
              <input type="submit" name="Submit43" value="Create Transaction" /><span id="current_shop"></span>
              </p>	      
             </td>
	      </tr>
          <input type="hidden" name="MM_insert" value="FormNew" />
          </form>
	    <?php } // if ?>
        <tr bordercolor="#CCCCCC" bgcolor="#99CC33">
        <td width="50"><strong>Shop</strong></td>
		  <td width="100"><strong>Trans. Date</strong></td>
		  <td width="200" bgcolor="#99CC33"><strong>Sale Type </strong></td>
		  <td><strong>Patron</strong></td>
		  <td width="300"><strong>Description</strong></td>
		  <td><strong>Type</strong></td>
		  <td width="70"><strong>Amount</strong></td>
		  <td width="50"><strong>Edit  </strong></td>
		  <td><strong>Paid</strong></td>
	    </tr>
   
        <?php while ($row_Recordset1 = mysql_fetch_assoc($Recordset1)) { //do { ?>
        
        <form method="post" name="FormView_<?php echo $row_Recordset1['transaction_id']; ?>" action="<?php echo $editFormAction; ?>">
          <tr  bordercolor='#CCCCCC' <?php echo "title='Transaction ID: " . $row_Recordset1['transaction_id'] . "'";
          	echo ((intval($row_Recordset1['transaction_id']) == intval($trans_id)) ? "bgcolor='#CCCC33'" :  "");
          	if ($row_Recordset1['paid'] == 1) { echo "bgcolor='#E6E7E6'"; } 
          	if ($row_Recordset1['transaction_type'] == "Deposit") { echo "class='deposit'"; }
          ?> >
          <td><?php echo $row_Recordset1['shop_id']; ?></td>
		  <td><span id="wday" style="font-size:96%;"><?php echo $row_Recordset1['date_wday']; ?></span></td>
		  <td><?php echo $row_Recordset1['transaction_type']; ?></td>
		  
		  <td><?php  // Patron or Anonymous	  
		  
		  			$query = 'SELECT anonymous FROM transaction_log WHERE transaction_id="' . $row_Recordset1['transaction_id'] . '";';
					$sql = mysql_query($query, $YBDB) or die(mysql_error());
					$result = mysql_fetch_assoc($sql);	
		  
		  			if($result['anonymous']) {
					  echo  "Anonymous"; 
		  			} else {
		  				echo $row_Recordset1['full_name'];
		  			}
		  ?>&nbsp;</td>

		  <td <?php echo "title='Description: " . $row_Recordset1['description_with_locations'] . "'"; ?> ><?php echo $row_Recordset1['description_with_locations']; ?>&nbsp;</td>
		  <td><?php echo $row_Recordset1['payment_type']; ?>&nbsp;</td>
		  <td><?php echo $row_Recordset1['format_amount']; ?>&nbsp;</td>
		  <td><?php $record_trans_id = $row_Recordset1['transaction_id']; 
						foreach ($_GET as $i => $value) {
							if ($i != "trans_id") {
								$trans_url .= "&$i" . "=" . $value;
							}	
						}
						
						if (isset($trans_url)) { 
							echo "<a href=\"?trans_id={$record_trans_id}$trans_url\">edit</a></td>";	
						} else {		  				
		  					echo "<a href=\"?trans_id={$record_trans_id}\">edit</a></td>";
		  				}
		  				$trans_url = "";	 
		  				?>
		  <td><input class="paid" type="checkbox" name="<?php $ti =  $row_Recordset1['transaction_id']; echo $ti; ?>" 
		  														value="<?php echo $row_Recordset1['paid'];?>"	
		  														 <?php if ($row_Recordset1['paid'] == 1) { echo "  checked"; }  ?>													
		  														>
		  														
		  </td>
	    </tr>
	    <?php  			
  			if ($row_Recordset1['transaction_type'] == "Deposit") {

				$difference = "";
				$diff = "";
				if (isset($row_Recordset1['change_fund'])) {
					$cf = $row_Recordset1['change_fund'];
					$difference = $cf - $change_fund;
					if ($difference <> $change_fund && $difference != 0) {
						$diff = "(" . number_format((float)$difference, 2, '.', '') . ")";					
					} else {
						$diff = "";					
					}				
				} else {
					$cf = $change_fund;				
				}
  				
				echo "<tr class='deposit_calculator'><td colspan='9'><div style='text-align:right;'>";  				
  				echo '<span style="padding-left:10px; padding-right:10px;" id="' . $ti .
  					  '_change" title="Mouse over number to change.  Change Fund should always balance out to the same amount it started with.">Change Fund:' . 
  						" $<span class='editable_change' id='" . $ti . "_editable_change'>$cf</span>";
  				if ($diff != "") {		 
					echo "<span id='" . $ti . "_different_change' style='padding-left: 5px; padding-right: 5px; color: red;'>$diff</span>";  								
				} else {
					echo "<span id='" . $ti . "_different_change' style='padding-left: 5px; padding-right: 5px; color: red; display: none;'></span>";
				}	  				
  				echo 	'</span>|
  						<span style="padding-left:10px; padding-right:10px;" id="' . $ti . '_credit">Credit Card:  <span></span></span>|
  						<span style="padding-left:10px; padding-right:10px;" id="' . $ti . '_check">Check:  <span></span></span>+
  						<span style="padding-left:10px; padding-right:10px;" id="' . $ti . '_cash">Cash:  <span></span></span>=
  						<span style="padding-left:10px; padding-right:10px;" id="' . $ti . '_sum">Sum:  <span></span></span>|
  						<span style="padding-left:10px; padding-right:10px;" id="' . $ti . '_difference">Difference:  <span></span></span>';
  				echo "</div></td></tr>";
  			}
  		?>
          <input type="hidden" name="MM_insert" value="FormUpdate">
          <input type="hidden" name="shop_visit_id" value="<?php echo $row_Recordset1['transaction_id']; ?>">
          </form>
	  <?php } //while ($row_Recordset1 = mysql_fetch_assoc($Recordset1)); // while Recordset1 ?>
        </table>  </tr>
        
  <tr>
    <td height="40" valign="bottom"><form id="form1" name="form1" method="post" action="">
    	<br \>
		<label for="transaction_search" style="font-weight:bold;">Transaction Search:</label>     
      <br \> 
        Show <input name="record_count" type="text" value="<?php echo $number_of_transactions;  ?>" size="3">
        transactions on or before:
        <input name="trans_date" type="text" id="trans_date" value="<?php         
	        	if ($_GET['trans_date']) { 
	        		echo $_GET['trans_date'];
	        	} else { 
	        		echo current_date(); 
	        	}
        ?>" size="10" maxlength="10" />
       
			<script>
				<?php
					if(isset( $_GET["shop_dayname"] )){
						$selected_shop_dayname =  $_GET["shop_dayname"];
					} else { $selected_shop_dayname = "alldays"; }
					if(isset( $_GET["trans_type"] )){
						$selected_trans_type =  $_GET["trans_type"];
					} else { $selected_trans_type = "all_types"; }
				?>
				 // remember pull-down list selections			
				 $(function() {
				   	$("[name='dayname']").val("<?php echo $selected_shop_dayname; ?>").prop("selected","selected");
				   	$("[name='trans_type']").val("<?php echo $selected_trans_type; ?>").prop("selected","selected");		  
				  });
			</script>        
        
        <select class="yb_standard" name="dayname">
          <option value="alldays" selected="selected">All Days</option>
          <option value="Monday">Monday</option>
          <option value="Tuesday">Tuesday</option>
          <option value="Wednesday">Wednesday</option>
          <option value="Thursday">Thursday</option>
          <option value="Friday">Friday</option>
          <option value="Saturday">Saturday</option>
          <option value="Sunday">Sunday</option>
          </select>
        
         transaction type <?php list_transaction_types_withheader('trans_type', 'all_types'); ?> 
		          
          <input type="submit" name="Submit" value="Submit" />
          <input type="hidden" name="MM_insert" value="ChangeDate" />
          
  	  </form>
  	  
  	  </td>
  	</tr>
  	<tr>
  		<td>
      <?php 
			$shop_id = current_shop_by_ip();
			if ($shop_id) {
		   	$sql = "SELECT *, IF(date <> curdate() AND shop_type = 'Mechanic Operation Shop',0,1) as CanEdit 
		        			FROM shops WHERE shop_id = $shop_id;";
				$query = mysql_query($sql, $YBDB) or die(mysql_error());
				$result = mysql_fetch_assoc($query);
     		}
     		
      	if(current_shop_by_ip()>=1) echo '<label class="open_shop" for="shop" style="font-weight:bold;">Current Shop:</label>'; 
      	else echo '<label class="open_shop" for="shop" style="font-weight:bold">No Shop</label>'; 
      	if (current_shop_by_ip()>=1) echo "<br \>" . "(" . $result['shop_id'] . ") " . 
      												$result['shop_location'] . " - " . $result['shop_type'] . " - " . $result['date'];
      	?>
      </td>
    </tr>
    <tr>
		<td>
		<br \>
		<label style="font-weight:bold;" class="open_shop" for="gnucash_csv">GnuCash CSV:</label>
		<br \>		
		<?php
			echo "<form method='post' name='gnucash_csv'><table><tr>";
			// populate year pull-down list
			echo "<td style='vertical-align:top;'><label class='gnucash_csv' for='gnucash_csv_year'>Year</label><br \>";
			list_distinct_shop_years("gnucash_csv_year","");				
			echo "</td>";
			
			// populate Accounts pull-down list
			echo "<td style='vertical-align:top; padding-left:10px; padding-right:10px'>
					<label  class='gnucash_csv' for='gnucash_csv_year'>Accounts</label><br \>";
			
			echo "<select id='gnucash_csv_accounts' class='yb_standard' multiple>";	
				foreach ( $gnucash_accounts as $key => $value ) {
					echo "<option value='$value'>$key</option>";
				}
			echo "</select>";

			// range bar	
			echo "<td style='vertical-align:top; padding-left:10px; padding-right:2px; padding-bottom:10px;'>
								<label class='gnucash_csv' for='gnucash_csv_range'>Deposit Range</label><br \>";			
			echo "<div id='range_slider'><div id='gnucash_csv_range'></div></div>";			
			
			echo "<br \><input type='text' id='slider_lower'>&nbsp;&nbsp;&nbsp;&nbsp;<input type='text' id='slider_upper'></td>";
			echo "<td td style='vertical-align:inherit;'><input id='gnucash_csv_submit' type='submit' name='Submit' /></td></tr></table></form>";
		
		?>
		</td>	    
    </tr>
</table>
<p>&nbsp;</p>
<?php include("include_footer.html"); ?>
<?php
mysql_free_result($Recordset1);
?>