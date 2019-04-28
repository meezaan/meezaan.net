<?php
include ('../../_connections/db_connector.php');
include ('../../_includes/_functions/stripslashes.php');
include ('../../_includes/_functions/functions.php');
include ('../../_includes/check_signin.php');
include ('../../_includes/variables.php');

//GET MESSAGE  CREATION TIME
$TIME = time();
$GM_TIME = $TIME - date('Z', $TIME);

function getMysqlDate($TIME){return date("Y-m-d", $TIME);}
function getMysqlDatetime($TIME){return date("Y-m-d H:i:s", $TIME);}
function getTimestamp($MYSQL_DATETIME){return strtotime($MYSQL_DATETIME);}

$EDIT_TIME = getMysqlDatetime($GM_TIME); //GMT TIME ALWAYS WILL GO INTO DATABASE

// Variables passed
$SIDEBAR_ID = $_REQUEST['sidebarid'];
$SIDEBARNAME = stripanyslashes($_REQUEST['sidebarname']);
$SIDEBARHEADER = stripanyslashes($_REQUEST['sidebarheader']);
$SIDEBARCONTENT = stripanyslashes($_REQUEST['sidebarcontent']);

checkEmptyVariable($SIDEBAR_ID);
checkEmptyVariable($SIDEBARNAME);
checkEmptyVariable($SIDEBARHEADER);
checkEmptyVariable($SIDEBARCONTENT);

//If it gets this far all the fields are filled in properly.  Enter data in database

mysql_query("UPDATE `sidebars` SET 
				
					`sidebar_name` = '".$SIDEBARNAME."',
					`sidebar_header` = '".$SIDEBARHEADER."',
					`sidebar_content` = '".$SIDEBARCONTENT."',
					`sidebar_last_editor` = '".$_SESSION['user_id']."',
					`sidebar_last_edit_time` = '".$EDIT_TIME."'
					
					WHERE `sidebar_id` = '".$SIDEBAR_ID."'") or die(mysql_error());	
			
header('location: ../index.php?section=Website Management&sectfunction=Sidebars&errormessage='.$MESSAGE_SIDEBAR_EDITED_SUCCESSFULLY);

?>