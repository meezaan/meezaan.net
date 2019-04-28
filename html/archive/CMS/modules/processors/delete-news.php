<?php
include ('../../_connections/db_connector.php');
include ('../../_includes/_functions/stripslashes.php');
include ('../../_includes/_functions/functions.php');
include ('../../_includes/check_signin.php');
include ('../../_includes/variables.php');

// Variables passed
$NEWS_ID = $_REQUEST['newsitem'];

checkEmptyVariable($NEWS_ID);

//If it gets this far a sidebar id has been submitted after confirmation

mysql_query("DELETE FROM `mod_news` WHERE `news_id` = '".$NEWS_ID."'") or die(mysql_error());	
			
header('location: ../index.php?section=Modules&sectfunction=News&errormessage='.$MESSAGE_NEWS_DELETED_SUCCESSFULLY);

?>