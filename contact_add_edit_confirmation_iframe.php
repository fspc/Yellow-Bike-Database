<?php
//This function redirects the Log to the Welcome Page
function redirect($sec_delay){
$sec_delay = $sec_delay* 1000;
echo <<<EOD
<SCRIPT language="JavaScript"> 
<!--
 function getgoing()
  {
    top.location="http://www.ybdb.austinyellowbike.org/shop_log.php?" + location.search.substring(1);
   }
 
 setTimeout('getgoing()',$sec_delay);
//--> 
</SCRIPT>
EOD;
}
?>

<?php

if (isset($_GET['email'])) {
	$email = $_GET['email'];
} else {
	$email = -1;
}
	
$editFormAction = $_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING'];

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Untitled Document</title>
<?php 
//Welcome page redirects
	redirect(15); //Always redirect in 60 seconds on this page
?>
<link href="css_yb_standard.css" rel="stylesheet" type="text/css" />
</head>

<body>
<table   border="0" align="center" cellpadding="1" cellspacing="0" class="yb_standardCENTER">
  <tr>
    <td height="900" valign="top"><p class="yb_standardCENTERred">First-time sign in almost complete </p>
      <p>You have been sent an email to confirm your subscription to:</p>
      <p><strong>The Yellow Bike Project - Monthly Newsletter </strong></p>
      <p> To begin receiving the newsletter follow the instructions in the confirmation email. </p>
      <p>&nbsp;</p>
    <p>You will be redirected to the sign-in sheet in a moment. <span class="yb_standardCENTERred">Please sign in there. </span></p>
    </td>
  </tr>
</table>
<p>&nbsp;</p>
<IFRAME SRC="http://groups.google.com/group/yellowbike-newsletter/boxsubscribe?email=<?php echo $email;?>" WIDTH=760 HEIGHT=500>
</IFRAME>


<p>&nbsp;</p>
</body>
</html>
