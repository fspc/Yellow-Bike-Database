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
   $error_message = 'Total Hours';
   break;
}

mysql_select_db($database_YBDB, $YBDB);
$query_Recordset1 = "SELECT total.date, DAYNAME(total.date) AS dayname, YEAR(total.date) AS year, WEEK(total.date) AS week, DAYOFWEEK(total.date) AS dayofweek, total.visits AS total_visits, firstv.first_count AS new_visits
FROM (SELECT date, COUNT(shop_visit_id) AS visits FROM shops s LEFT JOIN shop_hours sh ON s.shop_id = sh.shop_id GROUP BY date) AS total
LEFT JOIN (SELECT first_date, COUNT(contact_id) AS first_count FROM (SELECT sh.contact_id, MIN(s.date) as first_date, DAYNAME(MIN(s.date)) AS day_name FROM shops s LEFT JOIN shop_hours sh ON sh.shop_id=s.shop_id GROUP BY sh.contact_id) AS first_visits GROUP BY first_date) AS firstv
ON total.date = firstv.first_date ORDER BY total.date DESC";
$Recordset1 = mysql_query($query_Recordset1, $YBDB) or die(mysql_error());
$row_Recordset1 = mysql_fetch_assoc($Recordset1);   //Wait to fetch until do loop
$totalRows_Recordset1 = mysql_num_rows($Recordset1);

function print_table_daysofweek($year, $week, $total_visits, $new_visits){

	echo "<tr class='yb_standardCENTER'>";
	echo "<td class='yb_standardCENTERbold'>$year</td>\n";
	echo "<td class='yb_standardCENTERbold'>$week</td>\n";
	for ($i = 1; $i <= 7; $i++){
		echo "<td> <span class='yb_standardCENTERred'>$new_visits[$i]</span> / $total_visits[$i]</td>\n";
	} 
	echo "</tr>\n";
}

?>

<?php include("../include_header.html"); ?>
<table width="100%">
  <tr valign="top">
    <td><span class="yb_heading3red">New and Total Users by Day/Week</span></td>
	  </tr>
  <tr><td>Legend: <span class="yb_standardred">First Time Users</span> / Total Users</td></tr>
  <tr>
    <td>
      <table width="100%"   border="1" cellpadding="1" cellspacing="0">
        <tr bgcolor="#99CC33" class="yb_standardCENTERbold">
          <td>Year</td>
		        <td>Week</td>
		        <td height="35">Sunday</td>
			    <td>Monday</td>
			    <td>Tuesday</td>
			    <td>Wednesday</td>
			    <td>Thursday</td>
			    <td>Friday</td>
			    <td>Saturday</td>
			  </tr>
        
        <?php 
			  	$j=0;
				do {
					$j++;
					if ($row_Recordset1['week']<>$week && j<>1) { // reset if week has changed 
						print_table_daysofweek($year, $week, $total_visits, $new_visits);
						
						$year = $row_Recordset1['year'];
						$week = $row_Recordset1['week'];
						$total_visits = array(1 => 0,2 => 0,3 => 0,4 => 0,5 => 0,6 => 0,7 => 0);
						$new_visits = array(1 => 0,2 => 0,3 => 0,4 => 0,5 => 0,6 => 0,7 => 0);	
					} //if
				
					$dayofweek = intval($row_Recordset1['dayofweek']);
					$total_visits[$dayofweek] = $row_Recordset1['total_visits'];
					$new_visits[$dayofweek] = $row_Recordset1['new_visits'];
				
			   	} while ($row_Recordset1 = mysql_fetch_assoc($Recordset1)); // while ?>
        </table>	  </td>
	  </tr>
</table>
<br><table border="0" cellspacing="0" cellpadding="1">
  <tr>
    <td class="yb_heading3">Legend: </td>
	  <td>
	    <table border="1" cellspacing="0" cellpadding="1"><tr><td><span class="yb_standardred">First Time Users</span> Total Users
	      </td>
	  </tr></table>  </td></tr>
</table>

<?php include("../include_footer.html"); ?>
<?php
mysql_free_result($Recordset1);
?>
