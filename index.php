<?php
require_once('Connections/YBDB.php');
require_once('Connections/database_functions.php');

$shop_id = current_shop_by_ip();

//determine which page to load
if (isset($shop_id)) {
	$gotopage = PAGE_SHOP_LOG ."?shop_id={$shop_id}"; 
	header(sprintf("Location: %s",$gotopage ));
} else {
	$gotopage = PAGE_START_SHOP; 
	header(sprintf("Location: %s",$gotopage ));
}

?> 

