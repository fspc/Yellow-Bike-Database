<?php
require_once('Connections/YBDB.php');
require_once('Connections/database_functions.php'); 

$page_shop_log = PAGE_SHOP_LOG;
$nonshop = NONSHOP;

//?shop_id=2
if($_GET['shop_id']>0){
	$shop_id = $_GET['shop_id'];
} else {
	$shop_id =0;}
	
switch ($_GET['error']) {
case 'no_shop':
   $error_message = 'ERROR: A Shop at this location for today does not exist: Start New Shop';
   break;
case 'new_error_message':	//this is a sample error message.  insert error case here		
   $error_message = '';
   break;
default:
   $error_message = 'Start a New Shop OR View an Existing Shop';
   break;
}


//shop_date
if($_GET['shop_date']>0){
	$shop_date = $_GET['shop_date'];
} else {
	$shop_date =current_datetime();}	
	
//dayname
if($_GET['shop_dayname']=='alldays'){
	$shop_dayname = '';
} elseif(isset($_GET['shop_dayname'])) {
	$shop_dayname = "AND DAYNAME(date) = '" . $_GET['shop_dayname'] . "'";
} else {
	$shop_dayname = '';
}	

//record_count
if($_GET['record_count']>0){
	$record_count = $_GET['record_count'];
} else {
	$record_count = 10;}	

$ctrl_shoplocation = "ctrl_shoplocation";
$ctrl_shoptype = "ctrl_shoptype";

$editFormAction = $_SERVER['PHP_SELF'] . "?shop_date=$shop_date&shop_id=$shop_id";
$editFormAction_no_shopid = $_SERVER['PHP_SELF'] . "?shop_date=$shop_date";

mysql_select_db($database_YBDB, $YBDB);
$query_Recordset1 = "SELECT shops.shop_id, date, DAYNAME(date) as day ,shop_location, shop_type, ip_address, 
							IF(SUBSTRING(date,1,10) = curdate(),1,0) AS CanEdit, 
							COUNT(shop_visit_id) AS num_visitors, 
							ROUND(SUM(HOUR(TIMEDIFF( time_out, time_in)) + MINUTE(TIMEDIFF( time_out, time_in))/60)) AS total_hours 
							FROM shops LEFT JOIN shop_hours ON shops.shop_id = shop_hours.shop_id 
							WHERE date <= '{$shop_date}' {$shop_dayname} GROUP BY shop_id 
							ORDER BY date DESC , shop_id DESC LIMIT  0, $record_count;";
$Recordset1 = mysql_query($query_Recordset1, $YBDB) or die(mysql_error());
//$row_Recordset1 = mysql_fetch_assoc($Recordset1);   //Wait to fetch until do loop
$totalRows_Recordset1 = mysql_num_rows($Recordset1);

// action on submit
if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form_new")) {
  $insertSQL = sprintf("INSERT INTO shops (shop_location, shop_type, date, ip_address) VALUES (%s, %s, %s, %s)", 
  					GetSQLValueString($_POST['ctrl_shoplocation'], "text"), 
					GetSQLValueString($_POST['ctrl_shoptype'], "text"), 
					GetSQLValueString($_POST['ctrl_date'], "date"), 
					GetSQLValueString($_SERVER['REMOTE_ADDR'], "text"));

  mysql_select_db($database_YBDB, $YBDB);
  $Result1 = mysql_query($insertSQL, $YBDB) or die(mysql_error());
  
  //determines the shop_id just added to the database
  mysql_select_db($database_YBDB, $YBDB);
  $query_Recordset2 = "SELECT MAX(shop_id) AS shop_id FROM shops;";
  $Recordset2 = mysql_query($query_Recordset2, $YBDB) or die(mysql_error());
  $row_Recordset2 = mysql_fetch_assoc($Recordset2);
  $totalRows_Recordset2 = mysql_num_rows($Recordset2);
  $shop_id = $row_Recordset2["shop_id"];

  //the added shop_id is passed as a variable to the shop page
  $insertGoTo = "{$page_shop_log}?shop_id=" . $shop_id;
  mysql_free_result($Recordset2);
  header(sprintf("Location: %s", $insertGoTo));
  
  //header(sprintf("Location: %s", "index.html"));
}

//Update Record     isset($_POST["MM_update"])
if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "FormEdit")) {
  $updateSQL = sprintf("UPDATE shops SET date=%s, shop_location=%s, shop_type=%s WHERE shop_id=%s",
                       GetSQLValueString($_POST['date'], "date"),
                       GetSQLValueString($_POST['shop_location'], "text"),
					   GetSQLValueString($_POST['shop_type'], "text"),
                       GetSQLValueString($_POST['shop_id'], "int"));
					   //"2006-10-12 18:15:00"

  mysql_select_db($database_YBDB, $YBDB);
  $Result1 = mysql_query($updateSQL, $YBDB) or die(mysql_error());
  
  header(sprintf("Location: %s",$editFormAction_no_shopid ));   //$editFormAction
}

//Change Date     isset($_POST["MM_update"])
if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "ChangeDate")) {
  $editFormAction = $_SERVER['PHP_SELF'] . "?shop_date={$_POST['shop_date']}&shop_dayname={$_POST['dayname']}&record_count={$_POST['record_count']}";
  header(sprintf("Location: %s",$editFormAction ));   //$editFormAction
}
?>
<?php include("include_header_shop.html"); ?>

	    <table style="margin:auto">

        <tr valign="top">
          <td align="left"><span class="yb_heading3red"><?php echo $error_message; ?></span> </td>
        </tr>
        <tr>
          <td><table   border="1" cellpadding="1" cellspacing="0">
                <tr bgcolor="#99CC33">
                  <td><strong>ShopID</strong></td>
                  <td height="35"><strong>Date</strong></td>
                  <td><strong>Shop Location </strong></td>
                  <td><strong>Shop Type </strong></td>
                  <td><strong>Stats</strong></td>
                  <td><strong>Edit</strong></td>
                </tr>
                <?php if ($nonshop != 0) { ?>
                <tr>
                  <td height="30" colspan="6"><a href="individual_hours_log.php">Log Non-Shop Hours</a> </td>
                </tr>
                <?php } ?>
                <form action="<?php echo $editFormAction; ?>" method="post" name="form_new" id="form_new">
                  <tr>
                    <td height="30"><span class="yb_heading3red">Start New Shop:</span></td>
                    <td><input id="ctrl_date" name="ctrl_date" type="text" class="yb_standard" value="<?php echo current_datetime();  ?>" /></td>
                    <td><?php list_shop_locations($ctrl_shoplocation,"Treasure City") ?></td>
                    <td><?php list_shop_types($ctrl_shoptype) ?></td>
                    <td>&nbsp;</td>
                    <td><input name="Update" type="submit" id="Update" value="Create Shop" /></td>
                  </tr>
                  <input type="hidden" name="MM_insert" value="form_new" />
                </form>
              <tr bgcolor="#99CC33">
                  <td height="25" colspan="6" valign="bottom" bgcolor="#99CC33">&nbsp;&nbsp;&nbsp;&nbsp;View Existing Shops:</td>
              </tr>
                <form action="<?php echo $editFormAction; ?>" method="post" name="FormUpdate_<?php echo $row_Recordset1['shop_id']; ?>" id="FormUpdate_<?php echo $row_Recordset1['shop_id']; ?>">
                  <?php while ($row_Recordset1 = mysql_fetch_assoc($Recordset1)) { //do { 
			  if($shop_id == $row_Recordset1['shop_id']) {?>
                  <tr valign="bottom" bgcolor="#CCCC33">
                    <td>Edit Record:<br />
                        <?php echo "<a href=\"{$page_shop_log}?shop_id={$row_Recordset1['shop_id']}\">Shop ID: {$row_Recordset1['shop_id']}</a>";?></td>
                    <td><div><?php echo $row_Recordset1['date']; ?></div></td>
                    <td><?php list_shop_locations('shop_location', $row_Recordset1['shop_location']); ?></td>
                    <td><?php list_shop_types('shop_type', $row_Recordset1['shop_type']); ?></td>
                    <td><?php echo $row_Recordset1['num_visitors']; ?> Visitors<br />
                      ~<?php echo $row_Recordset1['total_hours']; ?> Hours</td>
                    <td><input name="Update" type="submit" id="Update" value="Update" /></td>
                  </tr>
                  <input type="hidden" name="MM_insert" value="FormEdit" />
                  <input type="hidden" name="shop_id" value="<?php echo $row_Recordset1['shop_id']; ?>" />
                  <input type="hidden" name="date" value="<?php echo $row_Recordset1['date']; ?>" />
                </form>
              <?php } else { // end if EDIT RECORD ?>
                <tr>
                  <td><?php echo "<a href=\"{$page_shop_log}?shop_id={$row_Recordset1['shop_id']}\"  class=\"examine_shop\">Shop ID: {$row_Recordset1['shop_id']}</a>";?></td>
                  <td><?php echo $row_Recordset1['date'] . ' (' . $row_Recordset1['day'] . ')'; ?></td>
                  <td><?php echo $row_Recordset1['shop_location']; ?></td>
                  <td><?php echo $row_Recordset1['shop_type']; ?></td>
                  <td><?php echo $row_Recordset1['num_visitors']; ?> Visitors<br />
                    ~<?php echo $row_Recordset1['total_hours']; ?> Hours</td>
                  <td><?php if($row_Recordset1['CanEdit'] == 1) {echo "<a href=\"{$_SERVER['PHP_SELF']}?shop_date={$shop_date}&shop_id={$row_Recordset1['shop_id']}\">edit</a>";} else {echo "&nbsp;";} ?></td>
                </tr>
                <?php
		  } // end if EDIT RECORD 
		  } // end WHILE count of recordset ?>
              </table>
              <form id="form1" name="form1" method="post" action="">
                <br />
                Show
                <input name="record_count" type="text" value="10" size="3" maxlength="3" />
                shops on or before:
                <input name="shop_date" type="text" id="shop_date" value="<?php echo $shop_date; ?>" size="10" maxlength="10" />
                Day of week:
                <select name="dayname">
                  <option value="alldays" selected="selected">All Days</option>
                  <option value="Monday">Monday</option>
                  <option value="Tuesday">Tuesday</option>
                  <option value="Wednesday">Wednesday</option>
                  <option value="Thursday">Thursday</option>
                  <option value="Friday">Friday</option>
                  <option value="Saturday">Saturday</option>
                  <option value="Sunday">Sunday</option>
                </select>
                <input type="submit" name="Submit" value="Submit" />
                <input type="hidden" name="MM_insert" value="ChangeDate" />
            </form></td>
        </tr>
      </table>
	  
	  <?php include("include_footer.html"); ?>

<?php mysql_free_result($Recordset1);?>
