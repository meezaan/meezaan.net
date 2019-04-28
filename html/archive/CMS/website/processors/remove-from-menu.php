<?php
include ('../../_connections/db_connector.php');
include ('../../_includes/_functions/stripslashes.php');
include ('../../_includes/_functions/functions.php');
include ('../../_includes/check_signin.php');
include ('../../_includes/variables.php');

// Variables passed
$MENU_ID = stripanyslashes($_REQUEST['menuid']);
$PAGE_ID = stripanyslashes($_REQUEST['pageid']);

checkEmptyVariable($MENU_ID);
checkEmptyVariable($PAGE_ID);

//If it gets this far all the fields are filled in properly.  Enter data in database

mysql_query("DELETE FROM `menu` WHERE 
					(`menu_id` = '".$MENU_ID."' AND
					`page_id` = '".$PAGE_ID."') OR
					(`menu_id` = '".$MENU_ID."' AND
					`page_parent` = '".$PAGE_ID."')
                                         ") or die(mysql_error());

header('location: ../index.php?section=Website Management&sectfunction=Menus&function=Edit a Menu&menuid='.$MENU_ID);

?>