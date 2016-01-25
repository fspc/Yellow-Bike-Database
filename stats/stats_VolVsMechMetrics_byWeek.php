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
$query_Recordset1 = "SELECT v.Year, v.Week, (v.NetSalesNewParts + v.SalesUsedParts + v.SalesBikes) as VolSales,
(m.NetSalesNewParts + m.SalesUsedParts + m.SalesBikes) as MechSales,
(v.NetSalesNewParts + v.SalesUsedParts + v.SalesBikes + if( m.SalesUsedParts is null,0,m.NetSalesNewParts + m.SalesUsedParts + m.SalesBikes)) as TotalSales,
v.TotalBikesSold as VolBikesSold,
m.TotalBikesSold as MechBikesSold,
v.TotalBikesSold + if(m.TotalBikesSold is null,0,m.TotalBikesSold) as TotalBikesSold,
if(m.TotalBikesSold is null, 1,0) as test,
m.ValueBikesFixed as MechValueBikesFixed,
m.TotalBikesFixed as MechBikesFixed
FROM view_Transactions_VolRunShop_byWeek_pvTbl v
LEFT JOIN view_Transactions_MechOper_byWeek_pvTbl as m on v.Year = m.Year AND v.Week = m.Week
ORDER BY v.Year DESC, v.Week DESC;";
$Recordset1 = mysql_query($query_Recordset1, $YBDB) or die(mysql_error());
//$row_Recordset1 = mysql_fetch_assoc($Recordset1);   //Wait to fetch until do loop
$totalRows_Recordset1 = mysql_num_rows($Recordset1);

?>

<?php include("../include_header_stats.html"); ?>

        <table>
        <tr valign="top">
          <td><span class="yb_heading3red">Volunteer vs. Mechanic Operation Metrics by Week</span></td>
	  </tr>
	  <tr valign="top">
          <td>View Hours by: Week, <a href="stats_VolVsMechMetrics_byMonth.php">Month</a>
	  </tr>
        <tr>
          <td>
            <table   border="1" cellpadding="1" cellspacing="0">
              <tr bgcolor="#E6E6E6" class="yb_standardCENTERbold">
                <td height="25" colspan="2">Time Period</td>
			    <td height="25" colspan="3">All Sales</td>
			    <td height="25" colspan="3">Number of Bikes</td>
			    <td height="25" colspan="2">Mechanic Bikes Fixed</td>
			  </tr>
              <tr valign="top" bgcolor="#99CC33" class="yb_standardCENTER">
                <td width="100" height="35">Year</td>
			    <td width="100">Week</td>
			    <td width="100">Volunteer</td>
			    <td width="100">MechOper</td>
			    <td width="100">Total</td>
			    <td width="100">Volunteer</td>
			    <td width="100">MechOper</td>
			    <td width="100">Total</td>
			    <td width="100">Value</td>
			    <td width="100">Number</td>
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
			      <td>&nbsp;</td>
			      <td>&nbsp;</td>
			      <td>&nbsp;</td>
			      <td>&nbsp;</td>
			    </tr>
                <input type="hidden" name="MM_insert" value="FormEdit">
                <input type="hidden" name="shop_id" value="<?php echo $row_Recordset1['shop_id']; ?>">
              </form>
		    <?php } else { // end if EDIT RECORD ?>
              <tr class="yb_standardRIGHT">
                <td><?php echo $row_Recordset1['Year']; ?></td>
			    <td>&nbsp;<?php echo $row_Recordset1['Week']; ?></td>
			    <td>&nbsp;<?php currency_format($row_Recordset1['VolSales'],0);?></td>
			    <td>&nbsp;<?php currency_format($row_Recordset1['MechSales'],0);?></td>
			    <td>&nbsp;<?php currency_format($row_Recordset1['TotalSales'],0);?></td>
			    <td>&nbsp;<?php echo number_format($row_Recordset1['VolBikesSold'],0);?></td>
			    <td>&nbsp;<?php echo number_format($row_Recordset1['MechBikesSold'],0);?></td>			    
			    <td>&nbsp;<?php echo number_format($row_Recordset1['TotalBikesSold'],0);?></td>
			    <td>&nbsp;<?php currency_format($row_Recordset1['MechValueBikesFixed'],0); ?></td>
			    <td>&nbsp;<?php echo number_format($row_Recordset1['MechBikesFixed'],0); ?></td>
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
