<?php
include ('../../_connections/db_connector.php');
include ('../../_includes/_functions/stripslashes.php');
include ('../../_includes/_functions/functions.php');
include ('../../_includes/check_signin.php');
include ('../../_includes/variables.php');

// Variables passed
$TITLE = stripanyslashes($_REQUEST['metatitle']);
$SUBTITLE = stripanyslashes($_REQUEST['metasubtitle']);
$KEYWORDS = stripanyslashes($_REQUEST['metakeywords']);
$DESC = stripanyslashes($_REQUEST['metadesc']);

checkEmptyVariable($TITLE);
checkEmptyVariable($SUBTITLE);
checkEmptyVariable($KEYWORDS);
checkEmptyVariable($DESC);

//If it gets this far all the fields are filled in properly.  Enter data in database

mysql_query("UPDATE `meta` SET 
				
					`meta_title` = '".$TITLE."',
					`meta_subtitle` = '".$SUBTITLE."',
					`meta_keywords` = '".$KEYWORDS."',
					`meta_description` = '".$DESC."'") or die(mysql_error());	
			
header('location: ../index.php?section=Meta and SEO&errormessage='.$MESSAGE_META_UPDATED_SUCCESSFULLY);

?>