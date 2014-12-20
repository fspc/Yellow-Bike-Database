<?php
require_once('../Connections/YBDB.php');
require_once('../Connections/database_functions.php'); 
$page_individual_history_log = INDIVIDUAL_HISTORY_LOG; 

$page = "stats_employeemetrics.php";

switch ($_GET['metric']) {
case 'OutputValueVsPayRatio':
   $metric = $_GET['metric'];
   $title = "Value Ratio - (Value of Bikes + Wheels Completed)/Pay";
   break;
case 'HoursPerBike':	//this is a sample error message.  insert error case here		
   $metric = $_GET['metric'];
   $title = "Hours Per Bike";
   break;
case 'NumBikes':	//this is a sample error message.  insert error case here		
   $metric = $_GET['metric'];
   $title = "Total Number of Bikes";
   break;
case 'AverageBikePrice':	//this is a sample error message.  insert error case here		
   $metric = $_GET['metric'];
   $title = "Average Bike Price";
   break;
case 'NumWheels':	//this is a sample error message.  insert error case here		
   $metric = $_GET['metric'];
   $title = "Number of Wheels";
   break;
case 'AverageWheelPrice':	//this is a sample error message.  insert error case here		
   $metric = $_GET['metric'];
   $title = "Average Wheel Price";
   break;
default:
   $metric = 'OutputValueVsPayRatio';
   $title = "Value Ratio - (Value of Bikes + Wheels Completed)/Pay";
   break;
}

switch ($_GET['period']) {
case 'Month':
   $period = $_GET['period'];
   break;
case 'Week':	//this is a sample error message.  insert error case here		
   $period = $_GET['period'];
   break;
default:
   $period = 'Month';
   break;
}

mysql_select_db($database_YBDB, $YBDB);
$query_Recordset1 = "select v.Year AS Year,v.$period AS $period,
max(if((v2.ContactID = 10019),v2.$metric,0)) AS BW,
max(if((v2.ContactID = 4009),v2.$metric,0)) AS Conti,
max(if((v2.ContactID = 107),v2.$metric,0)) AS Pete,
max(if((v2.ContactID = 554),v2.$metric,0)) AS Savanna, 
max(if((v2.ContactID = 1755),v2.$metric,0)) AS John 
from (view_EmployeeMetrics_TotalsBy$period v
left join view_EmployeeMetrics_TotalsBy$period v2 on(((v.Year = v2.Year) and (v.$period = v2.$period))))
group by `v`.`Year` DESC,`v`.`$period` DESC;";
$Recordset1 = mysql_query($query_Recordset1, $YBDB) or die(mysql_error());
//$row_Recordset1 = mysql_fetch_assoc($Recordset1);   //Wait to fetch until do loop
$totalRows_Recordset1 = mysql_num_rows($Recordset1);
$row_Recordset1 = mysql_fetch_assoc($Recordset1);

?>

<?php include("../include_header.html"); ?>

        <table>
        	<tr><td><?php echo "Employee Stats: 
        	<a href=\"$page?metric=OutputValueVsPayRatio&period=$period\">ValueRatio</a>, 
        	<a href=\"$page?metric=HoursPerBike&period=$period\">Hours Per Bike</a>, 
        	<a href=\"$page?metric=NumBikes&period=$period\">Number of Bikes</a>, 
        	<a href=\"$page?metric=AverageBikePrice&period=$period\">Average Bike Price</a>, 
        	<a href=\"$page?metric=NumWheels&period=$period\">Number of Wheels</a>, 
        	<a href=\"$page?metric=AverageWheelPrice&period=$period\">Avg Wheel Price</a>
        	<br/>View by: <a href=\"$page?metric=$metric&period=Month\">Month</a>, 
        	<a href=\"$page?metric=$metric&period=Week\">Week</a>"?></td></tr>
        	<tr valign="top"><td><span class="yb_heading3red"><?php echo $title; ?></span></td></tr>
        <tr>
          <td>
            <table   border="1" cellpadding="1" cellspacing="0">
              <tr valign="top" bgcolor="#99CC33" class="yb_standardCENTER">
                <td width="100" height="35">Year</td>
			    <td width="100"><?php echo $period?></td>
			    <td width="100">BW<br />
		        <td width="100">Conti<br />
		        <td width="100">John<br />
		        <td width="100">Pete<br />
		        <td width="100">Savanna<br />
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
			    <td class="yb_standardCENTER">&nbsp;<?php echo $row_Recordset1["$period"]; ?></td>
			    <td class="yb_standardCENTER">&nbsp;<?php echo $row_Recordset1['BW']; ?></td>
			    <td class="yb_standardCENTER">&nbsp;<?php echo $row_Recordset1['Conti']; ?></td>
			    <td class="yb_standardCENTER">&nbsp;<?php echo $row_Recordset1['John']; ?></td>
			    <td class="yb_standardCENTER">&nbsp;<?php echo $row_Recordset1['Pete']; ?></td>
			    <td class="yb_standardCENTER">&nbsp;<?php echo $row_Recordset1['Savanna']; ?></td>
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
