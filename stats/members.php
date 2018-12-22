<?php

require_once('../Connections/YBDB.php');
require_once('../Connections/database_functions.php');
$page_individual_history_log = INDIVIDUAL_HISTORY_LOG;
// Needs to volunteer at least this amount of defined hours before being considered a member
$membership_hours = MEMBERSHIP_HOURS;
$membership_days = MEMBERSHIP_DAYS;
$purchased_membership_days = PURCHASED_MEMBERSHIP_DAYS;

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
ROUND(SUM(HOUR(TIMEDIFF( time_out, time_in)) + MINUTE(TIMEDIFF( time_out, time_in))/60)) AS sort_hours  
FROM shop_hours 
LEFT JOIN contacts ON shop_hours.contact_id = contacts.contact_id 
LEFT JOIN shop_user_roles ON shop_hours.shop_user_role = shop_user_roles.shop_user_role_id 
WHERE  (SUBSTRING_INDEX(time_in, ' ', 1) >= DATE_SUB(CURDATE(),INTERVAL $days_range1 DAY)  
AND SUBSTRING_INDEX(time_in, ' ', 1) <= DATE_SUB(CURDATE(), INTERVAL $days_range2 DAY))
AND shop_user_roles.volunteer = 1 GROUP BY contact_id) AS members 
WHERE sort_hours >= $membership_hours AND sort_visits >= $membership_days 
GROUP by contact_id ORDER by sort_hours DESC, sort_visits DESC;";
$members = mysql_query($query, $YBDB) or die(mysql_error());
$num_member_rows = mysql_num_rows($members);


// Purchased Membership
$purchase_query = "SELECT contacts.contact_id, 
CONCAT(last_name, ', ', first_name, ' ',middle_initial) AS full_name, 
CONCAT(first_name, ' ', last_name) AS normal_full_name,
contacts.email AS email, contacts.phone AS phone, 
MAX(transaction_log.date) AS sort_hours, MAX(SUBSTRING_INDEX(DATE_ADD(date, INTERVAL 365 DAY), ' ', 1)) AS expiration_date
FROM transaction_log 
LEFT JOIN contacts ON transaction_log.sold_to = contacts.contact_id 
WHERE SUBSTRING_INDEX(date, ' ', 1) <= DATE_ADD(date, INTERVAL 365 DAY) 
AND (transaction_type='Memberships' AND paid=1) GROUP BY full_name;";
$purchased_membership = mysql_query($purchase_query, $YBDB) or die(mysql_error());

while ($result = mysql_fetch_assoc($purchased_membership)) {
	$purchased_membership_dictionary[$result['contact_id']] = $result;
}

?>

<?php include("../include_header_stats.html"); ?>

        <table class="stats">
        <tr valign="top">
	  	  </tr>
        <tr>
          <td>
            <table  border="1" cellpadding="1" cellspacing="0">
              <tr bgcolor="#99CC33" class="yb_standardCENTERbold">
                <td colspan="4" height="25">Metrics [<?php echo ">= $membership_days days and >= $membership_hours hours" ?>]</td>
	          </tr>
              <tr valign="top" bgcolor="#99CC33" class="yb_standardCENTER">
			    <td width="relative"><?php echo $num_member_rows; ?> Volunteers<br /></td>
			    <td width="relative">Visits<br /></td>
			    <td width="relative">Hours<br /></td>
		      </tr>
                <?php 
                $purchased = mysql_fetch_assoc($purchased_membership);
                while ($result = mysql_fetch_assoc($members)) {                 
                //do { 
			  		 ?> 
     			<tr id="<?php echo $result['contact_id']; ?>"> 
            	<?php if( isset($purchased_membership_dictionary[$result['contact_id']]) ) { ?>          
             <td class="yb_standardRIGHTred"><a style="text-decoration:none" href="<?php echo "{$page_individual_history_log}?contact_id=" . $result['contact_id']; ?>"><?php echo $result['full_name']; ?></a><br \>(paid until <?php echo $purchased_membership_dictionary[$result['contact_id']]['expiration_date']; ?>)</td>
					<?php } else { ?>
             <td class="yb_standardRIGHTred"><a style="text-decoration:none" href="<?php echo "{$page_individual_history_log}?contact_id=" . $result['contact_id']; ?>"><?php echo $result['full_name']; ?></a></td>					
					<?php } ?>		    
			    <td class="yb_standardRIGHT"><?php echo number_format($result['sort_visits'],0); ?></td>
			    <td class="yb_standardRIGHT"><?php echo number_format($result['sort_hours'],0); ?></td>
		      </tr>
              <?php
		  } // end WHILE count of recordset ?>
          </table>	  </td>
	  		</tr>
     		</table>   		
     		

			<br \><br \>     		
     		
     		<div id="range_input">Date Range: <?php echo "$year_ago - $today"; ?></div>
     		<div id="range"></div>
     		
     		<br \>
			<form method="post" name="range_query">
				<input id="members" type="submit" value="Submit" tabindex="14">
			</form>

     		<h3>Contact Information</h3>
			<b>Email Address:</b>&nbsp;
			<?php 
			mysql_free_result($members); 
			$members = mysql_query($query, $YBDB) or die(mysql_error()); 
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
			$members = mysql_query($query, $YBDB) or die(mysql_error()); 
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
			$members = mysql_query($query, $YBDB) or die(mysql_error()); 
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
