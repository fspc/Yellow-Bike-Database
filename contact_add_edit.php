<?php

require_once('Connections/YBDB.php'); 
require_once('Connections/database_functions.php');


/*require_once('php-console/src/PhpConsole/__autoload.php');
$handler = PhpConsole\Handler::getInstance();
$handler->start();*/


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


// setup the proper form action and form values .. not that $_GET is such a brilliant approach :)
if($_GET['contact_id'] == 'new_contact'){
			
	
	mysql_select_db($database_YBDB, $YBDB);
	
	// Find previous contact_id	
   $sql = "SELECT MAX(contact_id) as previous_contact_id FROM contacts;";
	$query = mysql_query($sql, $YBDB) or die(mysql_error());
	$result = mysql_fetch_assoc($query);
	$previous_contact_id = $result['previous_contact_id'];
	
		
	$new_contact_id = $previous_contact_id + 1;
	$contact_id = $new_contact_id;
	$contact_id_entry = 'new_contact';		

	
} elseif(isset($_GET['contact_id'])) {
	//else contact_id is assigned from passed value
	$contact_id = $_GET['contact_id'];
	$contact_id_entry = $_GET['contact_id'];
} else {
	$contact_id = -1;
	$contact_id_entry = -1;
}


$editFormAction = "?contact_id={$contact_id}&shop_id={$shop_id}";

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {


	/* Discover if submitted contact creation attempt is new.
		There should be at least a first and last name.
	*/
	mysql_select_db($database_YBDB, $YBDB);
	
	$query = 'SELECT MAX(contact_id) as contact_id FROM contacts;';
	$sql = mysql_query($query, $YBDB) or die(mysql_error());	
	$result = mysql_fetch_assoc($sql);	
	$submitted_contact_id = $result['contact_id'] + 1;	

	/*
		$handler->debug("submitted_contact_id",$submitted_contact_id - 1);
		$handler->debug("$_POST",$_POST['contact_id']);
		exit();
	*/

	// contact already exists it is less than $submitted_contact_id
	if($submitted_contact_id > $_POST['contact_id']) {
		$submitted_contact_id = $_POST['contact_id'];
	}	
	
	// if contact already exists, $submitted_contact_id now equals $_POST['contact_id], and it isn't new_contact
	if ($submitted_contact_id != $_POST['contact_id'] || $_POST === 'new_contact') {
		$submitted_contact_id =	$_POST['contact_id'];
	} else {

		// If full_name is empty we will use this contact_id
		$sql = "SELECT CONCAT(first_name, ' ', last_name) as full_name FROM contacts WHERE contact_id=" . $submitted_contact_id . ";";
		$query = mysql_query($sql, $YBDB) or die(mysql_error());
		$result = mysql_fetch_assoc($query);		
		$full_name = $result['full_name'];
	}
		
	//adds contact if new_contact is selected .. it's " " not ""
	if (empty($full_name)) {
		$contact_id_entry = 'new_contact';	
	}

	if ( $contact_id_entry === 'new_contact' ) {	
	
		// Get the actual contact_id because it may have changed on multiple terminals
		$query = 'SELECT MAX(contact_id) as contact_id FROM contacts;';
		$sql = mysql_query($query, $YBDB) or die(mysql_error());	
		$result = mysql_fetch_assoc($sql);	
		$submitted_contact_id = $result['contact_id'] + 1;			
		
		// Insert new contact information into a new record
		$updateSQL = 'INSERT INTO contacts (contact_id, first_name, middle_initial, last_name, email,' . 
													  ' phone, address1, address2, city, state, DOB, receive_newsletter, waiver, pass, zip)' .
												' VALUES (' . 
												$submitted_contact_id . ', ' .
												'"' . $_POST['first_name'] . '", ' . 
												'"' . $_POST['middle_initial'] . '", ' . 
												'"' . $_POST['last_name']  . '", ' .
												'"' . $_POST['email'] . '", ' .
												'"' . $_POST['phone'] . '", ' .
												'"' . $_POST['address1'] . '", ' .
												'"' . $_POST['address2'] . '", ' .
												'"' . $_POST['city'] . '", ' .
												'"' . $_POST['state'] . '", ' .
												'"' . $_POST['DOB'] . '", ' .
												'"' .	$_POST['email_list'] . '", ' .
												1 . ', ' .
												'ENCODE("' . $_POST['password'] . '",' . '"yblcatx"), ' .
												 '"' . $_POST['zip'] . '");';	
	} else {
		
		// Update existing contact record
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
						   	GetSQLValueString($submitted_contact_id, "int"));
	}

	mysql_select_db($database_YBDB, $YBDB);
	$Result1 = mysql_query($updateSQL, $YBDB) or die(mysql_error());

	// Are there any interests in the datatbase?
	$interests = [];
	$sql = "SELECT option_name, option_name_id FROM options;";	
	$query = mysql_query($sql, $YBDB) or die(mysql_error());
	while ($result = mysql_fetch_assoc($query)) {
		$interests[$result["option_name"]] = $result["option_name_id"];		
	}	
	
	if ($volunteer_interest_form && !isset($volunteer_interests_changename)) {
				
		// populate database with user defined interests if they do not exist
		$volunteer_interest = array_combine($volunteer_interests,$volunteer_interests);		
		
		$c = 0;
		foreach ($volunteer_interest as $interest) {
			// Insert new interest
			if ( is_null($interests[$interest]) ) {
				$query = "INSERT INTO options (option_name) VALUES('" . $interest . "');";				 
				$result = mysql_query($query, $YBDB) or die(mysql_error());
			}	
		}	
		
	} // end volunteer_interest_form populate and/or delete

	// Change or delete an interest(s) name	
	if( isset($volunteer_interests_changename) ) {
		foreach ($volunteer_interests_changename as $key => $interest) { 
			$sql = "UPDATE options SET option_name='" . $interest . 
						"' WHERE option_name='" . $key . "';";	
			$query = mysql_query($sql, $YBDB) or die(mysql_error());
		}
	} else if( isset($volunteer_interests_deletename) ) {
		foreach ($volunteer_interests_deletename as $interest) { 
			$sql = "DELETE FROM options WHERE option_name='" . $interest . "';";	
			$query = mysql_query($sql, $YBDB) or die(mysql_error());
		}		
	}

	// If checked, save in database	
	$interest_checked = [];
	if(!empty($_POST['interest_checkboxes'])) {
   	foreach($_POST['interest_checkboxes'] as $check) {
			$interest_checked[$check] = $check;
    	}
 	}

	// Find out if any selections are in the database, 
	// to decide whether an INSERT or DELETE needs to be done
	$sql = "SELECT selection FROM selections WHERE contact_id=" . $submitted_contact_id . ";";	
	$query = mysql_query($sql, $YBDB) or die(mysql_error());
	$selections = [];
	while ($result = mysql_fetch_assoc($query)) {
		$selections[$result["selection"]] = $result["selection"];		
	}	
	
	foreach ($interests as $selection => $interest_id) {
		if ( is_null($selections[$interest_id]) ) {  //INSERT
			if( !is_null($interest_checked[$selection]) ) {
				$sql = "INSERT INTO selections (contact_id, selection, selection_value) 
							VALUES (" .	$submitted_contact_id . "," . $interest_id . ",1);";			 
				$result = mysql_query($sql, $YBDB) or die(mysql_error());	
			}
		} else {	 //DELETE
			if( is_null($interest_checked[$selection]) ) {		
				$sql = "DELETE FROM selections WHERE selection=" . $interest_id . 
						  " AND contact_id=" . $submitted_contact_id . ";";	
				$query = mysql_query($sql, $YBDB) or die(mysql_error());		
			}
		}		
	}
	 
	// insert as update?  But it works. 	
	if ($_POST['comments']) {
		$sql = "INSERT INTO selections (contact_id, selection, selection_value) 
				VALUES (" .	$submitted_contact_id . ", 1,'" . $_POST['comments']  . "');";			 
				$result = mysql_query($sql, $YBDB) or die(mysql_error());	
	}	 	

  if ($_POST['contact_id_entry'] == 'new_contact' || $_POST['contact_id_entry'] == $submitted_contact_id){
  
  	//navigate back to shop that it came from
	$pagegoto = PAGE_SHOP_LOG . "?shop_id={$shop_id}&new_user_id={$contact_id}";
	header(sprintf("Location: %s", $pagegoto));

  }

} // Submitted

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
			    <td>
			    <?php 
					if($_GET['contact_id'] == 'new_contact'){
						echo 'New Contact';	
					} else {
				    	echo $row_Recordset1['contact_id']; 
				 	}
			    ?>
			    </td>
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
				  </div><input id="waiver_checkbox" name="waiver_checkbox" type="checkbox"> I agree <span id="waiver_error"></span>
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
			
							if($_GET['contact_id'] != 'new_contact'){					
								$sql = "SELECT options.option_name AS selection FROM selections, options 
											WHERE selections.selection=options.option_name_id AND
											contact_id=" . $row_Recordset1['contact_id']  . ";";	
								$query = mysql_query($sql, $YBDB) or die(mysql_error());
							}							
							
							$selections = [];
							
							if($_GET['contact_id'] != 'new_contact'){		
								while ($result = mysql_fetch_assoc($query)) {
									$selections[$result["selection"]] = $result["selection"];		
								}	
							}
			
							$columns = 3;
							$c = 0;
							$rows = 0;
							$interest_count = count($volunteer_interests);														
							 								 
							 while($rows < $interest_count + 3) {				
								echo "<tr>";							
								
								for($i = $rows - $columns; $i < $rows; $i++) {
									if($volunteer_interests[$i]) {						
																			
										if($volunteer_interests[$i] === $selections[$volunteer_interests[$i]]) {								
											echo "<td><input name='interest_checkboxes[]' class='interest_checkboxes' 
															value='$volunteer_interests[$i]' type='checkbox' checked>" . 
															$volunteer_interests[$i] . "</td>";	
										} else {
											echo "<td><input name='interest_checkboxes[]' class='interest_checkboxes' 
															value='$volunteer_interests[$i]' type='checkbox'>" . 
															$volunteer_interests[$i] . "</td>";										
										}							
									}
								}
								echo "</tr>";								
								$rows = $rows + $columns;				
							}
						?>
						<?php if($volunteer_interest_comments) {
							
									if($_GET['contact_id'] != 'new_contact'){	 
										$sql = "SELECT selection_value AS comments FROM selections 
												  WHERE selection=1 AND contact_id=" . $row_Recordset1['contact_id']  . ";";
										$query = mysql_query($sql, $YBDB) or die(mysql_error());
										$result = mysql_fetch_assoc($query);
									}	
										  						
						?>							
						<tr><td>&nbsp;</td></tr>					
						<tr>		  	 
				  	 		<td class="center_comment" colspan="2"><label id="contact_comment">Comments</label>
				  	 		<textarea  name="comments" cols="45" rows="3"><?php echo $result['comments']; ?></textarea></td>
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
        <input type="hidden" id="contact_id" name="contact_id"
        <?php
	        if($_GET['contact_id'] === 'new_contact'){
					echo "value='new_contact'";	
				} else {
				   echo "value='" . $row_Recordset1['contact_id'] . "'"; 
				} 
        	?>>
        <input type="hidden" name="email_list" id="email_list">
        <input type="hidden" name="contact_id_entry" value="<?php echo $contact_id_entry; ?>">
        </form>
	  </tr>
</table>




<?php include("include_footer.html"); ?>

<?php
mysql_free_result($Recordset1);
?>
