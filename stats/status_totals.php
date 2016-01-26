<?php

require_once('../Connections/YBDB.php');
require_once('../Connections/database_functions.php');
mysql_select_db($database_YBDB, $YBDB);

// ** ALTER TABLE shop_user_roles ADD other_volunteer tinyint(1) NOT NULL DEFAULT '0';
// UPDATE shop_user_roles SET other_volunteer=1 WHERE shop_user_role_id="Student Volunteer/Community Service Hours";

//  SELECT shop_user_role_id FROM shop_user_roles WHERE volunteer=1 AND other_volunteer!=1;;


$query = "SELECT shop_user_role, COUNT(DISTINCT shop_hours.contact_id) as unique_volunteers, 
			COUNT(shop_hours.contact_id) as volunteer_visits, 
			ROUND(SUM(HOUR(SUBTIME( TIME(time_out), TIME(time_in))) + MINUTE(SUBTIME( TIME(time_out), 
			TIME(time_in)))/60)) AS volunteer_hours 
			FROM shop_hours 
			LEFT JOIN contacts ON shop_hours.contact_id = contacts.contact_id 
			LEFT JOIN shop_user_roles ON shop_hours.shop_user_role = shop_user_roles.shop_user_role_id 
			WHERE shop_user_roles.volunteer = 1 
			OR shop_user_roles.other_volunteer = 1 
			AND time_in > DATE_SUB(CURDATE(),INTERVAL 12 MONTH) GROUP BY shop_user_role 
			ORDER BY volunteer_hours DESC;";
$volunteers_sql = mysql_query($query, $YBDB) or die(mysql_error());

$query = "SELECT COUNT(DISTINCT shop_hours.contact_id) as unique_volunteers,
			COUNT(shop_hours.contact_id) as volunteer_visits,
			ROUND(SUM(HOUR(SUBTIME( TIME(time_out), TIME(time_in))) + MINUTE(SUBTIME( TIME(time_out), TIME(time_in)))/60)) AS volunteer_hours
			FROM shop_hours 
			LEFT JOIN contacts ON shop_hours.contact_id = contacts.contact_id
			LEFT JOIN shop_user_roles ON shop_hours.shop_user_role = shop_user_roles.shop_user_role_id   
			WHERE shop_user_roles.volunteer = 1 
			OR shop_user_roles.other_volunteer = 1
			AND time_in > DATE_SUB(CURDATE(),INTERVAL 12 MONTH);";
$total_volunteers_sql = mysql_query($query, $YBDB) or die(mysql_error());

$query = "SELECT shop_user_role, COUNT(DISTINCT shop_hours.contact_id) as unique_visitors, 
			COUNT(shop_hours.contact_id) as visits, 
			ROUND(SUM(HOUR(SUBTIME( TIME(time_out), TIME(time_in))) + MINUTE(SUBTIME( TIME(time_out), 
			TIME(time_in)))/60)) AS hours 
			FROM shop_hours 
			LEFT JOIN contacts ON shop_hours.contact_id = contacts.contact_id 
			LEFT JOIN shop_user_roles ON shop_hours.shop_user_role = shop_user_roles.shop_user_role_id 
			WHERE shop_user_roles.volunteer = 0 
			AND shop_user_roles.other_volunteer = 0 
			AND time_in > DATE_SUB(CURDATE(),INTERVAL 12 MONTH) GROUP BY shop_user_role 
			ORDER BY hours DESC;";
$visitors_sql = mysql_query($query, $YBDB) or die(mysql_error());

$query = "SELECT COUNT(DISTINCT shop_hours.contact_id) as unique_visitors,
			COUNT(shop_hours.contact_id) as visits,
			ROUND(SUM(HOUR(SUBTIME( TIME(time_out), TIME(time_in))) + MINUTE(SUBTIME( TIME(time_out), TIME(time_in)))/60)) AS hours
			FROM shop_hours 
			LEFT JOIN contacts ON shop_hours.contact_id = contacts.contact_id
			LEFT JOIN shop_user_roles ON shop_hours.shop_user_role = shop_user_roles.shop_user_role_id   
			WHERE shop_user_roles.volunteer = 0 
			AND shop_user_roles.other_volunteer = 0
			AND time_in > DATE_SUB(CURDATE(),INTERVAL 12 MONTH);";
$total_visitors_sql = mysql_query($query, $YBDB) or die(mysql_error());

$query = "SELECT COUNT(DISTINCT shop_hours.contact_id) as unique_vv,
			COUNT(shop_hours.contact_id) as visits,
			ROUND(SUM(HOUR(SUBTIME( TIME(time_out), TIME(time_in))) + MINUTE(SUBTIME( TIME(time_out), TIME(time_in)))/60)) AS hours
			FROM shop_hours 
			LEFT JOIN contacts ON shop_hours.contact_id = contacts.contact_id
			LEFT JOIN shop_user_roles ON shop_hours.shop_user_role = shop_user_roles.shop_user_role_id   
			WHERE shop_user_roles.volunteer >= 0 
			OR shop_user_roles.other_volunteer >= 0
			AND time_in > DATE_SUB(CURDATE(),INTERVAL 12 MONTH);";
$total_sql = mysql_query($query, $YBDB) or die(mysql_error());

?>

<?php include("../include_header_stats.html"); ?>

        <table class="shop_log">
        <tr valign="top">
	  	  </tr>
        <tr>
          <td>
            <table  border="1" cellpadding="1" cellspacing="0">
              <tr bgcolor="#99CC33" class="yb_standardCENTERbold">
                <td colspan="4" height="25">Volunteers</td>
	          </tr>
              <tr valign="top" bgcolor="#99CC33" class="yb_standardCENTER">
             <td width="relative">Shop Roles<br /></td>
			    <td width="relative">Unique Volunteers<br /></td>
			    <td width="relative">Volunteer Visits<br /></td>
			    <td width="relative">Volunteer Hours</td>	
		      </tr>
                <?php while ($result = mysql_fetch_assoc($volunteers_sql)) { //do { 
			  		 ?> 
            <tr>
			    <td class="yb_standardRIGHTred">&nbsp;<?php echo $result['shop_user_role']; ?></td>
			    <td class="yb_standardRIGHT">&nbsp;<?php echo number_format($result['unique_volunteers'],0); ?></td>
			    <td class="yb_standardRIGHT">&nbsp;<?php echo number_format($result['volunteer_visits'],0); ?></td>
			    <td class="yb_standardRIGHTred">&nbsp;<?php echo number_format($result['volunteer_hours'],0); ?></td>
		      </tr>
              <?php
		  } // end WHILE count of recordset ?>
          </table>	  </td>
	  		</tr>
     		</table>
     		
        <table class="shop_log">
        <tr valign="top">
	  	  </tr>
        <tr>
          <td>
            <table  border="1" cellpadding="1" cellspacing="0">
              <tr bgcolor="#99CC33" class="yb_standardCENTERbold">
                <td colspan="4" height="25">Total Volunteers</td>
	          </tr>
              <tr valign="top" bgcolor="#99CC33" class="yb_standardCENTER">
			    <td width="relative">Unique Volunteers<br /></td>
			    <td width="relative">Volunteer Visits<br /></td>
			    <td width="relative">Volunteer Hours</td>	
		      </tr>
                <?php while ($result = mysql_fetch_assoc($total_volunteers_sql)) { //do { 
			  		 ?> 
            <tr>  
			    <td class="yb_standardRIGHT">&nbsp;<?php echo number_format($result['unique_volunteers'],0); ?></td>
			    <td class="yb_standardRIGHT">&nbsp;<?php echo number_format($result['volunteer_visits'],0); ?></td>
			    <td class="yb_standardRIGHTred">&nbsp;<?php echo number_format($result['volunteer_hours'],0); ?></td>
		      </tr>
              <?php
		  } // end WHILE count of recordset ?>
          </table>	  </td>
	  		</tr>
     		</table>	
		<br \><br \>	
		 <table class="shop_log">
        <tr valign="top">
	  	  </tr>
        <tr>
          <td>
            <table  border="1" cellpadding="1" cellspacing="0">
              <tr bgcolor="#99CC33" class="yb_standardCENTERbold">
                <td colspan="4" height="25">Visitors</td>
	          </tr>
              <tr valign="top" bgcolor="#99CC33" class="yb_standardCENTER">
             <td width="relative">Shop Roles<br /></td>
			    <td width="relative">Unique Visitors<br /></td>
			    <td width="relative">Visits<br /></td>
			    <td width="relative">Hours</td>	
		      </tr>
                <?php while ($result = mysql_fetch_assoc($visitors_sql)) { //do { 
			  		 ?> 
            <tr>
			    <td class="yb_standardRIGHTred">&nbsp;<?php echo $result['shop_user_role']; ?></td>
			    <td class="yb_standardRIGHT">&nbsp;<?php echo number_format($result['unique_visitors'],0); ?></td>
			    <td class="yb_standardRIGHT">&nbsp;<?php echo number_format($result['visits'],0); ?></td>
			    <td class="yb_standardRIGHTred">&nbsp;<?php echo number_format($result['hours'],0); ?></td>
		      </tr>
              <?php
		  } // end WHILE count of recordset ?>
          </table>	  </td>
	  		</tr>
     		</table>
     		
        <table class="shop_log">
        <tr valign="top">
	  	  </tr>
        <tr>
          <td>
            <table  border="1" cellpadding="1" cellspacing="0">
              <tr bgcolor="#99CC33" class="yb_standardCENTERbold">
                <td colspan="4" height="25">Total Visitors</td>
	          </tr>
              <tr valign="top" bgcolor="#99CC33" class="yb_standardCENTER">
			    <td width="relative">Unique Visitors<br /></td>
			    <td width="relative">Visits<br /></td>
			    <td width="relative">Hours</td>	
		      </tr>
                <?php while ($result = mysql_fetch_assoc($total_visitors_sql)) { //do { 
			  		 ?> 
            <tr>  
			    <td class="yb_standardRIGHT">&nbsp;<?php echo number_format($result['unique_visitors'],0); ?></td>
			    <td class="yb_standardRIGHT">&nbsp;<?php echo number_format($result['visits'],0); ?></td>
			    <td class="yb_standardRIGHTred">&nbsp;<?php echo number_format($result['hours'],0); ?></td>
		      </tr>
              <?php
		  } // end WHILE count of recordset ?>
          </table>	  </td>
	  		</tr>
     		</table>	

			<br \><br \>	

        <table class="shop_log">
        <tr valign="top">
	  	  </tr>
        <tr>
          <td>
            <table  border="1" cellpadding="1" cellspacing="0">
              <tr bgcolor="#99CC33" class="yb_standardCENTERbold">
                <td colspan="4" height="25">Total Volunteers and Visitors</td>
	          </tr>
              <tr valign="top" bgcolor="#99CC33" class="yb_standardCENTER">
			    <td width="relative">Unique<br /></td>
			    <td width="relative">Visits<br /></td>
			    <td width="relative">Hours</td>	
		      </tr>
                <?php while ($result = mysql_fetch_assoc($total_sql)) { //do { 
			  		 ?> 
            <tr>  
			    <td class="yb_standardRIGHT">&nbsp;<?php echo number_format($result['unique_vv'],0); ?></td>
			    <td class="yb_standardRIGHT">&nbsp;<?php echo number_format($result['visits'],0); ?></td>
			    <td class="yb_standardRIGHTred">&nbsp;<?php echo number_format($result['hours'],0); ?></td>
		      </tr>
              <?php
		  } // end WHILE count of recordset ?>
          </table>	  </td>
	  		</tr>
     		</table>	
		
		<?php include("../include_footer.html"); ?>
<?php
mysql_free_result($volunteers_sql);
mysql_free_result($total_volunteers_sql);
mysql_free_result($visitors_sql);
mysql_free_result($total_visitors_sql);
?>
