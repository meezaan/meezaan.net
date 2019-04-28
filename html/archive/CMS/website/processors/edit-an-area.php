<?php
include ('../../_connections/db_connector.php');
include ('../../_includes/_functions/stripslashes.php');
include ('../../_includes/_functions/functions.php');
include ('../../_includes/check_signin.php');
include ('../../_includes/variables.php');

// Variables passed
$AREA_ID = $_REQUEST['areaid'];
$AREACONTENT = stripanyslashes($_REQUEST['areacontent']);

checkEmptyVariable($AREA_ID);
checkEmptyVariable($AREACONTENT);

//If it gets this far all the fields are filled in properly.  Enter data in database

mysql_query("UPDATE `static_areas` SET 
				
					`area_content` = '".$AREACONTENT."'					
					WHERE
					`area_id` = '".$AREA_ID."'") or die(mysql_error());	
			
header('location: ../index.php?section=Website Management&sectfunction=Areas&errormessage='.$MESSAGE_AREA_EDITED_SUCCESSFULLY);

?>