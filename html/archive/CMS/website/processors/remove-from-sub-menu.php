<?php
include ('../../_connections/db_connector.php');
include ('../../_includes/_functions/stripslashes.php');
include ('../../_includes/_functions/functions.php');
include ('../../_includes/check_signin.php');
include ('../../_includes/variables.php');

// Variables passed
$MENU_ID = stripanyslashes($_REQUEST['menuid']);
$PAGE_ID = stripanyslashes($_REQUEST['pageid']);
$PARENT_ID = stripanyslashes($_REQUEST['parentid']);
$PAGE_LEVEL = stripanyslashes($_REQUEST['pagelevel']);
$REFER_PAGE = stripanyslashes($_REQUEST['referrer']);

checkEmptyVariable($MENU_ID);
checkEmptyVariable($PAGE_ID);
checkEmptyVariable($PARENT_ID);
checkEmptyVariable($PAGE_LEVEL);

//If it gets this far all the fields are filled in properly.  Enter data in database

mysql_query("DELETE FROM `menu` WHERE 
					`menu_id` = '".$MENU_ID."' AND
					`page_id` = '".$PAGE_ID."' AND
					`page_parent` = '".$PARENT_ID."' AND
					`page_level` = '".$PAGE_LEVEL."'
                                         ") or die(mysql_error());
if ($REFER_PAGE == "mainmenu") {
header('location: ../index.php?section=Website Management&sectfunction=Menus&function=Edit a Menu&menuid='.$MENU_ID);
exit;
}

header('location: ../index.php?section=Website Management&sectfunction=Menus&function=Add Sub Menu&menuid='.$MENU_ID.'&pageid='.$PARENT_ID);
?>