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
$query_Recordset1 = "SELECT * FROM view_EmployeeHours_byWeek;";
$Recordset1 = mysql_query($query_Recordset1, $YBDB) or die(mysql_error());
//$row_Recordset1 = mysql_fetch_assoc($Recordset1);   //Wait to fetch until do loop
$totalRows_Recordset1 = mysql_num_rows($Recordset1);

?>

<?php include("../include_header_stats.html"); ?>

        <table>
       		<tr valign="top"><td><span class="yb_heading3red">Staff Hours by Week</span></td></tr>
        <tr>          
        	<td>View Hours by: <a href="stats_paidstaffhours_byPayPeriod.php">PayPeriod</a>, <a href="stats_paidstaffhours_byWeek.php">Week</a>, <a href="stats_paidstaffhours_byMonth.php">Month</a></td>
	  </tr>
        <tr>
          <td>
            <table   border="1" cellpadding="1" cellspacing="0">
              <tr valign="top" bgcolor="#99CC33" class="yb_standardCENTER">
                <td width="100" height="35">Year</td>
			    <td width="100">Week</td>
			    <td width="200">Name<br />
		        <td width="100">Hours<br />
		      </tr>
              <form method="post" name="FormUpdate_<?php echo $row_Recordset1['shop_id']; ?>" action="<?php echo $editFormAction; ?>">
                <?php while ($row_Recordset1 = mysql_fetch_assoc($Recordset1)) { //do { 
			  if(1 == 2) {?>
                <tr valign="bottom" bgcolor="#CCCC33">
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
			    <td class="yb_standardCENTER">&nbsp;<?php echo $row_Recordset1['Year']; ?></td>
			    <td class="yb_standardCENTER">&nbsp;<?php echo $row_Recordset1['Week']; ?></td>
			    <td><a href="<?php echo "{$page_individual_history_log}?contact_id=" . $row_Recordset1['ContactID']; ?>"><?php echo $row_Recordset1['Name']; ?></a></td>
				<td class="yb_standardRIGHT">&nbsp;<?php echo number_format($row_Recordset1['Hours'],2); ?></td>
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
