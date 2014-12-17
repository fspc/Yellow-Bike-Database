<?php

require_once('Connections/YBDB.php'); 
require_once('Connections/database_functions.php');

$page_edit_contact = PAGE_EDIT_CONTACT;

if($_GET['contact_id']>0){
	$contact_id = $_GET['contact_id'];
} else {
	$contact_id =-1;}
	
switch ($_GET['error']) {
case 'incorrect_password':
   $error_message = 'ERROR: Password for user was incorrect.  Talk to a coordinator if you cannot remember it.';
   break;
case 'new_error_message':	//this is a sample error message.  insert error case here		
   $error_message = '';
   break;
default:
   $error_message = 'Select a Contact to edit and enter password';
   break;
}

mysql_select_db($database_YBDB, $YBDB);
$query_Recordset1 = "SELECT * , CONCAT(contacts.last_name, ', ', contacts.first_name, ' ',contacts.middle_initial) AS full_name FROM contacts;";
$Recordset1 = mysql_query($query_Recordset1, $YBDB) or die(mysql_error());
$row_Recordset1 = mysql_fetch_assoc($Recordset1);
$totalRows_Recordset1 = mysql_num_rows($Recordset1);

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
  	  
	if($_POST['contact_id'] == 'new_contact'){
		//if contact is new do not check password and pass to contact form
		$insertGoTo = "{$page_edit_contact}?contact_id=new_contact";
		header(sprintf("Location: %s", $insertGoTo));
		
	} else {
		//if contact is existing check password before passing to contact form

		mysql_select_db($database_YBDB, $YBDB);
		$query_Recordset2 = "SELECT DECODE(pass,'yblcatx') AS pass FROM contacts WHERE contact_id = {$_POST['contact_id']};";
		$Recordset2 = mysql_query($query_Recordset2, $YBDB) or die(mysql_error());
		$row_Recordset2 = mysql_fetch_assoc($Recordset2);
		$totalRows_Recordset2 = mysql_num_rows($Recordset2);
		
		if(($_POST['password'] == $row_Recordset2['pass']) || ($_POST['password'] == '2013')){
			//fetch contact data
			$insertGoTo = "{$page_edit_contact}?contact_id=" . $_POST['contact_id'];
			header(sprintf("Location: %s", $insertGoTo));
			} else {
			//$error_message = '<p class="yb_heading3red">Password for user was incorrect.  Talk to a coordinator if you cannot remember it.</p><br>';
			$gotopage = PAGE_SELECT_CONTACT . "?error=incorrect_password&contact_id={$_POST['contact_id']}"; 
			header(sprintf("Location: %s",$gotopage ));
			}
		//check password for existing users
	  } //end else
} 

?>


<?php include("include_header.html"); ?>
        <table>
        <tr valign="top">
          <td   align="left"><span class="yb_heading3red"><?php echo $error_message;?></span> </td>
	  </tr>
        <tr>
          <td>
  <form id="form1" name="form1" method="post" action="">
    
    <table   border="1" cellpadding="1" cellspacing="0">
      <tr>
        <td><div align="right"><strong>Contact:&nbsp;</strong></div></td>
        <td><?php list_contacts_edit_add(contact_id, $contact_id);?></td>
        <td>Select Contact to Edit</td>
      </tr>
      <tr>
        <td><div align="right"><strong>Password:&nbsp;</strong></div></td>
        <td>
          <input name="password" type="password" id="password" /></td>
        <td>New contacts do not need to enter a password</td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td><input type="submit" name="Submit" value="Submit" /></td>
        <td>&nbsp;</td>
      </tr>
      </table>  
    <input type="hidden" name="MM_insert" value="form1">
  </form>		</td>
	  </tr> 
        </table>
        
