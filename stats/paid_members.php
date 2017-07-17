<?php

require_once('../Connections/YBDB.php');
require_once('../Connections/database_functions.php');
$page_individual_history_log = INDIVIDUAL_HISTORY_LOG;
// Needs to volunteer at least this amount of defined hours before being considered a member

mysql_select_db($database_YBDB, $YBDB);

$today = date("Y/m/d");
$year_ago = date("Y/m/d", strtotime("$today -1 year"));

$today_date = new DateTime('now');
$past = new DateTime($year_ago);
$interval = $today_date->diff($past);

$chosen_date = $today;
$days_range1 = $interval->days;
$days_range2 = 0;

// Do some ajax stuff
if (isset($_POST['range1'])) {
	$range1 = $_POST['range1'];
	$range2 = $_POST['range2'];
	
	$choice1 = new DateTime($range1);
	$interval = $today_date->diff($choice1);
	$days_range1 = $interval->days;

	$choice2 = new DateTime($range2);
	$interval = $today_date->diff($choice2);
	$days_range2 = $interval->days;

	$year_ago = $range1;
	$today = $range2;
}	

// Membership via volunteering
$query = "SELECT contact_id, full_name, normal_full_name, email, phone, sort_visits, sort_hours FROM 
(SELECT contacts.contact_id, CONCAT(last_name, ', ', first_name, ' ',middle_initial) AS full_name,
CONCAT(first_name, ' ', last_name) AS normal_full_name,
contacts.email AS email, contacts.phone AS phone, 
COUNT(shop_hours.contact_id) as sort_visits, 
ROUND(SUM(HOUR(SUBTIME( TIME(time_out), TIME(time_in))) + MINUTE(SUBTIME( TIME(time_out), TIME(time_in)))/60)) AS sort_hours 
FROM shop_hours 
LEFT JOIN contacts ON shop_hours.contact_id = contacts.contact_id 
LEFT JOIN shop_user_roles ON shop_hours.shop_user_role = shop_user_roles.shop_user_role_id 
GROUP BY contact_id) AS members  
GROUP by contact_id ORDER by sort_hours DESC, sort_visits DESC;";
$patrons = mysql_query($query, $YBDB) or die(mysql_error());

while ($result = mysql_fetch_assoc($patrons)) {
	$purchased_membership_dictionary[$result['contact_id']] = $result;
}

// Purchased Membership
$purchase_query = "SELECT contacts.contact_id, 
CONCAT(last_name, ', ', first_name, ' ',middle_initial) AS full_name, 
CONCAT(first_name, ' ', last_name) AS normal_full_name,
contacts.email AS email, contacts.phone AS phone, 
MAX(transaction_log.date) AS sort_hours, SUBSTRING_INDEX(DATE_ADD(date, INTERVAL 365 DAY), ' ', 1) AS expiration_date
FROM transaction_log 
LEFT JOIN contacts ON transaction_log.sold_to = contacts.contact_id 
WHERE SUBSTRING_INDEX(date, ' ', 1) <= DATE_ADD(date, INTERVAL 365 DAY) 
AND (transaction_type='Memberships' AND paid=1) GROUP BY full_name;";
$purchased_membership = mysql_query($purchase_query, $YBDB) or die(mysql_error());
$num_member_rows = mysql_num_rows($purchased_membership);

?>


<?php include("../include_header_stats.html"); ?>

        <table class="stats">
        <tr valign="top">
	  	  </tr>
        <tr>
          <td>
            <table  border="1" cellpadding="1" cellspacing="0">
              <tr bgcolor="#99CC33" class="yb_standardCENTERbold">
                <td colspan="4" height="25">Membership</td>
	          </tr>
              <tr valign="top" bgcolor="#99CC33" class="yb_standardCENTER">
			    <td width="relative">Paid<br /></td>
			    <td width="relative">Expiration<br /></td>
			    <td width="relative">Visits [Lifetime]<br /></td>
			    <td width="relative">Hours [Lifetime]<br /></td>
		      </tr>
                <?php 
                //$purchased = mysql_fetch_assoc($purchased_membership);
                while ($result = mysql_fetch_assoc($purchased_membership)) {                 
                //do { 
			  		 ?> 
            <tr>           
             <td class="yb_standardRIGHTred"><a href="<?php echo "{$page_individual_history_log}?contact_id=" . $result['contact_id']; ?>"><?php echo $result['full_name']; ?></a></td>
			    <td class="yb_standardRIGHTred"><?php echo $result['expiration_date']; ?></td>
			    <td class="yb_standardRIGHT"><?php echo number_format($purchased_membership_dictionary[$result['contact_id']]['sort_visits'],0); ?></td>
			    <td class="yb_standardRIGHT"><?php echo number_format($purchased_membership_dictionary[$result['contact_id']]['sort_hours'],0); ?></td>						    
		      </tr>
              <?php
		  } // end WHILE count of recordset ?>
          </table>	  </td>
	  		</tr>
     		</table>   		
     

     		<h3>Contact Information</h3>
			<b>Email Address:</b>&nbsp;
			<?php 
			//mysql_free_result($members); 
			$members = mysql_query($purchase_query, $YBDB) or die(mysql_error()); 
			$c = 1;			
			while ($result = mysql_fetch_assoc($members)) {
			
				if ($result['email']) {
					if ($c < $num_member_rows) {
						echo $result['normal_full_name'] . " &lt;" . $result['email'] . "&gt;, ";
					} else {
						echo $result['normal_full_name'] . " &lt;" . $result['email'] . "&gt;";
					}
				}
				
				$c++;
			?> 
		
			<?php } // end WHILE count of recordset ?>
		
			<p></p>
			<b>Phone Numbers for Members Without Email Addresses</b><br \>
			<?php 
			mysql_free_result($members); 
			$members = mysql_query($purchase_query, $YBDB) or die(mysql_error()); 
			$c = 1;			
			while ($result = mysql_fetch_assoc($members)) {
			
				if ( $result['phone'] && !$result['email'] ) {
					if ($c < $num_member_rows) {
						echo $result['normal_full_name'] . ", " . $result['phone'] . "<br />";
					} else {
						echo $result['normal_full_name'] . ", " . $result['phone'];
					}
				}
				
				$c++;
			?> 
			<?php } // end WHILE count of recordset ?>
			
			<p></p>
			<u><b>CSV</b></u><br \>
			<b>Name, Phone, Email</b><br \>
			<?php 
			mysql_free_result($members); 
			$members = mysql_query($purchase_query, $YBDB) or die(mysql_error()); 
			$c = 1;			
			while ($result = mysql_fetch_assoc($members)) {
			
					if ($c < $num_member_rows) {
						echo $result['normal_full_name'] . ", " . $result['phone'] . ", ". $result['email'] . "<br \>";
					} else {
						echo $result['normal_full_name'] . ", " . $result['phone'] . ", ". $result['email'];
					}
				
				$c++;
			?> 
			<?php } // end WHILE count of recordset ?>			
     		
		<?php include("../include_footer.html"); ?>
<?php
mysql_free_result($members);
?>
