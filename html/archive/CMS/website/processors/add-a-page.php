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

$CREATION_TIME = getMysqlDatetime($GM_TIME); //GMT TIME ALWAYS WILL GO INTO DATABASE

// Variables passed
$PAGETITLE = stripanyslashes($_REQUEST['pagetitle']);
$PAGEHEADER = stripanyslashes($_REQUEST['pageheader']);
$PAGECONTENT = stripanyslashes($_REQUEST['pagecontent']);
$SIDEBARID = stripanyslashes($_REQUEST['sidebar_id']);

checkEmptyVariable($PAGETITLE);
checkEmptyVariable($PAGEHEADER);
checkEmptyVariable($PAGECONTENT);

//If it gets this far all the fields are filled in properly.  Enter data in database

mysql_query("INSERT INTO `page` 
				(
					`page_title`,
					`page_header`,
					`page_tag`,
					`page_content`,
					`page_creator`,
					`page_created_time`,
					`page_last_editor`,
					`page_last_edited_time`,
					`sidebar_id`
				)
					VALUES
				(
					'".$PAGETITLE."',
					'".$PAGEHEADER."',
					'test-tag-for-site',
					'".$PAGECONTENT."',
					'".$_SESSION['user_id']."',
					'".$CREATION_TIME."',
					'".$_SESSION['user_id']."',
					'".$CREATION_TIME."',
					'".$SIDEBARID."'
				)
			") or die(mysql_error());	

header('location: ../index.php?section=Website Management&sectfunction=Pages&errormessage='.$MESSAGE_PAGE_CREATED_SUCCESSFULLY);

?>