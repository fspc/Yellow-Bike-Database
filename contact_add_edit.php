<?php

require_once('Connections/YBDB.php'); 
require_once('Connections/database_functions.php');

$waiver = WAIVER;
$email_list = EMAIL_LIST;
$volunteer_interest_form = VOLUNTEER_INTEREST_FORM;
$volunteer_interest_form_name = VOLUNTEER_INTEREST_FORM_NAME;
$volunteer_interest_comments = VOLUNTEER_INTEREST_COMMENTS;

if($_GET['shop_id']>0){
	$shop_id = $_GET['shop_id'];
} else {
	$shop_id = current_shop_by_ip();
}


switch ($_GET['error']) {
case 'new_error_message':	//this is a sample error message.  insert error case here		
   $error_message = '';
   break;
default:
   $error_message = 'Enter or Update Contact Information - </span><span class="yb_standard"> 
   Thank-you for supporting Positive Spin. </p> </span><span class="yb_heading3red">';
   break;
}

$page_shop_log = PAGE_SHOP_LOG . "?shop_id=$shop_id";


if($_GET['contact_id'] == 'new_contact'){
			
	
	/* Discover if previous contact creation attempt was abandoned
		There should be at least a first and last name, if not we use
		previous contact_id, update it and start fresh	
	*/
	mysql_select_db($database_YBDB, $YBDB);
	
	// Find previous contact_id	
   $sql = "SELECT MAX(contact_id) as previous_contact_id FROM contacts;";
	$query = mysql_query($sql, $YBDB) or die(mysql_error());
	$result = mysql_fetch_assoc($query);
	$previous_contact_id = $result['previous_contact_id'];
	
	// If full_name is empty we will use this contact_id
	$sql = "SELECT CONCAT(first_name, ' ', last_name) as full_name FROM contacts WHERE contact_id=" . $previous_contact_id. ";";
	$query = mysql_query($sql, $YBDB) or die(mysql_error());
	$result = mysql_fetch_assoc($query);		
	
	$full_name = $result['full_name'];
		
	//adds contact if new_contact is selected .. it's " " not ""
	if ($full_name != " ") {
		
		$new_contact_id = $previous_contact_id + 1;
	
		$insertSQL = sprintf("INSERT INTO contacts (date_created) VALUES (%s)",
							   GetSQLValueString('current_time', "date"));
		$Result1 = mysql_query($insertSQL, $YBDB) or die(mysql_error());
	
		$contact_id = $new_contact_id;
		$contact_id_entry = 'new_contact';	

	} else {

		$insertSQL = sprintf("UPDATE contacts SET  date_created=%s WHERE contact_id=" . $previous_contact_id,
						   GetSQLValueString('current_time', "date"));
		$Result1 = mysql_query($insertSQL, $YBDB) or die(mysql_error());	
		
		$contact_id = $previous_contact_id;
		$contact_id_entry = 'new_contact';		
			
	}		

	
} elseif(isset($_GET['contact_id'])) {
	//else contact_id is assigned from passed value
	$contact_id = $_GET['contact_id'];
	$contact_id_entry = $_GET['contact_id'];
} else {
	$contact_id = -1;
	$contact_id_entry = -1;
}

$editFormAction = "?contact_id={$contact_id}&shop_id={$shop_id}";

require_once('php-console/src/PhpConsole/__autoload.php');
$handler = PhpConsole\Handler::getInstance();
$handler->start();

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {

	$updateSQL = sprintf("UPDATE contacts SET first_name=%s, middle_initial=%s, last_name=%s, email=%s, 
  								DOB=%s, phone=%s, address1=%s, address2=%s, city=%s, 
  								`state`=%s, zip=%s, pass=ENCODE(%s,'yblcatx') WHERE contact_id=%s",
                    	GetSQLValueString($_POST['first_name'], "text"),
                    	GetSQLValueString($_POST['middle_initial'], "text"),
                    	GetSQLValueString($_POST['last_name'], "text"),
                   	GetSQLValueString($_POST['email'], "text"),
					   	GetSQLValueString($_POST['DOB'], "date"),
                    	GetSQLValueString($_POST['phone'], "text"),
                    	GetSQLValueString($_POST['address1'], "text"),
                    	GetSQLValueString($_POST['address2'], "text"),
                    	GetSQLValueString($_POST['city'], "text"),
                    	GetSQLValueString($_POST['state'], "text"),
                    	GetSQLValueString($_POST['zip'], "text"),
					   	GetSQLValueString($_POST['password'], "text"),
					   	GetSQLValueString($_POST['contact_id'], "int"));

	mysql_select_db($database_YBDB, $YBDB);
	$Result1 = mysql_query($updateSQL, $YBDB) or die(mysql_error());

	// Are there any interests in the datatbase?
	$sql = "SELECT option_name FROM options;";	
	$query = mysql_query($sql, $YBDB) or die(mysql_error());
	while ($result = mysql_fetch_assoc($query)) {
		$interests[] = $result["option_name"];		
	}	
	$interests = array_combine($interests,$interests);
	
	if ($volunteer_interest_form && !isset($volunteer_interests_changename)) {
				
		// populate database with user defined interests if they do not exist
		$volunteer_interest = array_combine($volunteer_interests,$volunteer_interests);		
		
		foreach ($volunteer_interest as $interest) {
			// Insert new interest
			if ( !$interests[$interest] ) {
				$query = "INSERT INTO options (id, option_name, option_value) VALUES (" .
							$_POST['contact_id'] . ",'" . $interest . "',0);";				 
				$result = mysql_query($query, $YBDB) or die(mysql_error());
			}	
		}	
		
	} // end volunteer_interest_form populate and/or delete

	// Change or delete an interest(s) name	
	if( isset($volunteer_interests_changename) ) {
		foreach ($volunteer_interests_changename as $key => $interest) { 
			$sql = "UPDATE options SET option_name='" . $interest . 
						"' WHERE option_name='" . $interests[$key] . "';";	
			$query = mysql_query($sql, $YBDB) or die(mysql_error());
		}
	} else if( isset($volunteer_interests_deletename) ) {
		foreach ($volunteer_interests_deletename as $interest) { 
			$sql = "DELETE FROM options WHERE option_name='" . $interest . "';";	
			$query = mysql_query($sql, $YBDB) or die(mysql_error());
		}		
	}

  if ($_POST['contact_id_entry'] == 'new_contact'){
  
  	//navigate back to shop that it came from
	$pagegoto = PAGE_SHOP_LOG . "?shop_id={$shop_id}&new_user_id={$contact_id}";
	header(sprintf("Location: %s", $pagegoto));

  }
}

mysql_select_db($database_YBDB, $YBDB);
$query_Recordset1 = "SELECT *, DECODE(pass,'yblcatx') AS passdecode FROM contacts WHERE contact_id = $contact_id";
$Recordset1 = mysql_query($query_Recordset1, $YBDB) or die(mysql_error());
$row_Recordset1 = mysql_fetch_assoc($Recordset1);
$totalRows_Recordset1 = mysql_num_rows($Recordset1);
?>

<?php include("include_header_contacts.html"); ?>

<table>
  <tr valign="top">
    <td   align="left"><span class="yb_heading3red"><?php echo $error_message; ?></span></td>
	  </tr>
  <tr>
      
      <form method="post" name="form1" action="<?php echo $editFormAction; ?>">
        <table border="0" cellpadding="1" cellspacing="0" bordercolor="#CCCCCC">
          <tr>
            <td><label class="contacts">Contact_id:</label></td>
			    <td><?php echo $row_Recordset1['contact_id']; ?></td>
			 </tr>
          <tr >
            <td><label class="contacts">Name:</label></td>
			    <td><input id="first_name" type="text" name="first_name" value="<?php echo $row_Recordset1['first_name']; ?>" size="32">
			    		<span id="first_name_error"></span>
			    		<input name="middle_initial" type="text" value="<?php echo $row_Recordset1['middle_initial']; ?>" size="1" maxlength="1">
			    		<input id="last_name" type="text" name="last_name" value="<?php echo $row_Recordset1['last_name']; ?>" size="32">
						<span id="last_name_error"></span>			    
			   </td>
			    </tr>
          <tr>
            <td><label class="contacts">Date of Birth:</label></td>
			    <td><input id="birth_date" type="text" name="DOB" value="<?php echo $row_Recordset1['DOB']; ?>" size="10" /> 
			    </td>
			 </tr>			 
          <tr>
            <td><label class="contacts">Email:</label></td>
			    <td><input id="email" type="text" name="email" value="<?php echo $row_Recordset1['email']; ?>" size="32">
			    <span id="email_error"></span></td>
			 </tr>
			 <?php if($email_list) { ?>
			 <tr>
			  	<td><label class="contacts">Email List:</label></td>
				<td>
				<div id="email_list_block">
				<div id="email_list_toggle" style="width: 50px;"></div>
				<div id="email_list_error"></div>
				</div>				
				</td>			 
			 </tr>
			 <?php } ?>
          <tr >
            <td ><label class="contacts">Phone:</label></td>
			    <td><input id="phone" type="text" name="phone" value="<?php echo $row_Recordset1['phone']; ?>" size="32">
			    <span id="phone_error"></span></td>
			 </tr>
          <tr>
            <td><label class="contacts">Address1:</label></td>
			    <td><input type="text" name="address1" value="<?php echo $row_Recordset1['address1']; ?>" size="32"></td>
			 </tr>
          <tr >
            <td><label class="contacts">Address2:</label></td>
			    <td><input type="text" name="address2" value="<?php echo $row_Recordset1['address2']; ?>" size="32"></td>
			 </tr>
          <tr>
            <td><label class="contacts">City:</label></td>
			    <td><input type="text" name="city" value="<?php echo $row_Recordset1['city']; ?>" size="32"></td>
			 </tr>
          <tr>
            <td><label class="contacts">State:</label></td>
			    <td><input id="state_abbreviation" name="state" type="text" value="<?php echo $row_Recordset1['state']; ?>" size="2" maxlength="2"></td>
			 </tr>
          <tr >
            <td><label class="contacts">Zip Code:</label></td>
			    <td><input id="zip" type="text" name="zip" value="<?php echo $row_Recordset1['zip']; ?>" size="10"></td>
			 </tr>
          <tr>
            <td><label class="contacts">New Password:</label></td>
			    <td>
			    <input name="password" type="password" id="password" value="<?php echo $row_Recordset1['passdecode']; ?>" size="32">
			    </td>
			 </tr>
			<?php if($waiver) { ?>
			<tr>
				<td><label class="contacts">Waiver of Liability:</label></td>
			   <td>			  	<div id="waiver">
				  <p>
				  <?php include("Connections/waiver.txt"); ?>
				  <br />
				  </p>
				  </div><input id="waiver_checkbox" type="checkbox"> I agree <span id="waiver_error"></span>
			  	<input type="submit" id="waiver_button" value="Show Waiver" \>

				</td>
			 </tr>
			 <?php } ?>
			 <?php if($volunteer_interest_form) { ?>			 
			 <tr>
			 	<td><label class="contacts"><?php echo $volunteer_interest_form_name; ?>:</label></td>
			 				
			   <td>			  
			  		<div id="interest_form">
				  	<?php include("Connections/volunteer_interest_form_introduction.txt"); ?>
					<table>
						<tr><td>&nbsp;</td></tr>
						<?php 
							$columns = 3;
							$c = 0;
							$rows = 0;
							$interest_count = count($volunteer_interests);														
							 while($rows < $interest_count + 3) {				
								echo "<tr>";
								
								for($i = $rows - $columns; $i < $rows; $i++) {
									if($volunteer_interests[$i]) {								
										echo "<td><input value='$volunteer_interests[$i]' type='checkbox'>" . $volunteer_interests[$i] . "</td>";								
									}
								}
								echo "</tr>";								
								$rows = $rows + $columns;				
							}
						?>
						<?php if($volunteer_interest_comments) { ?>							
						<tr><td>&nbsp;</td></tr>					
						<tr>		  	 
				  	 		<td class="center_comment" colspan="2"><label id="contact_comment">Comments</label>
				  	 		<textarea  name="comment" cols="45" rows="3"></textarea></td>
				  		</tr>
				  		<?php } ?>
				  	</table>
		    		</div>
		    		<!-- Fill-in Form -->
		    	<input type="submit" id="interest_form_button" value="Check them out!" \>
		    	</td>  
		    </tr>
			 <?php } ?>
          	<tr>
          	 <td></td>
			    <td><br />				    
			    <input id="submit_contact" type="submit" value="Submit"></td>
		    	</tr>
          </table>
          
        <input type="hidden" name="MM_insert" value="form1">
        <input type="hidden" id="contact_id" name="contact_id" value="<?php echo $row_Recordset1['contact_id']; ?>">
        <input type="hidden" name="contact_id_entry" value="<?php echo $contact_id_entry; ?>">
        </form>
	  </tr>
</table>




<?php include("include_footer.html"); ?>

<?php
mysql_free_result($Recordset1);
?>
