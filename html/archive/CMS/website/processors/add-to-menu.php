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
CheckDuplicateMenus($MENU_ID,$PAGE_ID,'0','0');

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
					'0',
					''

				)
			") or die(mysql_error());	

header('location: ../index.php?section=Website Management&sectfunction=Menus&function=Edit a Menu&menuid='.$MENU_ID);

?>