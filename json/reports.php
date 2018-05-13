<?php

// new logic for reports

require_once('../Connections/database_functions.php');
require_once('../Connections/YBDB.php');
mysql_select_db($database_YBDB, $YBDB);
// This resolves an issue when mysql_fetch_assoc fails (doesn't work) because of something like é in the results
mysql_query("SET NAMES 'utf8'", $YBDB);

/*
require_once('../php-console/src/PhpConsole/__autoload.php');
$handler = PhpConsole\Handler::getInstance();
$handler->start();
*/

	// Return total contacts
	if (isset($_POST['total'])) {	
		$query = "SELECT COUNT(contact_id) AS total FROM contacts;";
		$sql = mysql_query($query, $YBDB) or die(mysql_error());
		$result = mysql_fetch_assoc($sql);
		echo json_encode($result);
	}

	// Return name
	if (isset($_POST['name'])) {
		$query = "SELECT CONCAT(contacts.first_name, ' ', contacts.middle_initial, ' ',contacts.last_name) AS full_name 
					FROM contacts 
					WHERE contact_id=" . $_POST['contact_id'] .";";
		 $sql = mysql_query($query, $YBDB) or die(mysql_error());
		 $result = mysql_fetch_assoc($sql);
		 $etherpad_conf["configurations"] = $etherpad;
		 $result = (object)array_merge((array)$result, (array)$etherpad_conf);
		 echo json_encode($result);		
	}

	// Return projects
	if (isset($_POST['projects'])) {
		$query = "SELECT project_id FROM projects;";
		$sql = mysql_query($query, $YBDB) or die(mysql_error());
		while ( $result = mysql_fetch_assoc($sql) ) {	
			$results[] = $result;				
		}	
		echo json_encode($results);		
	}
	
	// Return roles (statuses)
	if (isset($_POST['roles'])) {
		$query = "SELECT shop_user_role_id FROM shop_user_roles;";
		$sql = mysql_query($query, $YBDB) or die(mysql_error());
		while ( $result = mysql_fetch_assoc($sql) ) {	
			$results[] = $result;				
		}	
		echo json_encode($results);		
	}

	// Return individual history				var obj = $.parseJSON(data);
	if (isset($_POST['individual_history'])) {
		
		
		$query = "SELECT shop_id, shop_hours.shop_visit_id, shop_hours.contact_id, shop_hours.shop_user_role, shop_hours.project_id, 
									DATE(shop_hours.time_in) AS date, 
									DAYNAME(shop_hours.time_in) AS dayname, 
									shop_hours.time_in, shop_hours.time_out, 
									TIME_FORMAT(TIMEDIFF(time_out, time_in),'%k:%i') AS et, 
									shop_hours.comment, 
									CONCAT(contacts.last_name, ', ', contacts.first_name, ' ',contacts.middle_initial) 
									AS full_name, contacts.first_name 
									FROM shop_hours
									LEFT JOIN shop_user_roles ON shop_hours.shop_user_role=shop_user_roles.shop_user_role_id
									LEFT JOIN contacts ON shop_hours.contact_id=contacts.contact_id
									WHERE shop_hours.contact_id =" . $_POST['contact_id'] . " ORDER BY shop_id DESC;";																
									
		$sql = mysql_query($query, $YBDB) or die(mysql_error());	
		
		
		while ( $result = mysql_fetch_assoc($sql) ) {	
			$results[] = $result;				
		}	
		
		echo json_encode($results);
		
		
	} // individual_history

	// Return everyone
	if (isset($_POST['everyone'])) {

		$interval = 1;			
			
		$query = "SELECT * FROM (SELECT contacts.contact_id, CONCAT(contacts.first_name, ' ', contacts.middle_initial, ' ',contacts.last_name) AS full_name,
			COUNT(shop_hours.contact_id) as th_visits,
			ROUND(SUM(HOUR(TIMEDIFF( time_out, time_in)) + MINUTE(TIMEDIFF( time_out, time_in))/60)) AS th_hours
			FROM shop_hours
			LEFT JOIN contacts ON shop_hours.contact_id = contacts.contact_id
			LEFT JOIN shop_user_roles ON shop_hours.shop_user_role = shop_user_roles.shop_user_role_id
			GROUP BY contact_id
			ORDER BY last_name, first_name) AS total_hours
			LEFT JOIN (SELECT contacts.contact_id AS vh_contact_id,
			COUNT(shop_hours.contact_id) as vh_visits,
			ROUND(SUM(HOUR(TIMEDIFF( time_out, time_in)) + MINUTE(TIMEDIFF( time_out, time_in))/60)) AS vh_hours
			FROM shop_hours
			LEFT JOIN contacts ON shop_hours.contact_id = contacts.contact_id
			LEFT JOIN shop_user_roles ON shop_hours.shop_user_role = shop_user_roles.shop_user_role_id
			WHERE shop_user_roles.volunteer = 1
			GROUP BY contacts.contact_id
			ORDER BY last_name, first_name) AS volunteer_hours ON total_hours.contact_id = volunteer_hours.vh_contact_id
			LEFT JOIN (SELECT contacts.contact_id AS th3_contact_id,
			COUNT(shop_hours.contact_id) as th3_visits,
			ROUND(SUM(HOUR(TIMEDIFF( time_out, time_in)) + MINUTE(TIMEDIFF( time_out, time_in))/60)) AS th3_hours
			FROM shop_hours
			LEFT JOIN contacts ON shop_hours.contact_id = contacts.contact_id
			LEFT JOIN shop_user_roles ON shop_hours.shop_user_role = shop_user_roles.shop_user_role_id
			WHERE time_in > DATE_SUB(CURDATE(),INTERVAL $interval MONTH)
			GROUP BY contacts.contact_id
			ORDER BY last_name, first_name) AS total_hours3 ON total_hours.contact_id = total_hours3.th3_contact_id
			LEFT JOIN (SELECT contacts.contact_id AS vh3_contact_id,
			COUNT(shop_hours.contact_id) as vh3_visits,
			ROUND(SUM(HOUR(TIMEDIFF( time_out, time_in)) + MINUTE(TIMEDIFF( time_out, time_in))/60)) AS vh3_hours
			FROM shop_hours
			LEFT JOIN contacts ON shop_hours.contact_id = contacts.contact_id
			LEFT JOIN shop_user_roles ON shop_hours.shop_user_role = shop_user_roles.shop_user_role_id
			WHERE shop_user_roles.volunteer = 1 AND time_in > DATE_SUB(CURDATE(),INTERVAL $interval MONTH)
			GROUP BY contacts.contact_id
			ORDER BY last_name, first_name) AS volunteer_hours3 ON total_hours.contact_id = volunteer_hours3.vh3_contact_id;";	

		$sql = mysql_query($query, $YBDB) or die(mysql_error());
		$result = mysql_fetch_assoc($sql); 
		echo json_encode($result);

	} // return everyone	
	
	// Latest transaction_id
	if (isset($_POST['record_count'])) {

		$query = 'SELECT MAX(transaction_id) AS record_count FROM transaction_log;';
		$sql = mysql_query($query, $YBDB) or die(mysql_error());
		$result = mysql_fetch_assoc($sql);
		echo json_encode($result);

	}

?>
