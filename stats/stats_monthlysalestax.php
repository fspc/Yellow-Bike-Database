<?php
require_once('../Connections/YBDB.php');
require_once('../Connections/database_functions.php'); 
$page_individual_history_log = INDIVIDUAL_HISTORY_LOG; 
$accounting_group = ACCOUNTING_GROUP;
	
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
$query_Recordset1 = "SELECT year(t.date) as Year,quarter(t.date) as Quarter, month(t.date) as Month, round(sum(amount),2) as TotalSales, tt.accounting_group as AccountingGroup
FROM transaction_log t
LEFT JOIN transaction_types tt ON t.transaction_type = tt.transaction_type_id
WHERE tt.accounting_group = '$accounting_group' AND t.paid=1
GROUP BY year(t.date), month(t.date)
ORDER BY year(t.date) DESC, month(t.date) DESC;";
$Recordset1 = mysql_query($query_Recordset1, $YBDB) or die(mysql_error());
//$row_Recordset1 = mysql_fetch_assoc($Recordset1);   //Wait to fetch until do loop
$totalRows_Recordset1 = mysql_num_rows($Recordset1);

?>

<?php include("../include_header_stats.html"); ?>

        <table>
        <tr valign="top">
          <td><span class="yb_heading3red">Monthly Sales Tax Report</span> - Includes Volunteer and Paid Shop Sales</td>
	  </tr>
        <tr>
          <td>
            <table id="monthlysalestax"  border="1" cellpadding="1" cellspacing="0">
              
              <tr valign="top" bgcolor="#99CC33" class="yb_standardCENTER">
             	<th width="120" height="35">Year</th>
			    	<th width="60">Month</th>
			    	<th width="100">Total Sales</th>
			    	<th width="110">Accounting Group</th>
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
			    </tr>
                <input type="hidden" name="MM_insert" value="FormEdit">
                <input type="hidden" name="shop_id" value="<?php echo $row_Recordset1['shop_id']; ?>">
              </form>
		    <?php } else { // end if EDIT RECORD ?>
              <tr>
                <td><?php echo $row_Recordset1['Year']; ?></td>
			    <td>
			    	<?php echo '<a href="/transaction_log.php?month_search=' . $row_Recordset1['Year'] . '-' . $row_Recordset1['Month'] . '-01">' . $row_Recordset1['Month'] . '</a>'; ?>			    
			    </td>
			    <td class="yb_standardRIGHT"><?php currency_format($row_Recordset1['TotalSales'],2);?></td>
			    <td><?php echo $row_Recordset1['AccountingGroup']; ?></td>
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
