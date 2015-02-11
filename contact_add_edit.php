<?php

require_once('Connections/YBDB.php'); 
require_once('Connections/database_functions.php');

if($_GET['shop_id']>0){
	$shop_id = $_GET['shop_id'];
} else {
	$shop_id = current_shop_by_ip();
}

switch ($_GET['error']) {
case 'new_error_message':	//this is a sample error message.  insert error case here		
   $error_message = '';
   break;
default:
   $error_message = 'Enter or Update Contact Information - </span><span class="yb_standard"> 
   Thank-you for supporting Positive Spin. </p> </span><span class="yb_heading3red">';
   break;
}

$page_shop_log = PAGE_SHOP_LOG . "?shop_id=$shop_id";

if($_GET['contact_id'] == 'new_contact'){
	//adds contact is new_contact is selected
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

$editFormAction = "?contact_id={$contact_id}&shop_id={$shop_id}";

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {

	$updateSQL = sprintf("UPDATE contacts SET first_name=%s, middle_initial=%s, last_name=%s, email=%s, 
  								DOB=%s, receive_newsletter=%s, phone=%s, address1=%s, address2=%s, city=%s, 
  								`state`=%s, zip=%s, pass=ENCODE(%s,'yblcatx') WHERE contact_id=%s",
                    	GetSQLValueString($_POST['first_name'], "text"),
                    	GetSQLValueString($_POST['middle_initial'], "text"),
                    	GetSQLValueString($_POST['last_name'], "text"),
                   	GetSQLValueString($_POST['email'], "text"),
					   	GetSQLValueString($_POST['DOB'], "date"),
					   	GetSQLValueString($_POST['list_yes_no'], "int"),
                    	GetSQLValueString($_POST['phone'], "text"),
                    	GetSQLValueString($_POST['address1'], "text"),
                    	GetSQLValueString($_POST['address2'], "text"),
                    	GetSQLValueString($_POST['city'], "text"),
                    	GetSQLValueString($_POST['state'], "text"),
                    	GetSQLValueString($_POST['zip'], "text"),
					   	GetSQLValueString($_POST['password'], "text"),
					   	GetSQLValueString($_POST['contact_id'], "int"));

	mysql_select_db($database_YBDB, $YBDB);
	$Result1 = mysql_query($updateSQL, $YBDB) or die(mysql_error());
  
  if ($_POST['contact_id_entry'] == 'new_contact'){
  
  	//navigate back to shop that it came from
	$pagegoto = PAGE_SHOP_LOG . "?shop_id={$shop_id}&new_user_id={$contact_id}";
	header(sprintf("Location: %s", $pagegoto));

  }
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
      
      <form method="post" name="form1" action="<?php echo $editFormAction; ?>">
        <table border="0" cellpadding="1" cellspacing="0" bordercolor="#CCCCCC">
          <tr>
            <td><label>Contact_id:</label></td>
			    <td><?php echo $row_Recordset1['contact_id']; ?></td>
			 </tr>
          <tr >
            <td><label>Name:</label></td>
			    <td><input id="first_name" type="text" name="first_name" value="<?php echo $row_Recordset1['first_name']; ?>" size="32">
			    		<span id="first_name_error"></span>
			    		<input name="middle_initial" type="text" value="<?php echo $row_Recordset1['middle_initial']; ?>" size="1" maxlength="1">
			    		<input id="last_name" type="text" name="last_name" value="<?php echo $row_Recordset1['last_name']; ?>" size="32">
						<span id="last_name_error"></span>			    
			   </td>
			    </tr>
          <tr>
            <td><label>Date of Birth:</label></td>
			    <td><input id="birth_date" type="text" name="DOB" value="<?php echo $row_Recordset1['DOB']; ?>" size="10" /> 
			    </td>
			 </tr>			 
          <tr>
            <td><label>Email:</label></td>
			    <td><input id="email" type="text" name="email" value="<?php echo $row_Recordset1['email']; ?>" size="32">
			    <span id="email_error"></span></td>
			 </tr>
          <tr >
            <td ><label>Phone:</label></td>
			    <td><input id="phone" type="text" name="phone" value="<?php echo $row_Recordset1['phone']; ?>" size="32">
			    <span id="phone_error"></span></td>
			 </tr>
          <tr>
            <td><label>Address1:</label></td>
			    <td><input type="text" name="address1" value="<?php echo $row_Recordset1['address1']; ?>" size="32"></td>
			 </tr>
          <tr >
            <td><label>Address2:</label></td>
			    <td><input type="text" name="address2" value="<?php echo $row_Recordset1['address2']; ?>" size="32"></td>
			 </tr>
          <tr>
            <td><label>City:</label></td>
			    <td><input type="text" name="city" value="<?php echo $row_Recordset1['city']; ?>" size="32"></td>
			 </tr>
          <tr>
            <td><label>State:</label></td>
			    <td><input id="state_abbreviation" name="state" type="text" value="<?php echo $row_Recordset1['state']; ?>" size="2" maxlength="2"></td>
			 </tr>
          <tr >
            <td><label>zip:</label></td>
			    <td><input id="zip" type="text" name="zip" value="<?php echo $row_Recordset1['zip']; ?>" size="10"></td>
			 </tr>
          <tr>
            <td><label>New Password:</label></td>
			    <td>
			    <input name="password" type="password" id="password" value="<?php echo $row_Recordset1['passdecode']; ?>" size="32">
			    </td>
			 </tr>
			<tr>
				<td><label>Waiver of Liability:</label></td>
			   <td>			  	<div id="waiver">
				  <p>
				  I, and my heirs, in consideration of my participation in the Positive Spin Community 
				  Bike Project's Open Workshop hereby release Positive Spin,
				  its officers, employees and agents, and any other people officially connected with this 
				  organization, from any and all liability for damage to or loss of personal
				  property, sickness, or injury from whatever source, legal entanglements, imprisonment, 
				  death, or loss of money, which might occur while participating in said event/activity/class.
				  Specifically, I release Positive Spin from any liability or 
				  responsibility for my personal well-being, condition of tools and equipment provided 
				  and produced thereof, including, but not limited to, bicycles and modes of transportation 
				  produced by participants. The Positive Spin Community Bike Project is a working, 
				  mechanical environment and I am aware of the risks of participation. I hereby state 
				  that I am in sufficient physical condition to accept a rigorous level of physical 
				  activity and exertion, as is sometimes the case when working in a mechanical environment. 
				  I understand that participation in this program is strickly voluntary and I 
				  freely chose to participate. I understand Positive Spin does not provide medical coverage for me. 
				  I verify that I will be responsible  for any medical costs I incur as a result of my participation.
				  </p>
				  </div><input id="waiver_checkbox" type="checkbox"> I agree <span id="waiver_error"></span>
			  	<input type="submit" id="waiver_button" value="Show Waiver" \>

				</td>
			  </tr>
          	<tr>
          	 <td></td>
			    <td><br /><input id="submit_contact" type="submit" value="Submit"></td>
		    	</tr>
          </table>
		    <input type="hidden" name="list_yes_no" value="1">  <!--This overides the option not to be invited to the newsletter list.-->
        <input type="hidden" name="MM_insert" value="form1">
        <input type="hidden" name="contact_id" value="<?php echo $row_Recordset1['contact_id']; ?>">
        <input type="hidden" name="contact_id_entry" value="<?php echo $contact_id_entry; ?>">
        </form>
	  </tr>
</table>




<?php include("include_footer.html"); ?>

<?php
mysql_free_result($Recordset1);
?>
