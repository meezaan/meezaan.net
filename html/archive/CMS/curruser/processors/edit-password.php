<?php
include ('../../_connections/db_connector.php');
include ('../../_includes/_functions/stripslashes.php');
include ('../../_includes/_functions/functions.php');
include ('../../_includes/check_signin.php');
include ('../../_includes/variables.php');

// Variables passed
$CURRPASS = stripanyslashes($_REQUEST['currentpassword']);
$NEWPASS = stripanyslashes($_REQUEST['newpassword']);
$CONFIRM = stripanyslashes($_REQUEST['confirmnewpassword']);

checkEmptyVariable($CURRPASS);
checkEmptyVariable($NEWPASS);
checkEmptyVariable($CONFIRM);

checkCurrentPassword(md5($CURRPASS), $_SESSION['user_id']);

checkPasswordMatch($NEWPASS, $CONFIRM);

//If it gets this far all the fields are filled in properly.  Enter data in database

mysql_query("UPDATE `users` SET 
				
				`user_password` = '".md5($NEWPASS)."' WHERE `user_id` = '".$_SESSION['user_id']."'") 
					
				or die(mysql_error());	
			
header('location: ../../index.php?errormessage='.$PASSWORD_CHANGED);

?>