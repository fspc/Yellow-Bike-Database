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
$query_Recordset1 = "SELECT t.shop_id as ShopID ,  date_format(t.date,'%m/%d/%Y') as ShopDate,  dayname(t.date) as Day,
shops.shop_type as ShopType, ROUND(sum(t.amount),2) as Total, count(t.transaction_id) as CountOfTrans
FROM transaction_log t
LEFT JOIN shops ON t.shop_id=shops.shop_id
LEFT JOIN transaction_types AS ttype ON t.transaction_type = ttype.transaction_type_id
WHERE (ttype.accounting_group = 'Sales' AND t.paid=1) OR ttype.transaction_type_id = 'Incoming Donation - Cash'
GROUP BY t.shop_id, date_format(t.date,'%m/%d/%Y')
ORDER BY t.date DESC, t.shop_id DESC;";
$Recordset1 = mysql_query($query_Recordset1, $YBDB) or die(mysql_error());
//$row_Recordset1 = mysql_fetch_assoc($Recordset1);   //Wait to fetch until do loop
$totalRows_Recordset1 = mysql_num_rows($Recordset1);

?>

<?php include("../include_header_stats.html"); ?>

        <table>
        <tr valign="top">
          <td><span class="yb_heading3red">Shop Transaction Totals</span> - Total includes sales and cash donations</td>
	  </tr>
        <tr>
          <td>
            <table   border="1" cellpadding="1" cellspacing="0">
              
              <tr valign="top" bgcolor="#99CC33" class="yb_standardCENTER">
                <td width="100" height="35">Date</td>
                <td width="100" height="35">ShopID</td>
			    <td width="100">Day</td>
			    <td width="200">Shop Type</td>
			    <td width="100">Total</td>
			    <td width="100">Count of Transactions</td>
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
                <td><?php echo $row_Recordset1['ShopDate']; ?></td>
                <td><?php echo '<a href="/transaction_log.php?shop_id_search=' . $row_Recordset1['ShopID'] . '&record_count=500">' . $row_Recordset1['ShopID'] . '</a>'; ?></td>
			    <td valign="middle"><?php echo $row_Recordset1['Day']; ?></td>
			    <td valign="middle"><?php echo $row_Recordset1['ShopType']; ?></td>
			    <td class="yb_standardRIGHT"><?php currency_format($row_Recordset1['Total'],2);?></td>
			    <td class="yb_standardRIGHT"><?php echo $row_Recordset1['CountOfTrans']; ?></td>
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
