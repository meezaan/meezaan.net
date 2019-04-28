<?php
include ('../../_connections/db_connector.php');
include ('../../_includes/_functions/stripslashes.php');
include ('../../_includes/_functions/functions.php');
include ('../../_includes/check_signin.php');
include ('../../_includes/variables.php');

$TIME = time();
$GM_TIME = $TIME - date('Z', $TIME);

function getMysqlDate($TIME){return date("Y-m-d", $TIME);}
function getMysqlDatetime($TIME){return date("Y-m-d H:i:s", $TIME);}
function getTimestamp($MYSQL_DATETIME){return strtotime($MYSQL_DATETIME);}

$ADD_TIME = getMysqlDatetime($GM_TIME); //GMT TIME ALWAYS WILL GO INTO DATABASE

// Variables passed
$NEWSTITLE = $_REQUEST['newstitle'];
$NEWSCONTENT = stripanyslashes($_REQUEST['newscontent']);

checkEmptyVariable($NEWSCONTENT);
checkEmptyVariable($NEWSTITLE);

//If it gets this far all the fields are filled in properly.  Enter data in database

mysql_query("INSERT INTO `mod_news` 
			( `news_title`, `news_content`, `news_time`, `news_user_id`)
			VALUES
			('".$NEWSTITLE."','".$NEWSCONTENT."', '".$ADD_TIME."', '".$_SESSION['user_id']."')")
			or die(mysql_error());	
			
header('location: ../index.php?section=Modules&sectfunction=News&errormessage='.$MESSAGE_NEWS_ADDED_SUCCESSFULLY);

?>