<?php
include ('../../_connections/db_connector.php');
include ('../../_includes/_functions/stripslashes.php');
include ('../../_includes/_functions/functions.php');
include ('../../_includes/check_signin.php');
include ('../../_includes/variables.php');

// Variables passed
$CODE = stripanyslashes($_REQUEST['tracking-code']);

//If it gets this far all the fields are filled in properly.  Enter data in database

mysql_query("UPDATE `site_info` SET 
				
					`google_analytics` = '".$CODE."'")
					 or die(mysql_error());	
			
header('location: ../index.php?section=Website Management&errormessage='.$MESSAGE_UPDATED_ANALYTICS);

?>