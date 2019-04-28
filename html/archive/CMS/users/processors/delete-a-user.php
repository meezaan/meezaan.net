<?php
include ('../../_connections/db_connector.php');
include ('../../_includes/_functions/stripslashes.php');
include ('../../_includes/_functions/functions.php');
include ('../../_includes/check_signin.php');
include ('../../_includes/variables.php');

// Variables passed
$USER_ID = $_REQUEST['userid'];

checkEmptyVariable($USER_ID);

//If it gets this far a user id has been submitted after confirmation

mysql_query("DELETE FROM `users` WHERE `user_id` = '".$USER_ID."'") or die(mysql_error());	
mysql_query("UPDATE `page` SET `page_last_editor` = '1' WHERE `page_last_editor`= '".$USER_ID."'") or die(mysql_error());	
mysql_query("UPDATE `page` SET `page_creator` = '1' WHERE `page_creator`= '".$USER_ID."'") or die(mysql_error());
mysql_query("UPDATE `sidebars` SET `sidebar_creator` = '1' WHERE `sidebar_creator`= '".$USER_ID."'") or die(mysql_error());
mysql_query("UPDATE `sidebars` SET `sidebar_last_editor` = '1' WHERE `sidebar_last_editor`= '".$USER_ID."'") or die(mysql_error());
			
header('location: ../index.php?section=Users&errormessage='.$MESSAGE_USER_DELETED_SUCCESSFULLY);

?>