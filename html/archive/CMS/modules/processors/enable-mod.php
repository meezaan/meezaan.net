<?php
include ('../../_connections/db_connector.php');
include ('../../_includes/_functions/stripslashes.php');
include ('../../_includes/_functions/functions.php');
include ('../../_includes/check_signin.php');
include ('../../_includes/variables.php');

// Variables passed
$MODULE = stripanyslashes($_REQUEST['module']);


checkEmptyVariable($MODULE);

//If it gets this far all the fields are filled in properly.  Enter data in database

mysql_query("UPDATE `modules` SET 
				
					`mod_status_id` = '1'
					
					 WHERE `mod_id` = '".$MODULE."'") or die(mysql_error());	
			
header('location: ../index.php?section=Modules&errormessage='.$MODULE_ENABLED_SUCCESSFULLY);

?>