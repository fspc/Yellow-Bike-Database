<?php

require_once('../Connections/YBDB.php');
require_once('../Connections/database_functions.php');

// ** ALTER TABLE shop_user_roles ADD other_volunteer tinyint(1) NOT NULL DEFAULT '0';
// UPDATE shop_user_roles SET other_volunteer=1 WHERE shop_user_role_id="Student Volunteer/Community Service Hours";

//  SELECT shop_user_role_id FROM shop_user_roles WHERE volunteer=1 AND other_volunteer!=1;;

/*


+-------------------------------------------+-------------------+------------------+-----------------+
| shop_user_role                            | unique_volunteers | volunteer_visits | volunteer_hours |
+-------------------------------------------+-------------------+------------------+-----------------+
| Volunteer                                 |               103 |              425 |            1347 |
| Coordinator                               |                 7 |              238 |            1005 |
| Greeter                                   |                 7 |               62 |             188 |
| Student Volunteer/Community Service Hours |                13 |               53 |             136 |
+-------------------------------------------+-------------------+------------------+-----------------+

SELECT shop_user_role, COUNT(DISTINCT shop_hours.contact_id) as unique_volunteers, 
COUNT(shop_hours.contact_id) as volunteer_visits, 
ROUND(SUM(HOUR(SUBTIME( TIME(time_out), TIME(time_in))) + MINUTE(SUBTIME( TIME(time_out), 
TIME(time_in)))/60)) AS volunteer_hours 
FROM shop_hours 
LEFT JOIN contacts ON shop_hours.contact_id = contacts.contact_id 
LEFT JOIN shop_user_roles ON shop_hours.shop_user_role = shop_user_roles.shop_user_role_id 
WHERE shop_user_roles.volunteer = 1 
OR shop_user_roles.other_volunteer = 1 
AND time_in > DATE_SUB(CURDATE(),INTERVAL 12 MONTH) GROUP BY shop_user_role 
ORDER BY volunteer_hours DESC;

+----------------+-----------------+--------+-------+
| shop_user_role | unique_visitors | visits | hours |
+----------------+-----------------+--------+-------+
| Personal       |             141 |    303 |   679 |
| Shopping       |              61 |     66 |    48 |
+----------------+-----------------+--------+-------+

SELECT shop_user_role, COUNT(DISTINCT shop_hours.contact_id) as unique_visitors, 
COUNT(shop_hours.contact_id) as visits, 
ROUND(SUM(HOUR(SUBTIME( TIME(time_out), TIME(time_in))) + MINUTE(SUBTIME( TIME(time_out), 
TIME(time_in)))/60)) AS hours 
FROM shop_hours 
LEFT JOIN contacts ON shop_hours.contact_id = contacts.contact_id 
LEFT JOIN shop_user_roles ON shop_hours.shop_user_role = shop_user_roles.shop_user_role_id 
WHERE shop_user_roles.volunteer = 0 
AND shop_user_roles.other_volunteer = 0 
AND time_in > DATE_SUB(CURDATE(),INTERVAL 12 MONTH) GROUP BY shop_user_role 
ORDER BY hours DESC;


+-------------------+------------------+-----------------+
| unique_volunteers | volunteer_visits | volunteer_hours |
+-------------------+------------------+-----------------+
|               114 |              778 |            2676 |
+-------------------+------------------+-----------------+

SELECT COUNT(DISTINCT shop_hours.contact_id) as unique_volunteers,
COUNT(shop_hours.contact_id) as volunteer_visits,
ROUND(SUM(HOUR(SUBTIME( TIME(time_out), TIME(time_in))) + MINUTE(SUBTIME( TIME(time_out), TIME(time_in)))/60)) AS volunteer_hours
FROM shop_hours 
LEFT JOIN contacts ON shop_hours.contact_id = contacts.contact_id
LEFT JOIN shop_user_roles ON shop_hours.shop_user_role = shop_user_roles.shop_user_role_id   
WHERE shop_user_roles.volunteer = 1 
OR shop_user_roles.other_volunteer = 1
AND time_in > DATE_SUB(CURDATE(),INTERVAL 12 MONTH);

+-----------------+--------+-------+
| unique_visitors | visits | hours |
+-----------------+--------+-------+
|             188 |    369 |   727 |
+-----------------+--------+-------+

SELECT COUNT(DISTINCT shop_hours.contact_id) as unique_visitors,
COUNT(shop_hours.contact_id) as visits,
ROUND(SUM(HOUR(SUBTIME( TIME(time_out), TIME(time_in))) + MINUTE(SUBTIME( TIME(time_out), TIME(time_in)))/60)) AS hours
FROM shop_hours 
LEFT JOIN contacts ON shop_hours.contact_id = contacts.contact_id
LEFT JOIN shop_user_roles ON shop_hours.shop_user_role = shop_user_roles.shop_user_role_id   
WHERE shop_user_roles.volunteer = 0 
AND shop_user_roles.other_volunteer = 0
AND time_in > DATE_SUB(CURDATE(),INTERVAL 12 MONTH);


*/

?>

<?php include("../include_header_stats.html"); ?>

<link rel="stylesheet" type="text/css" href="../css/mystyle.css">

        <table id="shop_log">
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
			    <td class="yb_standardRIGHTred">&nbsp;<?php echo number_format($row_Recordset1['vh3_hours'],0); ?></td>
			    <td class="yb_standardRIGHT">&nbsp;<?php echo number_format($row_Recordset1['th3_hours'],0); ?></td>
			    <td class="yb_standardRIGHT">&nbsp;<?php echo number_format($row_Recordset1['th3_visits'],0); ?></td>
			    <td class="yb_standardRIGHTred">&nbsp;<?php echo number_format($row_Recordset1['vh_hours'],0); ?></td>
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
