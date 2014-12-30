<?php
require_once('Connections/YBDB.php');
require_once('Connections/database_functions.php'); 

$page_edit_contact = PAGE_EDIT_CONTACT; 
$page_individual_history_log = INDIVIDUAL_HISTORY_LOG;
$storage_period = STORAGE_PERIOD;
$default_transaction_type = DEFAULT_TRANSACTION_TYPE;

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
	
//shop_date
if($_GET['trans_date']>0){
	$trans_date = "AND date <= ADDDATE('{$_GET['trans_date']}',1)" ;
} else {
	$datetoday = current_date();
	$trans_date ="AND date <= ADDDATE('{$datetoday}',1)"; 
	$trans_date = "";  }	   
	
//dayname
if($_GET['shop_dayname']=='alldays'){
	$shop_dayname = '';
} elseif(isset($_GET['shop_dayname'])) {
	$shop_dayname = "AND DAYNAME(date) = '" . $_GET['shop_dayname'] . "'";
} else {
	$shop_dayname = '';
}	

//Transaction_type
if($_GET['trans_type']=='all_types'){
	$trans_type = '';
} elseif(isset($_GET['trans_type'])) {
	$trans_type = "AND transaction_log.transaction_type = '" . $_GET['trans_type'] . "'";
} else {
	$trans_type = '';
}	

//record_count
if($_GET['record_count']>0){
	$record_count = $_GET['record_count'];
} else {
	$record_count = 30;}

// This is the recordset for the list of logged transactions	


mysql_select_db($database_YBDB, $YBDB);
$query_Recordset1 = "SELECT *,
DATE_FORMAT(date,'%m/%d (%a)') as date_wday,
CONCAT('$',FORMAT(amount,2)) as format_amount,
CONCAT(contacts.last_name, ', ', contacts.first_name, ' ',contacts.middle_initial) AS full_name,
LEFT(IF(show_startdate, CONCAT(' [',
		DATE_FORMAT(DATE_ADD(date_startstorage,INTERVAL $storage_period DAY),'%W, %M %D'), '] ', transaction_log.description),
		IF(community_bike,CONCAT('Quantity(', quantity, ')  ', transaction_log.description), description)),100) as description_with_locations
FROM transaction_log
LEFT JOIN contacts ON transaction_log.sold_to=contacts.contact_id
LEFT JOIN transaction_types ON transaction_log.transaction_type=transaction_types.transaction_type_id
WHERE 1=1 {$trans_date} {$shop_dayname} {$trans_type} ORDER BY transaction_id DESC LIMIT  0, $record_count;";
$Recordset1 = mysql_query($query_Recordset1, $YBDB) or die(mysql_error());
$totalRows_Recordset1 = mysql_num_rows($Recordset1);

//Action on form update
$editFormAction = $_SERVER['PHP_SELF'];

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
	
	if ($initial_date_startstorage) {
		$date_startstorage = current_datetime();
		$date = "NULL";
	} else {
		$date_startstorage = "NULL";
		$date = current_datetime();
	} //end if
	
	$insertSQL = sprintf("INSERT INTO transaction_log (transaction_type,shop_id, date_startstorage, date, quantity) VALUES (%s,%s, %s ,%s,%s)",
					   GetSQLValueString($_POST['transaction_type'], "text"),
					   GetSQLValueString($shop_id, "text"),
					   GetSQLValueString($date_startstorage, "date"),
					   GetSQLValueString($date, "date"),
					   GetSQLValueString(1, "int"));
					   
	//echo $insertSQL; 
	mysql_select_db($database_YBDB, $YBDB);
	$Result1 = mysql_query($insertSQL, $YBDB) or die(mysql_error());

	// gets newest transaction ID
	mysql_select_db($database_YBDB, $YBDB);
	$query_Recordset4 = "SELECT MAX(transaction_id) as newtrans FROM transaction_log;";
	$Recordset4 = mysql_query($query_Recordset4, $YBDB) or die(mysql_error());
	$row_Recordset4 = mysql_fetch_assoc($Recordset4);
	$totalRows_Recordset4 = mysql_num_rows($Recordset4);
	$newtrans = $row_Recordset4['newtrans'];  //This field is used to set edit box preferences
	
	$LoadPage = $_SERVER['PHP_SELF'] . "?trans_id={$newtrans}";
	header(sprintf("Location: %s", $LoadPage));
} // end Form Submit New Transaction

//Form Edit Record ===============================================================================
if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "FormEdit") && ($_POST["EditSubmit"] == "Save")) {


	//Error Correction & good place for jquery 
	$sold_to = (($_POST['sold_to'] == 'no_selection') ? 1268 : $_POST['sold_to'] );
	$sold_by = (($_POST['sold_by'] == 'no_selection') ? 1268 : $_POST['sold_by'] );
	$date_startstorage = date_update_wo_timestamp($_POST['date_startstorage'], $_POST['db_date_startstorage']);
	$date = date_update_wo_timestamp($_POST['date'], $_POST['db_date']);
	$description = (($_POST['description'] == "") ? "No Description" : $_POST['description'] );	

	$updateSQL = sprintf("UPDATE transaction_log SET transaction_type=%s, date_startstorage=%s, date=%s, amount=%s, quantity=%s, description=%s, sold_to=%s, sold_by=%s, shop_id=%s WHERE transaction_id=%s",
						   GetSQLValueString($_POST['transaction_type'], "text"),
						   GetSQLValueString($date_startstorage, "date"),
						   GetSQLValueString($date, "date"),
						   GetSQLValueString($_POST['amount'], "double"),
						   GetSQLValueString($_POST['quantity'], "int"),
						   GetSQLValueString($description, "text"),
						   GetSQLValueString($sold_to, "int"),
						   GetSQLValueString($sold_by, "int"),
						   GetSQLValueString($_POST['shop_id'], "int"),
						   GetSQLValueString($_POST['transaction_id'], "int"));
						   //"2006-10-12 18:15:00"
	
	
	mysql_select_db($database_YBDB, $YBDB);
	$Result1 = mysql_query($updateSQL, $YBDB) or die(mysql_error());
	
	$trans_id = $_POST['transaction_id'];
	header(sprintf("Location: %s",$editFormAction . "?trans_id={$trans_id}" ));   //$editFormAction
}

//Form Edit Record Delete ===============================================================================
if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "FormEdit") && ($_POST["EditSubmit"] == "Delete")) {
	
	$trans_id = $_POST['transaction_id'];
	header(sprintf("Location: %s",$editFormAction . "?delete_trans_id={$trans_id}" ));   //$editFormAction
}

//Form Confirm Delete ===============================================================================
if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "ConfirmDelete") && ($_POST["DeleteConfirm"] == "Confirm Delete")) {

	$delete_trans_id = $_POST['delete_trans_id'];
	$insertSQL = "DELETE FROM transaction_log WHERE transaction_id = {$delete_trans_id}";
	mysql_select_db($database_YBDB, $YBDB);
	$Result1 = mysql_query($insertSQL, $YBDB) or die(mysql_error());
	
	header(sprintf("Location: %s", PAGE_SALE_LOG ));   //$editFormAction

//Cancel and go back to transaction ================================================================
} elseif ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "ConfirmDelete") && ($_POST["DeleteConfirm"] == "Cancel")) { 
	$delete_trans_id = $_POST['delete_trans_id'];
	header(sprintf("Location: %s", PAGE_SALE_LOG . "?trans_id={$delete_trans_id}" ));   //$editFormAction
}

//Change Date     isset($_POST["MM_update"]) =========================================================
if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "ChangeDate")) {
  $editFormAction = $_SERVER['PHP_SELF'] . "?trans_date={$_POST['trans_date']}&trans_type={$_POST['trans_type']}&shop_dayname={$_POST['dayname']}&record_count={$_POST['record_count']}";
  header(sprintf("Location: %s",$editFormAction ));   //$editFormAction
}

?>

<?php include("include_header.html"); ?>

<input type="hidden" name="cancel_return" value="http://positivespin.org/ybdb/transaction_log.php?error=transactioncanceled" />
<table border="0" cellpadding="1" cellspacing="0">
  <tr>
    <td align="left" valign="bottom"><?php echo $error_message ?> </td>
    </tr>
  
  <!-- All elements of edit transaction contained in this row -->  
  <tr>
    <td>
      <table border="1" cellpadding="1" cellspacing="0" bordercolor="#CCCCCC">
        <tr bordercolor="#CCCCCC" bgcolor="#99CC33">
          <td colspan="8" bgcolor="#99CC33"><div align="center"><strong>Bike and Sale Log </strong></div></td>
		  </tr>
        <?php 		// show delete tranaction confirmation =========================================
		if($delete_trans_id <> -1 ) { ?>
        <form method="post" name="FormConfirmDelete" action="<?php echo $editFormAction; ?>">
          <tr bordercolor="#CCCCCC" bgcolor="#CCCC33">
            <td colspan="8"><p><strong>Edit Transaction:
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
	  mysql_select_db($database_YBDB, $YBDB);
	  $query_Recordset3 = "SELECT * FROM transaction_types WHERE transaction_type_id = \"$trans_type\";";
	  $Recordset3 = mysql_query($query_Recordset3, $YBDB) or die(mysql_error());
	  $row_Recordset3 = mysql_fetch_assoc($Recordset3);
	  $totalRows_Recordset3 = mysql_num_rows($Recordset3);
	  
	  ?>
        
        <!-- The actual row for edit transactions -->
        <tr bgcolor="#CCCC33">

			<!-- the column for the edit transactions form -->          
          <td colspan="7">
            <form method="post" name="FormEdit" action="<?php echo $editFormAction; ?>">
              <table border="0" cellspacing="0" cellpadding="1">
                  
                   <td></td><td></td>
                   <td>
                    <input type="submit" name="EditSubmit" value="Save" align="right">
                    <input type="submit" name="EditSubmit" value="Close">
                    <input type="submit" name="EditSubmit" value="Delete">
                    <!-- Save before using paypal ->> -->
                    </td>
		  	    </tr>
                
                <tr><td>&nbsp;</td>
		  	    <td><label>Transaction ID:</label></td>
                <td><?php echo $row_Recordset2['transaction_id']; ?><em><?php echo $row_Recordset3['message_transaction_id']; ?></em></em></td>
		  	  </tr>
		  	    <tr><td>&nbsp;</td>
		  	    <td><label>ShopID:</label> </td>
                <td><input name="shop_id" type="text" id="amount" value="<?php echo $row_Recordset2['shop_id']; ?>" size="6" /></td>
		  	  </tr>
                <?php ?>
                <tr><td>&nbsp;</td><td><label>Select Type:</label></td>
		  	    <td><?php list_transaction_types('transaction_type',$row_Recordset2['transaction_type'] ); ?></td>
		  	  </tr>
                <?php //date_startstorage ==============================================================
			if($row_Recordset3['show_startdate']){?>
                <tr><td>&nbsp;</td>
		  	    <td><label>Storage Start Date:</label></td>
		  	    <td><input name="date_startstorage" type="text" id="date_startstorage" value="<?php 
			  echo $row_Recordset2['date_startstorage_day']; ?>" size="10" maxlength="10" />
		  	      (yyyy-mm-dd)</td>
		  	  </tr>
                <?php } //end if storage | start of date ================================================
			?>
                <tr><td>&nbsp;</td>
		  	    <td><label><?php echo $row_Recordset3['fieldname_date']; ?>:</label></td>
		  	    <td><input name="date" type="text" id="date" value="<?php echo $row_Recordset2['date_day']; ?>" size="10" maxlength="10" />
		  	      (yyyy-mm-dd)
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
           
                <?php  // end if show amount
			if($row_Recordset3['community_bike']){ //community bike will allow a quantity to be selected for Yellow Bikes and Kids Bikes?>
                <tr>
                  <td>&nbsp;</td>
		  	    <td valign="top"><label>Quantity:</label></td>
		  	    <td><input name="quantity" type="text" id="quantity" value="<?php echo $row_Recordset2['quantity']; ?>" size="3" maxlength="3" /></td>
		  	    </tr>
                <?php } // end if show quanitiy for community bikes
			if($row_Recordset3['show_description']){ ?>
                <tr><td>&nbsp;</td>
		  	  <td valign="top"><label><?php echo $row_Recordset3['fieldname_description']; ?>:</label></td>
		  	  <td><textarea name="description" cols="45" rows="3"><?php echo $row_Recordset2['description']; ?></textarea></td>
		  	  </tr>
		  	  
			 <?php if($row_Recordset3['show_amount']){ ?>
           <tr id="price"><td>&nbsp;</td>
			  	<td><label>Price:</label></td>
			  	<td><input name="amount" type="text" id="amount" value="<?php echo $row_Recordset2['format_amount']; ?>" size="6" /></td>
			  </tr>
			  <?php } ?>		  	  
		  	  
		  	  <?php if($row_Recordset3['show_payment']) { ?>
		  	  <tr id="payment_type">
		  	  		<td></td>
					<td><label>Payment Type:</label></td>
					<td>
						<input type="radio" name="payment_type" value="cash"
						<?php if ($row_Recordset2['payment_type'] == "cash") { echo "  checked"; }  ?>	>Cash
						<input type="radio" name="payment_type" value="credit"
						<?php if ($row_Recordset2['payment_type'] == "credit") { echo "  checked"; } ?>  >Credit Card
						<input type="radio" name="payment_type" value="check"
						<?php if ($row_Recordset2['payment_type'] == "check") { echo "  checked"; } ?>   >Check	
					</td>		  	  
		  	  </tr>
		  	  <?php } ?>
                <?php } // end if show_description 
				if($row_Recordset3['show_soldto_location']){ // if location show row?>
                <tr><td>&nbsp;</td>
		  	  <td><label><?php echo $row_Recordset3['fieldname_soldto']; ?>:</label></td>
		  	  <td><?php
			if($row_Recordset3['show_soldto_location']){
				list_donation_locations_withheader('sold_to', $row_Recordset2['sold_to']); 
				$record_trans_id = $row_Recordset2['transaction_id']; 
				// echo " <a href=\"location_add_edit.php?trans_id={$record_trans_id}&contact_id=new_contact\">Create New Location</a> | <a href=\"location_add_edit_select.php?trans_id={$record_trans_id}&contact_id=new_contact\">Edit Existing Location</a>";
			} else {
				//list_CurrentShopUsers_select('sold_to', $row_Recordset2['sold_to']);
			}  ?></td>
		  	  </tr> <?php } //end if show location row ?>
                <tr><td>&nbsp;</td>
			  <td><label><?php echo $row_Recordset3['fieldname_soldby']; ?>:</label></td>
			  <td><?php if(current_shop_by_ip()>0) list_current_coordinators_select('sold_by', $row_Recordset2['sold_by']); else list_contacts_coordinators('sold_by', $row_Recordset2['sold_by']); 
			  //list_contacts_coordinators('sold_by', $row_Recordset2['sold_by']);
              //list_current_coordinators_select('sold_by', $row_Recordset2['sold_by']);
			  ?>
               </td>
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
          <tr bordercolor="#CCCCCC" bgcolor="#CCCC33">
            <td colspan="8"><p><strong>Start New Transaction:</strong><br />&nbsp;&nbsp;&nbsp;&nbsp;Select Type: <?php list_transaction_types('transaction_type',$default_transaction_type); ?> 
              <input type="submit" name="Submit43" value="Create Transaction" />
              </p>	      </td>
	      </tr>
          <input type="hidden" name="MM_insert" value="FormNew">
          </form>
	    <?php } // if ?>
        <tr bordercolor="#CCCCCC" bgcolor="#99CC33">
        <td width="50"><strong>Shop</strong></td>
		  <td width="100"><strong>Date</strong></td>
		  <td width="200" bgcolor="#99CC33"><strong>Sale Type </strong></td>
		  <td width="70"><strong>Amount</strong></td>
		  <td width="300"><strong>Description</strong></td>
		  <td><strong>Patron</strong></td>
		  <td width="50"><strong>Edit  </strong></td>
		  <td><strong>Paid</strong></td>
	    </tr>
   
        <?php while ($row_Recordset1 = mysql_fetch_assoc($Recordset1)) { //do { ?>
        
        <form method="post" name="FormView_<?php echo $row_Recordset1['transaction_id']; ?>" action="<?php echo $editFormAction; ?>">
          <tr bordercolor='#CCCCCC' <?php
          	echo ((intval($row_Recordset1['transaction_id']) == intval($trans_id)) ? "bgcolor='#CCCC33'" :  "");
          	if ($row_Recordset1['paid'] == 1) { echo "bgcolor='#99CC33'"; } 
          ?> >
          <td><?php echo $row_Recordset1['shop_id']; ?></td>
		  <td><?php echo $row_Recordset1['date_wday']; ?></td>
		  <td><?php echo $row_Recordset1['transaction_type']; ?></td>
		  <td><?php echo $row_Recordset1['format_amount']; ?>&nbsp;</td>
		  <td><?php echo $row_Recordset1['description_with_locations']; ?>&nbsp;</td>
		  <td><?php echo $row_Recordset1['full_name']; ?></td>
		  <td><?php $record_trans_id = $row_Recordset1['transaction_id']; echo "<a href=\"{$_SERVER['PHP_SELF']}?trans_id={$record_trans_id}\">edit</a>"; ?></td>
		  <td><input class="paid" type="checkbox" name="<?php echo $row_Recordset1['transaction_id']; ?>" 
		  														value="<?php echo $row_Recordset1['paid'];?>"
																<?php if ($row_Recordset1['paid'] == 1) { echo "  checked"; }  ?>		  														
		  														
		  														>
		  </td>
	    </tr>
          <input type="hidden" name="MM_insert" value="FormUpdate">
          <input type="hidden" name="shop_visit_id" value="<?php echo $row_Recordset1['transaction_id']; ?>">
          </form>
	  <?php } //while ($row_Recordset1 = mysql_fetch_assoc($Recordset1)); // while Recordset1 ?>
        </table>  </tr>
  <tr>
    <td height="40" valign="bottom"><form id="form1" name="form1" method="post" action="">
      <p><br />
        Show
        <input name="record_count" type="text" value="30" size="3" maxlength="3" />
        transactions on or before:
        <input name="trans_date" type="text" id="trans_date" value="<?php echo current_date(); ?>" size="10" maxlength="10" />
        (date format YYYY-MM-DD) Day of week:
        <select name="dayname">
          <option value="alldays" selected="selected">All Days</option>
          <option value="Monday">Monday</option>
          <option value="Tuesday">Tuesday</option>
          <option value="Wednesday">Wednesday</option>
          <option value="Thursday">Thursday</option>
          <option value="Friday">Friday</option>
          <option value="Saturday">Saturday</option>
          <option value="Sunday">Sunday</option>
          </select>
        </p>
        <p>Type of transaction <?php list_transaction_types_withheader('trans_type', 'all_types'); ?> 
          <input type="submit" name="Submit" value="Add Filter" />
          <input type="hidden" name="MM_insert" value="ChangeDate" />
          </p>
  	  </form>
      <?php if(current_shop_by_ip()>1) echo "current shop"; else echo "no shop"; ?>
      </td>
    </tr>
</table>
<p>&nbsp;</p>
<?php include("include_footer.html"); ?>
<?php
mysql_free_result($Recordset1);
?>