<?php

require_once('../Connections/database_functions.php');
require_once('../Connections/YBDB.php');
mysql_select_db($database_YBDB, $YBDB);
$email_list_connector = EMAIL_LIST_CONNECTOR;
$email_list_connector_password = EMAIL_LIST_CONNECTOR_PASSWORD;
$ssl_certificate = SSL_CERTIFICATE;


	// test whether patron's name already exists
	if (isset($_POST['test_name'])) {
		
			if( $_POST['first_name'] && $_POST['last_name'] ) {
				$query = 'SELECT first_name, middle_initial, last_name, contact_id FROM contacts WHERE ' .
							'first_name="' . $_POST['first_name'] . '" AND middle_initial="' . $_POST['middle_initial'] .  
							'" AND last_name="' . $_POST['last_name'] . '";';
				$sql = mysql_query($query, $YBDB) or die(mysql_error());
				$result = mysql_fetch_assoc($sql);
				if ( is_array($result) && ($result["contact_id"] !== $_POST['contact_id']) ) {
					echo 1;			
				} else {
					echo 0;			
				}
			}
					
	}
	
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
	

	// pass banned contact_id values if they exist
	if (isset($_POST['banned'])) {

		if ($banned_individuals) {
			echo json_encode($banned_individuals);		
		}		

	}	
	
	// pass probation contact_id values if they exist
	if (isset($_POST['probation'])) {

		if ($probation_individuals) {
			echo json_encode($probation_individuals);		
		}		

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

	// send data to connector (local or remote)
	if (isset($_POST['email_list_connector']) == 'subscribe' || isset($_POST['email_list_connector']) == 'unsubscribe' ) {

		$json = array(
					'subscribe' => $_POST['email_list_connector'],
		      	'password'	=> $email_list_connector_password,
				 	'email'		=> $_POST['email'],
				  	'first_name' => $_POST['first_name'],
				  	'last_name'	=> $_POST['last_name'],
		    );

		$ch = curl_init();
		$curlConfig = array(
		    CURLOPT_URL            =>  $email_list_connector,
		    CURLOPT_POST           => true,
			 CURLOPT_SSL_VERIFYPEER => true,
		    CURLOPT_RETURNTRANSFER => true,
		    CURLOPT_POSTFIELDS     => json_encode($json),
		); 
  
  		if ($ssl_certificate) {    
      	$curlConfig[CURLOPT_CAINFO] = $ssl_certificate;		
		}				
		
		curl_setopt_array($ch, $curlConfig);
		$result = curl_exec($ch);
		curl_close($ch);	

		echo $result;	
						
	}
	
	if(isset($_POST['most_recent_contact_id'])) {
		$query = 'SELECT MAX(contact_id) as contact_id FROM contacts;';
		$sql = mysql_query($query, $YBDB) or die(mysql_error());	
		$result = mysql_fetch_assoc($sql);	
		echo $result['contact_id'];	
	}	
	
	if(isset($_POST['global_pad'])) {
	 $etherpad_conf["configurations"] = $etherpad_global;
	 echo json_encode($etherpad_conf);
	}	
	

?>