<?php
include ('../../_connections/db_connector.php');
include ('../../_includes/_functions/stripslashes.php');
include ('../../_includes/_functions/functions.php');
include ('../../_includes/check_signin.php');
include ('../../_includes/variables.php');

// Variables passed
$PAGE_ID = $_REQUEST['pageid'];

checkEmptyVariable($PAGE_ID);

//If it gets this far a page id has been submitted after confirmation

mysql_query("DELETE FROM `page` WHERE `page_id` = '".$PAGE_ID."'") or die(mysql_error());	
mysql_query("DELETE FROM `menu` WHERE `page_id` = '".$PAGE_ID."'") or die(mysql_error());	
			
header('location: ../index.php?section=Website Management&sectfunction=Pages&errormessage='.$MESSAGE_PAGE_DELETED_SUCCESSFULLY);

?>