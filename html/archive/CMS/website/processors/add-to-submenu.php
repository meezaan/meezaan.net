<?php
include ('../../_connections/db_connector.php');
include ('../../_includes/_functions/stripslashes.php');
include ('../../_includes/_functions/functions.php');
include ('../../_includes/check_signin.php');
include ('../../_includes/variables.php');

// Variables passed
$MENU_ID = stripanyslashes($_REQUEST['menuid']);
$PAGE_ID = stripanyslashes($_REQUEST['pageid']);
$PAGE_LEVEL = stripanyslashes($_REQUEST['pagelevel']);
$PARENT_ID = stripanyslashes($_REQUEST['parentid']);

checkEmptyVariable($MENU_ID);
checkEmptyVariable($PAGE_ID);
checkEmptyVariable($PARENT_ID);
CheckDuplicateMenus($MENU_ID,$PAGE_ID,$PAGE_LEVEL,$PARENT_ID);

//If it gets this far all the fields are filled in properly.  Enter data in database

mysql_query("INSERT INTO `menu` 
				(
					`menu_id`,
					`page_id`,
					`page_level`,
                                        `page_parent`
					)
					VALUES
				(
					'".$MENU_ID."',
					'".$PAGE_ID."',
					'".$PAGE_LEVEL."',
					'".$PARENT_ID."'
				)
			") or die(mysql_error());


header('location: ../index.php?section=Website Management&sectfunction=Menus&function=Add Sub Menu&menuid='.$MENU_ID.'&pageid='.$PARENT_ID);


?>