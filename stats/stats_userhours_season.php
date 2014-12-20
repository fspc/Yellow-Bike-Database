<?php
require_once('../Connections/YBDB.php');
require_once('../Connections/database_functions.php'); 
$page_individual_history_log = INDIVIDUAL_HISTORY_LOG; 
	
switch ($_GET['error']) {
case 'no_shop':
   $error_message = 'ERROR: A Shop at this location for today does not exist: Start New Shop';
   break;
case 'new_error_message':	//this is a sample error message.  insert error case here		
   $error_message = '';
   break;
default:
   $error_message = 'Total Hours';
   break;
}

mysql_select_db($database_YBDB, $YBDB);
$query_Recordset1 = "SELECT * FROM (SELECT contacts.contact_id, CONCAT(last_name, ', ', first_name, ' ',middle_initial) AS full_name,
COUNT(shop_hours.contact_id) as sort_visits,
ROUND(SUM(HOUR(SUBTIME( TIME(time_out), TIME(time_in))) + MINUTE(SUBTIME( TIME(time_out), TIME(time_in)))/60)) AS sort_hours
FROM shop_hours
LEFT JOIN contacts ON shop_hours.contact_id = contacts.contact_id
LEFT JOIN shop_user_roles ON shop_hours.shop_user_role = shop_user_roles.shop_user_role_id
WHERE shop_user_roles.volunteer = 1 AND time_in > DATE_SUB(CURDATE(),INTERVAL 3 MONTH)
GROUP BY contact_id
ORDER BY sort_hours DESC) AS sort_hours
LEFT JOIN (SELECT contacts.contact_id AS vh_contact_id,
COUNT(shop_hours.contact_id) as th_visits,
ROUND(SUM(HOUR(SUBTIME( TIME(time_out), TIME(time_in))) + MINUTE(SUBTIME( TIME(time_out), TIME(time_in)))/60)) AS th_hours
FROM shop_hours
LEFT JOIN contacts ON shop_hours.contact_id = contacts.contact_id
LEFT JOIN shop_user_roles ON shop_hours.shop_user_role = shop_user_roles.shop_user_role_id
GROUP BY contacts.contact_id
ORDER BY last_name, first_name) AS total_hours ON sort_hours.contact_id = total_hours.vh_contact_id
LEFT JOIN (SELECT contacts.contact_id AS vh_contact_id,
COUNT(shop_hours.contact_id) as vh_visits,
ROUND(SUM(HOUR(SUBTIME( TIME(time_out), TIME(time_in))) + MINUTE(SUBTIME( TIME(time_out), TIME(time_in)))/60)) AS vh_hours
FROM shop_hours
LEFT JOIN contacts ON shop_hours.contact_id = contacts.contact_id
LEFT JOIN shop_user_roles ON shop_hours.shop_user_role = shop_user_roles.shop_user_role_id
WHERE shop_user_roles.volunteer = 1
GROUP BY contacts.contact_id
ORDER BY last_name, first_name) AS volunteer_hours ON sort_hours.contact_id = volunteer_hours.vh_contact_id
LEFT JOIN (SELECT contacts.contact_id AS th3_contact_id,
COUNT(shop_hours.contact_id) as th3_visits,
ROUND(SUM(HOUR(SUBTIME( TIME(time_out), TIME(time_in))) + MINUTE(SUBTIME( TIME(time_out), TIME(time_in)))/60)) AS th3_hours
FROM shop_hours
LEFT JOIN contacts ON shop_hours.contact_id = contacts.contact_id
LEFT JOIN shop_user_roles ON shop_hours.shop_user_role = shop_user_roles.shop_user_role_id
WHERE time_in > DATE_SUB(CURDATE(),INTERVAL 3 MONTH)
GROUP BY contacts.contact_id
ORDER BY last_name, first_name) AS total_hours3 ON sort_hours.contact_id = total_hours3.th3_contact_id
LEFT JOIN (SELECT contacts.contact_id AS vh3_contact_id,
COUNT(shop_hours.contact_id) as vh3_visits,
ROUND(SUM(HOUR(SUBTIME( TIME(time_out), TIME(time_in))) + MINUTE(SUBTIME( TIME(time_out), TIME(time_in)))/60)) AS vh3_hours
FROM shop_hours
LEFT JOIN contacts ON shop_hours.contact_id = contacts.contact_id
LEFT JOIN shop_user_roles ON shop_hours.shop_user_role = shop_user_roles.shop_user_role_id
WHERE shop_user_roles.volunteer = 1 AND time_in > DATE_SUB(CURDATE(),INTERVAL 3 MONTH)
GROUP BY contacts.contact_id
ORDER BY first_name) AS volunteer_hours3 ON sort_hours.contact_id = volunteer_hours3.vh3_contact_id";
$Recordset1 = mysql_query($query_Recordset1, $YBDB) or die(mysql_error());
//$row_Recordset1 = mysql_fetch_assoc($Recordset1);   //Wait to fetch until do loop
$totalRows_Recordset1 = mysql_num_rows($Recordset1);

?>

<?php include("../include_header.html"); ?>

        <table>
        <tr valign="top">
          <td><span class="yb_heading3red">Hours by User</span></td>
	  </tr>
        <tr>
          <td>
            <table   border="1" cellpadding="1" cellspacing="0">
              <tr bgcolor="#99CC33" class="yb_standardCENTERbold">
                <td height="25">Shop User </td>
		        <td height="25" colspan="3"> Last 3 Months </td>
		        <td height="25" colspan="3">Lifetime</td>
	          </tr>
              <tr valign="top" bgcolor="#99CC33" class="yb_standardCENTER">
                <td width="200" height="35"></td>
			    <td width="100">Volunteer<br />
		        Hours</td>
			    <td width="100">Total<br />
		        Hours</td>
			    <td width="100">Visits</td>
			    <td width="100">Volunteer<br />
		        Hours</td>
			    <td width="100">Total<br />
		        Hours</td>
			    <td width="100">Visits</td>
		      </tr>
              <form method="post" name="FormUpdate_<?php echo $row_Recordset1['shop_id']; ?>" action="<?php echo $editFormAction; ?>">
                <?php while ($row_Recordset1 = mysql_fetch_assoc($Recordset1)) { //do { 
			  if(1 == 2) {?>
                <tr valign="bottom" bgcolor="#CCCC33">
                  <td>&nbsp;</td>
			      <td>&nbsp;</td>
			      <td>&nbsp;</td>
			      <td>&nbsp;</td>
			      <td>&nbsp;</td>
			      <td>&nbsp;</td>
			  <td>&nbsp;</td>
			    </tr>
                <input type="hidden" name="MM_insert" value="FormEdit">
                <input type="hidden" name="shop_id" value="<?php echo $row_Recordset1['shop_id']; ?>">
              </form>
		    <?php } else { // end if EDIT RECORD ?>
              <tr>
                <td><a href="<?php echo "{$page_individual_history_log}?contact_id=" . $row_Recordset1['contact_id']; ?>"><?php echo $row_Recordset1['full_name']; ?></a></td>
			    <td class="yb_standardRightred">&nbsp;<?php echo number_format($row_Recordset1['vh3_hours'],0); ?></td>
			    <td class="yb_standardRight">&nbsp;<?php echo number_format($row_Recordset1['th3_hours'],0); ?></td>
			    <td class="yb_standardRight">&nbsp;<?php echo number_format($row_Recordset1['th3_visits'],0); ?></td>
			    <td class="yb_standardRightred">&nbsp;<?php echo number_format($row_Recordset1['vh_hours'],0); ?></td>
			    <td class="yb_standardRight">&nbsp;<?php echo number_format($row_Recordset1['th_hours'],0); ?></td>
			    <td class="yb_standardRight">&nbsp;<?php echo number_format($row_Recordset1['th_visits'],0); ?></td>
		      </tr>
              <?php
		  } // end if EDIT RECORD 
		  } // end WHILE count of recordset ?>
          </table>	  </td>
	  </tr>
        </table>
		
		<?php include("../include_footer.html"); ?>
<?php
mysql_free_result($Recordset1);
?>
