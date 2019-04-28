<?php
include ('../../_connections/db_connector.php');
include ('../../_includes/_functions/stripslashes.php');
include ('../../_includes/_functions/functions.php');
include ('../../_includes/check_signin.php');
include ('../../_includes/variables.php');

// Variables passed
$NAME = stripanyslashes($_REQUEST['name']);
$USERNAME = stripanyslashes($_REQUEST['username']);
$EMAIL = stripanyslashes($_REQUEST['email']);
$TEL = stripanyslashes($_REQUEST['tel']);


checkEmptyVariable($NAME);
checkEmptyVariable($USERNAME);
checkEmptyVariable($EMAIL);

//If it gets this far all the fields are filled in properly.  Enter data in database

if (CheckEmailFormat($EMAIL)) { //if email format is correct, update database

mysql_query("UPDATE `users` SET 
				
				`user_name` = '".$NAME."',
				`user_username` = '".$USERNAME."',
				`user_email` = '".$EMAIL."',
				`user_tel` = '".$TEL."'
				 WHERE `user_id` = '".$_SESSION['user_id']."'") 
					
				or die(mysql_error());	
			
header('location: ../../index.php?errormessage='.$PROFILE_UPDATED);
}
else {
header('location: ../index.php?section=Current User&sectfunction=Update Profile&errormessage='.$EMAIL_MISFORMATTED);
}

?>