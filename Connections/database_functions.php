<?php
require_once('YBDB.php');

// DO NOT EDIT - USE Connections/local_configurations instead

/*    
	Choose your timezone from http://php.net/manual/en/timezones.php 
	
	Eastern ........... America/New_York
	Central ........... America/Chicago
	Mountain .......... America/Denver
	Mountain no DST ... America/Phoenix
	Pacific ........... America/Los_Angeles
	Alaska ............ America/Anchorage
	Hawaii ............ America/Adak
	Hawaii no DST ..... Pacific/Honolulu

*/
define("TIMEZONE", "America/New_York");


/*********
MEMBERSHIP
**********/
// Define when a volunteer becomes a member based hours and days for the calendar year vs a paid membership

// Needs to volunteer at least this amount of defined hours before being considered a member
define("MEMBERSHIP_HOURS",8);

// Needs to volunteer at least this number of days before being considered a member
define("MEMBERSHIP_DAYS",2);

// Define how long a patron remains a member if they purchase a membership.
define("PURCHASED_MEMBERSHIP_DAYS",365);

/*******
CONTACTS
********/

// Allow waiver (recommended) in Add/Edit Contacts; 1 = yes, 0 = no
// Waiver text may be modified in Connections/waiver.txt
define("WAIVER", 1);

define("WAIVER_LABEL","Waiver of Liability and Safer Spaces Agreement:");

// Allow email_list option in Add/Edit Contacts; 1 = yes, 0 = no 
define("EMAIL_LIST", 1);

// Define a url for an email connector that will connect to an email list.
// The url can be a server:port, program, etc.  
// Name (First, Last), email address, subscription choice and connector password will be sent to the connector.
//
// The purpose of email connectors is to provide autonomy in the choice
// of email services and programs.  E.g. mailman, googlegroups
// See ./examples for an example mailman connector
define("EMAIL_LIST_CONNECTOR","https://wvcompletestreets.org:9987");

// Define the password that is unique to the connector.
define("EMAIL_LIST_CONNECTOR_PASSWORD","bikebike");

// If a self-signed ssl certificate that is associated with the email connector is being provided, 
// designate an absolute path, if not, change to false.
define("SSL_CERTIFICATE", "/var/www/html/examples/cert.pem");

//// "Volunteer Interest" form ////
define("VOLUNTEER_INTEREST_FORM", true);

// Form name
define("VOLUNTEER_INTEREST_FORM_NAME","Volunteer Interests");

// NOTE: Introductory text can be modified in Connections/volunteer_interest_form_introduction.txt

// Define volunteer interests:
//
//  You can add new interests to this array.  
//  The order is kept as 3 columns, left to right.
//
//  If you want to delete or change the name of an interest, read instructions below carefully for
//  "Delete an interest(s) name and "Change an interest(s) name"
$volunteer_interests = array(	
									"Repairing Bikes", "Organizing Volunteers", "Serving as a Board Member",
									"Arranging Events", "Volunteering at Events", "Writing Grants/Fundraising",
									"Answering our Phone", "3D-printing", "Bicycle Valeting",		
									"Publicizing/Outreach", "Graphic Design", "Greeter at the Front Desk",
									"Accounting/Record Keeping", "Ordering parts/supplies", "Picking up Donated Bikes/Parts",
									"Teaching classes", "League Certified Instructor", "Pricing bikes",
									 "Fabricating", "Open Source Programming", "Other/Contact me for general help"
								);
								
// Provide a comment box - true of false
define("VOLUNTEER_INTEREST_COMMENTS", true);

// NOTE: The 2 variables ($volunteer_interest_changename & $volunteer_interests_deletename) 
// below allow you to change or delete an interest.
// Only uncomment one variable at a time, and follow the directions.

// Change an interest(s) name:
//
// 1.  Associate the name you want to change with a different name to the right as show below.
//     In this example "League Certified Instructor" will become "LCI".
//	2.  Change the interests name in $volunteer_interests above at the same time.  
// 3.  Visit your own contact, e.g. contact_add_edit.php?contact_id=1 and click on the Submit button,
//		 and the database will be updated.
// 4.  Comment out //$volunteer_interests_changename
//
// $volunteer_interests_changename = array("League Certified Instructor" => "LCI");

// [BUGGY - don't use it] Delete an interest(s) name.  
//
// 1.  Add the interest(s) you want to delete. Please understand
//     that by doing this you will delete the interest and all associated data.
// 2.  Remove the interest from $volunteer_interests above at the same time before saving this page, 
//     or it will be recreated.
// 3.  Visit your own contact, e.g. contact_add_edit.php?contact_id=1 and click on the Submit button,
//		 and the database will be updated.
//
//$volunteer_interests_deletename = array("LCI");

/***********
TRANSACTIONS
************/

// User defined constants - read sql/populate.sql for an explanation
define("STORAGE_PERIOD", 14);
define("ACCOUNTING_GROUP", "Sales");
define("DEFAULT_TRANSACTION_TYPE", "Sale - Used Parts");

// shop_user_role
define("DEFAULT_SHOP_USER", "Personal");

/* Change Fund - A specific amount of money for the purpose of making change. 
   The amount on hand should remain the same at all times; 
   therefore a change funds does not require replenishment.
*/
define("CHANGE_FUND", 20);

// How many hours should the shop be open from the time a person logins?  Hours display in pulldown in shop_log.php
// No overtime for volunteers.  :)
// shop will be current shop for the 24hr day yyyy-mm-dd (currently no check for hrs, only date)
define("SHOP_HOURS_LENGTH", 10);  


/* If you elect to keep records for non-shop hours, decide which shop should be used for that purpose.
   The first shop created, 1, makes sense.  A link will show in start_shop.php.
	If you do not want this functionality at all, choose 0.   
*/
define("NONSHOP", 0);

// How many transactions do you want shown by default
define("NUMBER_OF_TRANSACTIONS", 11);


// Define csv directory (see directions below for creating it)
define("CSV_DIRECTORY","csv"); 

// Make a directory to store csv accounting files.  Currently used for GnuCash format.
// Assuming the root of the website, and directory is called csv, and a Debian-based distribution:
// mkdir csv
// chown www-data:www-data csv 
// chmod 0700 csv 


// Define array mapping for Accounts.  Usually Asset Accounts since Income is generally the main type of transaction.
// Currently four types of accounts are supported:  checking, credit, account_receivable, donation 
// 	checking/credit = transaction has been 1) paid and is 2) cash & check [checking] or a credit card [credit] and 3) deposited
//				note: checking type may include $0 transactions, e.g. earn-a-bike
//	   account_receivable =  there is an 1). account receivable invoice and it has been 2) paid and 3) deposited
//		donation = transaction that currently has no assessed monetary amount (NULL), 
//					e.g. shop item giveaway or patron non-monetary donation
//					note:  this is a hack for record keeping in an accounting program, and not recommended; 
//					 bike donations/giveaways should be handled in a better way like using BikeBinder, 
//					 an inventory system application,that may be tied into YBDB someday
$gnucash_accounts = array(	"Assets:Current Assets:Checking Account" => "checking",
									"Assets:Current Assets:PayPal" => "credit",
									"Assets:Account Receivable" => "account_receivable",
									"Assets:Donations" => "donation"
								);

// Most collectives require only one shop at a time, but YBDB provides a way to handle 2 concurrent
// shops at different locations using the same instance of software.  If this option is on, 
// the current shop will still be shown, but users have the option of changing a transaction to the id 
// of the other shop location whether it is concurrent or whether it a shop from the same location that 
// happened at a previous time. 
// 
// Note: Remote shops function independently via their IP identification.
//
// Normally, you will want this set at 0.
define('SHOW_SHOP_ID',0);

// END OF USER DEFINED CONFIGURATIONS


if(file_exists( realpath($_SERVER["DOCUMENT_ROOT"]) . "/Connections/local_configurations.php")) {
	require_once('local_configurations.php');
}

// other constants
define("PAGE_START_SHOP", "/start_shop.php");
define("PAGE_SHOP_LOG", "/shop_log.php");
define("PAGE_EDIT_CONTACT", "/contact_add_edit.php");
define("PAGE_SELECT_CONTACT", "/contact_add_edit_select.php");
define("PAGE_SHOP_LOG_DELETE_VISIT", "/shop_log_delete_shopvisitid.php");
define("INDIVIDUAL_HOURS_LOG", "/individual_hours_log.php");
define("INDIVIDUAL_HISTORY_LOG", "/individual_history_log.php");
define("PAGE_SALE_LOG", "/transaction_log.php");
define("PAGE_EDIT_LOCATION", "/location_add_edit.php");
define("PAGE_SELECT_LOCATION", "/location_add_edit_select.php");

//This is a general function to generate the contents of a list box based on a MySQL query.  All necessary parameters for the query are passed 
function generate_list($querySQL,$list_value,$list_text, $form_name, $default_value)
{
	global $database_YBDB, $YBDB;
	mysql_select_db($database_YBDB, $YBDB);
	$recordset = mysql_query($querySQL, $YBDB) or die(mysql_error());
	$row_recordset = mysql_fetch_assoc($recordset);
	$totalRows_recordset = mysql_num_rows($recordset);
	$default_delimiter = '';
	
	// if a form name is supplied HTML listbox code is inserted
	if($form_name == "transaction_type" || $form_name == "gnucash_csv_year"){
		echo "<select class=\"yb_standard\" name=\"$form_name\">";
	} elseif($form_name <> "none"){ 
		echo "<select name=\"$form_name\">";	
	}

	echo "\n";
	do { 
		if( $default_value == $row_recordset[$list_value]){ 
			$default_delimiter = 'selected="selected"';
		} else { $default_delimiter = '';}
		echo '<option value="' . $row_recordset[$list_value] . '"' . $default_delimiter .'>' . $row_recordset[$list_text] . '</option>\n';		
		} while ($row_recordset = mysql_fetch_assoc($recordset));
 	$rows = mysql_num_rows($recordset);
 	if($rows > 0) {
      mysql_data_seek($recordset, 0);
	  $row_recordset = mysql_fetch_assoc($recordset);
		}
	mysql_free_result($recordset);
	
	// if a form name is supplied HTML listbox code is inserted
	if($form_name <> "none"){echo "</select>";}
}


function list_CurrentShopUsers($form_name = "none", $default_value = "", $max_name_length = 20 ){
	$current_shop = current_shop_by_ip();
	$querySQL = "SELECT DISTINCT full_name, shop_hours.contact_id ,hidden FROM shop_hours
					LEFT JOIN (SELECT LEFT(CONCAT(last_name, ', ', first_name, ' ',middle_initial),$max_name_length)
					AS full_name, contact_id, hidden FROM contacts) as contacts ON shop_hours.contact_id=contacts.contact_id
					WHERE shop_hours.shop_id = $current_shop
					ORDER BY full_name;";
	$list_value = "contact_id";
	$list_text = "full_name";
	generate_list($querySQL,$list_value,$list_text,$form_name, $default_value);
}

// #####################
// Drop down list queries - functions below could be made into one function if query, $list_value and $list_text parameters were passed

// Function provides specific MySQL parameters to the function that generates the list box code
function list_contacts($form_name = "none", $default_value = "", $max_name_length = 30 ){
	$querySQL = "SELECT LEFT(CONCAT(last_name, ', ', first_name, ' ',middle_initial),$max_name_length) AS full_name, contact_id, hidden FROM contacts WHERE (first_name <> '' OR last_name <> '') AND hidden <> 1 ORDER BY last_name, first_name, middle_initial";
	$list_value = "contact_id";
	$list_text = "full_name";
	generate_list($querySQL,$list_value,$list_text,$form_name, $default_value);
}


function list_coordinators($form_name = "none", $default_value = "", $max_name_length = 20 ){
	$querySQL = "SELECT LEFT(CONCAT(last_name, ', ', first_name, ' ',middle_initial),40) AS full_name, contacts.contact_id, hidden, shop_user_role FROM contacts
LEFT JOIN (SELECT contact_id, shop_user_role, sales FROM shop_hours
LEFT JOIN shop_user_roles ON shop_user_roles.shop_user_role_id = shop_hours.shop_user_role
WHERE shop_user_roles.sales = 1 GROUP BY contact_id) as shop_hours ON shop_hours.contact_id=contacts.contact_id
WHERE (first_name <> '' OR last_name <> '') AND hidden <> 1 AND shop_hours.sales  = 1
GROUP BY contacts.contact_id
ORDER BY last_name, first_name, middle_initial;";
	$list_value = "contact_id";
	$list_text = "full_name";
	generate_list($querySQL,$list_value,$list_text,$form_name, $default_value);
}

function list_current_coordinators($form_name = "none", $default_value = "", $max_name_length = 20 ){
	$current_shop = current_shop_by_ip();
	$querySQL = "SELECT LEFT(CONCAT(last_name, ', ', first_name, ' ',middle_initial),40) AS full_name, contacts.contact_id, hidden, shop_user_role FROM contacts
LEFT JOIN (SELECT contact_id, shop_user_role, sales FROM shop_hours LEFT JOIN shop_user_roles ON shop_user_roles.shop_user_role_id = shop_hours.shop_user_role WHERE shop_user_roles.sales = 1 AND shop_id = $current_shop GROUP BY contact_id) as shop_hours ON shop_hours.contact_id=contacts.contact_id
WHERE (first_name <> '' OR last_name <> '') AND hidden <> 1 AND shop_hours.sales  = 1
GROUP BY contacts.contact_id
ORDER BY last_name, first_name, middle_initial;";
	$list_value = "contact_id";
	$list_text = "full_name";
	generate_list($querySQL,$list_value,$list_text,$form_name, $default_value);
}

// Function provides specific MySQL parameters to the function that generates the list box code
function list_projects($form_name = "none", $default_value = ""){
	$querySQL = "SELECT project_id FROM projects WHERE active = 1 AND public = 1 ORDER BY project_id";
	$list_value = "project_id";
	$list_text = "project_id";
	generate_list($querySQL,$list_value,$list_text,$form_name, $default_value);
}

// Function provides specific MySQL parameters to the function that generates the list box code
function list_projects_collective($form_name = "none", $default_value = ""){
	$querySQL = "SELECT project_id FROM projects WHERE active = 1 ORDER BY public DESC, project_id";
	$list_value = "project_id";
	$list_text = "project_id";
	generate_list($querySQL,$list_value,$list_text,$form_name, $default_value);
}

// Function provides specific MySQL parameters to the function that generates the list box code
function list_shop_types($form_name = "none", $default_value = ""){
	$querySQL = "SELECT shop_type_id FROM shop_types ORDER BY list_order;";
	$list_value = "shop_type_id";
	$list_text = "shop_type_id";
	generate_list($querySQL,$list_value,$list_text,$form_name, $default_value);
}

// Function provides specific MySQL parameters to the function that generates the list box code
function list_shop_user_roles($form_name = "none", $default_value = ""){
	$querySQL = "SELECT shop_user_role_id FROM shop_user_roles ORDER BY shop_user_role_id;";
	$list_value = "shop_user_role_id";
	$list_text = "shop_user_role_id";
	generate_list($querySQL,$list_value,$list_text,$form_name, $default_value);
}

// Function provides specific MySQL parameters to the function that generates the list box code
function list_shop_locations($form_name = "none", $default_value = ""){
	$querySQL = "SELECT shop_location_id FROM shop_locations WHERE active = 1 ORDER BY shop_location_id;";
	$list_value = "shop_location_id";
	$list_text = "shop_location_id";
	generate_list($querySQL,$list_value,$list_text,$form_name, $default_value);
}

// Function provides specific MySQL parameters to the function that generates the list box code
function list_transaction_types($form_name = "none", $default_value = ""){
	$querySQL = "SELECT transaction_type_id FROM transaction_types ORDER BY rank + 0;";
	$list_value = "transaction_type_id";
	$list_text = "transaction_type_id";
	generate_list($querySQL,$list_value,$list_text,$form_name, $default_value);
}

function list_donation_types($form_name = "none", $default_value = ""){
	$querySQL = "SELECT transaction_type_id FROM transaction_types WHERE community_bike = 1;";
	$list_value = "transaction_type_id";
	$list_text = "transaction_type_id";
	generate_list($querySQL,$list_value,$list_text,$form_name, $default_value);
}

function list_donation_locations($form_name = "none", $default_value = "", $max_name_length = 20 ){
	$querySQL = "SELECT LEFT(CONCAT(last_name, ', ', first_name, ' ',middle_initial),$max_name_length) AS full_name, 
					location_name, contact_id FROM contacts WHERE location_type IS NULL ORDER BY location_name";
	$list_value = "contact_id";
	$list_text = "full_name";
	generate_list($querySQL,$list_value,$list_text,$form_name, $default_value);
}

// Function provides specific MySQL parameters to the function that generates the list box code
function list_distinct_shop_years($form_name = "none", $default_value = ""){
	$querySQL = "SELECT DISTINCT YEAR(date) AS date FROM shops WHERE date!='NULL' ORDER BY date DESC;";
	$list_value = "date";
	$list_text = "date";
	generate_list($querySQL,$list_value,$list_text,$form_name, $default_value);
}


function list_history($object) {
/*	require_once('../php-console/src/PhpConsole/__autoload.php');
	$handler = PhpConsole\Handler::getInstance();
	$handler->start();
	$handler->debug($object);*/
	
}

// #####################

// Date/Time functions
function currency_format($value, $places = 2){
	echo "$ "; 
	if(is_null($value)) echo number_format(0,$places); 
	else echo number_format($value,$places);
}

//function to convert server time to local time.  To be used by all other current date / time requests. 
function local_datetime(){
	date_default_timezone_set(TIMEZONE); 
	return time();
}

//function converts the current date/time into h:m am format 
function current_datetime(){
	return date("Y-m-d H:i:s",local_datetime());
}

//function converts the current date/time into YYYY-MM-DD am format 
function current_date(){
	return date("Y-m-d",local_datetime());
}

//function converts the current date/time into h:m am format 
function date_to_time($date_in){
	list($date, $time) = split('[ ]', $date_in);
	list($H, $i, $s) = split('[:]', $time);
	$time_out = date("g:i a", mktime($H, $i, $s, 1,1,2000));
	return $time_out;
}

//takes a date in and adds current time if date has changed
function date_update_wo_timestamp($date_in, $database_date){
	list($date, $time) = split('[ ]', $database_date);
	$timestamp_out = (($date == $date_in) ? $database_date : $date_in);
	return $timestamp_out;
}

function date_to_timestamp($date_in){
	list($date, $time) = split('[ ]', $start_time);
	list($Y, $m, $d) = split('[-]', $date);
	list($H, $i, $s) = split('[:]', $time);
	$time_out = mktime($H, $i, $s, $m, $d, $Y);
	return $time_out;
}

//
function datetime_to_time($date_in){
	list($date, $time) = split('[ ]', $date_in);
	list($H, $i, $s) = split('[:]', $time);
	$time_out = date("H:i:s", mktime($H, $i, $s, 1,1,2000));
	return $time_out;
}

//
function datetime_to_date($date_in){
	list($date, $time) = split('[ ]', $date_in);
	list($Y, $m, $d) = split('[-]', $date);
	$date_out = date("Y-m-d", mktime($H, $i, $s, $m,$d,$Y));
	return $date_out;
}

// END Date/Time functions

//Function creates list box with times every 15 minutes for the specified number of hours
function list_15min($start_time, $start_offset_min, $form_name, $hours, $display_elapsed_hours, $default_value){
	list($date, $time) = split('[ ]', $start_time);
	list($Y, $m, $d) = split('[-]', $date);
	list($H, $i, $s) = split('[:]', $time);
	//$min_inc is used to round round to nearest 15min
	$min_inc = 15 - intval($i) % 15;
	$start_tim15 = mktime($H, $i, 0, $m,$d,$Y) + $min_inc * 60 + $start_offset_min*60 ;
	//$start_time_am = date("H:i a", mktime($H, $i, $s, 1,1,2000));
	
	echo "<select name=\"$form_name\">";
	if($default_value <> "none"  && $default_value <> "0000-00-00 00:00:00"){
		//if a default value is requested it is displayed at the top of the list
		echo '<option value="' . $default_value . '">' . date_to_time($default_value) . '</option>';
	} 
	if (current_date() == $date) {
		// if current date does not match shop date current date will no be an option
		echo '<option value="current_time">Current Time</option>';
		echo '<option value="current_time">--------------------</option>';
	} 
	for ($j = 0; $j <= $hours*4; $j++) {
		$list_time_15 = $start_tim15 + $j*15*60;
		if ($display_elapsed_hours == 1) {
			$elapsed_hours = " &nbsp;&nbsp;[" . date("G:i",mktime(0, 0, 0, 1,1,2000) + ($j+1)*15*60). " hrs]";
		} else { 
			$elapsed_hours = "";
		}
			
		$list_time_15_return = date("Y-m-d H:i:s", $list_time_15); 
		$list_time_15_display = date("g:i a", $list_time_15). $elapsed_hours;
   		echo "<option value=\"". $list_time_15_return ."\">" . $list_time_15_display . "</option>";
	}
	echo "</select>";
	
}


function list_time($time_list_start, $time, $form_name = "none", $start_offset_min = 0 , $display_elapsed_hours = 0, $default_value = "none", $hours_listed = 8, $et = ""){
	if($time == "0000-00-00 00:00:00" || $default_value <> "none"){
		//create drop down
		//echo list_15min("0000-00-00 01:20:00", 4, "frm_time_out" );
		echo list_15min($time_list_start,$start_offset_min, $form_name, $hours_listed, $display_elapsed_hours, $default_value );
	} else {
		//list time out
		echo date_to_time($time) . "&nbsp;&nbsp;[{$et} hrs]";
	}

}

function sign_out($time_out, $first_name){
	if($time_out == "0000-00-00 00:00:00"){
		echo '<input type="submit" name="submit" class="sign_out" value="Sign Out: ' . $first_name . '" />';
	}
}

//This function corrects the datatype for form submitted variables 
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  $theValue = (!get_magic_quotes_gpc()) ? addslashes($theValue) : $theValue;

  switch ($theType) {
    case "text":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;    
    case "long":
    case "int":
      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
      break;
    case "double":
      $theValue = ($theValue != "") ? "'" . doubleval($theValue) . "'" : "NULL";
      break;
    case "date":
      if(($theValue == 'current_time') || ($theValue == 'Current Date')){
	  	$theValue = current_datetime();
	  }
	  
	  $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;
    case "defined":
      $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
      break;
  }
  return $theValue;
}

function dateandtimein($date, $time){ 
  if ($time <> 'current_time'){
  	$time = $date . ' ' . datetime_to_time($time); 
  }
  return $time;
}


// Drop-Down lists

function list_contacts_edit_add($form_name = "contact_id", $default_value = "")
{
	echo "<select name={$form_name} class='yb_standard'>\n";
	echo "<option value='new_contact'>Add New Contact</option>\n";
	echo "<option value='new_contact'>--------------</option>";
	list_contacts("none",$default_value);	
	echo "</select>\n";
}

function list_contacts_select_user($form_name = "contact_id", $default_value = "")
{
	echo "<select name={$form_name} class='yb_standard'>\n";
	echo "<option value='no_selection'>Select Yourself<section></section></option>\n";
	echo "<option value='no_selection'>--------------</option>";
	list_contacts("none",$default_value);	
	echo "</select>\n";
}

function list_CurrentShopUsers_select($form_name = "contact_id", $default_value = "")
{
	echo "<select name={$form_name} class='yb_standard'>\n";
	echo "<option value='no_selection'>Select Patron</option>\n";
	echo "<option value='no_selection'>--------------</option>";
	list_CurrentShopUsers("none",$default_value);	
	echo "</select>\n";
}

function list_contacts_YBP_project($form_name = "contact_id", $default_value = "")
{
	echo "<select name={$form_name} class='yb_standard'>\n";
	echo "<option value='1269'>Yellow Bike Project</option>\n";
	echo "<option value='no_selection'>--------------</option>";
	list_contacts("none",$default_value);	
	echo "</select>\n";
}

	function list_contacts_coordinators($form_name = "coordinator_id", $default_value = "")
{
	echo "<select name={$form_name} class='yb_standard'>\n";
	echo "<option value='no_selection'>Select Coordinator</option>\n";
	echo "<option value='no_selection'>--------------</option>";
	list_coordinators("none",$default_value);	
	echo "</select>\n";
}

	function list_current_coordinators_select($form_name = "coordinator_id", $default_value = "")
{
	echo "<select name={$form_name} class='yb_standard'>\n";
	echo "<option value='no_selection'>Select Coordinator</option>\n";
	echo "<option value='no_selection'>--------------</option>";
	list_current_coordinators("none",$default_value);	
	echo "</select>\n";
}

	function list_donation_locations_withheader($form_name = "coordinator_id", $default_value = "")
{
	echo "<select name={$form_name} class='yb_standard'>\n";
	echo "<option value='no_selection'>Select Patron</option>\n";
	echo "<option value='no_selection'>--------------</option>";
	list_donation_locations("none",$default_value);	
	echo "</select>\n";
}

function list_donation_locations_edit_add($form_name = "contact_id", $default_value = "")
{
	echo "<select name={$form_name} class='yb_standard'>\n";
	echo "<option value='new_contact'>Add New Location</option>\n";
	echo "<option value='new_contact'>--------------</option>";
	list_donation_locations("none",$default_value);	
	echo "</select>\n";
}

	function list_transaction_types_withheader($form_name = "transaction_types", $default_value = "")
{
	echo "<select name={$form_name} class='yb_standard'>\n";
	echo "<option value='all_types'>All Types</option>\n";
	echo "<option value='all_types'>--------------</option>";
	list_transaction_types("none",$default_value);	
	echo "</select>\n";
}

function list_yes_no($form_name = "list_yes_no", $default_value = 0)
{
	if ($default_value == 1){
		$select_yes = 'selected="selected"';
		$select_no = '';
	} else {
		$select_yes = '';
		$select_no = 'selected="selected"';
	}
	
	echo "<select name={$form_name} class='yb_standard'>\n";
	echo "<option value='1'". $select_yes .">Yes</option>\n";
	echo "<option value='0'". $select_no .">No</option>";	
	echo "</select>\n";
}

// END Drop-Down lists

function max_shop_id(){
	global $database_YBDB, $YBDB;	
    
	mysql_select_db($database_YBDB, $YBDB);
	$query_Recordset1 = "SELECT max(shop_id) as shop_id FROM shops;";
	$Recordset1 = mysql_query($query_Recordset1, $YBDB) or die(mysql_error());
	$row_Recordset1 = mysql_fetch_assoc($Recordset1);
	$totalRows_Recordset1 = mysql_num_rows($Recordset1);
	return $row_Recordset1['shop_id'];
}

// Is there currently a shop?
function current_shop_by_ip(){
	global $database_YBDB, $YBDB;
	$IP = $_SERVER['REMOTE_ADDR'];
	$current_date = current_date();
	
	mysql_select_db($database_YBDB, $YBDB);
	$query_Recordset1 = "SELECT shop_id FROM shops WHERE ip_address = '{$IP}' AND date = '{$current_date}' ORDER BY shop_id DESC;";
	$Recordset1 = mysql_query($query_Recordset1, $YBDB) or die(mysql_error());
	$row_Recordset1 = mysql_fetch_assoc($Recordset1);   
	$totalRows_Recordset1 = mysql_num_rows($Recordset1);
	return $row_Recordset1['shop_id'];
}


?>
