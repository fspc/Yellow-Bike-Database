<?php

require_once('../Connections/YBDB.php');
require_once('../Connections/database_functions.php');
mysql_select_db($database_YBDB, $YBDB);


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

$query = "SELECT COUNT(shop_id) as total_shops from shops 
WHERE (date >= DATE_SUB(CURDATE(),INTERVAL $days_range1 DAY)  AND date <= DATE_SUB(CURDATE(), INTERVAL $days_range2 DAY));";
$shop_totals_sql = mysql_query($query, $YBDB) or die(mysql_error());

$query = "SELECT shop_type, COUNT(shop_id) AS shop_totals from shops 
LEFT JOIN shop_types ON shops.shop_type = shop_types.shop_type_id 
WHERE (date >= DATE_SUB(CURDATE(),INTERVAL $days_range1 DAY) AND date <= DATE_SUB(CURDATE(), INTERVAL $days_range2 DAY)) 
GROUP BY shop_type ORDER BY shop_totals DESC;";
$shop_totals_by_types_sql = mysql_query($query, $YBDB) or die(mysql_error());

?>

<?php include("../include_header_stats.html"); ?>

        <table class="stats">
        <tr valign="top">
	  	  </tr>
        <tr>
          <td>
            <table  border="1" cellpadding="1" cellspacing="0">
              <tr bgcolor="#99CC33" class="yb_standardCENTERbold">
                <td colspan="4" height="25">Totals for Shop Openings</td>
	          </tr>
              <tr valign="top" bgcolor="#99CC33" class="yb_standardCENTER">
			    <td width="relative">Type<br /></td>
			    <td width="relative">Totals<br /></td>
		      </tr>
                <?php while ($result = mysql_fetch_assoc($shop_totals_by_types_sql)) { //do { 
			  		 ?> 
            <tr>
             <td class="yb_standardRIGHTred"><?php echo $result['shop_type'] ?></td>
			    <td class="yb_standardRIGHT"><?php echo number_format($result['shop_totals'],0); ?></td>
		      </tr>
              <?php
		  } // end WHILE count of recordset ?>
				<?php while ($result = mysql_fetch_assoc($shop_totals_sql)) { //do { 
			  		 ?> 		  		
		  		<tr>
					<td></td>
					<td class="yb_standardRIGHT"><?php echo number_format($result['total_shops'],0); ?></td>		  		
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
				<input id="shops" type="submit" value="Submit" tabindex="14">
			</form>
     		
		<?php include("../include_footer.html"); ?>
<?php
mysql_free_result($shop_totals_sql);
mysql_free_result($shop_totals_by_types_sql);
?>
