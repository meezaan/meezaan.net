<?php
include ('../../_connections/db_connector.php');
include ('../../_includes/_functions/stripslashes.php');
include ('../../_includes/_functions/functions.php');
include ('../../_includes/check_signin.php');
include ('../../_includes/variables.php');

// Variables passed
$NAME = stripanyslashes($_REQUEST['sitename']);
$TAGLINE = stripanyslashes($_REQUEST['tagline']);
$URL = stripanyslashes($_REQUEST['url']);

checkEmptyVariable($NAME);
checkEmptyVariable($URL);

//If it gets this far all the fields are filled in properly.  Enter data in database

mysql_query("UPDATE `site_info` SET 
				
					`site_name` = '".$NAME."',
					`site_tagline` = '".$TAGLINE."',
					`site_url` = '".$URL."'") or die(mysql_error());	
			
header('location: ../index.php?section=Licensing and Site Info&errormessage='.$MESSAGE_SITEINFO_UPDATED_SUCCESSFULLY);

?>