<?php
include ('../../_connections/db_connector.php');
include ('../../_includes/_functions/stripslashes.php');
include ('../../_includes/_functions/functions.php');
include ('../../_includes/check_signin.php');
include ('../../_includes/variables.php');

// Variables passed
$SIDEBAR_ID = $_REQUEST['sidebarid'];

checkEmptyVariable($SIDEBAR_ID);

//If it gets this far a sidebar id has been submitted after confirmation

mysql_query("DELETE FROM `sidebars` WHERE `sidebar_id` = '".$SIDEBAR_ID."'") or die(mysql_error());	
			
header('location: ../index.php?section=Website Management&sectfunction=Sidebars&errormessage='.$MESSAGE_SIDEBAR_DELETED_SUCCESSFULLY);

?>