<?php
// DO NOT EDIT - USE Connections/local_configurations instead

# FileName="Connection_php_mysql.htm"
# Type="MYSQL"
# HTTP="true"
$hostname_YBDB = "enter_hostname";
$database_YBDB = "enter_schema";
$username_YBDB = "enter_username";
$password_YBDB = "enter_password";
if(file_exists('Connections/local_configurations.php')) {
	require_once('local_configurations.php');
}
$YBDB = mysql_pconnect($hostname_YBDB, $username_YBDB, $password_YBDB) or trigger_error(mysql_error(),E_USER_ERROR);

?>
