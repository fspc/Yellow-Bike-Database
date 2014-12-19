<?php
require_once('Connections/YBDB.php'); 
require_once('Connections/database_functions.php');

// This variable tracks the transaction id to return to correct transaction
if($_GET['trans_id']>0){
	$trans_id = $_GET['trans_id'];
} else {
	$trans_id = -1;
}

switch ($_GET['error']) {
case 'new_error_message':	//this is a sample error message.  insert error case here		
   $error_message = '';
   break;
default:
   $error_message = 'Enter or Update Contact Information - </span><span class="yb_standard"> Yellow Bike uses this information soely to support the project and it is kept entirely private.  When we apply for grants it helps us to know a little bit about our shop users.  <p>Thanks for supporting The Yellow Bike Project. </p> </span><span class="yb_heading3red">';
   break;
}

// if contact_id = "new_contact" then a new contact is initialized during page load and contact_id is assigned to new contact
if($_GET['contact_id'] == 'new_contact'){
	$insertSQL = sprintf("INSERT INTO contacts (date_created) VALUES (%s)",
						   GetSQLValueString('current_time', "date"));
	mysql_select_db($database_YBDB, $YBDB);
	$Result1 = mysql_query($insertSQL, $YBDB) or die(mysql_error());
	
	mysql_select_db($database_YBDB, $YBDB);
	$query_Recordset2 = "SELECT MAX(contact_id) as new_contact_id FROM contacts;";
	$Recordset2 = mysql_query($query_Recordset2, $YBDB) or die(mysql_error());
	$row_Recordset2 = mysql_fetch_assoc($Recordset2);
	$totalRows_Recordset2 = mysql_num_rows($Recordset2);
	
	$contact_id = $row_Recordset2['new_contact_id'];
	$contact_id_entry = 'new_contact';
	mysql_free_result($Recordset2);
} elseif(isset($_GET['contact_id'])) {
	//else contact_id is assigned from passed value
	$contact_id = $_GET['contact_id'];
	$contact_id_entry = $_GET['contact_id'];
} else {
	$contact_id = -1;
	$contact_id_entry = -1;
}

$editFormAction = $_SERVER['PHP_SELF'] . "?contact_id={$contact_id}&trans_id={$trans_id}";

$page_sale_log = PAGE_SALE_LOG . "?trans_id={$trans_id}";

// update location info
if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
  $updateSQL = sprintf("UPDATE contacts SET first_name=%s, middle_initial=%s, last_name=%s, email=%s,  phone=%s, address1=%s, address2=%s, city=%s, `state`=%s, zip=%s, location_name=%s, hidden=%s WHERE contact_id=%s",
                       GetSQLValueString($_POST['first_name'], "text"),
                       GetSQLValueString($_POST['middle_initial'], "text"),
                       GetSQLValueString($_POST['last_name'], "text"),
                       GetSQLValueString($_POST['email'], "text"),
                       GetSQLValueString($_POST['phone'], "text"),
                       GetSQLValueString($_POST['address1'], "text"),
                       GetSQLValueString($_POST['address2'], "text"),
                       GetSQLValueString($_POST['city'], "text"),
                       GetSQLValueString($_POST['state'], "text"),
                       GetSQLValueString($_POST['zip'], "text"),
					   GetSQLValueString($_POST['location_name'], "text"),
					   GetSQLValueString($_POST['hidden'], "int"),
					   GetSQLValueString($_POST['contact_id'], "int"));

  mysql_select_db($database_YBDB, $YBDB);
  $Result1 = mysql_query($updateSQL, $YBDB) or die(mysql_error());
  
  if ($_POST['contact_id_entry'] == 'new_contact'){
  	//navigate back to transaction_id that it came from
	$pagegoto = $page_sale_log;
  } else {
  	$pagegoto = $editFormAction;
  }
  header(sprintf("Location: %s", $pagegoto));
}

mysql_select_db($database_YBDB, $YBDB);
$query_Recordset1 = "SELECT *, DECODE(pass,'yblcatx') AS passdecode FROM contacts WHERE contact_id = $contact_id";
$Recordset1 = mysql_query($query_Recordset1, $YBDB) or die(mysql_error());
$row_Recordset1 = mysql_fetch_assoc($Recordset1);
$totalRows_Recordset1 = mysql_num_rows($Recordset1);
?>


<?php include("include_header.html"); ?>
<table>
  <tr valign="top">
    <td   align="left"><span class="yb_heading3red"><?php echo $error_message; ?></span></td>
	  </tr>
  <tr>
    <td align="center">
      
      <form method="post" name="form1" action="<?php echo $editFormAction; ?>">
        <table width="500" border="1" cellpadding="1" cellspacing="0" bordercolor="#CCCCCC">
          <tr valign="bottom">
            <td height="35" colspan="2" align="center" valign="bottom" nowrap bgcolor="#99CC33"><strong>Location Information </strong></td>
			    </tr>
          <tr valign="bottom">
            <td width="200" height="26" align="right" nowrap>Location_id:</td>
			    <td><?php echo $row_Recordset1['contact_id']; ?></td>
			    </tr>
          <tr valign="bottom">
            <td height="26" align="right" nowrap>Donation Location: </td>
			    <td><input type="text" name="location_name" value="<?php echo $row_Recordset1['location_name']; ?>" size="32" /></td>
			    </tr>
          <tr valign="bottom">
            <td height="26" align="right" nowrap>Address1:</td>
			    <td><input type="text" name="address1" value="<?php echo $row_Recordset1['address1']; ?>" size="32"></td>
			    </tr>
          <tr valign="bottom">
            <td height="26" align="right" nowrap>Address2:</td>
			    <td><input type="text" name="address2" value="<?php echo $row_Recordset1['address2']; ?>" size="32"></td>
			    </tr>
          <tr valign="bottom">
            <td height="26" align="right" nowrap>City:</td>
			    <td><input type="text" name="city" value="<?php echo $row_Recordset1['city']; ?>" size="32"></td>
			    </tr>
          <tr valign="bottom">
            <td height="26" align="right" nowrap>State:</td>
			    <td><input name="state" type="text" value="<?php echo $row_Recordset1['state']; ?>" size="2" maxlength="2"></td>
			    </tr>
          <tr valign="bottom">
            <td height="26" align="right" nowrap>ZIP:</td>
			    <td><input type="text" name="zip" value="<?php echo $row_Recordset1['zip']; ?>" size="5"></td>
			    </tr>
          <tr valign="bottom">
            <td height="35" colspan="2" align="center" valign="bottom" nowrap bgcolor="#99CC33"><strong>Contact Information </strong></td>
			    </tr>
          <tr valign="bottom">
            <td height="26" align="right" nowrap>First_name:</td>
			    <td><input type="text" name="first_name" value="<?php echo $row_Recordset1['first_name']; ?>" size="32"></td>
			    </tr>
          <tr valign="bottom">
            <td height="26" align="right" nowrap>Middle_initial:</td>
			    <td><input name="middle_initial" type="text" value="<?php echo $row_Recordset1['middle_initial']; ?>" size="1" maxlength="1"></td>
			    </tr>
          <tr valign="bottom">
            <td height="26" align="right" nowrap>Last_name:</td>
			    <td><input type="text" name="last_name" value="<?php echo $row_Recordset1['last_name']; ?>" size="32"></td>
			    </tr>
          <tr valign="bottom">
            <td height="26" align="right" nowrap>Email:</td>
			    <td><input type="text" name="email" value="<?php echo $row_Recordset1['email']; ?>" size="32"></td>
			    </tr>
          <tr valign="bottom">
            <td height="26" align="right" nowrap>Phone:</td>
			    <td><input type="text" name="phone" value="<?php echo $row_Recordset1['phone']; ?>" size="32"></td>
			    </tr>
          <tr valign="bottom">
            <td nowrap align="right">&nbsp;</td>
			    <td><input type="submit" value="Update Contact Info"></td>
		        </tr>
          </table>
		    <input type="hidden" name="MM_insert" value="form1">
        <input type="hidden" name="contact_id" value="<?php echo $row_Recordset1['contact_id']; ?>">
        <input type="hidden" name="contact_id_entry" value="<?php echo $contact_id_entry; ?>">
        <input type="hidden" name="trans_id" value="<?php echo $trans_id; ?>">
        <input type="hidden" name="hidden" value="1">
        </form>	  </td>
	  </tr>
</table>
<p>&nbsp;</p>
<?php include("include_footer.html"); ?>
<?php
mysql_free_result($Recordset1);
?>
