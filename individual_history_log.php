<?php
require_once('Connections/YBDB.php');
require_once('Connections/database_functions.php'); 

$page_edit_contact = PAGE_EDIT_CONTACT;
$page_shop_log = PAGE_SHOP_LOG;
$individual_shop_id = 90;

mysql_select_db($database_YBDB, $YBDB);
	
if($_GET['visit_id']>0){
	$visit_id = $_GET['visit_id'];
} else {
	$visit_id =-1;}
	
if($_GET['new_user_id']>0){
	$new_user_id = $_GET['new_user_id'];
} else {
	$new_user_id = -1;
}

if($_GET['contact_id']>0){
	$contact_id = $_GET['contact_id'];
} else {
	$contact_id = -1;
}


//shop_date
if(ISSET($_GET['shop_date'])){
	$shop_date_filter = $_GET['shop_date'];
} else {
	$shop_date_filter = current_date();}	
	
//dayname
if($_GET['shop_dayname']=='alldays'){
	$shop_dayname = '';
} elseif(isset($_GET['shop_dayname'])) {
	$shop_dayname = "AND DAYNAME(DATE(time_in)) = '" . $_GET['shop_dayname'] . "'";
} else {
	$shop_dayname = '';
}	

//record_count
if($_GET['record_count']>0){
	$record_count = $_GET['record_count'];
} else {
	$record_count = 40;}	
	
$query_Recordset1 = "SELECT shop_id, shop_hours.shop_visit_id, shop_hours.contact_id, shop_hours.shop_user_role, shop_hours.project_id, DATE(shop_hours.time_in) AS date, DAYNAME(shop_hours.time_in) AS dayname, shop_hours.time_in, shop_hours.time_out, TIME_FORMAT(TIMEDIFF(time_out, time_in),'%k:%i') as et, shop_hours.comment, CONCAT(contacts.last_name, ', ', contacts.first_name, ' ',contacts.middle_initial) AS full_name, contacts.first_name FROM shop_hours
LEFT JOIN shop_user_roles ON shop_hours.shop_user_role=shop_user_roles.shop_user_role_id
LEFT JOIN contacts ON shop_hours.contact_id=contacts.contact_id
WHERE shop_hours.contact_id = {$contact_id} AND DATE(shop_hours.time_in) <= '{$shop_date_filter}' {$shop_dayname} ORDER BY time_in DESC
LIMIT  0, {$record_count};";
$Recordset1 = mysql_query($query_Recordset1, $YBDB) or die(mysql_error());
//$row_Recordset1 = mysql_fetch_assoc($Recordset1);
$totalRows_Recordset1 = mysql_num_rows($Recordset1);

mysql_select_db($database_YBDB, $YBDB);
$query_Recordset2 = "SELECT * FROM shops WHERE shop_id = $individual_shop_id;";
$Recordset2 = mysql_query($query_Recordset2, $YBDB) or die(mysql_error());
$row_Recordset2 = mysql_fetch_assoc($Recordset2);
$totalRows_Recordset2 = mysql_num_rows($Recordset2);
$shop_date = $row_Recordset2['date'];
$shop_location = $row_Recordset2['shop_location'];
$shop_type = $row_Recordset2['shop_type'];

//sets the default time for users to sign in
$shop_start_time = current_datetime();
$current_date = current_date();

//Action on form update
//shop_log2.php?shop_id=2&amp;visit_id=4
$editFormAction = $_SERVER['PHP_SELF'] . "?contact_id=$contact_id&visit_id=$visit_id";
$editFormAction_novisit = $_SERVER['PHP_SELF'] . "?contact_id=$contact_id";
//if (isset($_SERVER['QUERY_STRING'])) {
//  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
//}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form_new") && ($_POST["contact_id"] == "no_selection")){
	//if no contact is selected
	$error_message = '<span class="yb_heading3red">Please Select a User</span><br />';
} elseif ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form_new")) {
  
  $insertSQL = sprintf("INSERT INTO shop_hours (contact_id, shop_id, shop_user_role, time_in, comment, project_id) VALUES (%s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['contact_id'], "int"),
                       GetSQLValueString($individual_shop_id, "int"),
                       GetSQLValueString($_POST['user_role'], "text"),
                       GetSQLValueString(dateandtimein($_POST['date'], $_POST['time_in']), "date"),
					   GetSQLValueString($_POST['comment'], "text"),
					   GetSQLValueString($_POST['project'], "text"));

  mysql_select_db($database_YBDB, $YBDB);
  $Result1 = mysql_query($insertSQL, $YBDB) or die(mysql_error());

  $insertGoTo = "shop_log2.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $editFormAction_novisit));
}

//$_POST["MM_insert"] is in the form: FormUpdate_$VisitID OR FormUpdate_142.  This line seperates the visit id from the 
//list($is_UpdateForm, $visit_id) = split('[_]', $_POST["MM_insert"]);

//Update Record     isset($_POST["MM_update"])
if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "FormUpdate")) {
  $updateSQL = sprintf("UPDATE shop_hours SET time_out=%s WHERE shop_visit_id=%s",
                       GetSQLValueString($_POST['time_out'], "date"),
                       GetSQLValueString($_POST['shop_visit_id'], "int"));
					   //"2006-10-12 18:15:00"

  mysql_select_db($database_YBDB, $YBDB);
  $Result1 = mysql_query($updateSQL, $YBDB) or die(mysql_error());
  
  $gotopage = "index.html";
  header(sprintf("Location: %s",$editFormAction ));   //$editFormAction
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "FormEdit")) {
  $updateSQL = sprintf("UPDATE shop_hours SET contact_id=%s, shop_user_role=%s, project_id=%s, time_in=%s, time_out=%s, comment=%s WHERE shop_visit_id=%s",
                       GetSQLValueString($_POST['contact_id'], "int"),
                       GetSQLValueString($_POST['user_role'], "text"),
                       GetSQLValueString($_POST['project'], "text"),
                       GetSQLValueString(dateandtimein($_POST['date'], $_POST['time_in']), "date"),
					   GetSQLValueString($_POST['time_out'], "date"),
                       GetSQLValueString($_POST['comment'], "text"),
					   GetSQLValueString($_POST['shop_visit_id'], "int"));
					   //"2006-10-12 18:15:00"

  mysql_select_db($database_YBDB, $YBDB);
  $Result1 = mysql_query($updateSQL, $YBDB) or die(mysql_error());
  
  header(sprintf("Location: %s",$editFormAction_novisit ));   //$editFormAction
}

//Change Date     isset($_POST["MM_update"])
if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "ChangeDate")) {
  $editFormAction = $_SERVER['PHP_SELF'] . "?contact_id=$contact_id&shop_date={$_POST['shop_date']}&shop_dayname={$_POST['dayname']}&record_count={$_POST['record_count']}";
  header(sprintf("Location: %s",$editFormAction ));   //$editFormAction
}
?>


<?php include("include_header.html"); ?>

<table   border="0" cellpadding="1" cellspacing="0">
  <tr>
    <td align="left" valign="bottom"><?php echo $error_message;?>
      Shop ID: <span class="yb_standarditalics"><?php echo $individual_shop_id;?></span>; &nbsp;Location: <span class="yb_standarditalics"><?php echo $shop_location;?></span>; &nbsp;Date: <span class="yb_standarditalics"><?php echo $shop_date;?></span>; &nbsp;Shop Type: <span class="yb_standarditalics"><?php echo $shop_type;?></span>		</td>
    </tr>
  <tr>
    <td>
      <table   border="1" cellpadding="1" cellspacing="0" bordercolor="#CCCCCC">
        <tr bordercolor="#CCCCCC" bgcolor="#99CC33" class="yb_heading3">
          <td height="35">Shop User </td>
		  <td>Date</td>
		  <td>ShopID</td>
		  <td height="35">Status</td>
		  <td height="35">Time In </td>
		  <td height="35" bgcolor="#99CC33">Time Out </td>
		  <td height="35">Edit Data </td>
	    </tr>
        <form method="post" name="form_new" action="<?php echo $editFormAction; ?>">
          <input type="hidden" name="MM_insert" value="form_new">
          </form>
	    
	  <?php while ($row_Recordset1 = mysql_fetch_assoc($Recordset1)) { //do { 
	  if($visit_id == $row_Recordset1['shop_visit_id']) {?>
        <form method="post" name="FormUpdate_<?php echo $row_Recordset1['shop_visit_id']; ?>" action="<?php echo $editFormAction; ?>">
          <tr valign="bottom" bordercolor="#CCCCCC" bgcolor="#CCCC33">
            <td>Edit Record: <br> 
              <?php list_contacts('contact_id', $row_Recordset1['contact_id']); ?></td>
		  <td><input name="date" type="text" value="<?php echo $row_Recordset1['date']; ?>" size="10" maxlength="10" /></td>
		  <td>&nbsp;</td>
		  <td><?php list_shop_user_roles('user_role', $row_Recordset1['shop_user_role']); ?></td>
		  <td><?php list_time("{$current_date} 08:00:00",'0000-00-00 00:00:00','time_in',-60,0,$row_Recordset1['time_in'],16); ?></td>
		  <td><?php 
			if ($row_Recordset1['time_out'] <> '0000-00-00 00:00:00'){
				list_time($row_Recordset1['time_in'],$row_Recordset1['time_out'],'time_out',0,1,$row_Recordset1['time_out']);
			} ?></td>
		  <td><input type="submit" name="Submit" value="Update Changes" /></td>
	    </tr>
          <tr bordercolor="#CCCCCC" bgcolor="#CCCC33">
            <td colspan="7"><table border="0" cellspacing="0" cellpadding="1">
              <tr>
                <td width="125"><div align="right">Project:</div></td>
              <td><?php list_projects_collective('project', $row_Recordset1['project_id']); ?></td>
            </tr>
              <tr>
                <td><div align="right">Comment:</div></td>
              <td><input name="comment" type="text" value="<?php echo $row_Recordset1['comment']; ?>" size="90" /></td>
            </tr>
              <tr>
                <td><div align="right">Delete:</div></td>
              <td>Click to Delete this Shop User's Visit: <a href="<?php echo PAGE_SHOP_LOG_DELETE_VISIT . "?visit_id={$visit_id}&shop_id={$individual_shop_id}";?>">Delete</a> </td>
            </tr>
              </table>	    </td>
	      </tr>
          <input type="hidden" name="MM_insert" value="FormEdit">
          <input type="hidden" name="shop_visit_id" value="<?php echo $row_Recordset1['shop_visit_id']; ?>">
          </form>
	  <?php } else { //This section executes if it is not the visit_id selected NOT FOR EDIT ?> 
        <form method="post" name="FormUpdate_<?php echo $row_Recordset1['shop_visit_id']; ?>" action="<?php echo $editFormAction; ?>">
          <tr bordercolor="#CCCCCC">
            <td><?php echo $row_Recordset1['full_name']; ?></td>
		  <td><?php echo $row_Recordset1['date'] . " (" . $row_Recordset1['dayname'] . ")"; ?></td>
		  <td><?php echo "<a href=\"{$page_shop_log}?shop_id={$row_Recordset1['shop_id']}\">{$row_Recordset1['shop_id']}</a>";?></td>
		  <td><?php echo $row_Recordset1['shop_user_role']; ?></td>
		  <td><?php echo date_to_time($row_Recordset1['time_in']); ?></td>
		  <td><?php echo list_time($row_Recordset1['time_in'],$row_Recordset1['time_out'],'time_out',0,1,'none', 8,$row_Recordset1['et']); ?></td>
		  <td><?php echo "<a href=\"{$_SERVER['PHP_SELF']}?contact_id={$contact_id}&visit_id={$row_Recordset1['shop_visit_id']}\">edit</a>"; ?></td>
	    </tr>
          <input type="hidden" name="MM_insert" value="FormUpdate">
          <input type="hidden" name="shop_visit_id" value="<?php echo $row_Recordset1['shop_visit_id']; ?>">
          </form>
	  <?php } // if
	} //while ($row_Recordset1 = mysql_fetch_assoc($Recordset1)); // while Recordset1 ?>
        </table>  </tr>
</table>
<form id="form1" name="form1" method="post" action="">
                  <br />
                  Show
                  <input name="record_count" type="text" value="10" size="3" maxlength="3" />
                  shops on or before:
                  <input name="shop_date" type="text" id="shop_date" value="<?php echo current_date(); ?>" size="10" maxlength="10" />
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
                  (date format YYYY-MM-DD)
                  <input type="hidden" name="MM_insert" value="ChangeDate" />
      </form>
<p>&nbsp;</p>
<?php include("../include_footer.html"); ?>
<?php
mysql_free_result($Recordset1);
?>
