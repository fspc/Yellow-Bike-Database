<?php
require_once('../Connections/YBDB.php');
require_once('../Connections/database_functions.php'); 
	
switch ($_GET['error']) {
case 'no_shop':
   $error_message = 'ERROR: A Shop at this location for today does not exist: Start New Shop';
   break;
case 'new_error_message':	//this is a sample error message.  insert error case here		
   $error_message = '';
   break;
default:
   $error_message = 'New Users per Week';
   break;
}

mysql_select_db($database_YBDB, $YBDB);
$query_Recordset1 = "SELECT YEAR(date_created) as year, LEFT(MONTHNAME(date_created),3) as month, WEEK(date_created) as week, COUNT(contact_id) as 'new_users' FROM contacts WHERE first_name <> '' AND last_name <> '' GROUP BY YEAR(date_created), WEEK(date_created) ORDER BY YEAR(date_created) DESC, WEEK(date_created) DESC;";
$Recordset1 = mysql_query($query_Recordset1, $YBDB) or die(mysql_error());
//$row_Recordset1 = mysql_fetch_assoc($Recordset1);   //Wait to fetch until do loop
$totalRows_Recordset1 = mysql_num_rows($Recordset1);

$query_Recordset2 = "SELECT COUNT(contact_id) total_users FROM contacts WHERE first_name <> '' AND last_name <> ''  GROUP BY contact_id * 0;";
$Recordset2 = mysql_query($query_Recordset2, $YBDB) or die(mysql_error());
$row_Recordset2 = mysql_fetch_assoc($Recordset2);   //Wait to fetch until do loop
$total_users = $row_Recordset2['total_users'];

$query_Recordset3 = "SELECT year(date), week(date),
COUNT(shop_visit_id) AS total_visitors,
ROUND(SUM(HOUR(SUBTIME( TIME(time_out), TIME(time_in))) + MINUTE(SUBTIME( TIME(time_out), TIME(time_in)))/60)) AS total_hours
FROM shops LEFT JOIN shop_hours ON shops.shop_id = shop_hours.shop_id
GROUP BY year(date), week(date)
ORDER BY year(date) DESC, week(date) DESC";
$Recordset3 = mysql_query($query_Recordset3, $YBDB) or die(mysql_error());
//$row_Recordset3 = mysql_fetch_assoc($Recordset3);   //Wait to fetch until do loop
$totalRows_Recordset3 = mysql_num_rows($Recordset3);

$query_Recordset4 = "SELECT year(date), week(date),
COUNT(shop_visit_id) AS volunteer_visitors,
ROUND(SUM(HOUR(SUBTIME( TIME(time_out), TIME(time_in))) + MINUTE(SUBTIME( TIME(time_out), TIME(time_in)))/60)) AS volunteer_hours
FROM shops
LEFT JOIN shop_hours ON shops.shop_id = shop_hours.shop_id
LEFT JOIN shop_user_roles ON shop_hours.shop_user_role = shop_user_roles.shop_user_role_id
WHERE volunteer = 1
GROUP BY year(date), week(date)
ORDER BY year(date) DESC, week(date) DESC;";
$Recordset4 = mysql_query($query_Recordset4, $YBDB) or die(mysql_error());
//$row_Recordset4 = mysql_fetch_assoc($Recordset4);   //Wait to fetch until do loop
$totalRows_Recordset4 = mysql_num_rows($Recordset4);


?>


<?php include("../../include_header_stats.html"); ?>
        <table>
        <tr valign="top">
          <td><span class="yb_heading3red">New and Total Users by Week</span></td>
	  </tr>
        <tr>
          <td>
            <table   border="1" cellpadding="1" cellspacing="0">
              <tr valign="top" bgcolor="#99CC33" class="yb_standardCENTERbold">
                <td height="35" colspan="3">Date</td>
		        <td colspan="2" bgcolor="#99CC33">User Hours for Week</td>
		        <td colspan="3">Number of Visitors for Week </td>
		        <td>  	          Users to <br />	          Date </td>
	          </tr>
              <tr valign="top" bgcolor="#99CC33" class="yb_standardCENTER">
                <td width="60" height="25" class="yb_heading3">Year</td>
			    <td width="60" class="yb_heading3">Month</td>
			    <td width="60" class="yb_heading3">Week#</td>
			    <td width="100" bgcolor="#99CC33">Total </td>
			    <td width="100" bgcolor="#99CC33">Volunteer </td>
			    <td width="100">Total</td>
			    <td width="100">Volunteer</td>
			    <td width="100">First Time </td>
			    <td>Total</td>
			  </tr>
              <form method="post" name="FormUpdate_<?php echo $row_Recordset1['shop_id']; ?>" action="<?php echo $editFormAction; ?>">
                <?php while ($row_Recordset1 = mysql_fetch_assoc($Recordset1)) { //do { 
			  $row_Recordset3 = mysql_fetch_assoc($Recordset3);
			  $row_Recordset4 = mysql_fetch_assoc($Recordset4);
			  if(1 == 2) {?>
                <tr valign="bottom" bgcolor="#CCCC33">
                  <td bgcolor="#CCCC33">&nbsp;</td>
			      <td bgcolor="#CCCC33">&nbsp;</td>
			      <td bgcolor="#CCCC33">&nbsp;</td>
			      <td width="100">&nbsp;</td>
			      <td bgcolor="#CCCC33">&nbsp;</td>
			      <td bgcolor="#CCCC33">&nbsp;</td>
			      <td bgcolor="#CCCC33">&nbsp;</td>
			      <td bgcolor="#CCCC33">&nbsp;</td>
			      <td bgcolor="#CCCC33">&nbsp;</td>
			  </tr>
                <input type="hidden" name="MM_insert" value="FormEdit">
                <input type="hidden" name="shop_id" value="<?php echo $row_Recordset1['shop_id']; ?>">
              </form>
		    <?php } else { // end if EDIT RECORD ?>
              <tr class="yb_standardRIGHT">
                <td class="yb_standardCENTER"><?php echo $row_Recordset1['year']; ?></td>
			    <td class="yb_standardCENTER"><?php echo $row_Recordset1['month']; ?></td>
			    <td class="yb_standardCENTER"><?php echo $row_Recordset1['week']; ?></td>
			    <td>&nbsp;<?php echo number_format($row_Recordset3['total_hours'],0); ?></td>
			    <td class="yb_standardRIGHTred">&nbsp;<?php echo number_format($row_Recordset4['volunteer_hours'],0); ?></td>
			    <td><?php echo number_format($row_Recordset3['total_visitors'],0); ?></td>
			    <td class="yb_standardRIGHTred">&nbsp;<?php echo number_format($row_Recordset4['volunteer_visitors'],0); ?></td>
			    <td>&nbsp;<?php echo number_format($row_Recordset1['new_users'],0); ?></td>
			    <td>&nbsp;<?php echo number_format($total_users,0); $total_users -= $row_Recordset1['new_users']; ?></td>
			  </tr>
              <?php
		  } // end if EDIT RECORD 
		  } // end WHILE count of recordset ?>
          </table>	  </td>
	  </tr>
        </table>
		
		<?php include("../../include_footer.html"); ?>
<?php
mysql_free_result($Recordset1);
?>
