<?php

require_once('../Connections/YBDB.php');
require_once('../Connections/database_functions.php');
$page_individual_history_log = INDIVIDUAL_HISTORY_LOG;
mysql_select_db($database_YBDB, $YBDB);

// ** ALTER TABLE shop_user_roles ADD other_volunteer tinyint(1) NOT NULL DEFAULT '0';
// UPDATE shop_user_roles SET other_volunteer=1 WHERE shop_user_role_id="Student Volunteer/Community Service Hours";

//  SELECT shop_user_role_id FROM shop_user_roles WHERE volunteer=1 AND other_volunteer!=1;;

// Defaults


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

/*

SELECT contacts.contact_id, CONCAT(last_name, ', ', first_name, ' ',middle_initial) AS full_name,
COUNT(shop_hours.contact_id) as cs_volunteer_visits,
ROUND(SUM(HOUR(SUBTIME( TIME(time_out), TIME(time_in))) + MINUTE(SUBTIME( TIME(time_out), TIME(time_in)))/60)) AS cs_volunteer_hours
FROM shop_hours
LEFT JOIN contacts ON shop_hours.contact_id = contacts.contact_id
LEFT JOIN shop_user_roles ON shop_hours.shop_user_role = shop_user_roles.shop_user_role_id
WHERE  (time_in > DATE_SUB(CURDATE(),INTERVAL $days_range1 DAY)  AND time_in <= DATE_SUB(CURDATE(), INTERVAL $days_range2 DAY))
AND shop_user_roles.other_volunteer = 1
GROUP BY contact_id
ORDER BY sort_hours DESC;

*/


$query = "SELECT contacts.contact_id, CONCAT(last_name, ', ', first_name, ' ',middle_initial) AS full_name,
		COUNT(shop_hours.contact_id) as cs_volunteer_visits,
		ROUND(SUM(HOUR(SUBTIME( TIME(time_out), TIME(time_in))) + MINUTE(SUBTIME( TIME(time_out), TIME(time_in)))/60)) AS cs_volunteer_hours
		FROM shop_hours
		LEFT JOIN contacts ON shop_hours.contact_id = contacts.contact_id
		LEFT JOIN shop_user_roles ON shop_hours.shop_user_role = shop_user_roles.shop_user_role_id
		WHERE  (SUBSTRING_INDEX(time_in, ' ', 1) >= DATE_SUB(CURDATE(),INTERVAL $days_range1 DAY)  AND SUBSTRING_INDEX(time_in, ' ', 1) <= DATE_SUB(CURDATE(), INTERVAL $days_range2 DAY))
		AND shop_user_roles.other_volunteer = 1
		GROUP BY contact_id
		ORDER BY cs_volunteer_hours DESC;";
$cs_volunteers_sql = mysql_query($query, $YBDB) or die(mysql_error());

?>

<?php include("../include_header_stats.html"); ?>

        <table class="stats">
        <tr valign="top">
	  	  </tr>
        <tr>
          <td>
            <table  border="1" cellpadding="1" cellspacing="0">
              <tr bgcolor="#99CC33" class="yb_standardCENTERbold">
                <td colspan="4" height="25">Community Service Hours</td>
	          </tr>
              <tr valign="top" bgcolor="#99CC33" class="yb_standardCENTER">
			    <td width="relative">Name<br /></td>
			    <td width="relative">Visits<br /></td>
			    <td width="relative">Hours</td>	
		      </tr>
                <?php while ($result = mysql_fetch_assoc($cs_volunteers_sql)) { //do { 
			  		 ?> 
            <tr>
             <td class="yb_standardRIGHTred"><a href="<?php echo "{$page_individual_history_log}?contact_id=" . $result['contact_id']; ?>"><?php echo $result['full_name']; ?></a></td>
			    <td class="yb_standardRIGHT">&nbsp;<?php echo number_format($result['cs_volunteer_visits'],0); ?></td>
			    <td class="yb_standardRIGHTred">&nbsp;<?php echo number_format($result['cs_volunteer_hours'],0); ?></td>
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
				<input id="community_service_hours" type="submit" value="Submit" tabindex="14">
			</form>
     		
		<?php include("../include_footer.html"); ?>
<?php
mysql_free_result($volunteers_sql);
mysql_free_result($total_volunteers_sql);
mysql_free_result($visitors_sql);
mysql_free_result($total_visitors_sql);
mysql_free_result($total_sql);
?>
