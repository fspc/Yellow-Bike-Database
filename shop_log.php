<?php
require_once('Connections/YBDB.php');
require_once('Connections/database_functions.php'); 

$page_edit_contact = PAGE_EDIT_CONTACT; 
$page_individual_history_log = INDIVIDUAL_HISTORY_LOG;
$default_shop_user = DEFAULT_SHOP_USER;
$shop_hours_length = SHOP_HOURS_LENGTH;

mysql_select_db($database_YBDB, $YBDB);
//?shop_id=2
if($_GET['shop_id']>0){
	$shop_id = $_GET['shop_id'];
} else {
	$shop_id = current_shop_by_ip();
	if (isset($shop_id)) {
		//$shop_id stays the same
	} else {
		$gotopage = PAGE_START_SHOP . "?error=no_shop"; 
		header(sprintf("Location: %s",$gotopage ));
	}
}
	
if($_GET['visit_id']>0){
	$visit_id = $_GET['visit_id'];
} else {
	$visit_id =-1;}
	
if($_GET['new_user_id']>0){
	$new_user_id = $_GET['new_user_id'];
} else {
	$new_user_id = -1;
}
	
	
$query_Recordset1 = "SELECT shop_hours.shop_visit_id, shop_hours.contact_id, 
									shop_hours.shop_user_role, shop_hours.project_id, 
									shop_hours.time_in, shop_hours.time_out, 
									TIME_FORMAT(TIMEDIFF(time_out, time_in),'%k:%i') 
									AS et, shop_hours.comment, 
									CONCAT(contacts.last_name, ', ', contacts.first_name, ' ',contacts.middle_initial) 
									AS full_name, contacts.first_name FROM shop_hours
LEFT JOIN shop_user_roles ON shop_hours.shop_user_role=shop_user_roles.shop_user_role_id
LEFT JOIN contacts ON shop_hours.contact_id=contacts.contact_id
WHERE shop_hours.shop_id = $shop_id ORDER BY hours_rank, time_in DESC;";
$Recordset1 = mysql_query($query_Recordset1, $YBDB) or die(mysql_error());
//$row_Recordset1 = mysql_fetch_assoc($Recordset1);
$totalRows_Recordset1 = mysql_num_rows($Recordset1);

mysql_select_db($database_YBDB, $YBDB);
$query_Recordset2 = "SELECT *, IF(date <> curdate() AND shop_type = 'Mechanic Operation Shop',0,1) as CanEdit FROM shops WHERE shop_id = $shop_id;";
$Recordset2 = mysql_query($query_Recordset2, $YBDB) or die(mysql_error());
$row_Recordset2 = mysql_fetch_assoc($Recordset2);
$totalRows_Recordset2 = mysql_num_rows($Recordset2);
$shop_date = $row_Recordset2['date'];
$shop_location = $row_Recordset2['shop_location'];
$shop_type = $row_Recordset2['shop_type'];
$shop_CanEdit = $row_Recordset2['CanEdit'];

mysql_select_db($database_YBDB, $YBDB);
$query_Recordset3 = "SELECT MIN(time_in) as shop_start FROM shop_hours WHERE shop_id = $shop_id;";
$Recordset3 = mysql_query($query_Recordset3, $YBDB) or die(mysql_error());
$row_Recordset3 = mysql_fetch_assoc($Recordset3);
$totalRows_Recordset3 = mysql_num_rows($Recordset3);
$shop_start_time = $row_Recordset3['shop_start'];

//Action on form update
//shop_log2.php?shop_id=2&amp;visit_id=4
$editFormAction = $_SERVER['PHP_SELF'] . "?shop_id=$shop_id&visit_id=$visit_id&welcome=yes";
$editFormAction_novisit = $_SERVER['PHP_SELF'] . "?shop_id=$shop_id&welcome=yes";

//if (isset($_SERVER['QUERY_STRING'])) {
//  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
//}

//Form Submit New Shop User
if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form_new") && ($_POST["contact_id"] == "no_selection")){
	//if no contact is selected
	$error_message = '<span class="yb_heading3red">Please Select a User</span><br />';
} elseif ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form_new")) {
  $insertSQL = sprintf("INSERT INTO shop_hours (contact_id, shop_id, shop_user_role, time_in, comment, project_id) VALUES (%s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['contact_id'], "int"),
                       GetSQLValueString($shop_id, "int"),
                       GetSQLValueString($_POST['user_role'], "text"),
                       GetSQLValueString($_POST['time_in'], "date"),
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
                       GetSQLValueString($_POST['time_in'], "date"),
					   GetSQLValueString($_POST['time_out'], "date"),
                       GetSQLValueString($_POST['comment'], "text"),
					   GetSQLValueString($_POST['shop_visit_id'], "int"));
					   //"2006-10-12 18:15:00"

  mysql_select_db($database_YBDB, $YBDB);
  $Result1 = mysql_query($updateSQL, $YBDB) or die(mysql_error());
  
  header(sprintf("Location: %s",$editFormAction_novisit ));   //$editFormAction
}
?>


<?php include("include_header_shop.html"); ?>

	<table width="2200px">
  		<tr>
    		<td align="left" valign="bottom"><?php echo $error_message;?></td>
  		</tr>
      <form method="post" name="form_new" action="<?php echo $editFormAction; ?>">
		  <tr>
			<td><label>Shop ID:</label></td>
	      <td><?php echo $shop_id;?>; &nbsp;Location: 
	      	 <?php echo $shop_location;?>; &nbsp;Date: 
	      	 <?php echo $shop_date;?>; &nbsp;Shop Type: 
	      	 <?php echo $shop_type;?>
	      </td>		  
		  </tr>	        
        <tr>
          <td><label>Shop User:</label></td>
          <td>
              <?php list_contacts_select_user('contact_id', $new_user_id); ?>
          </td>
        </tr>
        <tr>
		  	<td><label>Status:</label></td>
		  	<td>
		    <?php list_shop_user_roles('user_role', $default_shop_user); ?>
		   </td>
		  </tr>
		  <tr>
		  	<td><label>Time In:</label></td>
		  	<td><strong>
		    <?php if($totalRows_Recordset1 <> 0){ 
						 list_time($shop_start_time,'0000-00-00 00:00:00','time_in',-60,0,'none',16); 						
						} else {
						 list_time("{$shop_date} 08:00:00",'0000-00-00 00:00:00','time_in',-15, 0, 'none',16);				
						}
									?>
		   </td>
		  </tr>
		  <tr>
		   <td><label>Project:</label></td>
			<td><?php list_projects_collective('project'); ?></td>
		  </tr>
		  <tr>
				<td><div align="right">Comments:</div></td>
			<td><textarea  name="comment" cols="45" rows="3"></textarea>
			</td>		  
		  </tr>
		  <tr>
		  	<td></td>
		  	<td valign="bottom"><input name="Submit" type="submit" value="Sign In" /></td>
		  </tr>
	     <input type="hidden" name="MM_insert" value="form_new">
       </form>     
       </tbody>
 	</table>
          
   <br /><br />
          
   <table  width="850px" style="margin-left:170px" border="1" cellpadding="1" cellspacing="0" bordercolor="#CCCCCC">
     <tbody     
	  <tr valign="bottom" bordercolor="#CCCCCC">
	    <td height="25" colspan="1" bgcolor="#99CC33">Existing Shop Users</td>
	    <td height="25" colspan="1" bgcolor="#99CC33">Status</td>
	    <td height="25" colspan="1" bgcolor="#99CC33">Time In</td>
	    <td height="25" colspan="1" bgcolor="#99CC33">Time Out</td>
	    <td height="25" colspan="1" bgcolor="#99CC33">Update Hours</td>
	    <td height="25" colspan="1" bgcolor="#99CC33">Edit</td>
	   </tr>
        <?php while ($row_Recordset1 = mysql_fetch_assoc($Recordset1)) { //do { 
	  if($visit_id == $row_Recordset1['shop_visit_id']) {?>
        <form method="post" name="FormUpdate_<?php echo $row_Recordset1['shop_visit_id']; ?>" action="<?php echo $editFormAction; ?>">
          <tr valign="bottom" bordercolor="#CCCCCC" bgcolor="#CCCC33">
            <td>Edit Record: <br> 
              <?php list_contacts('contact_id', $row_Recordset1['contact_id']); ?></td>
		  <td><?php list_shop_user_roles('user_role', $row_Recordset1['shop_user_role']); ?></td>
		  <td><?php list_time($shop_start_time,'0000-00-00 00:00:00','time_in',-60,0,$row_Recordset1['time_in'],16); ?></td>
		  <td><?php 
			if ($row_Recordset1['time_out'] <> '0000-00-00 00:00:00'){
				list_time($row_Recordset1['time_in'],$row_Recordset1['time_out'],'time_out',0,1,$row_Recordset1['time_out']);
			} ?></td>
		  <td><input type="submit" name="Submit" value="Update Changes" /></td>
		  <td></td>
	    </tr>
          <tr bordercolor="#CCCCCC" bgcolor="#CCCC33">
            <td colspan="6"><table border="0" cellspacing="0" cellpadding="1">
              <tr>
                <td width="125"><div align="right">Project:</div></td>
              <td><?php list_projects('project', $row_Recordset1['project_id']); ?></td>
            </tr>
              <tr>
                <td><div align="right">Comment:</div></td>
              <td><textarea  name="comment" cols="45" rows="3"><?php echo $row_Recordset1['comment']; ?></textarea>
              </td>
            </tr>
              <?php //if(current_shop_by_ip()>=$shop_id & (current_shop_by_ip()-5)<=$shop_id ) { 
						 // Not really necessary since the time can be zeroed out. 
						 // shop_log_delete_shopvisitid.php has been moved to the attic              
              ?>
              <!--
              <tr>
                <td><div align="right">Delete:</div></td>
              <td>Click to Delete this Shop User's Visit: <a href="<?php echo PAGE_SHOP_LOG_DELETE_VISIT . "?visit_id={$visit_id}&shop_id={$shop_id}";?>">Delete</a> </td>
            </tr> --> <?php // } //end if current shop?>
         </table>	   
	      </tr>
          <input type="hidden" name="MM_insert" value="FormEdit">
          <input type="hidden" name="shop_visit_id" value="<?php echo $row_Recordset1['shop_visit_id']; ?>">
          </form>
	  <?php } else { //This section executes if it is not the visit_id selected NOT FOR EDIT ?> 
        <form method="post" name="FormUpdate_<?php echo $row_Recordset1['shop_visit_id']; ?>" action="<?php echo $editFormAction; ?>">
          <tr bordercolor="#CCCCCC">
            <td><a href="<?php echo "{$page_individual_history_log}?contact_id=" . $row_Recordset1['contact_id']; ?>"><?php echo $row_Recordset1['full_name']; ?></a></td>
		  <td><?php echo $row_Recordset1['shop_user_role']; ?></td>
		  <td><?php echo date_to_time($row_Recordset1['time_in']); ?></td>
		  <td><?php echo list_time($row_Recordset1['time_in'],$row_Recordset1['time_out'],'time_out',0,1,'none', $shop_hours_length, $row_Recordset1['et']); ?></td>
		  <td><?php sign_out($row_Recordset1['time_out'], $row_Recordset1['first_name']); ?>&nbsp</td>
		  <td><?php if($shop_CanEdit == 1) {echo "<a href=\"{$_SERVER['PHP_SELF']}?shop_id={$shop_id}&visit_id={$row_Recordset1['shop_visit_id']}\">edit</a>";} else {echo "&nbsp";} ?></td>
	    </tr>
          <input type="hidden" name="MM_insert" value="FormUpdate">
          <input type="hidden" name="shop_visit_id" value="<?php echo $row_Recordset1['shop_visit_id']; ?>">
          </form>
	  <?php } // if
	} //while ($row_Recordset1 = mysql_fetch_assoc($Recordset1)); // while Recordset1 ?>
        </table>  </tr>
  <tr>
    <td height="40" valign="bottom"></td>
    </tr>
</table>
<p>&nbsp;</p>
<?php include("include_footer.html"); ?>
<?php
mysql_free_result($Recordset1);
?>
