<?php
include ('../../_connections/db_connector.php');
include ('../../_includes/_functions/stripslashes.php');
include ('../../_includes/_functions/functions.php');
include ('../../_includes/check_signin.php');
include ('../../_includes/variables.php');


// Variables passed
$NEWS_ID = $_REQUEST['newsitem'];
$NEWSTITLE = $_REQUEST['newstitle'];
$NEWSCONTENT = stripanyslashes($_REQUEST['newscontent']);

checkEmptyVariable($NEWS_ID);
checkEmptyVariable($NEWSCONTENT);
checkEmptyVariable($NEWSTITLE);

//If it gets this far all the fields are filled in properly.  Enter data in database

mysql_query("UPDATE `mod_news` SET 
				
					`news_title` = '".$NEWSTITLE."',
					`news_content` = '".$NEWSCONTENT."',
					`news_user_id` = '".$_SESSION['user_id']."'					
					WHERE
					`news_id` = '".$NEWS_ID."'") or die(mysql_error());	
			
header('location: ../index.php?section=Modules&sectfunction=News&errormessage='.$MESSAGE_NEWS_EDITED_SUCCESSFULLY);

?>