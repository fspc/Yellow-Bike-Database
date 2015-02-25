<?php

require_once('../Connections/database_functions.php');
require_once('../Connections/YBDB.php');
mysql_select_db($database_YBDB, $YBDB);

	// update waiver 
	if( isset($_POST['waiver']) ) {		
				
				$waiver = $_POST['waiver'];	
			
				$query = "UPDATE contacts SET waiver=" . $waiver . 
							" WHERE contact_id=" . $_POST['contact_id'] . ";";				 
				$result = mysql_query($query, $YBDB) or die(mysql_error());
		
	}
	
	// return waiver value	
	if (isset($_POST['waiver_value'])) {
		
			$query = 'SELECT waiver FROM contacts WHERE contact_id="' . $_POST['contact_id'] . '";';
			$sql = mysql_query($query, $YBDB) or die(mysql_error());
			$result = mysql_fetch_assoc($sql);
			echo $result['waiver'];
					
	}
	
	// update email_list
	if( isset($_POST['email_list']) ) {		
				
				$email_list = $_POST['email_list'];	
				$query = "UPDATE contacts SET receive_newsletter=" . $email_list .
							" WHERE contact_id=" . $_POST['contact_id'] . ";";				 
				$result = mysql_query($query, $YBDB) or die(mysql_error());
		
	}	

	// return email_list value	
	if (isset($_POST['email_list_value'])) {
		
			$query = 'SELECT receive_newsletter FROM contacts WHERE contact_id="' . $_POST['contact_id'] . '";';
			$sql = mysql_query($query, $YBDB) or die(mysql_error());
			$result = mysql_fetch_assoc($sql);
			echo $result['receive_newsletter'];
				
	}


?>